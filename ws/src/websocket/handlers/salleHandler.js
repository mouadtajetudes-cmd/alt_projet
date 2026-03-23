const { ObjectId } = require('mongodb')
const { broadcastToSalle } = require('../broadcast')

async function handleSalleSend(ws, mongoDB, conversationService, pgPool, msg) {
  const { salleId, content, messageType } = msg

  if (!ws.userId || !salleId) {
    ws.send(JSON.stringify({ type: 'error', message: 'Données manquantes' }))
    return
  }

  const messageDoc = {
    salleId: parseInt(salleId),
    senderId: ws.userId,
    senderName: ws.userName,
    content: content || '',
    type: messageType || 'text',
    timestamp: new Date(),
    isRead: false,
    reactions: []
  }

  const result = await mongoDB.collection('salle_messages').insertOne(messageDoc)
  messageDoc._id = result.insertedId

  await broadcastToSalle(conversationService, pgPool, parseInt(salleId), {
    type: 'salle_message',
    message: messageDoc
  })
}

async function handleSalleTyping(ws, conversationService, pgPool, msg) {
  const { salleId, typing } = msg
  if (!salleId || !ws.userId) return

  await broadcastToSalle(conversationService, pgPool, parseInt(salleId), {
    type: 'salle_typing',
    userId: ws.userId,
    userName: ws.userName,
    typing
  }, ws.userId)
}

async function handleSalleReact(ws, mongoDB, conversationService, pgPool, msg) {
  const { messageId, emoji, salleId } = msg

  if (!ws.userId || !messageId || !emoji) {
    ws.send(JSON.stringify({ type: 'error', message: 'Données manquantes' }))
    return
  }

  try {
    const message = await mongoDB.collection('salle_messages').findOne({ _id: new ObjectId(messageId) })
    if (!message) {
      ws.send(JSON.stringify({ type: 'error', message: 'Message non trouvé' }))
      return
    }

    const reactions = message.reactions || []
    const existingIndex = reactions.findIndex(
      r => r.userId === ws.userId && r.emoji === emoji
    )

    if (existingIndex !== -1) {
      reactions.splice(existingIndex, 1)
    } else {
      reactions.push({ userId: ws.userId, emoji })
    }

    await mongoDB.collection('salle_messages').updateOne(
      { _id: new ObjectId(messageId) },
      { $set: { reactions } }
    )

    await broadcastToSalle(conversationService, pgPool, message.salleId, {
      type: 'salle_message_reaction',
      messageId,
      reactions
    })
  } catch (error) {
    ws.send(JSON.stringify({ type: 'error', message: error.message }))
  }
}

async function handleSalleEdit(ws, mongoDB, conversationService, pgPool, msg) {
  const { messageId, newContent } = msg

  if (!ws.userId || !messageId || !newContent) {
    ws.send(JSON.stringify({ type: 'error', message: 'Données manquantes' }))
    return
  }

  try {
    const message = await mongoDB.collection('salle_messages').findOne({ _id: new ObjectId(messageId) })
    if (!message || message.senderId !== ws.userId) {
      ws.send(JSON.stringify({ type: 'error', message: 'Non autorisé' }))
      return
    }

    await mongoDB.collection('salle_messages').updateOne(
      { _id: new ObjectId(messageId) },
      { $set: { content: newContent, editedAt: new Date() } }
    )

    await broadcastToSalle(conversationService, pgPool, message.salleId, {
      type: 'salle_message_edited',
      messageId,
      content: newContent,
      editedAt: new Date()
    })
  } catch (error) {
    ws.send(JSON.stringify({ type: 'error', message: error.message }))
  }
}

async function handleSalleDelete(ws, mongoDB, conversationService, pgPool, msg) {
  const { messageId } = msg

  if (!ws.userId || !messageId) {
    ws.send(JSON.stringify({ type: 'error', message: 'Données manquantes' }))
    return
  }

  try {
    const message = await mongoDB.collection('salle_messages').findOne({ _id: new ObjectId(messageId) })
    if (!message || message.senderId !== ws.userId) {
      ws.send(JSON.stringify({ type: 'error', message: 'Non autorisé' }))
      return
    }

    await mongoDB.collection('salle_messages').deleteOne({ _id: new ObjectId(messageId) })

    await broadcastToSalle(conversationService, pgPool, message.salleId, {
      type: 'salle_message_deleted',
      messageId
    })
  } catch (error) {
    ws.send(JSON.stringify({ type: 'error', message: error.message }))
  }
}

module.exports = {
  handleSalleSend,
  handleSalleTyping,
  handleSalleReact,
  handleSalleEdit,
  handleSalleDelete
}
