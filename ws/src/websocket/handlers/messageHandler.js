const { ObjectId } = require('mongodb')
const { broadcastToConversation } = require('../broadcast')
const { formatMessage } = require('../../utils/formatters')

async function handleSendMessage(ws, mongoDB, conversationService, msg) {
  const { conversationId, content, messageType, mediaUrl, mediaName, mediaSize, replyTo } = msg

  if (!ws.userId) {
    ws.send(JSON.stringify({ type: 'error', message: 'Non authentifié' }))
    return
  }

  if (!conversationId) {
    ws.send(JSON.stringify({ type: 'error', message: 'conversationId requis' }))
    return
  }

  if (content && content.length > 2000) {
    ws.send(JSON.stringify({
      type: 'error',
      message: 'Message trop long. Max: 2000 caractères (compte gratuit)'
    }))
    return
  }

  const messageDoc = {
    conversationId: conversationId,
    senderId: ws.userId,
    sender: ws.userName,
    content: content || '',
    type: messageType || 'text',
    mediaUrl: mediaUrl || null,
    mediaName: mediaName || null,
    mediaSize: mediaSize || null,
    replyTo: replyTo || null,
    isRead: false,
    createdAt: new Date()
  }

  const result = await mongoDB.collection('messages').insertOne(messageDoc)
  messageDoc._id = result.insertedId

  await mongoDB.collection('conversations').updateOne(
    { _id: new ObjectId(conversationId) },
    { $set: { updatedAt: new Date() } }
  )

  const formatted = formatMessage(messageDoc)

  const participants = await conversationService.getConversationParticipants(mongoDB, conversationId)
  const { sendToUser } = require('../broadcast')
  participants.forEach(participantId => {
    sendToUser(participantId, {
      type: 'message',
      message: formatted
    })
  })
}

async function handleReact(ws, mongoDB, msg) {
  const { messageId, emoji } = msg
  
  if (!ws.userId || !messageId || !emoji) {
    ws.send(JSON.stringify({ type: 'error', message: 'Données manquantes' }))
    return
  }

  try {
    const message = await mongoDB.collection('messages').findOne({ _id: new ObjectId(messageId) })
    if (!message) {
      ws.send(JSON.stringify({ type: 'error', message: 'Message non trouvé' }))
      return
    }

    const reactions = message.reactions || []
    const existingIndex = reactions.findIndex(r => r.userId === ws.userId && r.emoji === emoji)
    
    if (existingIndex >= 0) {
      reactions.splice(existingIndex, 1)
    } else {
      reactions.push({ userId: ws.userId, userName: ws.userName, emoji })
    }

    await mongoDB.collection('messages').updateOne(
      { _id: new ObjectId(messageId) },
      { $set: { reactions } }
    )

    await broadcastToConversation({getConversationParticipants: require('../../services/conversationService').getConversationParticipants}, mongoDB, message.conversationId, {
      type: 'reaction',
      messageId,
      reactions
    })
  } catch (error) {
    ws.send(JSON.stringify({ type: 'error', message: error.message }))
  }
}

async function handleDelete(ws, mongoDB, msg) {
  const { messageId } = msg
  
  if (!ws.userId || !messageId) {
    ws.send(JSON.stringify({ type: 'error', message: 'Données manquantes' }))
    return
  }

  try {
    const message = await mongoDB.collection('messages').findOne({ _id: new ObjectId(messageId) })
    if (!message) {
      ws.send(JSON.stringify({ type: 'error', message: 'Message non trouvé' }))
      return
    }

    if (message.senderId !== ws.userId) {
      ws.send(JSON.stringify({ type: 'error', message: 'Non autorisé' }))
      return
    }

    await mongoDB.collection('messages').updateOne(
      { _id: new ObjectId(messageId) },
      { $set: { deleted: true, content: '', mediaUrl: null, mediaName: null, mediaSize: null, reactions: [] } }
    )

    await broadcastToConversation({getConversationParticipants: require('../../services/conversationService').getConversationParticipants}, mongoDB, message.conversationId, {
      type: 'deleted',
      messageId
    })
  } catch (error) {
    ws.send(JSON.stringify({ type: 'error', message: error.message }))
  }
}

async function handleEdit(ws, mongoDB, msg) {
  const { messageId, newContent, conversationId } = msg
  
  if (!ws.userId || !messageId || !newContent) {
    ws.send(JSON.stringify({ type: 'error', message: 'Données manquantes' }))
    return
  }

  try {
    const message = await mongoDB.collection('messages').findOne({ _id: new ObjectId(messageId) })
    if (!message) {
      ws.send(JSON.stringify({ type: 'error', message: 'Message non trouvé' }))
      return
    }

    if (message.senderId !== ws.userId) {
      ws.send(JSON.stringify({ type: 'error', message: 'Non autorisé' }))
      return
    }

    await mongoDB.collection('messages').updateOne(
      { _id: new ObjectId(messageId) },
      { $set: { content: newContent, edited: true, editedAt: new Date() } }
    )

    await broadcastToConversation({getConversationParticipants: require('../../services/conversationService').getConversationParticipants}, mongoDB, message.conversationId || conversationId, {
      type: 'edited',
      messageId,
      newContent
    })
  } catch (error) {
    ws.send(JSON.stringify({ type: 'error', message: error.message }))
  }
}

module.exports = {
  handleSendMessage,
  handleReact,
  handleDelete,
  handleEdit
}
