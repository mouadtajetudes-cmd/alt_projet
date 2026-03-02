<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-50 via-blue-50 to-purple-50 flex items-center justify-center p-4">
    <div class="w-full max-w-5xl grid md:grid-cols-2 bg-white rounded-3xl shadow-2xl overflow-hidden animate-fade-in">
      
      <div class="hidden md:flex flex-col justify-center p-12 bg-gradient-to-br from-primary to-purple-600 text-white">
        <div class="space-y-6">
          <div class="inline-flex items-center justify-center w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl">
            <font-awesome-icon icon="sign-in-alt" class="text-3xl" />
          </div>
          
          <h2 class="text-4xl font-bold">Bon retour !</h2>
          <p class="text-white/90 text-lg">
            Connectez-vous pour accéder à votre espace et reprendre vos conversations
          </p>
          
          <div class="space-y-4 pt-8">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-lg bg-white/20 flex items-center justify-center">
                <font-awesome-icon icon="comments" />
              </div>
              <div>
                <div class="font-semibold">Chat en temps réel</div>
                <div class="text-white/70 text-sm">Messagerie instantanée</div>
              </div>
            </div>
            
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-lg bg-white/20 flex items-center justify-center">
                <font-awesome-icon icon="users" />
              </div>
              <div>
                <div class="font-semibold">Communauté active</div>
                <div class="text-white/70 text-sm">Échangez avec les membres</div>
              </div>
            </div>
            
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-lg bg-white/20 flex items-center justify-center">
                <font-awesome-icon icon="lock" />
              </div>
              <div>
                <div class="font-semibold">Sécurité maximale</div>
                <div class="text-white/70 text-sm">Protection de vos données</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      
      <div class="p-8 md:p-12 flex flex-col justify-center">
        <div class="mb-8">
          <h1 class="text-3xl font-bold text-dark mb-2">Connexion</h1>
          <p class="text-dark-muted">Accédez à votre compte Alt Platform</p>
        </div>

        <form @submit.prevent="handleLogin" class="space-y-6">
          
          <div>
            <label class="block text-sm font-medium text-dark mb-2">
              Adresse e-mail
            </label>
            <div class="relative">
              <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                <font-awesome-icon icon="envelope" />
              </div>
              <input
                v-model="email"
                type="email"
                required
                placeholder="exemple@email.com"
                class="w-full pl-10 pr-4 py-3 border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all"
              />
            </div>
          </div>

          
          <div>
            <label class="block text-sm font-medium text-dark mb-2">
              Mot de passe
            </label>
            <div class="relative">
              <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                <font-awesome-icon icon="lock" />
              </div>
              <input
                v-model="password"
                type="password"
                required
                placeholder="••••••••"
                class="w-full pl-10 pr-4 py-3 border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all"
              />
            </div>
          </div>

          
          <div 
            v-if="error" 
            class="flex items-center gap-2 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl animate-slide-down"
          >
            <font-awesome-icon icon="info-circle" />
            <span>{{ error }}</span>
          </div>

          
          <button
            type="submit"
            :disabled="loading"
            class="w-full py-3 bg-gradient-to-r from-primary to-primary-light text-white font-semibold rounded-xl shadow-lg hover:-translate-y-0.5 hover:shadow-xl transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:translate-y-0"
          >
            <span v-if="!loading" class="flex items-center justify-center gap-2">
              <font-awesome-icon icon="sign-in-alt" />
              Se connecter
            </span>
            <span v-else class="flex items-center justify-center gap-2">
              <div class="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
              Connexion...
            </span>
          </button>

          <!-- Divider -->
          <div class="relative flex items-center justify-center my-6">
            <div class="absolute w-full border-t border-gray-300"></div>
            <span class="relative bg-white px-4 text-sm text-gray-500">Ou continuer avec</span>
          </div>

          <!-- OAuth Buttons -->
          <div class="space-y-3">
            <button
              type="button"
              @click="loginWithGoogle"
              class="w-full py-3 px-4 border-2 border-gray-300 rounded-xl font-medium text-gray-700 hover:bg-gray-50 transition-all duration-200 flex items-center justify-center gap-3"
            >
              <svg class="w-5 h-5" viewBox="0 0 24 24">
                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
              </svg>
              Continuer avec Google
            </button>

            <button
              type="button"
              @click="loginWithApple"
              class="w-full py-3 px-4 bg-black border-2 border-black rounded-xl font-medium text-white hover:bg-gray-900 transition-all duration-200 flex items-center justify-center gap-3"
            >
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M17.05 20.28c-.98.95-2.05.8-3.08.35-1.09-.46-2.09-.48-3.24 0-1.44.62-2.2.44-3.06-.35C2.79 15.25 3.51 7.59 9.05 7.31c1.35.07 2.29.74 3.08.8 1.18-.24 2.31-.93 3.57-.84 1.51.12 2.65.72 3.4 1.8-3.12 1.87-2.38 5.98.48 7.13-.57 1.5-1.31 2.99-2.54 4.09l.01-.01zM12.03 7.25c-.15-2.23 1.66-4.07 3.74-4.25.29 2.58-2.34 4.5-3.74 4.25z"/>
              </svg>
              Continuer avec Apple
            </button>
          </div>

          
          <div class="text-center pt-4">
            <p class="text-dark-muted">
              Pas encore de compte ?
              <router-link 
                to="/register" 
                class="text-primary font-semibold hover:underline ml-1"
              >
                Créer un compte
              </router-link>
            </p>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuth } from '../composables/useAuth'

const router = useRouter()
const { login } = useAuth()

const email = ref('')
const password = ref('')
const error = ref('')
const loading = ref(false)

const handleLogin = async () => {
  error.value = ''
  loading.value = true
  
  try {
    const response = await fetch('http://localhost:6090/auth/auth/login', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        email: email.value,
        password: password.value
      })
    })
    
    const data = await response.json()
    
    if (response.ok) {
      login(data.user, data.token)
      router.push('/chat')
    } else {
      error.value = data.message || data.error || 'Email ou mot de passe incorrect'
    }
  } catch (err) {
    error.value = 'Impossible de se connecter au serveur'
  } finally {
    loading.value = false
  }
}

const loginWithGoogle = async () => {
  try {
    const response = await fetch('http://localhost:6090/auth/auth/google')
    const data = await response.json()
    
    if (data.authUrl) {
      const width = 500
      const height = 600
      const left = window.screen.width / 2 - width / 2
      const top = window.screen.height / 2 - height / 2
      
      const popup = window.open(
        data.authUrl,
        'google-auth',
        `width=${width},height=${height},left=${left},top=${top}`
      )
      
      const handleMessage = async (event) => {
        if (event.data.type === 'google-auth-callback') {
          const authResponse = await fetch(
            `http://localhost:6090/auth/auth/google?code=${event.data.code}&state=${event.data.state}`
          )
          const result = await authResponse.json()
          
          if (authResponse.ok) {
            login(result.user, result.access_token)
            router.push('/chat')
            popup.close()
          } else {
            error.value = 'Échec de l\'authentification Google'
            popup.close()
          }
          
          window.removeEventListener('message', handleMessage)
        }
      }
      
      window.addEventListener('message', handleMessage)
    }
  } catch (err) {
    error.value = 'Erreur lors de la connexion Google'
  }
}

const loginWithApple = async () => {
  try {
    if (typeof AppleID === 'undefined') {
      error.value = 'Apple Sign In n\'est pas disponible'
      return
    }
    
    AppleID.auth.init({
      clientId: 'com.altplatform.service',
      scope: 'email name',
      redirectURI: 'http://localhost:5173/auth/apple/callback',
      usePopup: true
    })

    const response = await AppleID.auth.signIn()
    const { id_token, user } = response.authorization
    
    const authResponse = await fetch('http://localhost:6090/auth/auth/apple', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        id_token,
        nom: user?.name?.firstName || 'User',
        prenom: user?.name?.lastName || ''
      })
    })
    
    const result = await authResponse.json()
    
    if (authResponse.ok) {
      login(result.user, result.access_token)
      router.push('/chat')
    } else {
      error.value = 'Échec de l\'authentification Apple'
    }
  } catch (err) {
    console.error('Apple Sign In error:', err)
    error.value = 'Erreur lors de la connexion Apple'
  }
}
</script>
