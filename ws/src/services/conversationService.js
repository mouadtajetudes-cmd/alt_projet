const { ObjectId } = require('mongodb')

async function getConversationParticipants(mongoDB, conversationId) {
  try {
    const conv = await mongoDB.collection('conversations').findOne({
      _id: new ObjectId(conversationId)
    })
    return conv ? conv.participants : []
  } catch (e) {
    return []
  }
}

async function getSalleMembers(pgPool, salleId) {
  try {
    const salleResult = await pgPool.query('SELECT id_groupe FROM salles WHERE id_salle = $1', [salleId])
    if (salleResult.rows.length === 0) return []
    
    const groupId = salleResult.rows[0].id_groupe
    const membersResult = await pgPool.query(
      'SELECT id_utilisateur FROM membre_groupe WHERE id_groupe = $1',
      [groupId]
    )
    
    return membersResult.rows.map(row => String(row.id_utilisateur))
  } catch (error) {
    console.error('Erreur récupération membres salle:', error)
    return []
  }
}

module.exports = { getConversationParticipants, getSalleMembers }
