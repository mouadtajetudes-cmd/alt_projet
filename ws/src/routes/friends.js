const express = require('express')
const { pgPool } = require('../config/database')
const { createNotification } = require('../services/notificationService')

const router = express.Router()

router.post('/request', async (req, res) => {
  try {
    const { utilisateur_id, ami_id } = req.body

    if (!utilisateur_id || !ami_id) {
      return res.status(400).json({ error: 'utilisateur_id et ami_id requis' })
    }

    if (utilisateur_id === ami_id) {
      return res.status(400).json({ error: 'Vous ne pouvez pas vous ajouter vous-même' })
    }

    const existingCheck = await pgPool.query(`
      SELECT * FROM amities 
      WHERE (utilisateur_id = $1 AND ami_id = $2) 
         OR (utilisateur_id = $2 AND ami_id = $1)
    `, [utilisateur_id, ami_id])

    if (existingCheck.rows.length > 0) {
      const existing = existingCheck.rows[0]
      if (existing.statut === 'bloque') {
        return res.status(403).json({ error: 'Cette utilisateur est bloqué' })
      }
      return res.status(400).json({ error: 'Une relation existe déjà entre ces utilisateurs' })
    }

    const result = await pgPool.query(`
      INSERT INTO amities (utilisateur_id, ami_id, statut, date_demande)
      VALUES ($1, $2, 'en_attente', NOW())
      RETURNING *
    `, [utilisateur_id, ami_id])

    const sender = await pgPool.query('SELECT nom, prenom FROM utilisateurs WHERE id_utilisateur = $1', [utilisateur_id])
    if (sender.rows[0]) {
      await createNotification(
        ami_id,
        'friend_request',
        'Nouvelle demande d\'ami',
        `${sender.rows[0].prenom} ${sender.rows[0].nom} souhaite devenir votre ami`,
        { from_user_id: utilisateur_id, friendship_id: result.rows[0].id_amitie }
      )
    }

    res.json({ success: true, friendship: result.rows[0] })
  } catch (error) {
    console.error('Erreur envoi demande ami:', error)
    res.status(500).json({ error: error.message })
  }
})

router.put('/accept/:id', async (req, res) => {
  try {
    const { id } = req.params
    const { ami_id } = req.body

    const result = await pgPool.query(`
      UPDATE amities 
      SET statut = 'accepte', date_reponse = NOW()
      WHERE id_amitie = $1 AND ami_id = $2 AND statut = 'en_attente'
      RETURNING *
    `, [id, ami_id])

    if (result.rows.length === 0) {
      return res.status(404).json({ error: 'Demande non trouvée' })
    }

    const friendship = result.rows[0]
    const accepter = await pgPool.query('SELECT nom, prenom FROM utilisateurs WHERE id_utilisateur = $1', [ami_id])
    if (accepter.rows[0]) {
      await createNotification(
        friendship.utilisateur_id,
        'friend_accepted',
        'Demande d\'ami acceptée',
        `${accepter.rows[0].prenom} ${accepter.rows[0].nom} a accepté votre demande d'ami`,
        { friend_user_id: ami_id }
      )
    }

    res.json({ success: true, friendship: result.rows[0] })
  } catch (error) {
    console.error('Erreur acceptation demande ami:', error)
    res.status(500).json({ error: error.message })
  }
})

router.put('/refuse/:id', async (req, res) => {
  try {
    const { id } = req.params
    const { ami_id } = req.body

    const result = await pgPool.query(`
      UPDATE amities 
      SET statut = 'refuse', date_reponse = NOW()
      WHERE id_amitie = $1 AND ami_id = $2 AND statut = 'en_attente'
      RETURNING *
    `, [id, ami_id])

    if (result.rows.length === 0) {
      return res.status(404).json({ error: 'Demande non trouvée' })
    }

    res.json({ success: true, friendship: result.rows[0] })
  } catch (error) {
    console.error('Erreur refus demande ami:', error)
    res.status(500).json({ error: error.message })
  }
})

router.delete('/:friendshipId', async (req, res) => {
  try {
    const { friendshipId } = req.params
    const { userId } = req.body

    const result = await pgPool.query(`
      DELETE FROM amities 
      WHERE id_amitie = $1 
        AND (utilisateur_id = $2 OR ami_id = $2)
      RETURNING *
    `, [friendshipId, userId])

    if (result.rows.length === 0) {
      return res.status(404).json({ error: 'Amitié non trouvée' })
    }

    res.json({ success: true })
  } catch (error) {
    console.error('Erreur suppression ami:', error)
    res.status(500).json({ error: error.message })
  }
})

router.post('/block', async (req, res) => {
  try {
    const { utilisateur_id, ami_id } = req.body

    if (!utilisateur_id || !ami_id) {
      return res.status(400).json({ error: 'utilisateur_id et ami_id requis' })
    }

    await pgPool.query(`
      DELETE FROM amities 
      WHERE (utilisateur_id = $1 AND ami_id = $2) 
         OR (utilisateur_id = $2 AND ami_id = $1)
    `, [utilisateur_id, ami_id])

    const result = await pgPool.query(`
      INSERT INTO amities (utilisateur_id, ami_id, statut, date_demande)
      VALUES ($1, $2, 'bloque', NOW())
      RETURNING *
    `, [utilisateur_id, ami_id])

    res.json({ success: true, friendship: result.rows[0] })
  } catch (error) {
    console.error('Erreur blocage utilisateur:', error)
    res.status(500).json({ error: error.message })
  }
})

router.get('/:userId', async (req, res) => {
  try {
    const { userId } = req.params

    const result = await pgPool.query(`
      SELECT 
        a.id_amitie,
        a.statut,
        a.date_demande,
        a.date_reponse,
        u.id_utilisateur,
        u.nom,
        u.prenom,
        u.email
      FROM amities a
      JOIN utilisateurs u ON (
        CASE 
          WHEN a.utilisateur_id = $1 THEN u.id_utilisateur = a.ami_id
          ELSE u.id_utilisateur = a.utilisateur_id
        END
      )
      WHERE (a.utilisateur_id = $1 OR a.ami_id = $1)
        AND a.statut = 'accepte'
      ORDER BY a.date_reponse DESC
    `, [userId])

    res.json(result.rows)
  } catch (error) {
    console.error('Erreur récupération amis:', error)
    res.status(500).json({ error: error.message })
  }
})

router.get('/requests/received/:userId', async (req, res) => {
  try {
    const { userId } = req.params

    const result = await pgPool.query(`
      SELECT 
        a.id_amitie,
        a.statut,
        a.date_demande,
        u.id_utilisateur,
        u.nom,
        u.prenom,
        u.email
      FROM amities a
      JOIN utilisateurs u ON u.id_utilisateur = a.utilisateur_id
      WHERE a.ami_id = $1 AND a.statut = 'en_attente'
      ORDER BY a.date_demande DESC
    `, [userId])

    res.json(result.rows)
  } catch (error) {
    console.error('Erreur récupération demandes reçues:', error)
    res.status(500).json({ error: error.message })
  }
})

router.get('/requests/sent/:userId', async (req, res) => {
  try {
    const { userId } = req.params

    const result = await pgPool.query(`
      SELECT 
        a.id_amitie,
        a.statut,
        a.date_demande,
        u.id_utilisateur,
        u.nom,
        u.prenom,
        u.email
      FROM amities a
      JOIN utilisateurs u ON u.id_utilisateur = a.ami_id
      WHERE a.utilisateur_id = $1 AND a.statut = 'en_attente'
      ORDER BY a.date_demande DESC
    `, [userId])

    res.json(result.rows)
  } catch (error) {
    console.error('Erreur récupération demandes envoyées:', error)
    res.status(500).json({ error: error.message })
  }
})

router.get('/check/:userId/:targetUserId', async (req, res) => {
  try {
    const { userId, targetUserId } = req.params

    const result = await pgPool.query(`
      SELECT sont_amis($1, $2) as are_friends
    `, [userId, targetUserId])

    const friendship = await pgPool.query(`
      SELECT * FROM amities
      WHERE ((utilisateur_id = $1 AND ami_id = $2) 
          OR (utilisateur_id = $2 AND ami_id = $1))
    `, [userId, targetUserId])

    res.json({ 
      areFriends: result.rows[0].are_friends,
      friendship: friendship.rows[0] || null
    })
  } catch (error) {
    console.error('Erreur vérification amitié:', error)
    res.status(500).json({ error: error.message })
  }
})

module.exports = router
