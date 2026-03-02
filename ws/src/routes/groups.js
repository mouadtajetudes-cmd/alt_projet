const express = require('express')
const { pgPool } = require('../config/database')

const router = express.Router()

router.get('/user/:userId', async (req, res) => {
  try {
    const { userId } = req.params
    const result = await pgPool.query(`
      SELECT g.*, mg.role,
        (SELECT COUNT(*) FROM membre_groupe WHERE id_groupe = g.id_groupe) as membres_count,
        (SELECT COUNT(*) FROM salles WHERE id_groupe = g.id_groupe) as salles_count
      FROM groupes g
      JOIN membre_groupe mg ON g.id_groupe = mg.id_groupe
      WHERE mg.id_utilisateur = $1
      ORDER BY g.created_at DESC
    `, [userId])
    res.json(result.rows)
  } catch (error) {
    console.error('Erreur récupération groupes:', error)
    res.status(500).json({ error: error.message })
  }
})

router.get('/:id', async (req, res) => {
  try {
    const { id } = req.params
    
    const groupResult = await pgPool.query(`
      SELECT g.* FROM groupes g WHERE g.id_groupe = $1
    `, [id])

    if (groupResult.rows.length === 0) {
      return res.status(404).json({ error: 'Groupe non trouvé' })
    }

    const membresResult = await pgPool.query(`
      SELECT mg.id_utilisateur, mg.role, mg.joined_at,
        u.nom, u.prenom, u.email, u.administrateur, u.premium
      FROM membre_groupe mg
      JOIN utilisateurs u ON mg.id_utilisateur = u.id_utilisateur
      WHERE mg.id_groupe = $1
      ORDER BY mg.role, u.nom
    `, [id])

    const sallesResult = await pgPool.query(`
      SELECT * FROM salles WHERE id_groupe = $1 ORDER BY date_creation
    `, [id])

    res.json({
      ...groupResult.rows[0],
      membres: membresResult.rows,
      salles: sallesResult.rows
    })
  } catch (error) {
    console.error('Erreur récupération détails groupe:', error)
    res.status(500).json({ error: error.message })
  }
})

router.post('/', async (req, res) => {
  try {
    const { nom, description, niveau, createur_id } = req.body

    if (!nom || !createur_id) {
      return res.status(400).json({ error: 'Nom et créateur requis' })
    }

    const result = await pgPool.query(`
      INSERT INTO groupes (nom, description, niveau)
      VALUES ($1, $2, $3)
      RETURNING *
    `, [nom, description, niveau || 'debutant'])

    const groupe = result.rows[0]

    await pgPool.query(`
      INSERT INTO membre_groupe (id_groupe, id_utilisateur, role)
      VALUES ($1, $2, 'admin')
    `, [groupe.id_groupe, createur_id])

    await pgPool.query(`
      INSERT INTO salles (nom, id_groupe, description)
      VALUES ('Général', $1, 'Salle de discussion générale')
    `, [groupe.id_groupe])

    res.json(groupe)
  } catch (error) {
    console.error('Erreur création groupe:', error)
    res.status(500).json({ error: error.message })
  }
})

router.put('/:id', async (req, res) => {
  try {
    const { id } = req.params
    const { nom, description, niveau } = req.body

    const result = await pgPool.query(`
      UPDATE groupes
      SET nom = COALESCE($1, nom),
          description = COALESCE($2, description),
          niveau = COALESCE($3, niveau)
      WHERE id_groupe = $4
      RETURNING *
    `, [nom, description, niveau, id])

    if (result.rows.length === 0) {
      return res.status(404).json({ error: 'Groupe non trouvé' })
    }

    res.json(result.rows[0])
  } catch (error) {
    console.error('Erreur mise à jour groupe:', error)
    res.status(500).json({ error: error.message })
  }
})

router.delete('/:id', async (req, res) => {
  try {
    const { id } = req.params
    await pgPool.query('DELETE FROM groupes WHERE id_groupe = $1', [id])
    res.json({ success: true })
  } catch (error) {
    console.error('Erreur suppression groupe:', error)
    res.status(500).json({ error: error.message })
  }
})

router.post('/:id/members', async (req, res) => {
  try {
    const { id } = req.params
    const { userId, role } = req.body

    if (!userId) {
      return res.status(400).json({ error: 'userId requis' })
    }

    await pgPool.query(`
      INSERT INTO membre_groupe (id_groupe, id_utilisateur, role)
      VALUES ($1, $2, $3)
      ON CONFLICT (id_groupe, id_utilisateur) DO UPDATE SET role = $3
    `, [id, userId, role || 'membre'])

    res.json({ success: true })
  } catch (error) {
    console.error('Erreur ajout membre:', error)
    res.status(500).json({ error: error.message })
  }
})

router.delete('/:id/members/:userId', async (req, res) => {
  try {
    const { id, userId } = req.params
    await pgPool.query(`
      DELETE FROM membre_groupe WHERE id_groupe = $1 AND id_utilisateur = $2
    `, [id, userId])
    res.json({ success: true })
  } catch (error) {
    console.error('Erreur suppression membre:', error)
    res.status(500).json({ error: error.message })
  }
})

router.get('/:id/salles', async (req, res) => {
  try {
    const { id } = req.params
    const result = await pgPool.query(`
      SELECT * FROM salles WHERE id_groupe = $1 ORDER BY date_creation
    `, [id])
    res.json(result.rows)
  } catch (error) {
    console.error('Erreur récupération salles:', error)
    res.status(500).json({ error: error.message })
  }
})

router.post('/:id/salles', async (req, res) => {
  try {
    const { id } = req.params
    const { nom, description } = req.body

    if (!nom) {
      return res.status(400).json({ error: 'Nom de salle requis' })
    }

    const result = await pgPool.query(`
      INSERT INTO salles (nom, id_groupe, description)
      VALUES ($1, $2, $3)
      RETURNING *
    `, [nom, id, description])

    res.json(result.rows[0])
  } catch (error) {
    console.error('Erreur création salle:', error)
    res.status(500).json({ error: error.message })
  }
})

module.exports = router
