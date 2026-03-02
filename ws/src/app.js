require('dotenv').config()
const express = require('express')
const http = require('http')
const WebSocket = require('ws')
const cors = require('cors')
const path = require('path')

const { pgPool, connectMongoDB, cleanupBrokenConversations, mongoDB: getMongoDBInstance } = require('./config/database')
const { upload, uploadsDir } = require('./config/upload')
const conversationService = require('./services/conversationService')
const { handleAuth } = require('./websocket/handlers/authHandler')
const { handleSendMessage, handleReact, handleDelete, handleEdit } = require('./websocket/handlers/messageHandler')
const { handleTyping, handleStopTyping } = require('./websocket/handlers/typingHandler')
const {
  handleSalleSend,
  handleSalleTyping,
  handleSalleReact,
  handleSalleEdit,
  handleSalleDelete
} = require('./websocket/handlers/salleHandler')
const { onlineUsers, broadcastUserStatus } = require('./websocket/broadcast')

const PORT = process.env.WS_PORT || 3001

const app = express()
app.use(cors())
app.use(express.json())
app.use('/uploads', express.static(uploadsDir))

const server = http.createServer(app)
const wss = new WebSocket.Server({ server })

app.get('/health', (req, res) => {
  res.json({
    status: 'ok',
    onlineUsers: Array.from(onlineUsers.keys())
  })
})

app.post('/upload', upload.single('file'), async (req, res) => {
  try {
    if (!req.file) return res.status(400).json({ error: 'Aucun fichier fourni' })
    const fileUrl = `${req.protocol}://${req.get('host')}/uploads/${req.file.filename}`
    res.json({
      success: true,
      file: {
        filename: req.file.filename,
        originalName: req.file.originalname,
        size: req.file.size,
        mimetype: req.file.mimetype,
        url: fileUrl
      }
    })
  } catch (error) {
    console.error('Erreur upload:', error)
    res.status(500).json({ error: error.message })
  }
})

const mongoDB = getMongoDBInstance
const conversationsRouter = require('./routes/conversations')(mongoDB())
const groupsRouter = require('./routes/groups')
const friendsRouter = require('./routes/friends')
const notificationsRouter = require('./routes/notifications')
const sallesRouter = require('./routes/salles')(mongoDB(), pgPool)
const usersRouter = require('./routes/users')(mongoDB())

app.use('/conversations', conversationsRouter)
app.use('/groups', groupsRouter)
app.use('/friends', friendsRouter)
app.use('/notifications', notificationsRouter)
app.use('/salles', sallesRouter)
app.use('/users', usersRouter)

wss.on('connection', (ws) => {
  console.log('✅ Nouvelle connexion WebSocket')

  ws.on('message', async (data) => {
    try {
      const msg = JSON.parse(data.toString())

      if (msg.type === 'auth') {
        handleAuth(ws, wss, msg)
        return
      }

      if (msg.type === 'send') {
        await handleSendMessage(ws, mongoDB(), conversationService, msg)
        return
      }

      if (msg.type === 'typing') {
        await handleTyping(ws, conversationService, mongoDB(), msg)
        return
      }

      if (msg.type === 'stopTyping') {
        await handleStopTyping(ws, conversationService, mongoDB(), msg)
        return
      }

      if (msg.type === 'react') {
        await handleReact(ws, mongoDB(), msg)
        return
      }

      if (msg.type === 'delete') {
        await handleDelete(ws, mongoDB(), msg)
        return
      }

      if (msg.type === 'edit') {
        await handleEdit(ws, mongoDB(), msg)
        return
      }

      if (msg.type === 'salle_send') {
        await handleSalleSend(ws, mongoDB(), conversationService, pgPool, msg)
        return
      }

      if (msg.type === 'salle_typing') {
        await handleSalleTyping(ws, conversationService, pgPool, msg)
        return
      }

      if (msg.type === 'salle_react') {
        await handleSalleReact(ws, mongoDB(), conversationService, pgPool, msg)
        return
      }

      if (msg.type === 'salle_edit') {
        await handleSalleEdit(ws, mongoDB(), conversationService, pgPool, msg)
        return
      }

      if (msg.type === 'salle_delete') {
        await handleSalleDelete(ws, mongoDB(), conversationService, pgPool, msg)
        return
      }

    } catch (error) {
      console.error('Erreur WebSocket:', error)
      ws.send(JSON.stringify({ type: 'error', message: error.message }))
    }
  })

  ws.on('close', () => {
    if (ws.userId) {
      onlineUsers.delete(ws.userId)
      broadcastUserStatus(wss, ws.userId, 'offline')
      console.log(`❌ Utilisateur ${ws.userId} déconnecté`)
    }
  })

  ws.on('error', (error) => {
    console.error('WebSocket error:', error)
  })
})

async function start() {
  try {
    await connectMongoDB()
    await cleanupBrokenConversations()
    server.listen(PORT, () => {
      console.log(`🚀 WebSocket + REST API sur port ${PORT}`)
      console.log(`📁 Uploads disponibles sur http://localhost:${PORT}/uploads`)
    })
  } catch (error) {
    console.error('❌ Erreur démarrage:', error)
    process.exit(1)
  }
}

start()
