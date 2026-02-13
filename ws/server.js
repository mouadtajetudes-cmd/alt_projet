require('dotenv').config();
const express = require('express');
const http = require('http');
const WebSocket = require('ws');
const cors = require('cors');
const { MongoClient, ObjectId } = require('mongodb');

const PORT = process.env.WS_PORT || 3001;
const MONGO_URI = process.env.MONGO_URI || 'mongodb://alt.mongo:27017';
const CONV_ID = '698463bcfeacf0d5b5ce3efb'; 

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
}

const clients = new Map(); 

wss.on('connection', (ws) => {
  ws.on('message', async (data) => {
    try {
      const msg = JSON.parse(data.toString());

      if (msg.type === 'auth') {
        let userId = msg.userId || 'guest_' + Date.now();
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
        return;
      }

      if (msg.type === 'send') {
        const clientInfo = clients.get(ws);
        const userId = clientInfo.userId;
        const sender = msg.sender || clientInfo.userName || userId;
        
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
        
        const message = {
          id: result.insertedId.toString(),
          userId,
          sender,
          content: msg.content,
          time: messageDoc.createdAt.toISOString()
        };
        
        wss.clients.forEach(client => {
          if (client.readyState === WebSocket.OPEN) {
            const payload = { type: 'message', message };
            client.send(JSON.stringify(payload));
          }
        });
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
    clients.delete(ws);
  });

  ws.on('error', (error) => {
    console.error('WebSocket error:', error);
  });
});

async function start() {
  await connectMongoDB();
  server.listen(PORT, () => {
    console.log(`WebSocket avec MongoDB sur port ${PORT}`);
  });
}

start().catch(console.error);

