const express = require('express')
const { pgPool } = require('../config/database')

module.exports = function(mongoDB) {
  const router = express.Router()

  router.get('/:userId/stats', async (req, res) => {
    try {
      const { userId } = req.params
      console.log('[STATS] Fetching stats for user:', userId)

      let messagesCount = 0
      try {
        const userIdStr = String(userId)
        const userIdInt = parseInt(userId)
        
        const directMessages = await mongoDB.collection('messages').countDocuments({
          $or: [
            { senderId: userIdStr },
            { senderId: userIdInt }
          ]
        })
        console.log('[STATS] Direct messages count:', directMessages)
        
        const salleMessages = await mongoDB.collection('salle_messages').countDocuments({
          $or: [
            { senderId: userIdStr },
            { senderId: userIdInt }
          ]
        })
        console.log('[STATS] Salle messages count:', salleMessages)
        
        messagesCount = directMessages + salleMessages
        console.log('[STATS] Total messages count:', messagesCount)
      } catch (mongoError) {
        console.error('[STATS] Error counting messages:', mongoError)
      }

      let connectionsCount = 0
      try {
        const result = await pgPool.query(`
          SELECT COUNT(*) as count FROM amities
          WHERE (utilisateur_id = $1 OR ami_id = $1)
            AND statut = 'accepte'
        `, [userId])
        connectionsCount = parseInt(result.rows[0].count)
        console.log('[STATS] Connections count:', connectionsCount, 'for user', userId)
      } catch (pgError) {
        console.error('[STATS] Error counting connections:', pgError)
      }

      const response = {
        messagesCount,
        connectionsCount
      }
      console.log('[STATS] Sending response:', response)
      res.json(response)
    } catch (error) {
      console.error('[STATS] Erreur récupération stats utilisateur:', error)
      res.status(500).json({ error: error.message })
    }
  })

  return router
}
