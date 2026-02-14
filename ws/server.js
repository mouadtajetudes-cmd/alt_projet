require('dotenv').config();
const express = require('express');
const http = require('http');
const WebSocket = require('ws');
const cors = require('cors');
const multer = require('multer');
const path = require('path');
const fs = require('fs');
const { MongoClient, ObjectId } = require('mongodb');
const { Pool } = require('pg');

const PORT = process.env.WS_PORT || 3001;
const MONGO_URI = process.env.MONGO_URI || 'mongodb://alt.mongo:27017';

// PostgreSQL connection
const pgPool = new Pool({
  host: process.env.POSTGRES_HOST || 'alt.db',
  port: process.env.POSTGRES_PORT || 5432,
  database: process.env.POSTGRES_DB || 'alt',
  user: process.env.POSTGRES_USER || 'alt',
  password: process.env.POSTGRES_PASSWORD || 'alt'
});

const app = express();
app.use(cors());
app.use(express.json());

const uploadsDir = path.join(__dirname, 'uploads');
if (!fs.existsSync(uploadsDir)) {
  fs.mkdirSync(uploadsDir, { recursive: true });
}

app.use('/uploads', express.static(uploadsDir));

const storage = multer.diskStorage({
  destination: (req, file, cb) => {
    cb(null, uploadsDir);
  },
  filename: (req, file, cb) => {
    const uniqueSuffix = Date.now() + '-' + Math.round(Math.random() * 1E9);
    cb(null, uniqueSuffix + path.extname(file.originalname));
  }
});

const upload = multer({
  storage: storage,
  limits: { fileSize: 10 * 1024 * 1024 },
  fileFilter: (req, file, cb) => {
    const allowedTypes = /jpeg|jpg|png|gif|pdf|doc|docx|txt|zip|rar/;
    const extname = allowedTypes.test(path.extname(file.originalname).toLowerCase());
    const mimetype = allowedTypes.test(file.mimetype);
    if (mimetype && extname) return cb(null, true);
    cb(new Error('Type de fichier non autorisé!'));
  }
});

const server = http.createServer(app);
const wss = new WebSocket.Server({ server });

let mongoDB;

async function connectMongoDB() {
  const client = new MongoClient(MONGO_URI);
  await client.connect();
  mongoDB = client.db('chat_db');
  console.log('✅ MongoDB connecté');
}

const onlineUsers = new Map();

app.get('/health', (req, res) => {
  res.json({
    status: 'ok',
    onlineUsers: Array.from(onlineUsers.keys())
  });
});

app.post('/upload', upload.single('file'), async (req, res) => {
  try {
    if (!req.file) return res.status(400).json({ error: 'Aucun fichier fourni' });
    const fileUrl = `${req.protocol}://${req.get('host')}/uploads/${req.file.filename}`;
    res.json({
      success: true,
      file: {
        filename: req.file.filename,
        originalName: req.file.originalname,
        size: req.file.size,
        mimetype: req.file.mimetype,
        url: fileUrl
      }
    });
  } catch (error) {
    console.error('Erreur upload:', error);
    res.status(500).json({ error: error.message });
  }
});

app.get('/conversations/:userId', async (req, res) => {
  try {
    const userId = String(req.params.userId);

    const conversations = await mongoDB.collection('conversations')
      .find({ participants: userId })
      .sort({ updatedAt: -1 })
      .toArray();

    const conversationsWithDetails = await Promise.all(
      conversations.map(async (conv) => {
        const convId = conv._id.toString();

        const lastMessage = await mongoDB.collection('messages')
          .findOne(
            { conversationId: convId },
            { sort: { createdAt: -1 } }
          );

        const unreadCount = await mongoDB.collection('messages')
          .countDocuments({
            conversationId: convId,
            senderId: { $ne: userId },
            isRead: false
          });

        return {
          ...conv,
          _id: convId,
          lastMessage: lastMessage ? formatMessage(lastMessage) : null,
          unreadCount
        };
      })
    );

    res.json(conversationsWithDetails);
  } catch (error) {
    console.error('Erreur récupération conversations:', error);
    res.status(500).json({ error: error.message });
  }
});

app.get('/conversations/:convId/messages', async (req, res) => {
  try {
    const { convId } = req.params;
    const limit = parseInt(req.query.limit) || 50;
    const page = parseInt(req.query.page) || 1;
    const skip = (page - 1) * limit;

    const messages = await mongoDB.collection('messages')
      .find({ conversationId: convId })
      .sort({ createdAt: 1 })
      .skip(skip)
      .limit(limit)
      .toArray();

    res.json({ messages: messages.map(formatMessage) });
  } catch (error) {
    console.error('Erreur récupération messages:', error);
    res.status(500).json({ error: error.message });
  }
});

app.post('/conversations', async (req, res) => {
  try {
    const { participants, name } = req.body;

    if (!participants || participants.length < 2) {
      return res.status(400).json({ error: 'Au moins 2 participants requis' });
    }

    const stringParticipants = participants.map(p => String(p));

    // Block self-conversation
    const uniqueParticipants = [...new Set(stringParticipants)];
    if (uniqueParticipants.length < 2) {
      return res.status(400).json({ error: 'Impossible de créer une conversation avec soi-même' });
    }

    if (stringParticipants.length === 2) {
      const existing = await mongoDB.collection('conversations')
        .findOne({
          participants: { $all: stringParticipants, $size: 2 }
        });

      if (existing) {
        return res.json({ ...existing, _id: existing._id.toString() });
      }
    }

    const conversation = {
      participants: stringParticipants,
      name: name || null,
      createdAt: new Date(),
      updatedAt: new Date()
    };

    const result = await mongoDB.collection('conversations').insertOne(conversation);
    conversation._id = result.insertedId.toString();

    res.json(conversation);
  } catch (error) {
    console.error('Erreur création conversation:', error);
    res.status(500).json({ error: error.message });
  }
});

app.put('/conversations/:convId/read', async (req, res) => {
  try {
    const { convId } = req.params;
    const { userId } = req.body;

    if (!userId) return res.status(400).json({ error: 'userId requis' });

    await mongoDB.collection('messages').updateMany(
      {
        conversationId: convId,
        senderId: { $ne: String(userId) },
        isRead: false
      },
      { $set: { isRead: true } }
    );

    res.json({ success: true });
  } catch (error) {
    console.error('Erreur marquage lecture:', error);
    res.status(500).json({ error: error.message });
  }
});

// ==================== GROUPS ROUTES ====================

// Get all groups for a user
app.get('/groups/user/:userId', async (req, res) => {
  try {
    const { userId } = req.params;
    const result = await pgPool.query(`
      SELECT g.*, mg.role, u.nom || ' ' || u.prenom as createur_nom,
        (SELECT COUNT(*) FROM membre_groupe WHERE id_groupe = g.id_groupe) as membres_count,
        (SELECT COUNT(*) FROM salles WHERE id_groupe = g.id_groupe) as salles_count
      FROM groupes g
      JOIN membre_groupe mg ON g.id_groupe = mg.id_groupe
      LEFT JOIN utilisateurs u ON g.createur_id = u.id_utilisateur
      WHERE mg.id_utilisateur = $1
      ORDER BY g.date_modification DESC
    `, [userId]);
    res.json(result.rows);
  } catch (error) {
    console.error('Erreur récupération groupes:', error);
    res.status(500).json({ error: error.message });
  }
});

app.get('/groups/:id', async (req, res) => {
  try {
    const { id } = req.params;
    const groupResult = await pgPool.query(`
      SELECT g.*, u.nom || ' ' || u.prenom as createur_nom
      FROM groupes g
      LEFT JOIN utilisateurs u ON g.createur_id = u.id_utilisateur
      WHERE g.id_groupe = $1
    `, [id]);

    if (groupResult.rows.length === 0) {
      return res.status(404).json({ error: 'Groupe non trouvé' });
    }

    const membresResult = await pgPool.query(`
      SELECT mg.id_utilisateur, mg.role, mg.joined_at,
        u.nom, u.prenom, u.email, u.administrateur, u.premium
      FROM membre_groupe mg
      JOIN utilisateurs u ON mg.id_utilisateur = u.id_utilisateur
      WHERE mg.id_groupe = $1
      ORDER BY mg.role, u.nom
    `, [id]);

    const sallesResult = await pgPool.query(`
      SELECT * FROM salles WHERE id_groupe = $1 ORDER BY date_creation
    `, [id]);

    res.json({
      ...groupResult.rows[0],
      membres: membresResult.rows,
      salles: sallesResult.rows
    });
  } catch (error) {
    console.error('Erreur récupération détails groupe:', error);
    res.status(500).json({ error: error.message });
  }
});

app.post('/groups', async (req, res) => {
  try {
    const { nom, description, avatar_url, niveau, createur_id } = req.body;

    if (!nom || !createur_id) {
      return res.status(400).json({ error: 'Nom et créateur requis' });
    }

    const result = await pgPool.query(`
      INSERT INTO groupes (nom, description, avatar_url, niveau, createur_id)
      VALUES ($1, $2, $3, $4, $5)
      RETURNING *
    `, [nom, description, avatar_url, niveau || 1, createur_id]);

    const groupe = result.rows[0];

    // Add creator as admin member
    await pgPool.query(`
      INSERT INTO membre_groupe (id_groupe, id_utilisateur, role)
      VALUES ($1, $2, 'admin')
    `, [groupe.id_groupe, createur_id]);

    // Create default "Général" salle
    await pgPool.query(`
      INSERT INTO salles (nom, id_groupe, description)
      VALUES ('Général', $1, 'Salle de discussion générale')
    `, [groupe.id_groupe]);

    res.json(groupe);
  } catch (error) {
    console.error('Erreur création groupe:', error);
    res.status(500).json({ error: error.message });
  }
});

app.put('/groups/:id', async (req, res) => {
  try {
    const { id } = req.params;
    const { nom, description, avatar_url, niveau } = req.body;

    const result = await pgPool.query(`
      UPDATE groupes
      SET nom = COALESCE($1, nom),
          description = COALESCE($2, description),
          avatar_url = COALESCE($3, avatar_url),
          niveau = COALESCE($4, niveau),
          date_modification = CURRENT_TIMESTAMP
      WHERE id_groupe = $5
      RETURNING *
    `, [nom, description, avatar_url, niveau, id]);

    if (result.rows.length === 0) {
      return res.status(404).json({ error: 'Groupe non trouvé' });
    }

    res.json(result.rows[0]);
  } catch (error) {
    console.error('Erreur mise à jour groupe:', error);
    res.status(500).json({ error: error.message });
  }
});

app.delete('/groups/:id', async (req, res) => {
  try {
    const { id } = req.params;
    await pgPool.query('DELETE FROM groupes WHERE id_groupe = $1', [id]);
    res.json({ success: true });
  } catch (error) {
    console.error('Erreur suppression groupe:', error);
    res.status(500).json({ error: error.message });
  }
});

app.post('/groups/:id/members', async (req, res) => {
  try {
    const { id } = req.params;
    const { userId, role } = req.body;

    if (!userId) {
      return res.status(400).json({ error: 'userId requis' });
    }

    await pgPool.query(`
      INSERT INTO membre_groupe (id_groupe, id_utilisateur, role)
      VALUES ($1, $2, $3)
      ON CONFLICT (id_groupe, id_utilisateur) DO UPDATE SET role = $3
    `, [id, userId, role || 'membre']);

    res.json({ success: true });
  } catch (error) {
    console.error('Erreur ajout membre:', error);
    res.status(500).json({ error: error.message });
  }
});

app.delete('/groups/:id/members/:userId', async (req, res) => {
  try {
    const { id, userId } = req.params;
    await pgPool.query(`
      DELETE FROM membre_groupe WHERE id_groupe = $1 AND id_utilisateur = $2
    `, [id, userId]);
    res.json({ success: true });
  } catch (error) {
    console.error('Erreur suppression membre:', error);
    res.status(500).json({ error: error.message });
  }
});

app.get('/groups/:id/salles', async (req, res) => {
  try {
    const { id } = req.params;
    const result = await pgPool.query(`
      SELECT * FROM salles WHERE id_groupe = $1 ORDER BY date_creation
    `, [id]);
    res.json(result.rows);
  } catch (error) {
    console.error('Erreur récupération salles:', error);
    res.status(500).json({ error: error.message });
  }
});

app.post('/groups/:id/salles', async (req, res) => {
  try {
    const { id } = req.params;
    const { nom, description } = req.body;

    if (!nom) {
      return res.status(400).json({ error: 'Nom de salle requis' });
    }

    const result = await pgPool.query(`
      INSERT INTO salles (nom, id_groupe, description)
      VALUES ($1, $2, $3)
      RETURNING *
    `, [nom, id, description]);

    res.json(result.rows[0]);
  } catch (error) {
    console.error('Erreur création salle:', error);
    res.status(500).json({ error: error.message });
  }
});

app.delete('/salles/:id', async (req, res) => {
  try {
    const { id } = req.params;
    await pgPool.query('DELETE FROM salles WHERE id_salle = $1', [id]);
    res.json({ success: true });
  } catch (error) {
    console.error('Erreur suppression salle:', error);
    res.status(500).json({ error: error.message });
  }
});

app.post('/conversations/:convId/pin', async (req, res) => {
  try {
    const { convId } = req.params;
    const { userId, pinned } = req.body;

    if (!userId) {
      return res.status(400).json({ error: 'userId requis' });
    }

    if (pinned) {
      await pgPool.query(`
        INSERT INTO conversations_epinglees (id_utilisateur, conversation_id)
        VALUES ($1, $2)
        ON CONFLICT DO NOTHING
      `, [userId, convId]);
    } else {
      await pgPool.query(`
        DELETE FROM conversations_epinglees
        WHERE id_utilisateur = $1 AND conversation_id = $2
      `, [userId, convId]);
    }

    res.json({ success: true });
  } catch (error) {
    console.error('Erreur épinglage conversation:', error);
    res.status(500).json({ error: error.message });
  }
});

app.get('/conversations/pinned/:userId', async (req, res) => {
  try {
    const { userId } = req.params;
    const result = await pgPool.query(`
      SELECT conversation_id FROM conversations_epinglees
      WHERE id_utilisateur = $1
      ORDER BY date_epingle DESC
    `, [userId]);
    res.json(result.rows.map(r => r.conversation_id));
  } catch (error) {
    console.error('Erreur récupération conversations épinglées:', error);
    res.status(500).json({ error: error.message });
  }
});

async function getConversationParticipants(conversationId) {
  try {
    const conv = await mongoDB.collection('conversations').findOne({
      _id: new ObjectId(conversationId)
    });
    return conv ? conv.participants : [];
  } catch (e) {
    return [];
  }
}

function sendToUser(userId, payload) {
  const userWs = onlineUsers.get(String(userId));
  if (userWs && userWs.readyState === WebSocket.OPEN) {
    userWs.send(JSON.stringify(payload));
  }
}

async function broadcastToConversation(conversationId, payload, excludeUserId = null) {
  const participants = await getConversationParticipants(conversationId);
  participants.forEach(participantId => {
    if (participantId !== excludeUserId) {
      sendToUser(participantId, payload);
    }
  });
}

wss.on('connection', (ws) => {
  console.log('✅ Nouvelle connexion WebSocket');

  ws.on('message', async (data) => {
    try {
      const msg = JSON.parse(data.toString());

      if (msg.type === 'auth') {
        const userId = String(msg.userId || 'guest_' + Date.now());
        const userName = msg.userName || userId;

        ws.userId = userId;
        ws.userName = userName;
        onlineUsers.set(userId, ws);

        ws.send(JSON.stringify({ type: 'auth:ok', userId }));
        broadcastUserStatus(userId, 'online');
        return;
      }

      if (msg.type === 'send') {
        const { conversationId, content, messageType, mediaUrl, mediaName, mediaSize, replyTo } = msg;

        if (!ws.userId) {
          ws.send(JSON.stringify({ type: 'error', message: 'Non authentifié' }));
          return;
        }

        if (!conversationId) {
          ws.send(JSON.stringify({ type: 'error', message: 'conversationId requis' }));
          return;
        }

        if (content && content.length > 2000) {
          ws.send(JSON.stringify({
            type: 'error',
            message: 'Message trop long. Max: 2000 caractères (compte gratuit)'
          }));
          return;
        }

        const messageDoc = {
          conversationId: conversationId,
          senderId: ws.userId,
          sender: ws.userName,
          content: content || '',
          type: messageType || 'text',
          mediaUrl: mediaUrl || null,
          mediaName: mediaName || null,
          mediaSize: mediaSize || null,
          replyTo: replyTo || null,
          isRead: false,
          createdAt: new Date()
        };

        const result = await mongoDB.collection('messages').insertOne(messageDoc);
        messageDoc._id = result.insertedId;

        await mongoDB.collection('conversations').updateOne(
          { _id: new ObjectId(conversationId) },
          { $set: { updatedAt: new Date() } }
        );

        const formatted = formatMessage(messageDoc);

        const participants = await getConversationParticipants(conversationId);
        participants.forEach(participantId => {
          sendToUser(participantId, {
            type: 'message',
            message: formatted
          });
        });

        return;
      }

      if (msg.type === 'typing') {
        if (!ws.userId || !msg.conversationId) return;
        await broadcastToConversation(msg.conversationId, {
          type: 'typing',
          userId: ws.userId,
          sender: ws.userName,
          conversationId: msg.conversationId
        }, ws.userId);
        return;
      }

      if (msg.type === 'stopTyping') {
        if (!ws.userId || !msg.conversationId) return;
        await broadcastToConversation(msg.conversationId, {
          type: 'stopTyping',
          userId: ws.userId,
          conversationId: msg.conversationId
        }, ws.userId);
        return;
      }

      if (msg.type === 'react') {
        const { messageId, emoji } = msg;
        
        if (!ws.userId || !messageId || !emoji) {
          ws.send(JSON.stringify({ type: 'error', message: 'Données manquantes' }));
          return;
        }

        try {
          const message = await mongoDB.collection('messages').findOne({ _id: new ObjectId(messageId) });
          if (!message) {
            ws.send(JSON.stringify({ type: 'error', message: 'Message non trouvé' }));
            return;
          }

          const reactions = message.reactions || [];
          const existingIndex = reactions.findIndex(r => r.userId === ws.userId && r.emoji === emoji);
          
          if (existingIndex >= 0) {
            // Remove reaction
            reactions.splice(existingIndex, 1);
          } else {
            // Add reaction
            reactions.push({ userId: ws.userId, userName: ws.userName, emoji });
          }

          await mongoDB.collection('messages').updateOne(
            { _id: new ObjectId(messageId) },
            { $set: { reactions } }
          );

          await broadcastToConversation(message.conversationId, {
            type: 'reaction',
            messageId,
            reactions
          });
        } catch (error) {
          ws.send(JSON.stringify({ type: 'error', message: error.message }));
        }
        return;
      }

      if (msg.type === 'delete') {
        const { messageId } = msg;
        
        if (!ws.userId || !messageId) {
          ws.send(JSON.stringify({ type: 'error', message: 'Données manquantes' }));
          return;
        }

        try {
          const message = await mongoDB.collection('messages').findOne({ _id: new ObjectId(messageId) });
          if (!message) {
            ws.send(JSON.stringify({ type: 'error', message: 'Message non trouvé' }));
            return;
          }

          // Only allow deleting own messages
          if (message.senderId !== ws.userId) {
            ws.send(JSON.stringify({ type: 'error', message: 'Non autorisé' }));
            return;
          }

          await mongoDB.collection('messages').updateOne(
            { _id: new ObjectId(messageId) },
            { $set: { deleted: true, content: '', mediaUrl: null, mediaName: null, mediaSize: null, reactions: [] } }
          );

          await broadcastToConversation(message.conversationId, {
            type: 'deleted',
            messageId
          });
        } catch (error) {
          ws.send(JSON.stringify({ type: 'error', message: error.message }));
        }
        return;
      }

      if (msg.type === 'edit') {
        const { messageId, newContent, conversationId } = msg;
        
        if (!ws.userId || !messageId || !newContent) {
          ws.send(JSON.stringify({ type: 'error', message: 'Données manquantes' }));
          return;
        }

        try {
          const message = await mongoDB.collection('messages').findOne({ _id: new ObjectId(messageId) });
          if (!message) {
            ws.send(JSON.stringify({ type: 'error', message: 'Message non trouvé' }));
            return;
          }

          // Only allow editing own messages
          if (message.senderId !== ws.userId) {
            ws.send(JSON.stringify({ type: 'error', message: 'Non autorisé' }));
            return;
          }

          await mongoDB.collection('messages').updateOne(
            { _id: new ObjectId(messageId) },
            { $set: { content: newContent, edited: true, editedAt: new Date() } }
          );

          await broadcastToConversation(message.conversationId || conversationId, {
            type: 'edited',
            messageId,
            newContent
          });
        } catch (error) {
          ws.send(JSON.stringify({ type: 'error', message: error.message }));
        }
        return;
      }

    } catch (error) {
      console.error('Erreur WebSocket:', error);
      ws.send(JSON.stringify({ type: 'error', message: error.message }));
    }
  });

  ws.on('close', () => {
    if (ws.userId) {
      onlineUsers.delete(ws.userId);
      broadcastUserStatus(ws.userId, 'offline');
      console.log(`❌ Utilisateur ${ws.userId} déconnecté`);
    }
  });

  ws.on('error', (error) => {
    console.error('WebSocket error:', error);
  });
});

function formatMessage(msg) {
  return {
    id: msg._id.toString(),
    conversationId: msg.conversationId,
    userId: msg.senderId,
    sender: msg.sender || msg.senderId,
    content: msg.content,
    type: msg.type,
    mediaUrl: msg.mediaUrl,
    mediaName: msg.mediaName,
    mediaSize: msg.mediaSize,
    isRead: msg.isRead,
    reactions: msg.reactions || [],
    deleted: msg.deleted || false,
    time: msg.createdAt instanceof Date ? msg.createdAt.toISOString() : msg.createdAt
  };
}

function broadcastUserStatus(userId, status) {
  wss.clients.forEach(client => {
    if (client.readyState === WebSocket.OPEN) {
      client.send(JSON.stringify({
        type: 'user:status',
        userId,
        status
      }));
    }
  });
}

async function cleanupBrokenConversations() {
  try {
    const result = await mongoDB.collection('conversations').deleteMany({
      participants: 'undefined'
    });
    if (result.deletedCount > 0) {
      console.log(`🧹 Nettoyé ${result.deletedCount} conversations corrompues`);
    }

    const msgResult = await mongoDB.collection('messages').deleteMany({
      senderId: 'undefined'
    });
    if (msgResult.deletedCount > 0) {
      console.log(`🧹 Nettoyé ${msgResult.deletedCount} messages corrompus`);
    }
  } catch (e) {
    console.error('Erreur nettoyage:', e);
  }
}

async function start() {
  try {
    await connectMongoDB();
    await cleanupBrokenConversations();
    server.listen(PORT, () => {
      console.log(`🚀 WebSocket + REST API sur port ${PORT}`);
      console.log(`📁 Uploads disponibles sur http://localhost:${PORT}/uploads`);
    });
  } catch (error) {
    console.error('❌ Erreur démarrage:', error);
    process.exit(1);
  }
}

start();

