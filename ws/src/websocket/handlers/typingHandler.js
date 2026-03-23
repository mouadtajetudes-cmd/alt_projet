const { broadcastToConversation } = require('../broadcast')

async function handleTyping(ws, conversationService, mongoDB, msg) {
  if (!ws.userId || !msg.conversationId) return
  
  await broadcastToConversation(
    conversationService, 
    mongoDB, 
    msg.conversationId, 
    {
      type: 'typing',
      userId: ws.userId,
      sender: ws.userName,
      conversationId: msg.conversationId
    }, 
    ws.userId
  )
}

async function handleStopTyping(ws, conversationService, mongoDB, msg) {
  if (!ws.userId || !msg.conversationId) return
  
  await broadcastToConversation(
    conversationService, 
    mongoDB, 
    msg.conversationId, 
    {
      type: 'stopTyping',
      userId: ws.userId,
      conversationId: msg.conversationId
    }, 
    ws.userId
  )
}

module.exports = {
  handleTyping,
  handleStopTyping
}
