function formatMessage(msg) {
  return {
    id: msg._id.toString(),
    conversationId: msg.conversationId,
    userId: msg.senderId,
    sender: msg.sender || msg.senderId,
    content: msg.content,
    type: msg.type,
    mediaUrl: msg.mediaUrl,
    mediaName: msg.mediaName,
    mediaSize: msg.mediaSize,
    isRead: msg.isRead,
    reactions: msg.reactions || [],
    deleted: msg.deleted || false,
    time: msg.createdAt instanceof Date ? msg.createdAt.toISOString() : msg.createdAt
  }
}

module.exports = { formatMessage }
