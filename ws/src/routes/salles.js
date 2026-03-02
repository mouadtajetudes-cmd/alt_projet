const express = require('express')
const { ObjectId } = require('mongodb')

module.exports = function(mongoDB, pgPool) {
  const router = express.Router()

  router.get('/:salleId/messages', async (req, res) => {
    try {
      const { salleId } = req.params
      const limit = parseInt(req.query.limit) || 50
      const skip = parseInt(req.query.skip) || 0

      const messages = await mongoDB.collection('salle_messages')
        .find({ salleId: parseInt(salleId) })
        .sort({ timestamp: -1 })
        .limit(limit)
        .skip(skip)
        .toArray()

      res.json({ messages: messages.reverse() })
    } catch (error) {
      console.error('Erreur récupération messages salle:', error)
      res.status(500).json({ error: error.message })
    }
  })

  router.post('/:salleId/messages', async (req, res) => {
    try {
      const { salleId } = req.params
      const { senderId, senderName, content, type = 'text' } = req.body

      if (!senderId || !content) {
        return res.status(400).json({ error: 'senderId et content requis' })
      }

      const message = {
        salleId: parseInt(salleId),
        senderId: String(senderId),
        senderName,
        content,
        type,
        timestamp: new Date(),
        isRead: false,
        reactions: []
      }

      const result = await mongoDB.collection('salle_messages').insertOne(message)
      message._id = result.insertedId

      res.json(message)
    } catch (error) {
      console.error('Erreur envoi message salle:', error)
      res.status(500).json({ error: error.message })
    }
  })

  router.put('/messages/:messageId', async (req, res) => {
    try {
      const { messageId } = req.params
      const { content, userId } = req.body

      if (!content) {
        return res.status(400).json({ error: 'content requis' })
      }

      const message = await mongoDB.collection('salle_messages').findOne({ 
        _id: new ObjectId(messageId) 
      })

      if (!message || message.senderId !== String(userId)) {
        return res.status(403).json({ error: 'Non autorisé' })
      }

      await mongoDB.collection('salle_messages').updateOne(
        { _id: new ObjectId(messageId) },
        { 
          $set: { 
            content, 
            editedAt: new Date() 
          } 
        }
      )

      res.json({ success: true })
    } catch (error) {
      console.error('Erreur modification message salle:', error)
      res.status(500).json({ error: error.message })
    }
  })

  router.delete('/messages/:messageId', async (req, res) => {
    try {
      const { messageId } = req.params
      const { userId } = req.body

      const message = await mongoDB.collection('salle_messages').findOne({ 
        _id: new ObjectId(messageId) 
      })

      if (!message || message.senderId !== String(userId)) {
        return res.status(403).json({ error: 'Non autorisé' })
      }

      await mongoDB.collection('salle_messages').deleteOne({ 
        _id: new ObjectId(messageId) 
      })

      res.json({ success: true })
    } catch (error) {
      console.error('Erreur suppression message salle:', error)
      res.status(500).json({ error: error.message })
    }
  })

  router.post('/messages/:messageId/react', async (req, res) => {
    try {
      const { messageId } = req.params
      const { userId, emoji } = req.body

      if (!userId || !emoji) {
        return res.status(400).json({ error: 'userId et emoji requis' })
      }

      const message = await mongoDB.collection('salle_messages').findOne({ 
        _id: new ObjectId(messageId) 
      })

      if (!message) {
        return res.status(404).json({ error: 'Message non trouvé' })
      }

      const reactions = message.reactions || []
      const existingReactionIndex = reactions.findIndex(
        r => r.userId === String(userId) && r.emoji === emoji
      )

      if (existingReactionIndex !== -1) {
        reactions.splice(existingReactionIndex, 1)
      } else {
        reactions.push({ userId: String(userId), emoji })
      }

      await mongoDB.collection('salle_messages').updateOne(
        { _id: new ObjectId(messageId) },
        { $set: { reactions } }
      )

      res.json({ success: true, reactions })
    } catch (error) {
      console.error('Erreur réaction message salle:', error)
      res.status(500).json({ error: error.message })
    }
  })

  router.delete('/:id', async (req, res) => {
    try {
      const { id } = req.params
      await pgPool.query('DELETE FROM salles WHERE id_salle = $1', [id])
      res.json({ success: true })
    } catch (error) {
      console.error('Erreur suppression salle:', error)
      res.status(500).json({ error: error.message })
    }
  })

  return router
}
