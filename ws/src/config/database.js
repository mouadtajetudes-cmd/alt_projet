const { MongoClient } = require('mongodb')
const { Pool } = require('pg')

const MONGO_URI = process.env.MONGO_URI || 'mongodb://alt.mongo:27017'

const pgPool = new Pool({
  host: process.env.POSTGRES_HOST || 'alt.db',
  port: process.env.POSTGRES_PORT || 5432,
  database: process.env.POSTGRES_DB || 'alt',
  user: process.env.POSTGRES_USER || 'alt',
  password: process.env.POSTGRES_PASSWORD || 'alt'
})

let mongoDB

async function connectMongoDB() {
  const client = new MongoClient(MONGO_URI)
  await client.connect()
  mongoDB = client.db('chat_db')
  console.log('✅ MongoDB connecté')
}

async function cleanupBrokenConversations() {
  try {
    const result = await mongoDB.collection('conversations').deleteMany({
      participants: 'undefined'
    })
    if (result.deletedCount > 0) {
      console.log(`🧹 Nettoyé ${result.deletedCount} conversations corrompues`)
    }

    const msgResult = await mongoDB.collection('messages').deleteMany({
      senderId: 'undefined'
    })
    if (msgResult.deletedCount > 0) {
      console.log(`🧹 Nettoyé ${msgResult.deletedCount} messages corrompus`)
    }
  } catch (e) {
    console.error('Erreur nettoyage:', e)
  }
}

module.exports = {
  pgPool,
  mongoDB: () => mongoDB,
  connectMongoDB,
  cleanupBrokenConversations
}
