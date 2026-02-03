require('dotenv').config();
const express = require('express');
const http = require('http');
const { Server } = require('socket.io');
const cors = require('cors');
const helmet = require('helmet');
const jwt = require('jsonwebtoken');
const { MongoClient } = require('mongodb');
const { Pool } = require('pg');
const winston = require('winston');

// ============================================
// Configuration
// ============================================
const PORT = process.env.WS_PORT || 3001;
const HOST = process.env.WS_HOST || '0.0.0.0';
const JWT_SECRET = process.env.JWT_SECRET;

// ============================================
// Logger Configuration
// ============================================
const logger = winston.createLogger({
  level: process.env.LOG_LEVEL || 'info',
  format: winston.format.combine(
    winston.format.timestamp(),
    winston.format.json()
  ),
  transports: [
    new winston.transports.Console({
      format: winston.format.combine(
        winston.format.colorize(),
        winston.format.simple()
      )
    })
  ]
});

// ============================================
// Express App Setup
// ============================================
const app = express();
app.use(helmet());
app.use(cors());
app.use(express.json());

// Health check endpoint
app.get('/health', (req, res) => {
  res.json({
    status: 'ok',
    service: 'ALT WebSocket Service',
    uptime: process.uptime(),
    timestamp: new Date().toISOString()
  });
});

const server = http.createServer(app);

// ============================================
// Socket.IO Setup
// ============================================
const io = new Server(server, {
  cors: {
    origin: process.env.CORS_ORIGIN || '*',
    methods: ['GET', 'POST'],
    credentials: true
  },
  pingTimeout: 60000,
  pingInterval: 25000
});

// ============================================
// Database Connections
// ============================================
let mongoClient;
let mongoDB;
let pgPool;

// MongoDB Connection
async function connectMongoDB() {
  try {
    const mongoUri = process.env.MONGO_URI || 'mongodb://alt.mongo:27017/chat_db';
    mongoClient = new MongoClient(mongoUri);
    await mongoClient.connect();
    mongoDB = mongoClient.db();
    logger.info('✅ MongoDB connected successfully');
  } catch (error) {
    logger.error('❌ MongoDB connection error:', error);
    process.exit(1);
  }
}

// PostgreSQL Connection
function connectPostgreSQL() {
  pgPool = new Pool({
    host: process.env.DB_HOST,
    port: process.env.DB_PORT,
    database: process.env.DB_NAME,
    user: process.env.DB_USER,
    password: process.env.DB_PASS
  });

  pgPool.on('error', (err) => {
    logger.error('PostgreSQL pool error:', err);
  });

  logger.info('✅ PostgreSQL pool created');
}

// ============================================
// JWT Middleware
// ============================================
io.use((socket, next) => {
  const token = socket.handshake.auth.token || socket.handshake.headers.authorization?.replace('Bearer ', '');
  
  if (!token) {
    if (process.env.NODE_ENV === 'development') {
      socket.userId = 'guest_' + Date.now();
      socket.username = 'Guest User';
      logger.info(`Guest connection allowed from ${socket.handshake.address}`);
      return next();
    }
    logger.warn(`Connection rejected: No token provided from ${socket.handshake.address}`);
    return next(new Error('Authentication required'));
  }

  try {
    const decoded = jwt.verify(token, JWT_SECRET);
    socket.userId = decoded.userId || decoded.id;
    socket.username = decoded.username;
    logger.info(`User authenticated: ${socket.username} (${socket.userId})`);
    next();
  } catch (error) {
    logger.warn(`Invalid token from ${socket.handshake.address}: ${error.message}`);
    return next(new Error('Invalid token'));
  }
});

// ============================================
// In-Memory User Tracking
// ============================================
const onlineUsers = new Map(); // userId -> Set of socket IDs
const typingUsers = new Map(); // roomId -> Set of userIds

// ============================================
// Socket.IO Event Handlers
// ============================================
io.on('connection', (socket) => {
  logger.info(`🔌 User connected: ${socket.username} (${socket.id})`);

  // Track online user
  if (!onlineUsers.has(socket.userId)) {
    onlineUsers.set(socket.userId, new Set());
  }
  onlineUsers.get(socket.userId).add(socket.id);

  // Notify user is online
  socket.broadcast.emit('user:online', {
    userId: socket.userId,
    username: socket.username,
    timestamp: new Date()
  });

  // ===========================================
  // JOIN ROOM/CONVERSATION
  // ===========================================
  socket.on('room:join', async (data) => {
    const { roomId, conversationId } = data;
    const room = roomId || conversationId;

    try {
      // Verify user has access to this room (check PostgreSQL)
      const result = await pgPool.query(
        `SELECT 1 FROM conversation_members 
         WHERE conversation_id = $1 AND user_id = $2`,
        [room, socket.userId]
      );

      if (result.rows.length === 0) {
        socket.emit('error', { message: 'Access denied to this room' });
        return;
      }

      socket.join(room);
      logger.info(`User ${socket.username} joined room ${room}`);

      socket.emit('room:joined', {
        roomId: room,
        timestamp: new Date()
      });

      // Notify others in room
      socket.to(room).emit('user:joined', {
        userId: socket.userId,
        username: socket.username,
        roomId: room,
        timestamp: new Date()
      });
    } catch (error) {
      logger.error('Error joining room:', error);
      socket.emit('error', { message: 'Failed to join room' });
    }
  });

  // ===========================================
  // LEAVE ROOM
  // ===========================================
  socket.on('room:leave', (data) => {
    const { roomId } = data;
    socket.leave(roomId);
    logger.info(`User ${socket.username} left room ${roomId}`);

    socket.to(roomId).emit('user:left', {
      userId: socket.userId,
      username: socket.username,
      roomId,
      timestamp: new Date()
    });
  });

  // ===========================================
  // SEND MESSAGE
  // ===========================================
  socket.on('message:send', async (data) => {
    const { roomId, conversationId, content, type = 'text', attachments = [] } = data;
    const room = roomId || conversationId;

    try {
      // Save message to MongoDB
      const message = {
        conversationId: room,
        userId: socket.userId,
        username: socket.username,
        content,
        type,
        attachments,
        timestamp: new Date(),
        delivered: false,
        read: false
      };

      const result = await mongoDB.collection('messages').insertOne(message);
      message._id = result.insertedId;

      logger.info(`Message sent by ${socket.username} to room ${room}`);

      // Broadcast to room
      io.to(room).emit('message:new', {
        ...message,
        id: message._id.toString()
      });

      // Send acknowledgment to sender
      socket.emit('message:ack', {
        tempId: data.tempId,
        messageId: message._id.toString(),
        timestamp: message.timestamp
      });
    } catch (error) {
      logger.error('Error sending message:', error);
      socket.emit('error', { message: 'Failed to send message' });
    }
  });

  // ===========================================
  // TYPING INDICATOR
  // ===========================================
  socket.on('typing:start', (data) => {
    const { roomId } = data;
    
    if (!typingUsers.has(roomId)) {
      typingUsers.set(roomId, new Set());
    }
    typingUsers.get(roomId).add(socket.userId);

    socket.to(roomId).emit('typing:user', {
      userId: socket.userId,
      username: socket.username,
      roomId,
      isTyping: true
    });
  });

  socket.on('typing:stop', (data) => {
    const { roomId } = data;
    
    if (typingUsers.has(roomId)) {
      typingUsers.get(roomId).delete(socket.userId);
    }

    socket.to(roomId).emit('typing:user', {
      userId: socket.userId,
      username: socket.username,
      roomId,
      isTyping: false
    });
  });

  // ===========================================
  // MESSAGE READ/DELIVERED STATUS
  // ===========================================
  socket.on('message:delivered', async (data) => {
    const { messageId, roomId } = data;

    try {
      await mongoDB.collection('messages').updateOne(
        { _id: new MongoClient.ObjectId(messageId) },
        { $set: { delivered: true, deliveredAt: new Date() } }
      );

      socket.to(roomId).emit('message:status', {
        messageId,
        status: 'delivered',
        timestamp: new Date()
      });
    } catch (error) {
      logger.error('Error updating message status:', error);
    }
  });

  socket.on('message:read', async (data) => {
    const { messageId, roomId } = data;

    try {
      await mongoDB.collection('messages').updateOne(
        { _id: new MongoClient.ObjectId(messageId) },
        { $set: { read: true, readAt: new Date() } }
      );

      socket.to(roomId).emit('message:status', {
        messageId,
        status: 'read',
        timestamp: new Date()
      });
    } catch (error) {
      logger.error('Error updating message status:', error);
    }
  });

  // ===========================================
  // PRESENCE
  // ===========================================
  socket.on('presence:update', (data) => {
    const { status } = data; // 'online', 'away', 'busy', 'offline'
    
    socket.broadcast.emit('presence:changed', {
      userId: socket.userId,
      username: socket.username,
      status,
      timestamp: new Date()
    });
  });

  // ===========================================
  // DISCONNECT
  // ===========================================
  socket.on('disconnect', () => {
    logger.info(`🔌 User disconnected: ${socket.username} (${socket.id})`);

    // Remove from online users
    if (onlineUsers.has(socket.userId)) {
      onlineUsers.get(socket.userId).delete(socket.id);
      
      // If no more connections, user is offline
      if (onlineUsers.get(socket.userId).size === 0) {
        onlineUsers.delete(socket.userId);
        
        socket.broadcast.emit('user:offline', {
          userId: socket.userId,
          username: socket.username,
          timestamp: new Date()
        });
      }
    }

    // Clean up typing indicators
    typingUsers.forEach((users, roomId) => {
      if (users.has(socket.userId)) {
        users.delete(socket.userId);
        socket.to(roomId).emit('typing:user', {
          userId: socket.userId,
          username: socket.username,
          roomId,
          isTyping: false
        });
      }
    });
  });

  // ===========================================
  // ERROR HANDLING
  // ===========================================
  socket.on('error', (error) => {
    logger.error(`Socket error for ${socket.username}:`, error);
  });
});

// ============================================
// Start Server
// ============================================
async function startServer() {
  try {
    // Connect to databases
    await connectMongoDB();
    connectPostgreSQL();

    // Start server
    server.listen(PORT, HOST, () => {
      logger.info('═══════════════════════════════════════════════');
      logger.info('🚀 ALT WebSocket Service Started');
      logger.info(`📡 Listening on http://${HOST}:${PORT}`);
      logger.info(`🌍 Environment: ${process.env.NODE_ENV}`);
      logger.info('═══════════════════════════════════════════════');
    });
  } catch (error) {
    logger.error('Failed to start server:', error);
    process.exit(1);
  }
}

// ============================================
// Graceful Shutdown
// ============================================
process.on('SIGTERM', async () => {
  logger.info('SIGTERM signal received: closing HTTP server');
  server.close(async () => {
    logger.info('HTTP server closed');
    
    if (mongoClient) {
      await mongoClient.close();
      logger.info('MongoDB connection closed');
    }
    
    if (pgPool) {
      await pgPool.end();
      logger.info('PostgreSQL pool closed');
    }
    
    process.exit(0);
  });
});

process.on('SIGINT', async () => {
  logger.info('SIGINT signal received: closing HTTP server');
  server.close(async () => {
    logger.info('HTTP server closed');
    
    if (mongoClient) {
      await mongoClient.close();
      logger.info('MongoDB connection closed');
    }
    
    if (pgPool) {
      await pgPool.end();
      logger.info('PostgreSQL pool closed');
    }
    
    process.exit(0);
  });
});

// Start the server
startServer();
