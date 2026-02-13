<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50 py-8 px-4">
    <div class="max-w-5xl mx-auto">
      
      <div class="bg-white rounded-3xl shadow-lg overflow-hidden mb-6 animate-fade-in">
        <div class="h-32 bg-gradient-to-r from-primary to-purple-600"></div>
        <div class="px-8 pb-8">
          <div class="flex flex-col md:flex-row items-center md:items-end gap-6 -mt-16">
            
            <div class="relative group">
              <div 
                v-if="user?.avatar_url"
                class="w-32 h-32 rounded-full border-4 border-white shadow-xl overflow-hidden"
              >
                <img 
                  :src="`http://localhost:6090/auth${user.avatar_url}`" 
                  :alt="`${user.prenom} ${user.nom}`"
                  class="w-full h-full object-cover"
                />
              </div>
              <div 
                v-else
                class="w-32 h-32 rounded-full bg-gradient-to-br from-primary to-primary-light border-4 border-white shadow-xl flex items-center justify-center text-white text-4xl font-bold"
              >
                {{ user?.nom?.[0]?.toUpperCase() }}{{ user?.prenom?.[0]?.toUpperCase() }}
              </div>
              
              <!-- Upload button on hover -->
              <label 
                for="avatar-upload" 
                class="absolute inset-0 flex items-center justify-center bg-black/50 rounded-full opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer"
              >
                <font-awesome-icon icon="camera" class="text-white text-2xl" />
              </label>
              <input 
                id="avatar-upload" 
                type="file" 
                accept="image/*" 
                @change="handleAvatarUpload" 
                class="hidden"
              />
              
              <div 
                v-if="isAdmin"
                class="absolute -bottom-2 -right-2 w-12 h-12 bg-orange-500 rounded-full border-4 border-white flex items-center justify-center shadow-lg"
                title="Administrateur"
              >
                <font-awesome-icon icon="crown" class="text-white text-lg" />
              </div>
            </div>

            
            <div class="flex-1 text-center md:text-left">
              <h1 class="text-3xl font-bold text-dark mb-2">
                {{ user?.prenom }} {{ user?.nom }}
              </h1>
              <div class="flex flex-wrap items-center justify-center md:justify-start gap-2 mb-3">
                <span 
                  class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-medium"
                  :class="isAdmin ? 'bg-orange-100 text-orange-700' : 'bg-blue-100 text-blue-700'"
                >
                  <font-awesome-icon :icon="isAdmin ? 'crown' : 'user'" />
                  {{ isAdmin ? 'Administrateur' : 'Utilisateur' }}
                </span>
                <span 
                  v-if="isPremium"
                  class="inline-flex items-center gap-1.5 px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-sm font-medium"
                >
                  <font-awesome-icon icon="gem" />
                  Premium
                </span>
              </div>
              <p class="text-dark-muted">
                <font-awesome-icon icon="envelope" class="mr-2" />
                {{ user?.email }}
              </p>
            </div>

            
            <div class="flex gap-2">
              <button 
                v-if="!editing"
                @click="editing = true"
                class="flex items-center gap-2 px-6 py-3 bg-primary text-white rounded-xl hover:bg-primary-dark transition-colors shadow-md"
              >
                <font-awesome-icon icon="edit" />
                <span>Modifier</span>
              </button>
              <button 
                @click="logout"
                class="flex items-center gap-2 px-6 py-3 bg-red-600 text-white rounded-xl hover:bg-red-700 transition-colors shadow-md"
              >
                <font-awesome-icon icon="sign-out-alt" />
                <span>Déconnexion</span>
              </button>
            </div>
          </div>
        </div>
      </div>

      
      <div class="grid md:grid-cols-3 gap-6">
        
        <div class="space-y-6">
          
          <div class="bg-white rounded-2xl shadow-lg p-6 animate-slide-up">
            <h3 class="text-lg font-semibold text-dark mb-4 flex items-center gap-2">
              <font-awesome-icon icon="info-circle" class="text-primary" />
              Statistiques
            </h3>
            <div class="space-y-4">
              <div class="flex justify-between items-center">
                <span class="text-dark-muted">Membre depuis</span>
                <span class="font-semibold text-dark">{{ formatDate(user?.created_at) }}</span>
              </div>
              <div class="flex justify-between items-center">
                <span class="text-dark-muted">Messages envoyés</span>
                <span class="font-semibold text-dark">0</span>
              </div>
              <div class="flex justify-between items-center">
                <span class="text-dark-muted">Connexions</span>
                <span class="font-semibold text-dark">0</span>
              </div>
            </div>
          </div>

          
          <div class="bg-white rounded-2xl shadow-lg p-6 animate-slide-up" style="animation-delay: 0.1s">
            <h3 class="text-lg font-semibold text-dark mb-4">Actions rapides</h3>
            <div class="space-y-2">
              <router-link 
                to="/chat"
                class="flex items-center gap-3 p-3 hover:bg-blue-50 rounded-xl transition-colors text-dark"
              >
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                  <font-awesome-icon icon="comments" class="text-blue-600" />
                </div>
                <div>
                  <div class="font-medium">Chat</div>
                  <div class="text-xs text-dark-muted">Messagerie</div>
                </div>
              </router-link>
              
              <router-link 
                to="/users"
                class="flex items-center gap-3 p-3 hover:bg-purple-50 rounded-xl transition-colors text-dark"
              >
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                  <font-awesome-icon icon="users" class="text-purple-600" />
                </div>
                <div>
                  <div class="font-medium">Utilisateurs</div>
                  <div class="text-xs text-dark-muted">Liste des membres</div>
                </div>
              </router-link>
            </div>
          </div>
        </div>

        
        <div class="md:col-span-2">
          <div class="bg-white rounded-2xl shadow-lg p-8 animate-slide-up" style="animation-delay: 0.2s">
            <div v-if="loading" class="flex justify-center items-center py-12">
              <div class="flex gap-2">
                <div class="w-3 h-3 bg-primary rounded-full animate-bounce" style="animation-delay: 0ms"></div>
                <div class="w-3 h-3 bg-primary rounded-full animate-bounce" style="animation-delay: 150ms"></div>
                <div class="w-3 h-3 bg-primary rounded-full animate-bounce" style="animation-delay: 300ms"></div>
              </div>
            </div>

            <div v-else-if="user">
              
              <div v-if="!editing">
                <h2 class="text-2xl font-bold text-dark mb-6">Informations personnelles</h2>
                
                <div class="grid md:grid-cols-2 gap-6">
                  <div class="space-y-2">
                    <label class="text-sm font-medium text-dark-muted">Prénom</label>
                    <div class="p-4 bg-gray-50 rounded-xl text-dark font-medium">
                      {{ user.prenom }}
                    </div>
                  </div>
                  
                  <div class="space-y-2">
                    <label class="text-sm font-medium text-dark-muted">Nom</label>
                    <div class="p-4 bg-gray-50 rounded-xl text-dark font-medium">
                      {{ user.nom }}
                    </div>
                  </div>
                  
                  <div class="space-y-2 md:col-span-2">
                    <label class="text-sm font-medium text-dark-muted">Email</label>
                    <div class="p-4 bg-gray-50 rounded-xl text-dark font-medium flex items-center gap-2">
                      <font-awesome-icon icon="envelope" class="text-gray-400" />
                      {{ user.email }}
                    </div>
                  </div>
                  
                  <div class="space-y-2 md:col-span-2">
                    <label class="text-sm font-medium text-dark-muted">Téléphone</label>
                    <div class="p-4 bg-gray-50 rounded-xl text-dark font-medium flex items-center gap-2">
                      <font-awesome-icon icon="phone" class="text-gray-400" />
                      {{ user.telephone || 'Non renseigné' }}
                    </div>
                  </div>
                </div>
              </div>

              
              <div v-else>
                <h2 class="text-2xl font-bold text-dark mb-6">Modifier le profil</h2>
                
                <form @submit.prevent="updateProfile" class="space-y-6">
                  <div class="grid md:grid-cols-2 gap-6">
                    <div>
                      <label class="block text-sm font-medium text-dark mb-2">Prénom</label>
                      <input
                        v-model="form.prenom"
                        type="text"
                        required
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all"
                      />
                    </div>
                    
                    <div>
                      <label class="block text-sm font-medium text-dark mb-2">Nom</label>
                      <input
                        v-model="form.nom"
                        type="text"
                        required
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all"
                      />
                    </div>
                    
                    <div class="md:col-span-2">
                      <label class="block text-sm font-medium text-dark mb-2">Email</label>
                      <input
                        v-model="form.email"
                        type="email"
                        required
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all"
                      />
                    </div>
                  </div>

                  
                  <div 
                    v-if="error" 
                    class="flex items-center gap-2 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl"
                  >
                    <font-awesome-icon icon="info-circle" />
                    <span>{{ error }}</span>
                  </div>

                  <div 
                    v-if="success" 
                    class="flex items-center gap-2 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl"
                  >
                    <font-awesome-icon icon="check" />
                    <span>{{ success }}</span>
                  </div>

                  
                  <div class="flex gap-3">
                    <button
                      type="submit"
                      :disabled="saving"
                      class="flex-1 flex items-center justify-center gap-2 py-3 bg-gradient-to-r from-primary to-primary-light text-white font-semibold rounded-xl shadow-lg hover:-translate-y-0.5 hover:shadow-xl transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                      <font-awesome-icon icon="check" />
                      <span>{{ saving ? 'Enregistrement...' : 'Enregistrer' }}</span>
                    </button>
                    
                    <button
                      type="button"
                      @click="cancelEdit"
                      class="px-6 py-3 bg-gray-200 text-dark font-semibold rounded-xl hover:bg-gray-300 transition-colors"
                    >
                      Annuler
                    </button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

const user = ref(null)
const loading = ref(true)
const editing = ref(false)
const saving = ref(false)
const error = ref('')
const success = ref('')
const form = ref({
  nom: '',
  prenom: '',
  email: ''
})

const isAdmin = computed(() => {
  if (!user.value) return false
  return user.value.administrateur === 'true'
})

const isPremium = computed(() => {
  if (!user.value) return false
  return user.value.premium === 'true'
})

const loadProfile = async () => {
  loading.value = true
  const token = localStorage.getItem('token')
  
  if (!token) {
    router.push('/login')
    return
  }
  
  try {
    const payload = JSON.parse(atob(token.split('.')[1]))
    const userId = payload.sub || payload.user?.id
    
    if (!userId) {
      throw new Error('User ID not found in token')
    }
    
    const response = await fetch(`http://localhost:6090/auth/users/${userId}`, {
      headers: { 'Authorization': `Bearer ${token}` }
    })
    
    if (response.ok) {
      user.value = await response.json()
      form.value = {
        nom: user.value.nom,
        prenom: user.value.prenom,
        email: user.value.email
      }
    }
  } catch (err) {
    error.value = 'Erreur de chargement'
  } finally {
    loading.value = false
  }
}

const updateProfile = async () => {
  error.value = ''
  success.value = ''
  saving.value = true
  
  const token = localStorage.getItem('token')
  
  try {
    const payload = JSON.parse(atob(token.split('.')[1]))
    const userId = payload.sub || payload.user?.id
    
    const response = await fetch(`http://localhost:6090/auth/users/${userId}`, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${token}`
      },
      body: JSON.stringify(form.value)
    })
    
    if (response.ok) {
      user.value = await response.json()
      success.value = 'Profil mis à jour avec succès'
      editing.value = false
      setTimeout(() => {
        success.value = ''
      }, 3000)
    } else {
      error.value = 'Erreur lors de la mise à jour'
    }
  } catch (err) {
    error.value = 'Impossible de mettre à jour le profil'
  } finally {
    saving.value = false
  }
}

const handleAvatarUpload = async (event) => {
  const file = event.target.files[0]
  if (!file) return
  
  if (!file.type.startsWith('image/')) {
    error.value = 'Veuillez sélectionner une image valide'
    return
  }
  
  if (file.size > 5 * 1024 * 1024) {
    error.value = "L'image ne doit pas dépasser 5 Mo";
    return
  }
  
  const token = localStorage.getItem('token')
  const formData = new FormData()
  formData.append('avatar', file)
  
  loading.value = true
  error.value = ''
  
  try {
    const response = await fetch('http://localhost:6090/auth/users/upload-avatar', {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${token}`
      },
      body: formData
    })
    
    if (response.ok) {
      const data = await response.json()
      user.value.avatar_url = data.avatar_url
      success.value = 'Photo de profil mise à jour !'
      setTimeout(() => {
        success.value = ''
      }, 3000)
    } else {
      const data = await response.json()
      error.value = data.error || 'Erreur lors du téléchargement'
    }
  } catch (err) {
    error.value = 'Erreur de connexion au serveur'
  } finally {
    loading.value = false
  }
}

const cancelEdit = () => {
  editing.value = false
  error.value = ''
  success.value = ''
  form.value = {
    nom: user.value.nom,
    prenom: user.value.prenom,
    email: user.value.email
  }
}

const logout = () => {
  localStorage.removeItem('token')
  router.push('/login')
}

const formatDate = (dateString) => {
  if (!dateString) return 'Non disponible'
  const date = new Date(dateString)
  if (isNaN(date.getTime())) return 'Non disponible'
  return date.toLocaleDateString('fr-FR')
}

onMounted(() => {
  loadProfile()
})
</script>
