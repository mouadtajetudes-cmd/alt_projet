const { pgPool } = require('../config/database')

async function createNotification(userId, type, titre, contenu, data = {}) {
  try {
    const result = await pgPool.query(`
      INSERT INTO notifications (id_utilisateur, type, titre, contenu, data)
      VALUES ($1, $2, $3, $4, $5)
      RETURNING *
    `, [userId, type, titre, contenu, JSON.stringify(data)])

    return result.rows[0]
  } catch (error) {
    console.error('Erreur création notification:', error)
    return null
  }
}

module.exports = { createNotification }
