<template>
<<<<<<< HEAD
  <div class="login-page">
    <div class="container">
      <div class="login-card">
        <h2>Connexion</h2>
        
        <form @submit.prevent="handleLogin">
          <div class="form-group">
            <label>Email</label>
            <input v-model="email" type="email" required placeholder="votre@email.com">
          </div>
          
          <div class="form-group">
            <label>Mot de passe</label>
            <input v-model="password" type="password" required placeholder="••••••••">
          </div>
          
          <button type="submit" class="btn-primary">Se connecter</button>
          
          <p v-if="error" class="error">{{ error }}</p>
=======
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
>>>>>>> origin/groupe/develop
        </form>
      </div>
    </div>
  </div>
</template>

<<<<<<< HEAD
<script>
import { ref } from 'vue'
import { useRouter } from 'vue-router'

export default {
  name: 'Login',
  setup() {
    const router = useRouter()
    const email = ref('')
    const password = ref('')
    const error = ref('')
    
    const handleLogin = async () => {
      error.value = ''
      
      try {
        const response = await fetch('http://localhost:6090/auth/login', {
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
          localStorage.setItem('token', data.access_token)
          localStorage.setItem('user', JSON.stringify(data.user))
          router.push('/')
        } else {
          error.value = data.message || 'Erreur de connexion'
        }
      } catch (err) {
        error.value = 'Erreur de connexion au serveur'
      }
    }
    
    return {
      email,
      password,
      error,
      handleLogin
    }
  }
}
</script>

<style scoped>
.login-page {
  padding: 3rem 0;
}

.container {
  max-width: 500px;
  margin: 0 auto;
  padding: 0 1rem;
}

.login-card {
  background: white;
  padding: 2rem;
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.login-card h2 {
  margin-bottom: 1.5rem;
  text-align: center;
  color: #333;
}

.form-group {
  margin-bottom: 1rem;
}

.form-group label {
  display: block;
  margin-bottom: 0.5rem;
  color: #333;
  font-weight: 500;
}

.form-group input {
  width: 100%;
  padding: 0.75rem;
  border: 2px solid #e0e0e0;
  border-radius: 4px;
  font-size: 1rem;
}

.form-group input:focus {
  outline: none;
  border-color: #0d6efd;
}

.btn-primary {
  width: 100%;
  padding: 0.75rem;
  background: #0d6efd;
  color: white;
  border: none;
  border-radius: 4px;
  font-size: 1rem;
  cursor: pointer;
  transition: background 0.2s;
}

.btn-primary:hover {
  background: #0b5ed7;
}

.error {
  margin-top: 1rem;
  padding: 0.75rem;
  background: #f8d7da;
  color: #721c24;
  border-radius: 4px;
  text-align: center;
}
</style>
=======
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
      // Utiliser le composable useAuth pour la connexion réactive
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
</script>
>>>>>>> origin/groupe/develop
