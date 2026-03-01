const express = require('express')
const { pgPool } = require('../config/database')

const router = express.Router()

router.get('/:userId', async (req, res) => {
  try {
    const { userId } = req.params
    const limit = parseInt(req.query.limit) || 50

    const result = await pgPool.query(`
      SELECT * FROM notifications
      WHERE id_utilisateur = $1
      ORDER BY created_at DESC
      LIMIT $2
    `, [userId, limit])

    res.json(result.rows)
  } catch (error) {
    console.error('Erreur récupération notifications:', error)
    res.status(500).json({ error: error.message })
  }
})

router.get('/:userId/unread', async (req, res) => {
  try {
    const { userId } = req.params

    const result = await pgPool.query(`
      SELECT COUNT(*) as count FROM notifications
      WHERE id_utilisateur = $1 AND lue = FALSE
    `, [userId])

    res.json({ count: parseInt(result.rows[0].count) })
  } catch (error) {
    console.error('Erreur comptage notifications:', error)
    res.status(500).json({ error: error.message })
  }
})

router.post('/', async (req, res) => {
  try {
    const { id_utilisateur, type, titre, contenu, data } = req.body

    if (!id_utilisateur || !type || !titre) {
      return res.status(400).json({ error: 'id_utilisateur, type et titre requis' })
    }

    const result = await pgPool.query(`
      INSERT INTO notifications (id_utilisateur, type, titre, contenu, data)
      VALUES ($1, $2, $3, $4, $5)
      RETURNING *
    `, [id_utilisateur, type, titre, contenu, JSON.stringify(data || {})])

    const notification = result.rows[0]
    res.json(notification)
  } catch (error) {
    console.error('Erreur création notification:', error)
    res.status(500).json({ error: error.message })
  }
})

router.put('/:id/read', async (req, res) => {
  try {
    const { id } = req.params

    await pgPool.query(`
      UPDATE notifications
      SET lue = TRUE
      WHERE id_notification = $1
    `, [id])

    res.json({ success: true })
  } catch (error) {
    console.error('Erreur marquage notification lue:', error)
    res.status(500).json({ error: error.message })
  }
})

router.put('/:userId/read-all', async (req, res) => {
  try {
    const { userId } = req.params

    await pgPool.query(`
      UPDATE notifications
      SET lue = TRUE
      WHERE id_utilisateur = $1 AND lue = FALSE
    `, [userId])

    res.json({ success: true })
  } catch (error) {
    console.error('Erreur marquage toutes notifications lues:', error)
    res.status(500).json({ error: error.message })
  }
})

router.delete('/:id', async (req, res) => {
  try {
    const { id } = req.params

    await pgPool.query(`
      DELETE FROM notifications
      WHERE id_notification = $1
    `, [id])

    res.json({ success: true })
  } catch (error) {
    console.error('Erreur suppression notification:', error)
    res.status(500).json({ error: error.message })
  }
})

module.exports = router
