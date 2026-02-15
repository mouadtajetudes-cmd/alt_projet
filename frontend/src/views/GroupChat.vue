<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50 flex">
    <div class="flex-1 flex flex-col max-w-7xl mx-auto w-full">
      <div class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
        <div class="flex items-center gap-4">
          <button
            @click="goBack"
            class="p-2 hover:bg-gray-100 rounded-lg transition-colors"
          >
            <font-awesome-icon icon="arrow-left" class="text-gray-600" />
          </button>
          <div>
            <h1 class="text-xl font-bold text-gray-900">{{ salle?.nom || 'Salle' }}</h1>
            <p class="text-sm text-gray-500">
              <font-awesome-icon icon="layer-group" class="mr-1" />
              {{ groupe?.nom || 'Groupe' }} · {{ members.length }} membre(s)
            </p>
          </div>
        </div>
        <div class="flex items-center gap-2">
          <span v-if="wsConnected" class="flex items-center gap-2 text-green-600 text-sm">
            <span class="w-2 h-2 bg-green-600 rounded-full animate-pulse"></span>
            En ligne
          </span>
          <span v-else class="flex items-center gap-2 text-red-600 text-sm">
            <span class="w-2 h-2 bg-red-600 rounded-full"></span>
            Déconnecté
          </span>
        </div>
      </div>

      <div 
        ref="messagesArea"
        class="flex-1 overflow-y-auto p-6 space-y-4"
        style="max-height: calc(100vh - 200px)"
      >
        <div v-if="loading" class="flex justify-center py-12">
          <div class="flex gap-2">
            <div class="w-3 h-3 bg-blue-600 rounded-full animate-bounce"></div>
            <div class="w-3 h-3 bg-blue-600 rounded-full animate-bounce" style="animation-delay: 150ms"></div>
            <div class="w-3 h-3 bg-blue-600 rounded-full animate-bounce" style="animation-delay: 300ms"></div>
          </div>
        </div>

        <div v-else-if="messages.length === 0" class="text-center py-12">
          <font-awesome-icon icon="comments" class="text-gray-300 text-6xl mb-4" />
          <p class="text-gray-500">Aucun message. Soyez le premier à écrire !</p>
        </div>

        <div v-else v-for="message in messages" :key="message._id" class="animate-slide-up">
          <div :class="['flex gap-3', message.senderId === currentUserId ? 'flex-row-reverse' : '']">
            <div 
              class="w-10 h-10 rounded-full flex items-center justify-center text-white font-semibold flex-shrink-0"
              :class="message.senderId === currentUserId ? 'bg-blue-600' : 'bg-purple-600'"
            >
              {{ message.senderName?.[0] || 'U' }}
            </div>

            <div :class="['flex flex-col max-w-[70%]', message.senderId === currentUserId ? 'items-end' : 'items-start']">
              <div class="flex items-center gap-2 mb-1">
                <span class="text-sm font-medium text-gray-700">
                  {{ message.senderId === currentUserId ? 'Vous' : message.senderName }}
                </span>
                <span class="text-xs text-gray-400">
                  {{ formatTime(message.timestamp) }}
                </span>
              </div>

              <div 
                :class="[
                  'px-4 py-3 rounded-2xl',
                  message.senderId === currentUserId 
                    ? 'bg-blue-600 text-white' 
                    : 'bg-gray-100 text-gray-900'
                ]"
              >
                <p class="whitespace-pre-wrap break-words">{{ message.content }}</p>
                <p v-if="message.editedAt" class="text-xs opacity-70 mt-1">(modifié)</p>
              </div>

              <div v-if="message.reactions && message.reactions.length > 0" class="flex gap-1 mt-1">
                <button
                  v-for="(reaction, idx) in getGroupedReactions(message.reactions)"
                  :key="idx"
                  @click="toggleReaction(message._id, reaction.emoji)"
                  class="px-2 py-1 bg-white border rounded-full text-sm hover:bg-gray-50 transition-colors"
                  :class="reaction.userIds.includes(currentUserId) ? 'border-blue-500' : 'border-gray-300'"
                >
                  {{ reaction.emoji }} {{ reaction.count }}
                </button>
              </div>

              <div v-if="message.senderId === currentUserId" class="flex gap-2 mt-2">
                <button
                  @click="startEdit(message)"
                  class="text-xs text-gray-500 hover:text-blue-600 transition-colors"
                >
                  Modifier
                </button>
                <button
                  @click="deleteMessage(message._id)"
                  class="text-xs text-gray-500 hover:text-red-600 transition-colors"
                >
                  Supprimer
                </button>
                <button
                  @click="showEmojiPicker = message._id"
                  class="text-xs text-gray-500 hover:text-yellow-600 transition-colors"
                >
                  Réagir
                </button>
              </div>
            </div>
          </div>
        </div>

        <div v-if="typingUsers.length > 0" class="text-sm text-gray-500 italic">
          {{ typingUsers.join(', ') }} {{ typingUsers.length > 1 ? 'sont en train' : 'est en train' }} d'écrire...
        </div>
      </div>

      <div class="bg-white border-t border-gray-200 p-4">
        <div v-if="editingMessage" class="mb-2 p-2 bg-yellow-50 border-l-4 border-yellow-400 flex items-center justify-between">
          <span class="text-sm text-yellow-700">Modification: {{ editingMessage.content.substring(0, 50) }}...</span>
          <button @click="cancelEdit" class="text-yellow-700 hover:text-yellow-900">
            <font-awesome-icon icon="times" />
          </button>
        </div>

        <form @submit.prevent="sendMessage" class="flex gap-3">
          <input
            v-model="messageInput"
            @input="handleTyping"
            type="text"
            placeholder="Tapez votre message..."
            class="flex-1 px-4 py-3 border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
          />
          <button
            type="submit"
            :disabled="!messageInput.trim()"
            class="px-6 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <font-awesome-icon icon="paper-plane" />
          </button>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, nextTick, computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import '../views/views.css'

const router = useRouter()
const route = useRoute()

const salleId = ref(route.params.salleId)
const salle = ref(null)
const groupe = ref(null)
const members = ref([])
const messages = ref([])
const messageInput = ref('')
const editingMessage = ref(null)
const loading = ref(true)
const ws = ref(null)
const wsConnected = ref(false)
const typingUsers = ref([])
const typingTimeout = ref(null)
const messagesArea = ref(null)
const showEmojiPicker = ref(null)

const currentUser = ref(null)
const currentUserId = computed(() => {
  if (!currentUser.value) return null
  return String(currentUser.value.id_utilisateur || currentUser.value.id)
})

onMounted(async () => {
  await loadCurrentUser()
  await loadSalleInfo()
  await loadMessages()
  connectWebSocket()
})

onUnmounted(() => {
  if (ws.value) ws.value.close()
  if (typingTimeout.value) clearTimeout(typingTimeout.value)
})

async function loadCurrentUser() {
  const storedUser = localStorage.getItem('user')
  if (storedUser) {
    currentUser.value = JSON.parse(storedUser)
  }
}

async function loadSalleInfo() {
  try {
    const salleResponse = await fetch(`http://localhost:3001/groups/${salleId.value}/salles`)
    if (!salleResponse.ok) throw new Error('Failed to load salle')
    const salles = await salleResponse.json()
    salle.value = salles.find(s => s.id_salle === parseInt(salleId.value))

    if (salle.value) {
      const groupeResponse = await fetch(`http://localhost:3001/groups/${salle.value.id_groupe}`)
      if (groupeResponse.ok) {
        groupe.value = await groupeResponse.json()
      }

      const membersResponse = await fetch(`http://localhost:3001/groups/${salle.value.id_groupe}/members`)
      if (membersResponse.ok) {
        members.value = await membersResponse.json()
      }
    }
  } catch (error) {
    console.error('Error loading salle info:', error)
  }
}

async function loadMessages() {
  loading.value = true
  try {
    const response = await fetch(`http://localhost:3001/salles/${salleId.value}/messages?limit=100`)
    if (!response.ok) throw new Error('Failed to load messages')
    const data = await response.json()
    messages.value = data.messages || []
    nextTick(() => scrollToBottom())
  } catch (error) {
    console.error('Error loading messages:', error)
  } finally {
    loading.value = false
  }
}

function connectWebSocket() {
  if (ws.value) ws.value.close()

  ws.value = new WebSocket('ws://localhost:3001')

  ws.value.onopen = () => {
    wsConnected.value = true
    ws.value.send(JSON.stringify({
      type: 'auth',
      userId: currentUserId.value,
      userName: `${currentUser.value.prenom} ${currentUser.value.nom}`
    }))
  }

  ws.value.onmessage = (event) => {
    const data = JSON.parse(event.data)

    if (data.type === 'salle_message') {
      messages.value.push(data.message)
      nextTick(() => scrollToBottom())
    } else if (data.type === 'salle_typing') {
      handleRemoteTyping(data.userId, data.userName, data.typing)
    } else if (data.type === 'salle_message_reaction') {
      const message = messages.value.find(m => m._id === data.messageId)
      if (message) {
        message.reactions = data.reactions
      }
    } else if (data.type === 'salle_message_edited') {
      const message = messages.value.find(m => m._id === data.messageId)
      if (message) {
        message.content = data.content
        message.editedAt = data.editedAt
      }
    } else if (data.type === 'salle_message_deleted') {
      const index = messages.value.findIndex(m => m._id === data.messageId)
      if (index !== -1) {
        messages.value.splice(index, 1)
      }
    }
  }

  ws.value.onclose = () => {
    wsConnected.value = false
    setTimeout(() => connectWebSocket(), 3000)
  }
}

async function sendMessage() {
  const content = messageInput.value.trim()
  if (!content || !ws.value || !wsConnected.value) return

  if (editingMessage.value) {
    ws.value.send(JSON.stringify({
      type: 'salle_edit',
      messageId: editingMessage.value._id,
      newContent: content
    }))
    editingMessage.value = null
  } else {
    ws.value.send(JSON.stringify({
      type: 'salle_send',
      salleId: salleId.value,
      content,
      messageType: 'text'
    }))
  }

  messageInput.value = ''
  sendTyping(false)
}

function handleTyping() {
  sendTyping(true)
  
  if (typingTimeout.value) clearTimeout(typingTimeout.value)
  typingTimeout.value = setTimeout(() => {
    sendTyping(false)
  }, 2000)
}

function sendTyping(typing) {
  if (ws.value && wsConnected.value) {
    ws.value.send(JSON.stringify({
      type: 'salle_typing',
      salleId: salleId.value,
      typing
    }))
  }
}

function handleRemoteTyping(userId, userName, typing) {
  if (userId === currentUserId.value) return

  const index = typingUsers.value.indexOf(userName)
  if (typing && index === -1) {
    typingUsers.value.push(userName)
  } else if (!typing && index !== -1) {
    typingUsers.value.splice(index, 1)
  }
}

function startEdit(message) {
  editingMessage.value = message
  messageInput.value = message.content
}

function cancelEdit() {
  editingMessage.value = null
  messageInput.value = ''
}

function deleteMessage(messageId) {
  if (!confirm('Supprimer ce message ?')) return

  ws.value.send(JSON.stringify({
    type: 'salle_delete',
    messageId
  }))
}

function toggleReaction(messageId, emoji) {
  ws.value.send(JSON.stringify({
    type: 'salle_react',
    messageId,
    emoji,
    salleId: salleId.value
  }))
}

function getGroupedReactions(reactions) {
  const grouped = {}
  reactions.forEach(r => {
    if (!grouped[r.emoji]) {
      grouped[r.emoji] = { emoji: r.emoji, count: 0, userIds: [] }
    }
    grouped[r.emoji].count++
    grouped[r.emoji].userIds.push(r.userId)
  })
  return Object.values(grouped)
}

function scrollToBottom() {
  if (messagesArea.value) {
    messagesArea.value.scrollTop = messagesArea.value.scrollHeight
  }
}

function formatTime(timestamp) {
  const date = new Date(timestamp)
  const now = new Date()
  const diff = now - date
  const hours = date.getHours().toString().padStart(2, '0')
  const minutes = date.getMinutes().toString().padStart(2, '0')

  if (diff < 86400000) {
    return `${hours}:${minutes}`
  } else {
    const day = date.getDate().toString().padStart(2, '0')
    const month = (date.getMonth() + 1).toString().padStart(2, '0')
    return `${day}/${month} ${hours}:${minutes}`
  }
}

function goBack() {
  router.push('/groups')
}
</script>
