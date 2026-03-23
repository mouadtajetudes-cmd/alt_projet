const WebSocket = require('ws')

const onlineUsers = new Map()

function sendToUser(userId, payload) {
  const userWs = onlineUsers.get(String(userId))
  if (userWs && userWs.readyState === WebSocket.OPEN) {
    userWs.send(JSON.stringify(payload))
  }
}

function broadcastUserStatus(wss, userId, status) {
  wss.clients.forEach(client => {
    if (client.readyState === WebSocket.OPEN) {
      client.send(JSON.stringify({
        type: 'user:status',
        userId,
        status
      }))
    }
  })
}

async function broadcastToConversation(conversationService, mongoDB, conversationId, payload, excludeUserId = null) {
  const participants = await conversationService.getConversationParticipants(mongoDB, conversationId)
  participants.forEach(participantId => {
    if (participantId !== excludeUserId) {
      sendToUser(participantId, payload)
    }
  })
}

async function broadcastToSalle(conversationService, pgPool, salleId, payload, excludeUserId = null) {
  const members = await conversationService.getSalleMembers(pgPool, salleId)
  members.forEach(memberId => {
    if (memberId !== excludeUserId) {
      sendToUser(memberId, payload)
    }
  })
}

module.exports = {
  onlineUsers,
  sendToUser,
  broadcastUserStatus,
  broadcastToConversation,
  broadcastToSalle
}
