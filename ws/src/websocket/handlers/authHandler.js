const { onlineUsers, broadcastUserStatus } = require('../broadcast')

function handleAuth(ws, wss, msg) {
  const userId = String(msg.userId || 'guest_' + Date.now())
  const userName = msg.userName || userId

  ws.userId = userId
  ws.userName = userName
  onlineUsers.set(userId, ws)

  ws.send(JSON.stringify({ type: 'auth:ok', userId }))
  broadcastUserStatus(wss, userId, 'online')
}

module.exports = { handleAuth }
