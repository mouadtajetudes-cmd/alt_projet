<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50">
    <div class="max-w-7xl mx-auto h-screen flex">
      <div
        class="w-80 bg-white border-r border-gray-200 flex flex-col transition-transform duration-300"
        :class="{'hidden md:flex': !sidebarOpen, 'flex': sidebarOpen}"
      >
        <div class="p-4 border-b border-gray-200">
          <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold text-dark">Messages</h2>
            <button 
              @click="sidebarOpen = false"
              class="md:hidden p-2 hover:bg-gray-100 rounded-lg transition-colors"
            >
              <font-awesome-icon icon="times" class="w-5 h-5" />
            </button>
          </div>

          <div class="relative">
            <font-awesome-icon icon="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
            <input 
              v-model="searchQuery"
              type="text" 
              placeholder="Rechercher..." 
              class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all"
            >
          </div>
        </div>

        <div class="flex-1 overflow-y-auto">
          <div class="p-4 text-center text-gray-500">
            <p class="text-sm">Chat global en temps réel</p>
            <div class="mt-4 p-3 bg-blue-50 rounded-lg">
              <div class="flex items-center gap-2 text-primary font-medium">
                <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                <span>{{ onlineUsers }} en ligne</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="flex-1 flex flex-col bg-white">
        <div 
          class="p-4 border-b border-gray-200 bg-white transition-all duration-200"
          :class="{'shadow-md backdrop-blur-sm': headerScrolled}"
        >
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
              <button 
                @click="sidebarOpen = true"
                class="md:hidden p-2 hover:bg-gray-100 rounded-lg transition-colors"
              >
                <font-awesome-icon icon="bars" class="w-5 h-5" />
              </button>

              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-primary to-primary-light flex items-center justify-center text-white text-xl">
                  <font-awesome-icon icon="comments" />
                </div>
                <div>
                  <h3 class="font-semibold text-dark">Chat Global</h3>
                  <div class="flex items-center gap-1 text-sm text-gray-500">
                    <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                    <span>{{ wsConnected ? 'Connecté' : 'Déconnecté' }}</span>
                  </div>
                </div>
              </div>
            </div>

            <div class="flex items-center gap-2">
              <router-link 
                to="/profile"
                class="px-4 py-2 text-sm font-medium text-primary hover:bg-blue-50 rounded-lg transition-colors"
              >
                Profil
              </router-link>
              <router-link 
                to="/users"
                class="px-4 py-2 text-sm font-medium text-primary hover:bg-blue-50 rounded-lg transition-colors"
              >
                Utilisateurs
              </router-link>
            </div>
          </div>
        </div>

        <div 
          ref="messagesArea"
          class="flex-1 overflow-y-auto p-6 space-y-4 bg-gray-50"
          @scroll="checkHeaderScroll"
        >
          <div v-if="loading" class="flex items-center justify-center h-full">
            <div class="flex gap-2">
              <div class="w-3 h-3 bg-primary rounded-full animate-bounce" style="animation-delay: 0ms"></div>
              <div class="w-3 h-3 bg-primary rounded-full animate-bounce" style="animation-delay: 150ms"></div>
              <div class="w-3 h-3 bg-primary rounded-full animate-bounce" style="animation-delay: 300ms"></div>
            </div>
          </div>

          <div v-else-if="currentUser">
            <div class="flex items-center justify-center py-4">
              <span class="px-4 py-1 bg-white border border-gray-200 rounded-full text-xs text-gray-600 font-medium shadow-sm">
                Aujourd'hui
              </span>
            </div>

            <div 
              v-for="message in messages"
              :key="message.id"
              class="flex animate-slide-up"
              :class="isMyMessage(message.userId) ? 'justify-end' : 'justify-start'"
            >
              <div 
                class="max-w-md"
                :class="isMyMessage(message.userId) ? 'order-2' : 'order-1'"
              >
                <div 
                  v-if="!isMyMessage(message.userId)"
                  class="bg-white border border-gray-200 rounded-2xl rounded-tl-sm px-4 py-3 shadow-sm"
                >
                  <div class="font-semibold text-primary text-sm mb-1">
                    {{ message.sender || message.userId }}
                  </div>
                  <p class="text-dark">{{ message.content }}</p>
                  <div class="flex items-center gap-2 mt-2">
                    <span class="text-xs text-gray-500">{{ formatMessageTime(message.time) }}</span>
                  </div>
                </div>

                <div 
                  v-else
                  class="bg-gradient-to-br from-primary to-primary-light rounded-2xl rounded-tr-sm px-4 py-3 shadow-md"
                >
                  <p class="text-white">{{ message.content }}</p>
                  <div class="flex items-center justify-end gap-2 mt-2">
                    <span class="text-xs text-white/80">{{ formatMessageTime(message.time) }}</span>
                    <font-awesome-icon icon="check" class="w-4 h-4 text-white/80" />
                  </div>
                </div>
              </div>
            </div>

            <div v-if="typingUser" class="flex justify-start animate-slide-up">
              <div class="bg-white border border-gray-200 rounded-2xl rounded-tl-sm px-5 py-3 shadow-sm">
                <div class="text-xs text-primary font-medium mb-1">{{ typingUser }}</div>
                <div class="flex gap-1">
                  <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0ms"></div>
                  <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 150ms"></div>
                  <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 300ms"></div>
                </div>
              </div>
            </div>
          </div>

          <div v-else class="flex items-center justify-center h-full text-gray-400">
            <div class="text-center">
              <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
              </svg>
              <p class="text-lg">Connexion en cours...</p>
            </div>
          </div>
        </div>

        <div v-if="currentUser" class="p-4 border-t border-gray-200 bg-white">
          <div class="flex items-end gap-3">
            <button class="p-2 hover:bg-gray-100 rounded-lg transition-colors flex-shrink-0">
              <font-awesome-icon icon="paperclip" class="w-6 h-6 text-gray-600" />
            </button>
            
            <div class="flex-1 relative">
              <textarea
                v-model="messageInput"
                @keypress.enter.exact.prevent="sendMessage"
                @input="handleTyping"
                placeholder="Écrire un message..."
                rows="1"
                class="w-full px-4 py-3 pr-12 border-2 border-gray-300 rounded-2xl focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent resize-none transition-all"
              ></textarea>
              
              <button class="absolute right-3 top-1/2 -translate-y-1/2 p-1 hover:bg-gray-100 rounded-lg transition-colors">
                <font-awesome-icon icon="smile" class="w-5 h-5 text-gray-600" />
              </button>
            </div>

            <button 
              @click="sendMessage"
              :disabled="!messageInput.trim()"
              class="px-5 py-3 bg-gradient-to-br from-primary to-primary-light text-white font-medium rounded-2xl transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:translate-y-0 disabled:hover:shadow-none flex-shrink-0 relative overflow-hidden group"
            >
              <span class="relative z-10 flex items-center gap-2">
                <font-awesome-icon icon="paper-plane" class="w-5 h-5" />
                Envoyer
              </span>
              <div class="absolute inset-0 bg-gradient-to-b from-white/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-200"></div>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, nextTick } from 'vue'
import { useRouter } from 'vue-router'
import './views.css'

const router = useRouter()

const currentUser = ref(null)
const loading = ref(true)
const messages = ref([])
const messageInput = ref('')
const ws = ref(null)
const wsConnected = ref(false)
const typingUser = ref(null)
const sidebarOpen = ref(true)
const headerScrolled = ref(false)
const messagesArea = ref(null)
const searchQuery = ref('')
const onlineUsers = ref(1)

const loadCurrentUser = async () => {
  try {
    const token = localStorage.getItem('token')
    if (!token) {
      router.push('/login')
      return
    }

    const payload = JSON.parse(atob(token.split('.')[1]))
    const userId = payload.sub || payload.user?.id
    
    if (!userId) {
      throw new Error('User ID not found in token')
    }

    const response = await fetch(`http://localhost:6090/auth/users/${userId}`, {
      headers: { 'Authorization': `Bearer ${token}` }
    })
    
    if (!response.ok) {
      throw new Error('Failed to load profile')
    }
    
    const data = await response.json()
    currentUser.value = data
    loading.value = false
    
    connectWebSocket()
  } catch (error) {
    console.error('Error loading user:', error)
    loading.value = false
    setTimeout(() => {
      router.push('/login')
    }, 2000)
  }
}

const notificationSound = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBjOJ0fPTgjMGHW7A7+OZRA0PVKzo8qtbFwxEnN/zuWwhBjqP0/PQfjAHHW2/8OaYRQ4OTKbm8qlbGApDmN/yum0hBjmM0fPRfi8IHmu+8OSXRw8OTKPl8apbFwtBlt3zuW0iBTeL0PLRfS8IHWq88OOWRxAOSaHk86lbGQxAldz0uWwjBzaKz/HSfS4JHGm78OKVSBAOSKDj9KlbGg1AkNv0uGwkBjaI0PHSfS0JHGi68OKVSRAPRp/i9alZGwxCk9r0uGolBzWHz/HUfC0KHWa58OKVShEPR5zi9KlYGw1BkNn0uWkkBzWGzvHUey0KHWW48OOUShEPRZvh9apYHAxCj9j0uWkkBzSFzvHVei0LHGa38OSUSHIPR5rg9KpYHA1CjtfzumkkBzSEzvHUei0LHGW18OSUSHIPR5nf9KpXHA1CjdXzumgkBzODzvHUeS0LHGWz8OOUSHIPR5je9KpXGw1CjNTyumgkBzOCzvHTeSsMHWSy8OOSRxIQSJfe9apXGgxBi9PyuWckBzOBzvHTeSsMHWSy8OORRxIQR5bd9KpXGgxChNLyu2kjBjSBzvHTeSwNHmSy7+ORRxIPRpXc9KhWGQtBg9Hyu2giB')
const isPageFocused = ref(true)
const unreadCount = ref(0)
const originalTitle = 'Chat - Alt'

const playNotificationSound = () => {
  try {
    notificationSound.play().catch(err => console.log('Sound play failed:', err))
  } catch (err) {
    console.log('Sound error:', err)
  }
}

const updatePageTitle = () => {
  if (unreadCount.value > 0 && !isPageFocused.value) {
    document.title = `(${unreadCount.value}) ${originalTitle}`
  } else {
    document.title = originalTitle
    unreadCount.value = 0
  }
}

const connectWebSocket = () => {
  const token = localStorage.getItem('token')
  ws.value = new WebSocket('ws://localhost:3001')
  
  ws.value.onopen = () => {
    wsConnected.value = true
    ws.value.send(JSON.stringify({
      type: 'auth',
      token: token,
      userId: currentUser.value.email,
      userName: `${currentUser.value.nom} ${currentUser.value.prenom}`
    }))
  }
  
  ws.value.onmessage = (event) => {
    const data = JSON.parse(event.data)
    
    if (data.type === 'history') {
      messages.value = data.messages
      scrollToBottom()
    } else if (data.type === 'message') {
      messages.value.push(data.message)
      scrollToBottom()
      
      if (!isMyMessage(data.message.email) && !isPageFocused.value) {
        playNotificationSound()
        unreadCount.value++
        updatePageTitle()
      }
    } else if (data.type === 'typing') {
      if (!isMyMessage(data.userId)) {
        typingUser.value = data.sender || data.userId
        setTimeout(() => {
          typingUser.value = null
        }, 3000)
      }
    }
  }
  
  ws.value.onclose = () => {
    wsConnected.value = false
    setTimeout(() => {
      if (currentUser.value) {
        connectWebSocket()
      }
    }, 3000)
  }
  
  ws.value.onerror = (error) => {
    console.error('WebSocket error:', error)
    wsConnected.value = false
  }
}

const handleFocus = () => {
  isPageFocused.value = true
  unreadCount.value = 0
  updatePageTitle()
}

const handleBlur = () => {
  isPageFocused.value = false
}

const sendMessage = () => {
  if (messageInput.value.trim() && ws.value && wsConnected.value) {
    const payload = {
      type: 'send',
      content: messageInput.value,
      sender: `${currentUser.value.nom} ${currentUser.value.prenom}`
    }
    ws.value.send(JSON.stringify(payload))
    messageInput.value = ''
  }
}

const handleTyping = () => {
  if (ws.value && wsConnected.value) {
    ws.value.send(JSON.stringify({
      type: 'typing',
      email: currentUser.value.email,
      sender: `${currentUser.value.nom} ${currentUser.value.prenom}`
    }))
  }
}

const isMyMessage = (userId) => {
  return userId === currentUser.value.email
}

const formatMessageTime = (timestamp) => {
  const date = new Date(timestamp)
  return date.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' })
}

const scrollToBottom = () => {
  nextTick(() => {
    if (messagesArea.value) {
      messagesArea.value.scrollTop = messagesArea.value.scrollHeight
    }
  })
}

const checkHeaderScroll = () => {
  if (messagesArea.value) {
    headerScrolled.value = messagesArea.value.scrollTop > 10
  }
}

onMounted(() => {
  loadCurrentUser()
  window.addEventListener('focus', handleFocus)
  window.addEventListener('blur', handleBlur)
})

onUnmounted(() => {
  if (ws.value) {
    ws.value.close()
  }
  window.removeEventListener('focus', handleFocus)
  window.removeEventListener('blur', handleBlur)
  document.title = originalTitle
})
</script>
