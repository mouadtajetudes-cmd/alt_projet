<template>
  <div class="fixed top-0 left-0 right-0 z-50">


    <nav 
      class="bg-white border-b transition-all duration-200"
      :class="scrolled ? 'shadow-md backdrop-blur-sm' : 'border-gray-200'"
    >
      <div class="max-w-7xl mx-auto px-4">
        <div class="flex items-center justify-between h-18">
          <router-link to="/" class="flex items-center group">
            <span class="text-2xl font-bold text-primary transition-colors duration-200 group-hover:text-primary-dark">Alt</span>
          </router-link>

          
          <div class="hidden lg:flex items-center gap-1">
            <router-link 
              v-for="item in menuItems" 
              :key="item.path"
              :to="item.path"
              class="flex items-center gap-2 px-4 py-2 rounded-lg text-dark-muted hover:text-primary hover:bg-blue-50 font-medium transition-all duration-150"
            >
              <font-awesome-icon :icon="item.icon" class="text-sm" />
              <span>{{ item.label }}</span>
            </router-link>
          </div>

          <div class="hidden lg:flex items-center gap-3">
            <router-link 
              v-if="!isAuthenticated"
              to="/login" 
              class="flex items-center gap-2 px-4 py-2 text-dark-muted hover:text-dark font-medium transition-colors duration-150"
            >
              <font-awesome-icon icon="sign-in-alt" />
              <span>Connexion</span>
            </router-link>
            <router-link 
              v-if="!isAuthenticated"
              to="/register"
              class="flex items-center gap-2 px-5 py-2.5 bg-primary hover:bg-primary-dark text-white font-medium rounded-full transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2"
            >
              <font-awesome-icon icon="user-plus" />
              <span>Inscription</span>
            </router-link>
            
            <NotificationBell v-if="isAuthenticated" />
            
            <router-link 
              v-if="isAuthenticated"
              to="/profile"
              class="flex items-center gap-2 px-4 py-2 text-dark-muted hover:text-primary hover:bg-blue-50 rounded-lg font-medium transition-all duration-150"
            >
              <font-awesome-icon icon="user-circle" class="text-lg" />
              <span>Profil</span>
            </router-link>
            <button 
              v-if="isAuthenticated"
              @click="handleLogout"
              class="flex items-center gap-2 px-4 py-2 text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg font-medium transition-all duration-150"
            >
              <font-awesome-icon icon="sign-out-alt" />
              <span>Déconnexion</span>
            </button>
          </div>

          <button 
            @click="mobileMenuOpen = !mobileMenuOpen"
            class="lg:hidden p-2 text-dark-muted hover:text-dark transition-colors"
            aria-label="Menu"
          >
            <font-awesome-icon :icon="mobileMenuOpen ? 'times' : 'bars'" class="text-xl" />
          </button>
        </div>
      </div>
    </nav>

    <transition name="mobile-menu">
      <div 
        v-if="mobileMenuOpen"
        class="lg:hidden bg-white border-b border-gray-200 shadow-lg animate-slide-down"
      >
        <div class="max-w-7xl mx-auto px-4 py-4 space-y-2">
          <router-link 
            v-for="item in menuItems" 
            :key="item.path"
            :to="item.path"
            @click="mobileMenuOpen = false"
            class="flex items-center gap-3 px-4 py-3 rounded-lg text-dark hover:bg-blue-50 hover:text-primary font-medium transition-all"
          >
            <font-awesome-icon :icon="item.icon" class="w-5" />
            <span>{{ item.label }}</span>
          </router-link>
          
          <div class="pt-4 border-t border-gray-200 space-y-2">
            <router-link 
              v-if="!isAuthenticated"
              to="/login" 
              @click="mobileMenuOpen = false"
              class="flex items-center gap-3 px-4 py-3 text-dark-muted hover:text-primary hover:bg-blue-50 rounded-lg font-medium"
            >
              <font-awesome-icon icon="sign-in-alt" class="w-5" />
              <span>Connexion</span>
            </router-link>
            <router-link 
              v-if="!isAuthenticated"
              to="/register"
              @click="mobileMenuOpen = false"
              class="flex items-center gap-3 px-4 py-3 bg-primary text-white rounded-lg font-medium"
            >
              <font-awesome-icon icon="user-plus" class="w-5" />
              <span>Inscription</span>
            </router-link>
            
            <router-link 
              v-if="isAuthenticated"
              to="/profile"
              @click="mobileMenuOpen = false"
              class="flex items-center gap-3 px-4 py-3 text-dark-muted hover:text-primary hover:bg-blue-50 rounded-lg font-medium"
            >
              <font-awesome-icon icon="user-circle" class="w-5" />
              <span>Profil</span>
            </router-link>
            <button 
              v-if="isAuthenticated"
              @click="handleLogout"
              class="w-full flex items-center gap-3 px-4 py-3 text-red-600 hover:bg-red-50 rounded-lg font-medium"
            >
              <font-awesome-icon icon="sign-out-alt" class="w-5" />
              <span>Déconnexion</span>
            </button>
          </div>
        </div>
      </div>
    </transition>
  </div>

  
  <div class="h-[116px]"></div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import NotificationBell from './NotificationBell.vue'
import '../views/views.css'

const router = useRouter()
const scrolled = ref(false)
const mobileMenuOpen = ref(false)

const isAuthenticated = computed(() => {
  return !!localStorage.getItem('token')
})

const menuItems = computed(() => {
  const items = [
    { path: '/', label: 'Accueil', icon: 'home' }
  ]
  
  if (isAuthenticated.value) {
    items.push(
      { path: '/chat', label: 'Chat', icon: 'comments' },
      { path: '/friends', label: 'Amis', icon: 'user-friends' },
      { path: '/users', label: 'Utilisateurs', icon: 'users' },
      { path: '/groups', label: 'Groupes', icon: 'layer-group' },
      { path: '/marketplace', label: 'Marketplace', icon: 'gem' },
      { path: '/social', label: 'Social', icon: 'heart' },
      { path: '/avatar', label: 'Avatar', icon: 'user-circle' }
    )
  }
  
  return items
})

const handleScroll = () => {
  scrolled.value = window.scrollY > 10
}

const handleLogout = () => {
  localStorage.removeItem('token')
  localStorage.removeItem('user')
  mobileMenuOpen.value = false
  router.push('/login')
}

onMounted(() => {
  window.addEventListener('scroll', handleScroll)
})

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
})
</script>
