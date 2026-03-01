<template>
  <div class="relative">
    <button
      @click="toggleDropdown"
      class="relative p-2 text-gray-600 hover:text-primary hover:bg-blue-50 rounded-lg transition-all"
    >
      <font-awesome-icon icon="bell" class="text-xl" />
      <span 
        v-if="unreadCount > 0"
        class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center font-bold animate-pulse"
      >
        {{ unreadCount > 9 ? '9+' : unreadCount }}
      </span>
    </button>

    <transition name="dropdown">
      <div
        v-if="showDropdown"
        class="absolute right-0 mt-2 w-96 bg-white rounded-xl shadow-2xl border border-gray-200 z-50 max-h-[32rem] overflow-hidden flex flex-col"
      >
        <div class="p-4 border-b border-gray-200 flex items-center justify-between bg-gradient-to-r from-blue-50 to-purple-50">
          <h3 class="font-bold text-gray-900">Notifications</h3>
          <button
            v-if="unreadCount > 0"
            @click="markAllAsRead"
            class="text-xs text-blue-600 hover:text-blue-800 font-medium"
          >
            Tout marquer comme lu
          </button>
        </div>

        <div class="overflow-y-auto flex-1">
          <div v-if="loading" class="p-8 text-center">
            <div class="flex justify-center gap-2">
              <div class="w-2 h-2 bg-blue-600 rounded-full animate-bounce"></div>
              <div class="w-2 h-2 bg-blue-600 rounded-full animate-bounce" style="animation-delay: 150ms"></div>
              <div class="w-2 h-2 bg-blue-600 rounded-full animate-bounce" style="animation-delay: 300ms"></div>
            </div>
          </div>

          <div v-else-if="notifications.length === 0" class="p-8 text-center">
            <font-awesome-icon icon="bell-slash" class="text-gray-300 text-4xl mb-2" />
            <p class="text-gray-500 text-sm">Aucune notification</p>
          </div>

          <div v-else>
            <div
              v-for="notification in notifications"
              :key="notification.id_notification"
              @click="handleNotificationClick(notification)"
              :class="[
                'p-4 border-b border-gray-100 hover:bg-gray-50 transition-colors cursor-pointer',
                !notification.lue ? 'bg-blue-50' : ''
              ]"
            >
              <div class="flex items-start gap-3">
                <div 
                  :class="[
                    'w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0',
                    getNotificationColor(notification.type)
                  ]"
                >
                  <font-awesome-icon :icon="getNotificationIcon(notification.type)" class="text-white" />
                </div>
                <div class="flex-1 min-w-0">
                  <p class="font-semibold text-gray-900 text-sm">{{ notification.titre }}</p>
                  <p class="text-sm text-gray-600 mt-1">{{ notification.contenu }}</p>
                  <p class="text-xs text-gray-400 mt-1">{{ formatTime(notification.created_at) }}</p>
                </div>
                <button
                  @click.stop="deleteNotification(notification.id_notification)"
                  class="text-gray-400 hover:text-red-500 transition-colors"
                >
                  <font-awesome-icon icon="times" class="text-sm" />
                </button>
              </div>
            </div>
          </div>
        </div>

        <div class="p-2 border-t border-gray-200 bg-gray-50">
          <button
            @click="goToNotifications"
            class="w-full text-center text-sm text-blue-600 hover:text-blue-800 font-medium py-2 rounded-lg hover:bg-blue-50 transition-colors"
          >
            Voir toutes les notifications
          </button>
        </div>
      </div>
    </transition>

    <div
      v-if="showDropdown"
      @click="showDropdown = false"
      class="fixed inset-0 z-40"
    ></div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const showDropdown = ref(false)
const notifications = ref([])
const loading = ref(false)
const ws = ref(null)

const currentUser = computed(() => {
  const user = localStorage.getItem('user')
  return user ? JSON.parse(user) : null
})

const currentUserId = computed(() => {
  return currentUser.value?.id_utilisateur || currentUser.value?.id
})

const unreadCount = computed(() => {
  return notifications.value.filter(n => !n.lue).length
})

onMounted(() => {
  loadNotifications()
  connectWebSocket()
})

onUnmounted(() => {
  if (ws.value) ws.value.close()
})

function connectWebSocket() {
  ws.value = new WebSocket('ws://localhost:3001')

  ws.value.onopen = () => {
    ws.value.send(JSON.stringify({
      type: 'auth',
      userId: currentUserId.value
    }))
  }

  ws.value.onmessage = (event) => {
    const data = JSON.parse(event.data)
    if (data.type === 'notification') {
      notifications.value.unshift(data.notification)
      playNotificationSound()
    }
  }

  ws.value.onclose = () => {
    setTimeout(() => connectWebSocket(), 3000)
  }
}

async function loadNotifications() {
  if (!currentUserId.value) return
  
  loading.value = true
  try {
    const response = await fetch(`http://localhost:3001/notifications/${currentUserId.value}?limit=20`)
    if (response.ok) {
      notifications.value = await response.json()
    }
  } catch (error) {
    console.error('Erreur chargement notifications:', error)
  } finally {
    loading.value = false
  }
}

function toggleDropdown() {
  showDropdown.value = !showDropdown.value
  if (showDropdown.value) {
    loadNotifications()
  }
}

async function markAllAsRead() {
  try {
    const response = await fetch(`http://localhost:3001/notifications/${currentUserId.value}/read-all`, {
      method: 'PUT'
    })
    if (response.ok) {
      notifications.value.forEach(n => n.lue = true)
    }
  } catch (error) {
    console.error('Erreur marquage notifications:', error)
  }
}

async function handleNotificationClick(notification) {
  if (!notification.lue) {
    await fetch(`http://localhost:3001/notifications/${notification.id_notification}/read`, {
      method: 'PUT'
    })
    notification.lue = true
  }

  showDropdown.value = false

  if (notification.type === 'friend_request') {
    router.push('/friends?tab=received')
  } else if (notification.type === 'friend_accepted') {
    router.push('/friends')
  } else if (notification.type === 'new_message') {
    router.push('/chat')
  } else if (notification.type === 'group_invite') {
    router.push('/groups')
  }
}

async function deleteNotification(id) {
  try {
    const response = await fetch(`http://localhost:3001/notifications/${id}`, {
      method: 'DELETE'
    })
    if (response.ok) {
      notifications.value = notifications.value.filter(n => n.id_notification !== id)
    }
  } catch (error) {
    console.error('Erreur suppression notification:', error)
  }
}

function goToNotifications() {
  showDropdown.value = false
  router.push('/notifications')
}

function getNotificationIcon(type) {
  const icons = {
    'friend_request': 'user-plus',
    'friend_accepted': 'user-check',
    'new_message': 'comment',
    'group_invite': 'users',
    'system': 'info-circle'
  }
  return icons[type] || 'bell'
}

function getNotificationColor(type) {
  const colors = {
    'friend_request': 'bg-blue-500',
    'friend_accepted': 'bg-green-500',
    'new_message': 'bg-purple-500',
    'group_invite': 'bg-orange-500',
    'system': 'bg-gray-500'
  }
  return colors[type] || 'bg-gray-500'
}

function formatTime(timestamp) {
  const date = new Date(timestamp)
  const now = new Date()
  const diff = now - date
  
  const minutes = Math.floor(diff / 60000)
  const hours = Math.floor(diff / 3600000)
  const days = Math.floor(diff / 86400000)

  if (minutes < 1) return 'À l\'instant'
  if (minutes < 60) return `Il y a ${minutes}min`
  if (hours < 24) return `Il y a ${hours}h`
  if (days < 7) return `Il y a ${days}j`
  
  return date.toLocaleDateString('fr-FR', { day: '2-digit', month: 'short' })
}

function playNotificationSound() {
  const audio = new Audio('/notification.mp3')
  audio.volume = 0.5
  audio.play().catch(() => {})
}
</script>

<style scoped>
.dropdown-enter-active, .dropdown-leave-active {
  transition: all 0.2s ease;
}

.dropdown-enter-from {
  opacity: 0;
  transform: translateY(-10px);
}

.dropdown-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}
</style>
