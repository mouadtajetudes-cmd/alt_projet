const express = require('express')
const { ObjectId } = require('mongodb')
const { formatMessage } = require('../utils/formatters')

module.exports = function(mongoDB) {
  const router = express.Router()

  router.get('/:userId', async (req, res) => {
    try {
      const userId = String(req.params.userId)

      const conversations = await mongoDB.collection('conversations')
        .find({ participants: userId })
        .sort({ updatedAt: -1 })
        .toArray()

      const conversationsWithDetails = await Promise.all(
        conversations.map(async (conv) => {
          const convId = conv._id.toString()

          const lastMessage = await mongoDB.collection('messages')
            .findOne(
              { conversationId: convId },
              { sort: { createdAt: -1 } }
            )

          const unreadCount = await mongoDB.collection('messages')
            .countDocuments({
              conversationId: convId,
              senderId: { $ne: userId },
              isRead: false
            })

          return {
            ...conv,
            _id: convId,
            lastMessage: lastMessage ? formatMessage(lastMessage) : null,
            unreadCount
          }
        })
      )

      res.json(conversationsWithDetails)
    } catch (error) {
      console.error('Erreur récupération conversations:', error)
      res.status(500).json({ error: error.message })
    }
  })

  router.get('/:convId/messages', async (req, res) => {
    try {
      const { convId } = req.params
      const limit = parseInt(req.query.limit) || 50
      const page = parseInt(req.query.page) || 1
      const skip = (page - 1) * limit

      const messages = await mongoDB.collection('messages')
        .find({ conversationId: convId })
        .sort({ createdAt: 1 })
        .skip(skip)
        .limit(limit)
        .toArray()

      res.json({ messages: messages.map(formatMessage) })
    } catch (error) {
      console.error('Erreur récupération messages:', error)
      res.status(500).json({ error: error.message })
    }
  })

  router.post('/', async (req, res) => {
    try {
      const { participants, name } = req.body

      if (!participants || participants.length < 2) {
        return res.status(400).json({ error: 'Au moins 2 participants requis' })
      }

      const stringParticipants = participants.map(p => String(p))
      const uniqueParticipants = [...new Set(stringParticipants)]
      
      if (uniqueParticipants.length < 2) {
        return res.status(400).json({ error: 'Impossible de créer une conversation avec soi-même' })
      }

      if (stringParticipants.length === 2) {
        const existing = await mongoDB.collection('conversations')
          .findOne({
            participants: { $all: stringParticipants, $size: 2 }
          })

        if (existing) {
          return res.json({ ...existing, _id: existing._id.toString() })
        }
      }

      const conversation = {
        participants: stringParticipants,
        name: name || null,
        createdAt: new Date(),
        updatedAt: new Date()
      }

      const result = await mongoDB.collection('conversations').insertOne(conversation)
      conversation._id = result.insertedId.toString()

      res.json(conversation)
    } catch (error) {
      console.error('Erreur création conversation:', error)
      res.status(500).json({ error: error.message })
    }
  })

  router.put('/:convId/read', async (req, res) => {
    try {
      const { convId } = req.params
      const { userId } = req.body

      if (!userId) return res.status(400).json({ error: 'userId requis' })

      await mongoDB.collection('messages').updateMany(
        {
          conversationId: convId,
          senderId: { $ne: String(userId) },
          isRead: false
        },
        { $set: { isRead: true } }
      )

      res.json({ success: true })
    } catch (error) {
      console.error('Erreur marquage lecture:', error)
      res.status(500).json({ error: error.message })
    }
  })

  router.post('/:convId/pin', async (req, res) => {
    try {
      const { convId } = req.params
      const { userId, pinned } = req.body
      const { pgPool } = require('../config/database')

      if (!userId) {
        return res.status(400).json({ error: 'userId requis' })
      }

      if (pinned) {
        await pgPool.query(`
          INSERT INTO conversations_epinglees (id_utilisateur, conversation_id)
          VALUES ($1, $2)
          ON CONFLICT DO NOTHING
        `, [userId, convId])
      } else {
        await pgPool.query(`
          DELETE FROM conversations_epinglees
          WHERE id_utilisateur = $1 AND conversation_id = $2
        `, [userId, convId])
      }

      res.json({ success: true })
    } catch (error) {
      console.error('Erreur épinglage conversation:', error)
      res.status(500).json({ error: error.message })
    }
  })

  router.get('/pinned/:userId', async (req, res) => {
    try {
      const { userId } = req.params
      const { pgPool } = require('../config/database')
      
      const result = await pgPool.query(`
        SELECT conversation_id FROM conversations_epinglees
        WHERE id_utilisateur = $1
        ORDER BY date_epingle DESC
      `, [userId])
      
      res.json(result.rows.map(r => r.conversation_id))
    } catch (error) {
      console.error('Erreur récupération conversations épinglées:', error)
      res.status(500).json({ error: error.message })
    }
  })

  return router
}
