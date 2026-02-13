require('dotenv').config();
const express = require('express');
const http = require('http');
const WebSocket = require('ws');
const cors = require('cors');
const { MongoClient, ObjectId } = require('mongodb');

const PORT = process.env.WS_PORT || 3001;
const MONGO_URI = process.env.MONGO_URI || 'mongodb://alt.mongo:27017';
const CONV_ID = '698463bcfeacf0d5b5ce3efb';

// const jwt = require('jsonwebtoken');
// const JWT_SECRET = process.env.JWT_SECRET;
// plus tard 

const app = express();
app.use(cors());

app.get('/health', (req, res) => {
  res.json({ status: 'ok' });
});

const server = http.createServer(app);
const wss = new WebSocket.Server({ server });

let mongoDB;

async function connectMongoDB() {
  const client = new MongoClient(MONGO_URI);
  await client.connect();
  mongoDB = client.db('chat_db');
  console.log('[MongoDB] Connected');
}

const clients = new Map(); 

wss.on('connection', (ws) => {
  console.log('[WS] New connection');

  ws.on('message', async (data) => {
    try {
      const msg = JSON.parse(data.toString());
      console.log('[SERVER] Message received, type:', msg.type, msg);

      if (msg.type === 'auth') {
        let userId = msg.userId || 'guest_' + Date.now();
        
        /*
        const token = msg.token;
        if (token) {
          try {
            const decoded = jwt.verify(token, JWT_SECRET);
            userId = decoded.userId || decoded.id;
          } catch (error) {
            ws.send(JSON.stringify({ type: 'error', message: 'Token invalide' }));
            return;
          }
        }
        // plus tard
        */
        
        const userName = msg.userName || userId;
        clients.set(ws, { userId, userName });
        ws.send(JSON.stringify({ type: 'auth:ok', userId }));
        const messages = await mongoDB.collection('messages')
          .find({ conversationId: CONV_ID })
          .sort({ createdAt: 1 })
          .toArray();
        
        const history = messages.map(m => ({
          id: m._id.toString(),
          userId: m.senderId,
          sender: m.sender || m.senderId,
          content: m.content,
          time: m.createdAt.toISOString()
        }));
        
        ws.send(JSON.stringify({ type: 'history', messages: history }));
        console.log(`[AUTH] ${userId}, ${history.length} messages loaded`);
        return;
      }

      if (msg.type === 'send') {
        console.log('[SEND] Processing send message...');
        const clientInfo = clients.get(ws);
        console.log('[SEND] clientInfo:', clientInfo);
        const userId = clientInfo.userId;
        const sender = msg.sender || clientInfo.userName || userId;
        console.log('[SEND] userId:', userId, 'sender:', sender);
        
        /*
        const conv = await mongoDB.collection('conversations').findOne({
          _id: new ObjectId(CONV_ID),
          participants: userId
        });
        
        if (!conv) {
          ws.send(JSON.stringify({ type: 'error', message: 'Accès refusé' }));
          return;
        }
        // plus tard
        */
        
        const messageDoc = {
          conversationId: CONV_ID,
          senderId: userId,
          sender: sender,
          content: msg.content,
          type: 'text',
          isRead: false,
          createdAt: new Date()
        };
        
        const result = await mongoDB.collection('messages').insertOne(messageDoc);
        console.log('[SEND] Message saved, ID:', result.insertedId);
        
        const message = {
          id: result.insertedId.toString(),
          userId,
          sender,
          content: msg.content,
          time: messageDoc.createdAt.toISOString()
        };
        
        console.log('[SEND] Message prepared for broadcast:', message);
        console.log(`[SEND] Broadcasting from ${sender} (${userId}): ${msg.content}`);
        
        let broadcastCount = 0;
        wss.clients.forEach(client => {
          if (client.readyState === WebSocket.OPEN) {
            const payload = { type: 'message', message };
            console.log('[BROADCAST] Sending to client:', payload);
            client.send(JSON.stringify(payload));
            broadcastCount++;
          }
        });
        console.log(`[SEND] Message broadcasted to ${broadcastCount} clients`);
        return;
      }

      if (msg.type === 'typing') {
        const clientInfo = clients.get(ws);
        const userId = clientInfo.userId;
        const sender = clientInfo.userName || userId;
        wss.clients.forEach(client => {
          if (client !== ws && client.readyState === WebSocket.OPEN) {
            client.send(JSON.stringify({ type: 'typing', userId, sender }));
          }
        });
        return;
      }

    } catch (error) {
      console.error('Erreur:', error);
    }
  });

  ws.on('close', () => {
    const clientInfo = clients.get(ws);
    const userId = clientInfo ? clientInfo.userId : 'unknown';
    console.log(`[WS] Disconnection: ${userId}`);
    clients.delete(ws);
  });

  ws.on('error', (error) => {
    console.error('Erreur WebSocket:', error);
  });
});

async function start() {
  await connectMongoDB();
  server.listen(PORT, () => {
    console.log(`WebSocket avec MongoDB sur port ${PORT}`);
  });
}

start().catch(console.error);

