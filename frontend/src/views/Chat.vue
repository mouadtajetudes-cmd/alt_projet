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
              @click="showNewConversationModal = true"
              class="p-2 bg-primary text-white hover:bg-primary-dark rounded-lg transition-colors"
              title="Nouvelle conversation"
            >
              <font-awesome-icon icon="plus" class="w-4 h-4" />
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
          <div v-if="filteredConversations.length === 0" class="p-4 text-center text-gray-500">
            <p class="text-sm">Aucune conversation</p>
            <p class="text-xs mt-2">Cliquez sur + pour démarrer</p>
          </div>

          <div v-else class="divide-y divide-gray-100">
            <div
              v-for="conv in filteredConversations"
              :key="conv._id"
              @click="selectConversation(conv)"
              class="p-4 hover:bg-gray-50 cursor-pointer transition-colors"
              :class="{'bg-blue-50 border-l-4 border-primary': selectedConversation?._id === conv._id}"
            >
              <div class="flex items-center gap-3">
                <div class="relative">
                  <div class="w-12 h-12 rounded-full bg-gradient-to-br from-primary to-primary-light flex items-center justify-center text-white font-semibold text-sm">
                    {{ getInitials(getOtherParticipant(conv)) }}
                  </div>
                  <div 
                    class="absolute bottom-0 right-0 w-3 h-3 rounded-full border-2 border-white"
                    :class="isUserOnline(getOtherParticipantId(conv)) ? 'bg-green-500' : 'bg-gray-400'"
                  ></div>
                </div>
                <div class="flex-1 min-w-0">
                  <div class="flex items-center justify-between">
                    <h3 class="font-semibold text-dark truncate">{{ getOtherParticipant(conv) }}</h3>
                    <span v-if="conv.lastMessage" class="text-xs text-gray-400">
                      {{ formatMessageTime(conv.lastMessage.time) }}
                    </span>
                  </div>
                  <div class="flex items-center justify-between mt-1">
                    <p class="text-sm text-gray-500 truncate">
                      {{ conv.lastMessage?.content || 'Nouvelle conversation' }}
                    </p>
                    <span
                      v-if="conv.unreadCount > 0"
                      class="ml-2 min-w-[20px] h-5 px-1.5 bg-red-500 text-white text-xs rounded-full font-bold flex items-center justify-center"
                    >
                      {{ conv.unreadCount }}
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="flex-1 flex flex-col bg-white">
        <div
          class="p-4 border-b border-gray-200 bg-white transition-all duration-200"
          :class="{'shadow-md': headerScrolled}"
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
                  <font-awesome-icon :icon="selectedConversation ? 'user' : 'comments'" />
                </div>
                <div>
                  <h3 class="font-semibold text-dark">
                    {{ selectedConversation ? getOtherParticipant(selectedConversation) : 'Sélectionnez une conversation' }}
                  </h3>
                  <div v-if="selectedConversation" class="flex items-center gap-1 text-sm">
                    <div class="w-2 h-2 rounded-full" :class="isUserOnline(getOtherParticipantId(selectedConversation)) ? 'bg-green-500' : 'bg-gray-400'"></div>
                    <span :class="isUserOnline(getOtherParticipantId(selectedConversation)) ? 'text-green-600' : 'text-gray-500'">
                      {{ isUserOnline(getOtherParticipantId(selectedConversation)) ? 'En ligne' : 'Hors ligne' }}
                    </span>
                  </div>
                  <div v-else class="flex items-center gap-1 text-sm text-gray-500">
                    <div class="w-2 h-2 rounded-full animate-pulse" :class="wsConnected ? 'bg-green-500' : 'bg-red-500'"></div>
                    <span>{{ wsConnected ? 'Connecté' : 'Déconnecté' }}</span>
                  </div>
                </div>
              </div>
            </div>
            <div class="flex items-center gap-2">
              <router-link to="/profile" class="px-4 py-2 text-sm font-medium text-primary hover:bg-blue-50 rounded-lg transition-colors">
                Profil
              </router-link>
              <router-link to="/users" class="px-4 py-2 text-sm font-medium text-primary hover:bg-blue-50 rounded-lg transition-colors">
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

          <div v-else-if="!selectedConversation" class="flex items-center justify-center h-full text-gray-400">
            <div class="text-center">
              <font-awesome-icon icon="comments" class="text-6xl mb-4 text-gray-300" />
              <p class="text-lg font-medium">Sélectionnez une conversation</p>
              <p class="text-sm mt-2">ou créez-en une nouvelle avec le bouton +</p>
            </div>
          </div>

          <div v-else-if="currentUser">
            <div v-if="messages.length === 0" class="flex items-center justify-center h-64 text-gray-400">
              <div class="text-center">
                <font-awesome-icon icon="paper-plane" class="text-4xl mb-3 text-gray-300" />
                <p>Envoyez le premier message !</p>
              </div>
            </div>

            <div v-else>
              <div
                v-for="message in messages"
                :key="message.id"
                class="flex mb-3"
                :class="isMyMessage(message.userId) ? 'justify-end' : 'justify-start'"
              >
                <div class="max-w-md">
                  <div
                    v-if="!isMyMessage(message.userId)"
                    class="bg-white border border-gray-200 rounded-2xl rounded-tl-sm px-4 py-3 shadow-sm"
                  >
                    <div class="font-semibold text-primary text-sm mb-1">
                      {{ message.sender }}
                    </div>

                    <div v-if="message.type === 'image' && message.mediaUrl" class="mb-2">
                      <img :src="message.mediaUrl" :alt="message.mediaName || 'image'" class="max-w-xs rounded-lg cursor-pointer hover:opacity-90 transition-opacity" @click="openImage(message.mediaUrl)" />
                    </div>

                    <a v-else-if="message.type === 'file' && message.mediaUrl" :href="message.mediaUrl" target="_blank" class="flex items-center gap-2 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors mb-2">
                      <font-awesome-icon icon="file-alt" class="text-primary text-xl" />
                      <div class="flex-1 min-w-0">
                        <div class="text-sm font-medium text-dark truncate">{{ message.mediaName || 'Document' }}</div>
                        <div v-if="message.mediaSize" class="text-xs text-gray-400">{{ formatFileSize(message.mediaSize) }}</div>
                      </div>
                      <font-awesome-icon icon="download" class="text-gray-400" />
                    </a>

                    <p v-if="message.content" class="text-dark">{{ message.content }}</p>

                    <div v-if="message.reactions && message.reactions.length > 0" class="flex flex-wrap gap-1 mt-2">
                      <button
                        v-for="(group, emoji) in groupReactions(message.reactions)"
                        :key="emoji"
                        @click="reactToMessage(message.id, emoji)"
                        class="inline-flex items-center gap-1 px-2 py-0.5 bg-gray-100 hover:bg-gray-200 rounded-full text-xs transition-colors"
                        :class="{ 'ring-2 ring-primary': hasReacted(message.reactions, emoji) }"
                        :title="group.map(r => r.userName).join(', ')"
                      >
                        <span>{{ emoji }}</span>
                        <span class="text-gray-600">{{ group.length }}</span>
                      </button>
                    </div>

                    <div class="flex items-center gap-2 mt-2">
                      <span class="text-xs text-gray-500">{{ formatMessageTime(message.time) }}</span>
                      <button
                        @click="toggleReactionPicker(message.id)"
                        class="text-gray-400 hover:text-gray-600 transition-colors"
                        title="Réagir"
                      >
                        <font-awesome-icon icon="smile" class="w-3 h-3" />
                      </button>
                    </div>

                    <div v-if="showReactionPicker === message.id" class="absolute bg-white border border-gray-200 rounded-lg shadow-xl p-2 z-10 flex gap-1 mt-1">
                      <button
                        v-for="emoji in quickReactions"
                        :key="emoji"
                        @click="reactToMessage(message.id, emoji); showReactionPicker = null"
                        class="w-8 h-8 flex items-center justify-center hover:bg-gray-100 rounded text-lg transition-colors"
                      >
                        {{ emoji }}
                      </button>
                    </div>
                  </div>
                  <div
                    v-else
                    class="bg-gradient-to-br from-primary to-primary-light rounded-2xl rounded-tr-sm px-4 py-3 shadow-md relative group"
                  >
                    <button
                      v-if="!message.deleted"
                      @click="deleteMessage(message.id)"
                      class="absolute -top-2 -left-2 w-6 h-6 bg-red-500 text-white rounded-full opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center shadow-lg hover:bg-red-600"
                      title="Supprimer"
                    >
                      <font-awesome-icon icon="trash" class="w-3 h-3" />
                    </button>

                    <div v-if="message.deleted" class="flex items-center gap-2 text-white/60 italic">
                      <font-awesome-icon icon="info-circle" class="w-4 h-4" />
                      <span class="text-sm">Message supprimé</span>
                    </div>

                    <template v-else>
                      <div v-if="message.type === 'image' && message.mediaUrl" class="mb-2">
                        <img :src="message.mediaUrl" :alt="message.mediaName || 'image'" class="max-w-xs rounded-lg cursor-pointer hover:opacity-90 transition-opacity" @click="openImage(message.mediaUrl)" />
                      </div>

                      <a v-else-if="message.type === 'file' && message.mediaUrl" :href="message.mediaUrl" target="_blank" class="flex items-center gap-2 p-3 bg-white/20 rounded-lg hover:bg-white/30 transition-colors mb-2">
                      <font-awesome-icon icon="file-alt" class="text-white text-xl" />
                      <div class="flex-1 min-w-0">
                        <div class="text-sm font-medium text-white truncate">{{ message.mediaName || 'Document' }}</div>
                        <div v-if="message.mediaSize" class="text-xs text-white/70">{{ formatFileSize(message.mediaSize) }}</div>
                      </div>
                      <font-awesome-icon icon="download" class="text-white/70" />
                      </a>

                      <p v-if="message.content" class="text-white">{{ message.content }}</p>

                      <div v-if="message.reactions && message.reactions.length > 0" class="flex flex-wrap gap-1 mt-2">
                      <button
                        v-for="(group, emoji) in groupReactions(message.reactions)"
                        :key="emoji"
                        @click="reactToMessage(message.id, emoji)"
                        class="inline-flex items-center gap-1 px-2 py-0.5 bg-white/20 hover:bg-white/30 rounded-full text-xs transition-colors"
                        :class="{ 'ring-2 ring-white': hasReacted(message.reactions, emoji) }"
                        :title="group.map(r => r.userName).join(', ')"
                      >
                        <span>{{ emoji }}</span>
                          <span class="text-white/80">{{ group.length }}</span>
                        </button>
                      </div>

                      <div class="flex items-center justify-end gap-2 mt-2">
                      <button
                        @click="toggleReactionPicker(message.id)"
                        class="text-white/60 hover:text-white transition-colors"
                        title="Réagir"
                      >
                        <font-awesome-icon icon="smile" class="w-3 h-3" />
                      </button>
                        <span class="text-xs text-white/80">{{ formatMessageTime(message.time) }}</span>
                        <font-awesome-icon icon="check" class="w-3 h-3 text-white/80" />
                      </div>

                      <div v-if="showReactionPicker === message.id" class="absolute bg-white border border-gray-200 rounded-lg shadow-xl p-2 z-10 flex gap-1 mt-1">
                      <button
                        v-for="emoji in quickReactions"
                        :key="emoji"
                        @click="reactToMessage(message.id, emoji); showReactionPicker = null"
                        class="w-8 h-8 flex items-center justify-center hover:bg-gray-100 rounded text-lg transition-colors"
                      >
                        {{ emoji }}
                        </button>
                      </div>
                    </template>
                  </div>
                </div>
              </div>
            </div>

            <div v-if="typingUser" class="flex justify-start mb-3">
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
        </div>

        <div v-if="currentUser && selectedConversation" class="p-4 border-t border-gray-200 bg-white">
          <div v-if="selectedFile" class="mb-3 flex items-center gap-3 p-3 bg-blue-50 rounded-xl border border-blue-200">
            <div v-if="filePreviewUrl" class="w-16 h-16 rounded-lg overflow-hidden flex-shrink-0">
              <img :src="filePreviewUrl" class="w-full h-full object-cover" />
            </div>
            <font-awesome-icon v-else icon="file-alt" class="text-3xl text-primary flex-shrink-0" />
            <div class="flex-1 min-w-0">
              <div class="text-sm font-medium text-dark truncate">{{ selectedFile.name }}</div>
              <div class="text-xs text-gray-500">{{ formatFileSize(selectedFile.size) }}</div>
            </div>
            <button @click="clearSelectedFile" class="p-1.5 hover:bg-blue-100 rounded-lg transition-colors">
              <font-awesome-icon icon="times" class="text-gray-500" />
            </button>
          </div>

          <div v-if="uploading" class="mb-3">
            <div class="w-full bg-gray-200 rounded-full h-2">
              <div class="bg-primary h-2 rounded-full transition-all duration-300" style="width: 50%"></div>
            </div>
            <p class="text-xs text-gray-500 mt-1 text-center">Envoi en cours...</p>
          </div>

          <div class="flex items-end gap-3">
            <button
              @click="$refs.fileInput.click()"
              class="p-3 text-gray-500 hover:text-primary hover:bg-blue-50 rounded-xl transition-colors flex-shrink-0"
              title="Joindre un fichier"
            >
              <font-awesome-icon icon="paperclip" class="w-5 h-5" />
            </button>
            <input
              ref="fileInput"
              type="file"
              accept="image/*,.pdf,.doc,.docx,.txt,.zip,.rar"
              @change="handleFileSelect"
              class="hidden"
            />
            <div class="flex-1 relative">
              <textarea
                v-model="messageInput"
                @keypress.enter.exact.prevent="sendMessage"
                @input="handleTyping"
                placeholder="Écrire un message..."
                rows="1"
                class="w-full px-4 py-3 pr-12 border-2 border-gray-300 rounded-2xl focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent resize-none transition-all"
              ></textarea>
            </div>

            <div class="relative flex-shrink-0">
              <button
                @click="showEmojiPicker = !showEmojiPicker"
                class="p-3 text-gray-500 hover:text-yellow-500 hover:bg-yellow-50 rounded-xl transition-colors"
                title="Emoji"
              >
                <font-awesome-icon icon="smile" class="w-5 h-5" />
              </button>

              <div
                v-if="showEmojiPicker"
                class="absolute bottom-14 right-0 bg-white border border-gray-200 rounded-2xl shadow-xl p-3 z-50 w-72"
              >
                <div class="grid grid-cols-8 gap-1 max-h-48 overflow-y-auto">
                  <button
                    v-for="emoji in emojiList"
                    :key="emoji"
                    @click="insertEmoji(emoji)"
                    class="w-8 h-8 flex items-center justify-center hover:bg-gray-100 rounded-lg text-xl transition-colors cursor-pointer"
                  >
                    {{ emoji }}
                  </button>
                </div>
              </div>
            </div>
            <button
              @click="sendMessage"
              :disabled="!messageInput.trim() && !selectedFile"
              class="px-5 py-3 bg-gradient-to-br from-primary to-primary-light text-white font-medium rounded-2xl transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:translate-y-0 disabled:hover:shadow-none flex-shrink-0"
            >
              <span class="flex items-center gap-2">
                <font-awesome-icon icon="paper-plane" class="w-5 h-5" />
                Envoyer
              </span>
            </button>
          </div>
        </div>

        <div v-else-if="currentUser && !selectedConversation" class="p-4 border-t border-gray-200 bg-gray-100">
          <div class="text-center text-gray-400 text-sm py-2">
            Sélectionnez une conversation pour envoyer un message
          </div>
        </div>
      </div>
    </div>

    <div
      v-if="showNewConversationModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
      @click.self="showNewConversationModal = false"
    >
      <div class="bg-white rounded-2xl p-6 max-w-md w-full mx-4 max-h-[80vh] flex flex-col">
        <div class="flex items-center justify-between mb-6">
          <h3 class="text-xl font-bold text-dark">Nouvelle conversation</h3>
          <button
            @click="showNewConversationModal = false"
            class="p-2 hover:bg-gray-100 rounded-lg transition-colors"
          >
            <font-awesome-icon icon="times" class="w-5 h-5 text-gray-600" />
          </button>
        </div>
        <div class="flex-1 overflow-y-auto space-y-2">
          <div v-if="allUsers.length === 0" class="text-center py-8 text-gray-500">
            Aucun utilisateur disponible
          </div>
          <div
            v-for="user in allUsers"
            :key="user.id_utilisateur"
            @click="createConversation(user.id_utilisateur)"
            class="p-4 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors flex items-center gap-3"
          >
            <div class="w-12 h-12 rounded-full bg-gradient-to-br from-primary to-primary-light flex items-center justify-center text-white font-semibold">
              {{ user.nom?.[0] || '?' }}{{ user.prenom?.[0] || '?' }}
            </div>
            <div>
              <h4 class="font-semibold text-dark">{{ user.nom }} {{ user.prenom }}</h4>
              <p class="text-sm text-gray-500">{{ user.email }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div
    v-if="previewImage"
    class="fixed inset-0 bg-black/80 flex items-center justify-center z-50 p-4"
    @click.self="previewImage = null"
  >
    <div class="relative max-w-4xl max-h-[90vh]">
      <button
        @click="previewImage = null"
        class="absolute -top-4 -right-4 w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-lg hover:bg-gray-100 transition-colors z-10"
      >
        <font-awesome-icon icon="times" class="text-gray-700" />
      </button>
      <img :src="previewImage" class="max-w-full max-h-[85vh] rounded-xl shadow-2xl object-contain" />
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, nextTick, computed } from 'vue'
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
const conversations = ref([])
const selectedConversation = ref(null)
const allUsers = ref([])
const showNewConversationModal = ref(false)
const isPageFocused = ref(true)
const unreadCount = ref(0)
const originalTitle = 'Chat - Alt'
const selectedFile = ref(null)
const filePreviewUrl = ref(null)
const uploading = ref(false)
const showEmojiPicker = ref(false)
const previewImage = ref(null)
const onlineUsers = ref(new Set())
const showReactionPicker = ref(null)
const replyTo = ref(null)
const editingMessage = ref(null)
const searchMessagesQuery = ref('')
const pinnedConversations = ref(new Set())

const quickReactions = ['❤️', '👍', '😂', '😮', '😢', '🙏']

const emojiList = [
  '😀','😂','😍','🥰','😊','😎','🤔','😢',
  '😡','🥳','😴','🤗','😇','🤩','😋','😜',
  '👍','👎','❤️','🔥','✨','🎉','💯','👏',
  '🙏','💪','✅','❌','⭐','🌟','💬','📎',
  '📷','🎵','🎶','☀️','🌙','⚡','💡','🏠',
  '🚀','💻','📱','📄','🔗','🎁','🍕','☕'
]

const currentUserId = computed(() => {
  if (!currentUser.value) return null
  return String(currentUser.value.id_utilisateur || currentUser.value.id)
})

const filteredConversations = computed(() => {
  if (!searchQuery.value) return conversations.value
  const q = searchQuery.value.toLowerCase()
  return conversations.value.filter(conv => {
    return getOtherParticipant(conv).toLowerCase().includes(q)
  })
})

const notificationSound = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBjOJ0fPTgjMGHW7A7+OZRA0PVKzo8qtbFwxEnN/zuWwhBjqP0/PQfjAHHW2/8OaYRQ4OTKbm8qlbGApDmN/yum0hBjmM0fPRfi8IHmu+8OSXRw8OTKPl8apbFwtBlt3zuW0iBTeL0PLRfS8IHWq88OOWRxAOSaHk86lbGQxAldz0uWwjBzaKz/HSfS4JHGm78OKVSBAOSKDj9KlbGg1AkNv0uGwkBjaI0PHSfS0JHGi68OKVSRAPRp/i9alZGwxCk9r0uGolBzWHz/HUfC0KHWa58OKVShEPR5zi9KlYGw1BkNn0uWkkBzWGzvHUey0KHWW48OOUShEPRZvh9apYHAxCj9j0uWkkBzSFzvHVei0LHGa38OSUSHIPR5rg9KpYHA1CjtfzumkkBzSEzvHUei0LHGW18OSUSHIPR5nf9KpXHA1CjdXzumgkBzODzvHUeS0LHGWz8OOUSHIPR5je9KpXGw1CjNTyumgkBzOCzvHTeSsMHWSy8OOSRxIQSJfe9apXGgxBi9PyuWckBzOBzvHTeSsMHWSy8OORRxIQR5bd9KpXGgxChNLyu2kjBjSBzvHTeSwNHmSy7+ORRxIPRpXc9KhWGQtBg9Hyu2giB')

const playNotificationSound = () => {
  try { notificationSound.play().catch(() => {}) } catch (e) {}
}

const updatePageTitle = () => {
  if (unreadCount.value > 0 && !isPageFocused.value) {
    document.title = `(${unreadCount.value}) ${originalTitle}`
  } else {
    document.title = originalTitle
    unreadCount.value = 0
  }
}

const loadCurrentUser = async () => {
  try {
    const storedUser = localStorage.getItem('user')
    if (storedUser) {
      currentUser.value = JSON.parse(storedUser)
      loading.value = false
      await loadAllUsers()
      await loadConversations()
      connectWebSocket()
      return
    }
    
    const token = localStorage.getItem('token')
    if (!token) { router.push('/login'); return }

    const payload = JSON.parse(atob(token.split('.')[1]))
    const userId = payload.sub || payload.user?.id
    if (!userId) throw new Error('User ID not found')

    const response = await fetch(`http://localhost:6090/auth/users/${userId}`, {
      headers: { 'Authorization': `Bearer ${token}` }
    })
    if (!response.ok) throw new Error('Failed to load profile')

    currentUser.value = await response.json()
    localStorage.setItem('user', JSON.stringify(currentUser.value))
    loading.value = false

    await loadAllUsers()
    await loadConversations()
    connectWebSocket()
  } catch (error) {
    console.error('Error loading user:', error)
    loading.value = false
    setTimeout(() => router.push('/login'), 2000)
  }
}



const loadConversations = async () => {
  try {
    const response = await fetch(`http://localhost:3001/conversations/${currentUserId.value}`)
    if (!response.ok) throw new Error('Failed to load conversations')
    conversations.value = await response.json()
  } catch (error) {
    console.error('Error loading conversations:', error)
  }
}

const loadAllUsers = async () => {
  try {
    const token = localStorage.getItem('token')
    const response = await fetch('http://localhost:6090/auth/users', {
      headers: { 'Authorization': `Bearer ${token}` }
    })
    if (!response.ok) throw new Error('Failed to load users')
    const users = await response.json()
    allUsers.value = users.filter(u => String(u.id_utilisateur) !== currentUserId.value)
  } catch (error) {
    console.error('Error loading users:', error)
  }
}

const selectConversation = async (conversation) => {
  selectedConversation.value = conversation
  messages.value = []
  typingUser.value = null

  try {
    const response = await fetch(`http://localhost:3001/conversations/${conversation._id}/messages?limit=50`)
    if (!response.ok) throw new Error('Failed to load messages')
    const data = await response.json()
    messages.value = data.messages || []
    scrollToBottom()

    await fetch(`http://localhost:3001/conversations/${conversation._id}/read`, {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ userId: currentUserId.value })
    })
    await loadConversations()
  } catch (error) {
    console.error('Error selecting conversation:', error)
  }
}

const createConversation = async (userId) => {
  try {
    const response = await fetch('http://localhost:3001/conversations', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        participants: [currentUserId.value, String(userId)]
      })
    })
    if (!response.ok) throw new Error('Failed to create conversation')
    const conversation = await response.json()
    showNewConversationModal.value = false
    await loadConversations()
    await selectConversation(conversation)
  } catch (error) {
    console.error('Error creating conversation:', error)
  }
}

const getOtherParticipant = (conversation) => {
  if (!conversation.participants) return 'Utilisateur'
  const otherUserId = conversation.participants.find(p => String(p) !== currentUserId.value)
  const user = allUsers.value.find(u => String(u.id_utilisateur) === String(otherUserId))
  if (!user) return 'Utilisateur'
  
  const fullName = `${user.nom} ${user.prenom}`
  const currentUserName = `${currentUser.value?.nom} ${currentUser.value?.prenom}`
  
  if (fullName === currentUserName) {
    return `${fullName} (${user.email})`
  }
  
  return fullName
}

const getInitials = (name) => {
  return name.split(' ').map(n => n[0] || '').join('').toUpperCase().slice(0, 2)
}

const getOtherParticipantId = (conversation) => {
  if (!conversation.participants) return null
  return conversation.participants.find(p => String(p) !== currentUserId.value)
}

const isUserOnline = (userId) => {
  return onlineUsers.value.has(String(userId))
}

const connectWebSocket = () => {
  if (ws.value) {
    ws.value.close()
    ws.value = null
  }

  ws.value = new WebSocket('ws://localhost:3001')

  ws.value.onopen = () => {
    wsConnected.value = true
    ws.value.send(JSON.stringify({
      type: 'auth',
      userId: currentUserId.value,
      userName: `${currentUser.value.nom} ${currentUser.value.prenom}`
    }))
  }

  ws.value.onmessage = (event) => {
    const data = JSON.parse(event.data)

    if (data.type === 'message') {
      const msg = data.message
      const isMe = String(msg.userId) === currentUserId.value

      if (selectedConversation.value && msg.conversationId === selectedConversation.value._id) {
        if (isMe) {
          const tempIdx = messages.value.findIndex(m => m.id && m.id.startsWith('temp_'))
          if (tempIdx !== -1) {
            messages.value.splice(tempIdx, 1, msg)
          }
        } else {
          messages.value.push(msg)
          scrollToBottom()
        }
      }

      if (!isMe && !isPageFocused.value) {
        playNotificationSound()
        unreadCount.value++
        updatePageTitle()
      }

      loadConversations()
    }

    if (data.type === 'typing') {
      if (selectedConversation.value && data.conversationId === selectedConversation.value._id) {
        if (String(data.userId) !== currentUserId.value) {
          typingUser.value = data.sender
          setTimeout(() => { typingUser.value = null }, 3000)
        }
      }
    }

    if (data.type === 'stopTyping') {
      if (selectedConversation.value && data.conversationId === selectedConversation.value._id) {
        typingUser.value = null
      }
    }

    if (data.type === 'user:status') {
      if (data.status === 'online') {
        onlineUsers.value.add(String(data.userId))
      } else if (data.status === 'offline') {
        onlineUsers.value.delete(String(data.userId))
      }
    }

    if (data.type === 'reaction') {
      const messageIndex = messages.value.findIndex(m => m.id === data.messageId)
      if (messageIndex !== -1) {
        messages.value[messageIndex].reactions = data.reactions
      }
    }

    if (data.type === 'edited') {
      const messageIndex = messages.value.findIndex(m => m.id === data.messageId)
      if (messageIndex !== -1) {
        messages.value[messageIndex].content = data.newContent
        messages.value[messageIndex].edited = true
      }
    }

    if (data.type === 'deleted') {
      const messageIndex = messages.value.findIndex(m => m.id === data.messageId)
      if (messageIndex !== -1) {
        messages.value[messageIndex].deleted = true
        messages.value[messageIndex].content = ''
        messages.value[messageIndex].mediaUrl = null
        messages.value[messageIndex].reactions = []
      }
    }
  }

  ws.value.onclose = () => {
    wsConnected.value = false
    setTimeout(() => {
      if (currentUser.value) connectWebSocket()
    }, 3000)
  }

  ws.value.onerror = () => {
    wsConnected.value = false
  }
}

const sendMessage = async () => {
  if ((!messageInput.value.trim() && !selectedFile.value) || !ws.value || !wsConnected.value || !selectedConversation.value) return

  if (editingMessage.value) {
    handleEditSave()
    return
  }

  let mediaUrl = null
  let mediaName = null
  let mediaSize = null
  let messageType = 'text'
  if (selectedFile.value) {
    uploading.value = true
    try {
      const formData = new FormData()
      formData.append('file', selectedFile.value)
      const uploadResponse = await fetch('http://localhost:3001/upload', {
        method: 'POST',
        body: formData
      })
      if (!uploadResponse.ok) throw new Error('Upload failed')
      const uploadData = await uploadResponse.json()
      mediaUrl = uploadData.file.url
      mediaName = uploadData.file.originalName
      mediaSize = uploadData.file.size
      messageType = selectedFile.value.type.startsWith('image/') ? 'image' : 'file'
    } catch (error) {
      console.error('Upload error:', error)
      uploading.value = false
      return
    }
    uploading.value = false
  }

  const content = messageInput.value.trim()
  messageInput.value = ''
  clearSelectedFile()
  showEmojiPicker.value = false

  const tempMessage = {
    id: 'temp_' + Date.now(),
    conversationId: selectedConversation.value._id,
    userId: currentUserId.value,
    sender: `${currentUser.value.nom} ${currentUser.value.prenom}`,
    content: content,
    type: messageType,
    mediaUrl: mediaUrl,
    mediaName: mediaName,
    mediaSize: mediaSize,
    time: new Date().toISOString(),
    isRead: false,
    replyTo: replyTo.value ? {
      id: replyTo.value.id,
      content: replyTo.value.content,
      sender: replyTo.value.sender
    } : null
  }

  messages.value.push(tempMessage)
  scrollToBottom()

  ws.value.send(JSON.stringify({
    type: 'send',
    content: content,
    conversationId: selectedConversation.value._id,
    messageType: messageType,
    mediaUrl: mediaUrl,
    mediaName: mediaName,
    mediaSize: mediaSize,
    replyTo: replyTo.value ? {
      id: replyTo.value.id,
      content: replyTo.value.content,
      sender: replyTo.value.sender
    } : null
  }))

  replyTo.value = null
}

const setReplyTo = (message) => {
  replyTo.value = message
  document.querySelector('input[type="text"]')?.focus()
}

const cancelReply = () => {
  replyTo.value = null
}

const startEdit = (message) => {
  editingMessage.value = message
  messageInput.value = message.content
  document.querySelector('input[type="text"]')?.focus()
}

const cancelEdit = () => {
  editingMessage.value = null
  messageInput.value = ''
}

const handleEditSave = () => {
  if (!messageInput.value.trim() || !editingMessage.value) return

  ws.value.send(JSON.stringify({
    type: 'edit',
    messageId: editingMessage.value.id,
    newContent: messageInput.value.trim(),
    conversationId: selectedConversation.value._id
  }))

  messageInput.value = ''
  editingMessage.value = null
}

const togglePinConversation = async () => {
  if (!selectedConversation.value) return

  const convId = selectedConversation.value._id
  const isPinned = pinnedConversations.value.has(convId)

  try {
    const response = await fetch(`http://localhost:3001/conversations/${convId}/pin`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        userId: currentUserId.value,
        pinned: !isPinned
      })
    })

    if (response.ok) {
      if (isPinned) {
        pinnedConversations.value.delete(convId)
      } else {
        pinnedConversations.value.add(convId)
      }
    }
  } catch (error) {
    console.error('Error toggling pin:', error)
  }
}

const loadPinnedConversations = async () => {
  try {
    const response = await fetch(`http://localhost:3001/conversations/pinned/${currentUserId.value}`)
    if (response.ok) {
      const pinned = await response.json()
      pinnedConversations.value = new Set(pinned)
    }
  } catch (error) {
    console.error('Error loading pinned conversations:', error)
  }
}

const handleFileSelect = (event) => {
  const file = event.target.files[0]
  if (!file) return
  if (file.size > 10 * 1024 * 1024) {
    alert('Le fichier ne doit pas dépasser 10 Mo')
    return
  }
  selectedFile.value = file
  if (file.type.startsWith('image/')) {
    filePreviewUrl.value = URL.createObjectURL(file)
  } else {
    filePreviewUrl.value = null
  }
}

const clearSelectedFile = () => {
  if (filePreviewUrl.value) URL.revokeObjectURL(filePreviewUrl.value)
  selectedFile.value = null
  filePreviewUrl.value = null
}

const insertEmoji = (emoji) => {
  messageInput.value += emoji
}

const openImage = (url) => {
  previewImage.value = url
}

const formatFileSize = (bytes) => {
  if (!bytes) return ''
  if (bytes < 1024) return bytes + ' o'
  if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' Ko'
  return (bytes / (1024 * 1024)).toFixed(1) + ' Mo'
}

const toggleReactionPicker = (messageId) => {
  showReactionPicker.value = showReactionPicker.value === messageId ? null : messageId
}

const reactToMessage = (messageId, emoji) => {
  if (!ws.value || !wsConnected.value) return
  ws.value.send(JSON.stringify({
    type: 'react',
    messageId,
    emoji
  }))
  showReactionPicker.value = null
}

const groupReactions = (reactions) => {
  const grouped = {}
  reactions.forEach(r => {
    if (!grouped[r.emoji]) grouped[r.emoji] = []
    grouped[r.emoji].push(r)
  })
  return grouped
}

const hasReacted = (reactions, emoji) => {
  return reactions.some(r => r.emoji === emoji && r.userId === currentUserId.value)
}

const deleteMessage = (messageId) => {
  if (!confirm('Supprimer ce message ?')) return
  if (!ws.value || !wsConnected.value) return
  ws.value.send(JSON.stringify({
    type: 'delete',
    messageId
  }))
}

let typingTimeout = null
const handleTyping = () => {
  if (!ws.value || !wsConnected.value || !selectedConversation.value) return
  if (typingTimeout) clearTimeout(typingTimeout)

  ws.value.send(JSON.stringify({
    type: 'typing',
    conversationId: selectedConversation.value._id
  }))

  typingTimeout = setTimeout(() => {
    if (ws.value && wsConnected.value) {
      ws.value.send(JSON.stringify({
        type: 'stopTyping',
        conversationId: selectedConversation.value._id
      }))
    }
  }, 2000)
}

const isMyMessage = (userId) => {
  return String(userId) === currentUserId.value
}

const formatMessageTime = (timestamp) => {
  if (!timestamp) return ''
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

const handleFocus = () => {
  isPageFocused.value = true
  unreadCount.value = 0
  updatePageTitle()
}

const handleBlur = () => {
  isPageFocused.value = false
}

onMounted(() => {
  loadCurrentUser()
  window.addEventListener('focus', handleFocus)
  window.addEventListener('blur', handleBlur)
  
  setTimeout(() => {
    if (currentUser.value) loadPinnedConversations()
  }, 500)
})

onUnmounted(() => {
  if (ws.value) ws.value.close()
  if (typingTimeout) clearTimeout(typingTimeout)
  window.removeEventListener('focus', handleFocus)
  window.removeEventListener('blur', handleBlur)
  document.title = originalTitle
})
</script>
