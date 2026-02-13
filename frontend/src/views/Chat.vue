<template>
  <div class="chat-page">
    <div class="container">
      <div v-if="!currentUser" class="user-selection">
        <h1>Sélectionner un utilisateur</h1>
        <p>Choisissez votre identité pour le chat</p>
        
        <div class="users-list">
          <div 
            v-for="user in users" 
            :key="user.id_utilisateur"
            class="user-item"
            @click="selectUser(user)"
          >
            <div class="user-name">{{ user.nom }} {{ user.prenom }}</div>
            <div class="user-email">{{ user.email }}</div>
          </div>
        </div>
      </div>
      
      <!-- Chat Container -->
      <div v-else class="chat-container">
        <div class="chat-header">
          <div class="current-user">
            <h3>{{ currentUser.nom }} {{ currentUser.prenom }}</h3>
            <div class="status">
              <span :class="['status-badge', wsConnected ? '' : 'disconnected']"></span>
              {{ wsConnected ? 'Connecté' : 'Déconnecté' }}
            </div>
          </div>
          <button class="btn-logout" @click="logout">Changer d'utilisateur</button>
        </div>
        
        <div class="messages-area" ref="messagesArea">
          <div 
            v-for="message in messages" 
            :key="message.id"
            :class="['message', message.userId === currentUser.email ? 'mine' : '']"
          >
            <div class="message-bubble">
              <div v-if="message.userId !== currentUser.email" class="message-sender">
                {{ message.sender || message.userId }}
              </div>
              <div class="message-text">{{ message.content }}</div>
              <div class="message-time">{{ formatTime(message.time) }}</div>
            </div>
          </div>
        </div>
        
        <div v-if="typingUser" class="typing-indicator">
          {{ typingUser }} est en train d'écrire...
        </div>
        
        <div class="chat-input">
          <input 
            v-model="messageInput"
            @input="handleTyping"
            @keypress.enter="sendMessage"
            type="text" 
            placeholder="Tapez votre message..."
          >
          <button class="btn-send" @click="sendMessage">➤</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Chat',
  data() {
    return {
      users: [],
      currentUser: null,
      messages: [],
      messageInput: '',
      ws: null,
      wsConnected: false,
      typingUser: null
    }
  },
  mounted() {
    this.loadUsers()
  },
  beforeUnmount() {
    if (this.ws) {
      this.ws.close()
    }
  },
  methods: {
    async loadUsers() {
      try {
        console.log('[LOAD USERS] Loading users...')
        const response = await fetch('http://localhost:6090/auth/users')
        console.log('[LOAD USERS] Response status:', response.status)
        const data = await response.json()
        console.log('[LOAD USERS] Users loaded:', data.length)
        this.users = data
      } catch (error) {
        console.error('[LOAD USERS] Error:', error)
      }
    },
    
    selectUser(user) {
      this.currentUser = user
      this.connectWebSocket()
    },
    
    connectWebSocket() {
      console.log('[WS] Connecting...')
      this.ws = new WebSocket('ws://localhost:3001')
      
      this.ws.onopen = () => {
        console.log('[WS] Connected')
        this.wsConnected = true
        this.ws.send(JSON.stringify({
          type: 'auth',
          userId: this.currentUser.email,
          userName: `${this.currentUser.nom} ${this.currentUser.prenom}`
        }))
        console.log('[WS] Auth message sent')
      }
      
      this.ws.onmessage = (event) => {
        const data = JSON.parse(event.data)
        console.log('[WS] Message received:', data.type, data)
        
        if (data.type === 'history') {
          this.messages = data.messages
          console.log('[HISTORY] Loaded:', this.messages.length, 'messages')
          console.log('[HISTORY] Details:', this.messages)
          this.scrollToBottom()
        } else if (data.type === 'message') {
          console.log('[MESSAGE] Received from server:', data)
          console.log('[MESSAGE] data.message:', data.message)
          console.log('[MESSAGE] Array before push:', this.messages.length)
          this.messages.push(data.message)
          console.log('[MESSAGE] Array after push:', this.messages.length)
          console.log('[MESSAGE] Last message:', this.messages[this.messages.length - 1])
          this.scrollToBottom()
        } else if (data.type === 'typing') {
          if (data.userId !== this.currentUser.email) {
            this.typingUser = data.sender || data.userId
            setTimeout(() => {
              this.typingUser = null
            }, 3000)
          }
        }
      }
      
      this.ws.onclose = () => {
        console.log('[WS] Disconnected')
        this.wsConnected = false
      }
      
      this.ws.onerror = (error) => {
        console.error('[WS] Error:', error)
        this.wsConnected = false
      }
    },
    
    sendMessage() {
      console.log('[SEND] Attempting to send message...')
      console.log('[SEND] messageInput:', this.messageInput)
      console.log('[SEND] ws:', this.ws ? 'exists' : 'null')
      console.log('[SEND] wsConnected:', this.wsConnected)
      
      if (this.messageInput.trim() && this.ws && this.wsConnected) {
        const payload = {
          type: 'send',
          content: this.messageInput,
          sender: `${this.currentUser.nom} ${this.currentUser.prenom}`
        }
        console.log('[SEND] Sending payload:', payload)
        this.ws.send(JSON.stringify(payload))
        this.messageInput = ''
      } else {
        console.error('[SEND] Conditions not met:')
        console.error('[SEND] Empty?', !this.messageInput.trim())
        console.error('[SEND] WS null?', !this.ws)
        console.error('[SEND] Disconnected?', !this.wsConnected)
      }
    },
    
    handleTyping() {
      if (this.ws && this.wsConnected) {
        this.ws.send(JSON.stringify({
          type: 'typing',
          email: this.currentUser.email,
          sender: `${this.currentUser.nom} ${this.currentUser.prenom}`
        }))
      }
    },
    
    logout() {
      if (this.ws) {
        this.ws.close()
      }
      this.currentUser = null
      this.messages = []
      this.wsConnected = false
    },
    
    formatTime(timestamp) {
      const date = new Date(timestamp)
      return date.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' })
    },
    
    scrollToBottom() {
      this.$nextTick(() => {
        const messagesArea = this.$refs.messagesArea
        if (messagesArea) {
          messagesArea.scrollTop = messagesArea.scrollHeight
        }
      })
    }
  }
}
</script>

<style scoped>
.chat-page {
  padding: 2rem 0;
}

.container {
  max-width: 1000px;
  margin: 0 auto;
  padding: 0 1rem;
}

.user-selection {
  background: white;
  padding: 2rem;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.user-selection h1 {
  color: #333;
  margin-bottom: 0.5rem;
}

.user-selection p {
  color: #666;
  margin-bottom: 1.5rem;
}

.users-list {
  max-height: 400px;
  overflow-y: auto;
}

.user-item {
  padding: 1rem;
  margin: 0.5rem 0;
  background: #f8f9fa;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.2s;
}

.user-item:hover {
  background: #0d6efd;
  color: white;
  transform: translateX(5px);
}

.user-name {
  font-weight: 600;
  font-size: 1rem;
}

.user-email {
  font-size: 0.875rem;
  color: #999;
  margin-top: 0.25rem;
}

.user-item:hover .user-email {
  color: rgba(255,255,255,0.8);
}

.chat-container {
  background: white;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  height: 600px;
  display: flex;
  flex-direction: column;
}

/* Header */
.chat-header {
  background: #0d6efd;
  color: white;
  padding: 1rem 1.5rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
  border-radius: 12px 12px 0 0;
}

.current-user h3 {
  font-size: 1rem;
  margin-bottom: 0.25rem;
}

.status {
  font-size: 0.875rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.status-badge {
  display: inline-block;
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: #4caf50;
}

.status-badge.disconnected {
  background: #f44336;
}

.btn-logout {
  background: rgba(255,255,255,0.2);
  border: none;
  padding: 0.5rem 1rem;
  border-radius: 4px;
  color: white;
  cursor: pointer;
}

.btn-logout:hover {
  background: rgba(255,255,255,0.3);
}

/* Messages */
.messages-area {
  flex: 1;
  overflow-y: auto;
  padding: 1.5rem;
  background: #f5f5f5;
}

.message {
  margin-bottom: 1rem;
}

.message.mine {
  text-align: right;
}

.message-bubble {
  display: inline-block;
  max-width: 60%;
  padding: 0.75rem 1rem;
  border-radius: 12px;
}

.message:not(.mine) .message-bubble {
  background: white;
  box-shadow: 0 2px 5px rgba(0,0,0,0.1);
  text-align: left;
}

.message.mine .message-bubble {
  background: #0d6efd;
  color: white;
}

.message-sender {
  font-size: 0.75rem;
  font-weight: 600;
  margin-bottom: 0.25rem;
  color: #0d6efd;
}

.message-text {
  line-height: 1.4;
  word-wrap: break-word;
}

.message-time {
  font-size: 0.7rem;
  margin-top: 0.25rem;
  opacity: 0.7;
}

.typing-indicator {
  padding: 0.5rem 1.5rem;
  font-size: 0.875rem;
  color: #666;
  font-style: italic;
}

/* Input */
.chat-input {
  padding: 1rem 1.5rem;
  background: white;
  border-top: 1px solid #e0e0e0;
  display: flex;
  gap: 0.75rem;
  border-radius: 0 0 12px 12px;
}

.chat-input input {
  flex: 1;
  padding: 0.75rem 1rem;
  border: 2px solid #e0e0e0;
  border-radius: 25px;
  outline: none;
}

.chat-input input:focus {
  border-color: #0d6efd;
}

.btn-send {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  background: #0d6efd;
  border: none;
  color: white;
  font-size: 1.25rem;
  cursor: pointer;
  transition: transform 0.2s;
}

.btn-send:hover {
  transform: scale(1.1);
}
</style>
