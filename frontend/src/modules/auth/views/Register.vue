<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-50 via-purple-50 to-blue-50 flex items-center justify-center p-4">
    <div class="w-full max-w-5xl grid md:grid-cols-2 bg-white rounded-3xl shadow-2xl overflow-hidden animate-fade-in">
      
      <div class="hidden md:flex flex-col justify-center p-12 bg-gradient-to-br from-purple-600 to-primary text-white">
        <div class="space-y-6">
          <div class="inline-flex items-center justify-center w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl">
            <font-awesome-icon icon="user-plus" class="text-3xl" />
          </div>
          
          <h2 class="text-4xl font-bold">Rejoignez-nous !</h2>
          <p class="text-white/90 text-lg">
            Créez votre compte gratuitement et accédez à toutes les fonctionnalités de la plateforme
          </p>
          
          <div class="space-y-4 pt-8">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-lg bg-white/20 flex items-center justify-center">
                <font-awesome-icon icon="check" />
              </div>
              <div>
                <div class="font-semibold">Inscription rapide</div>
                <div class="text-white/70 text-sm">En moins d'une minute</div>
              </div>
            </div>
            
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-lg bg-white/20 flex items-center justify-center">
                <font-awesome-icon icon="comments" />
              </div>
              <div>
                <div class="font-semibold">Accès immédiat au chat</div>
                <div class="text-white/70 text-sm">Commencez à échanger</div>
              </div>
            </div>
            
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-lg bg-white/20 flex items-center justify-center">
                <font-awesome-icon icon="lock" />
              </div>
              <div>
                <div class="font-semibold">100% Gratuit</div>
                <div class="text-white/70 text-sm">Pas de carte bancaire requise</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      
      <div class="p-8 md:p-12 flex flex-col justify-center max-h-screen overflow-y-auto">
        <div class="mb-8">
          <h1 class="text-3xl font-bold text-dark mb-2">Inscription</h1>
          <p class="text-dark-muted">Créez votre compte Alt Platform</p>
        </div>

        <form @submit.prevent="handleRegister" class="space-y-5">
          
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-dark mb-2">
                Prénom
              </label>
              <div class="relative">
                <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                  <font-awesome-icon icon="user" />
                </div>
                <input
                  v-model="prenom"
                  type="text"
                  required
                  placeholder="Prénom"
                  class="w-full pl-10 pr-4 py-3 border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all"
                />
              </div>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-dark mb-2">
                Nom
              </label>
              <div class="relative">
                <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                  <font-awesome-icon icon="user" />
                </div>
                <input
                  v-model="nom"
                  type="text"
                  required
                  placeholder="Nom"
                  class="w-full pl-10 pr-4 py-3 border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all"
                />
              </div>
            </div>
          </div>

          
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
                placeholder="8 caractères minimum"
                class="w-full pl-10 pr-4 py-3 border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all"
              />
            </div>
          </div>

          
          <div>
            <label class="block text-sm font-medium text-dark mb-2">
              Confirmer le mot de passe
            </label>
            <div class="relative">
              <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                <font-awesome-icon icon="lock" />
              </div>
              <input
                v-model="confirmPassword"
                type="password"
                required
                placeholder="Confirmer le mot de passe"
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

          
          <div 
            v-if="success" 
            class="flex items-center gap-2 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl animate-slide-down"
          >
            <font-awesome-icon icon="check" />
            <span>{{ success }}</span>
          </div>

          
          <button
            type="submit"
            :disabled="loading"
            class="w-full py-3 bg-gradient-to-r from-purple-600 to-primary text-white font-semibold rounded-xl shadow-lg hover:-translate-y-0.5 hover:shadow-xl transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:translate-y-0"
          >
            <span v-if="!loading" class="flex items-center justify-center gap-2">
              <font-awesome-icon icon="user-plus" />
              Créer mon compte
            </span>
            <span v-else class="flex items-center justify-center gap-2">
              <div class="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
              Inscription...
            </span>
          </button>

          
          <div class="text-center pt-4">
            <p class="text-dark-muted">
              Déjà un compte ?
              <router-link 
                to="/login" 
                class="text-primary font-semibold hover:underline ml-1"
              >
                Se connecter
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

const router = useRouter()

const nom = ref('')
const prenom = ref('')
const email = ref('')
const password = ref('')
const confirmPassword = ref('')
const error = ref('')
const success = ref('')
const loading = ref(false)

const handleRegister = async () => {
  error.value = ''
  success.value = ''
  
  if (password.value.length < 8) {
    error.value = 'Le mot de passe doit contenir au moins 8 caractères'
    return
  }
  
  if (password.value !== confirmPassword.value) {
    error.value = 'Les mots de passe ne correspondent pas'
    return
  }
  
  loading.value = true
  
  try {
    const response = await fetch('http://localhost:6090/auth/auth/register', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        nom: nom.value,
        prenom: prenom.value,
        email: email.value,
        password: password.value
      })
    })
    
    const data = await response.json()
    
    if (response.ok) {
      success.value = 'Inscription réussie ! Redirection...'
      setTimeout(() => router.push('/login'), 1500)
    } else {
      error.value = data.message || 'Erreur lors de l\'inscription'
    }
  } catch (err) {
    error.value = 'Impossible de créer le compte'
  } finally {
    loading.value = false
  }
}
</script>
