<template>
  <div class="page">
    <div class="container">
      <div v-if="!currentUser" class="selection">
        <h2>💬 Sélectionner un utilisateur</h2>
        <div v-for="user in users" :key="user.id_utilisateur" class="user-item" @click="selectUser(user)">
          {{ user.nom }} {{ user.prenom }}
        </div>
      </div>
      
      <div v-else class="chat">
        <div class="header">
          <div>
            <h3>{{ currentUser.nom }} {{ currentUser.prenom }}</h3>
            <small :style="{color: wsConnected ? '#4caf50' : '#f44336'}">
              {{ wsConnected ? '● Connecté' : '● Déconnecté' }}
            </small>
          </div>
          <div>
            <router-link to="/users" class="btn">Utilisateurs</router-link>
            <button @click="logout" class="btn">Changer</button>
          </div>
        </div>
        
        <div class="messages" ref="messagesArea">
          <div v-for="message in messages" :key="message.id" 
               :class="['message', message.userId === currentUser.email ? 'mine' : '']">
            <div class="bubble">
              <div v-if="message.userId !== currentUser.email" class="sender">
                {{ message.sender || message.userId }}
              </div>
              <div>{{ message.content }}</div>
              <small>{{ formatTime(message.time) }}</small>
            </div>
          </div>
        </div>
        
        <div v-if="typingUser" class="typing">{{ typingUser }} écrit...</div>
        
        <div class="input">
          <input v-model="messageInput" @input="handleTyping" 
                 @keypress.enter="sendMessage" placeholder="Message...">
          <button @click="sendMessage">➤</button>
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
        const token = localStorage.getItem('token')
        const response = await fetch('http://localhost:6090/auth/users', {
          headers: {
            'Authorization': `Bearer ${token}`
          }
        })
        const data = await response.json()
        this.users = data
      } catch (error) {
        console.error('Error loading users:', error)
      }
    },
    
    selectUser(user) {
      this.currentUser = user
      this.connectWebSocket()
    },
    
    connectWebSocket() {
      this.ws = new WebSocket('ws://localhost:3001')
      
      this.ws.onopen = () => {
        this.wsConnected = true
        this.ws.send(JSON.stringify({
          type: 'auth',
          userId: this.currentUser.email,
          userName: `${this.currentUser.nom} ${this.currentUser.prenom}`
        }))
      }
      
      this.ws.onmessage = (event) => {
        const data = JSON.parse(event.data)
        
        if (data.type === 'history') {
          this.messages = data.messages
          this.scrollToBottom()
        } else if (data.type === 'message') {
          this.messages.push(data.message)
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
        this.wsConnected = false
      }
      
      this.ws.onerror = (error) => {
        this.wsConnected = false
      }
    },
    
    sendMessage() {
      if (this.messageInput.trim() && this.ws && this.wsConnected) {
        const payload = {
          type: 'send',
          content: this.messageInput,
          sender: `${this.currentUser.nom} ${this.currentUser.prenom}`
        }
        this.ws.send(JSON.stringify(payload))
        this.messageInput = ''
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
.page {
  min-height: 100vh;
  background: #f5f7fa;
  padding: 2rem 1rem;
}

.container {
  max-width: 900px;
  margin: 0 auto;
}

.selection {
  background: white;
  padding: 2rem;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.selection h2 {
  margin-bottom: 1.5rem;
  color: #333;
}

.user-item {
  padding: 1rem;
  margin: 0.5rem 0;
  background: #f5f7fa;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.2s;
}

.user-item:hover {
  background: #2196F3;
  color: white;
}

.chat {
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.08);
  height: 600px;
  display: flex;
  flex-direction: column;
}

.header {
  background: #2196F3;
  color: white;
  padding: 1rem 1.5rem;
  border-radius: 12px 12px 0 0;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.header h3 {
  font-size: 1rem;
  margin-bottom: 0.25rem;
}

.header small {
  font-size: 0.85rem;
}

.btn {
  background: rgba(255,255,255,0.2);
  border: none;
  padding: 0.5rem 1rem;
  border-radius: 8px;
  color: white;
  cursor: pointer;
  text-decoration: none;
  margin-left: 0.5rem;
}

.btn:hover {
  background: rgba(255,255,255,0.3);
}

.messages {
  flex: 1;
  overflow-y: auto;
  padding: 1.5rem;
  background: #f5f7fa;
}

.message {
  margin-bottom: 1rem;
}

.message.mine {
  text-align: right;
}

.bubble {
  display: inline-block;
  max-width: 60%;
  padding: 0.75rem 1rem;
  border-radius: 12px;
}

.message:not(.mine) .bubble {
  background: white;
  text-align: left;
}

.message.mine .bubble {
  background: #2196F3;
  color: white;
}

.sender {
  font-size: 0.75rem;
  font-weight: 600;
  margin-bottom: 0.25rem;
  color: #2196F3;
}

.bubble small {
  font-size: 0.7rem;
  opacity: 0.7;
  display: block;
  margin-top: 0.25rem;
}

.typing {
  padding: 0.5rem 1.5rem;
  font-size: 0.875rem;
  color: #666;
  font-style: italic;
}

.input {
  padding: 1rem 1.5rem;
  border-top: 1px solid #e0e0e0;
  display: flex;
  gap: 0.75rem;
}

.input input {
  flex: 1;
  padding: 0.75rem 1rem;
  border: 2px solid #e0e0e0;
  border-radius: 20px;
  outline: none;
}

.input input:focus {
  border-color: #2196F3;
}

.input button {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  background: #2196F3;
  border: none;
  color: white;
  font-size: 1.25rem;
  cursor: pointer;
}

.input button:hover {
  background: #1976D2;
}
</style>
