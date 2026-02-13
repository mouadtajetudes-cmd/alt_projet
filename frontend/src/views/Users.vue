<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50 py-8 px-4">
    <div class="max-w-7xl mx-auto">
      
      <div class="bg-white rounded-2xl shadow-lg p-6 mb-6 animate-fade-in">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
          <div>
            <h1 class="text-3xl font-bold text-dark flex items-center gap-3">
              <font-awesome-icon icon="users" class="text-primary" />
              Gestion des utilisateurs
            </h1>
            <p class="text-dark-muted mt-1">{{ users.length }} utilisateur(s) au total</p>
          </div>
          
          <div class="flex gap-2">
            <router-link 
              to="/chat"
              class="flex items-center gap-2 px-4 py-2 bg-blue-50 text-primary rounded-xl hover:bg-blue-100 transition-colors"
            >
              <font-awesome-icon icon="comments" />
              <span>Chat</span>
            </router-link>
            <router-link 
              to="/profile"
              class="flex items-center gap-2 px-4 py-2 bg-purple-50 text-purple-600 rounded-xl hover:bg-purple-100 transition-colors"
            >
              <font-awesome-icon icon="user-circle" />
              <span>Profil</span>
            </router-link>
            <button 
              v-if="isAdmin"
              @click="openCreateModal"
              class="flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-primary to-primary-light text-white rounded-xl hover:shadow-lg transition-all"
            >
              <font-awesome-icon icon="plus" />
              <span>Nouvel utilisateur</span>
            </button>
          </div>
        </div>
      </div>

      
      <div class="bg-white rounded-2xl shadow-lg p-6 mb-6 animate-slide-up">
        <div class="grid md:grid-cols-3 gap-4">
          
          <div class="md:col-span-2">
            <div class="relative">
              <font-awesome-icon icon="search" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
              <input
                v-model="search"
                @input="filterUsers"
                type="text"
                placeholder="Rechercher par nom, prénom ou email..."
                class="w-full pl-10 pr-4 py-3 border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all"
              />
            </div>
          </div>
          
          
          <div v-if="isAdmin">
            <select 
              v-model="roleFilter"
              @change="filterUsers"
              class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all"
            >
              <option value="">Tous les rôles</option>
              <option value="admin">Administrateurs</option>
              <option value="user">Utilisateurs</option>
              <option value="premium">Premium</option>
            </select>
          </div>
        </div>
      </div>

      
      <BaseTable
        title="Liste des utilisateurs"
        icon="users"
        :columns="tableColumns"
        :rows="filtered"
        :loading="loading"
        row-key="id_utilisateur"
        empty-message="Aucun utilisateur trouvé. Essayez de modifier vos filtres de recherche."
        class="animate-slide-up"
        style="animation-delay: 0.1s"
      >
        
        <template #cell-user="{ row }">
          <div class="flex items-center gap-3">
            <div 
              v-if="row.avatar_url"
              class="w-10 h-10 rounded-full overflow-hidden border-2 border-gray-200"
            >
              <img 
                :src="`http://localhost:6090/auth${row.avatar_url}`" 
                :alt="`${row.prenom} ${row.nom}`"
                class="w-full h-full object-cover"
              />
            </div>
            <div 
              v-else
              class="w-10 h-10 rounded-full bg-gradient-to-br from-primary to-primary-light flex items-center justify-center text-white font-semibold"
            >
              {{ row.nom[0] }}{{ row.prenom[0] }}
            </div>
            <div>
              <div class="font-semibold text-dark">{{ row.prenom }} {{ row.nom }}</div>
              <div v-if="!isAdmin" class="text-sm text-dark-muted">{{ row.email }}</div>
            </div>
          </div>
        </template>

        
        <template #cell-status="{ row }">
          <div class="flex gap-2">
            <span 
              class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium"
              :class="row.administrateur === 'true' ? 'bg-orange-100 text-orange-700' : 'bg-blue-100 text-blue-700'"
            >
              <font-awesome-icon :icon="row.administrateur === 'true' ? 'crown' : 'user'" />
              {{ row.administrateur === 'true' ? 'Admin' : 'User' }}
            </span>
            <span 
              v-if="row.premium === 'true'"
              class="inline-flex items-center gap-1.5 px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-medium"
            >
              <font-awesome-icon icon="gem" />
              Premium
            </span>
          </div>
        </template>

        
        <template #cell-actions="{ row }">
          <div class="flex items-center justify-end gap-2">
            <button 
              @click="editUser(row)"
              class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
              title="Modifier"
            >
              <font-awesome-icon icon="edit" />
            </button>
            <button 
              @click="deleteUser(row.id_utilisateur)"
              class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
              title="Supprimer"
            >
              <font-awesome-icon icon="trash" />
            </button>
          </div>
        </template>
      </BaseTable>
    </div>

    
    <transition name="modal">
      <div 
        v-if="showModal" 
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
        @click.self="closeModal"
      >
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto animate-slide-down">
          <div class="sticky top-0 bg-white border-b border-gray-200 px-8 py-6 flex items-center justify-between">
            <h2 class="text-2xl font-bold text-dark flex items-center gap-2">
              <font-awesome-icon :icon="editing ? 'edit' : 'plus'" class="text-primary" />
              {{ editing ? 'Modifier l\'utilisateur' : 'Nouvel utilisateur' }}
            </h2>
            <button 
              @click="closeModal"
              class="p-2 hover:bg-gray-100 rounded-lg transition-colors"
            >
              <font-awesome-icon icon="times" class="text-gray-600" />
            </button>
          </div>

          <form @submit.prevent="saveUser" class="p-8 space-y-6">
            
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
            </div>

            
            <div class="grid md:grid-cols-2 gap-6">
              <div>
                <label class="block text-sm font-medium text-dark mb-2">Email</label>
                <input
                  v-model="form.email"
                  type="email"
                  required
                  class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all"
                />
              </div>
              
              <div>
                <label class="block text-sm font-medium text-dark mb-2">Téléphone</label>
                <input
                  v-model="form.telephone"
                  type="text"
                  class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all"
                />
              </div>
            </div>

            
            <div v-if="!editing">
              <label class="block text-sm font-medium text-dark mb-2">Mot de passe</label>
              <input
                v-model="form.password"
                type="password"
                required
                class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all"
              />
            </div>

            
            <div class="flex gap-6">
              <label class="flex items-center gap-2 cursor-pointer">
                <input
                  v-model="form.administrateur"
                  type="checkbox"
                  class="w-5 h-5 text-primary rounded focus:ring-2 focus:ring-primary"
                />
                <span class="flex items-center gap-2 text-dark font-medium">
                  <font-awesome-icon icon="crown" class="text-orange-500" />
                  Administrateur
                </span>
              </label>
              
              <label class="flex items-center gap-2 cursor-pointer">
                <input
                  v-model="form.premium"
                  type="checkbox"
                  class="w-5 h-5 text-primary rounded focus:ring-2 focus:ring-primary"
                />
                <span class="flex items-center gap-2 text-dark font-medium">
                  <font-awesome-icon icon="gem" class="text-yellow-500" />
                  Premium
                </span>
              </label>
            </div>

            
            <div 
              v-if="error" 
              class="flex items-center gap-2 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl"
            >
              <font-awesome-icon icon="info-circle" />
              <span>{{ error }}</span>
            </div>

            
            <div class="flex gap-3 pt-4">
              <button
                type="submit"
                class="flex-1 flex items-center justify-center gap-2 py-3 bg-gradient-to-r from-primary to-primary-light text-white font-semibold rounded-xl shadow-lg hover:-translate-y-0.5 hover:shadow-xl transition-all duration-200"
              >
                <font-awesome-icon :icon="editing ? 'check' : 'plus'" />
                <span>{{ editing ? 'Enregistrer' : 'Créer' }}</span>
              </button>
              
              <button
                type="button"
                @click="closeModal"
                class="px-6 py-3 bg-gray-200 text-dark font-semibold rounded-xl hover:bg-gray-300 transition-colors"
              >
                Annuler
              </button>
            </div>
          </form>
        </div>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import BaseTable from '@/components/BaseTable.vue'
import './views.css'

const users = ref([])
const filtered = ref([])
const search = ref('')
const roleFilter = ref('')
const loading = ref(true)
const isAdmin = ref(false)
const showModal = ref(false)
const editing = ref(false)
const error = ref('')

const form = ref({
  nom: '',
  prenom: '',
  email: '',
  telephone: '',
  password: '',
  administrateur: false,
  premium: false
})

const editId = ref(null)

const tableColumns = computed(() => {
  const cols = [{ key: 'user', label: 'Utilisateur' }]
  
  if (isAdmin.value) {
    cols.push(
      { key: 'email', label: 'Email' },
      { key: 'status', label: 'Statut' },
      { key: 'actions', label: 'Actions', cellClass: 'text-right' }
    )
  }
  
  return cols
})

const loadUsers = async () => {
  loading.value = true
  const token = localStorage.getItem('token')
  
  try {
    const payload = JSON.parse(atob(token.split('.')[1]))
    const userId = payload.sub || payload.user?.id
    
    const userResponse = await fetch(`http://localhost:6090/auth/users/${userId}`, {
      headers: { 'Authorization': `Bearer ${token}` }
    })
    
    if (userResponse.ok) {
      const userData = await userResponse.json()
      isAdmin.value = (userData.administrateur === 'true')
    }
  } catch (err) {
    console.error('Error checking admin status:', err)
  }
  
  const endpoint = isAdmin.value ? 'http://localhost:6090/auth/ausers' : 'http://localhost:6090/auth/users'
  
  try {
    const response = await fetch(endpoint, {
      headers: { 'Authorization': `Bearer ${token}` }
    })
    if (response.ok) {
      users.value = await response.json()
      filtered.value = users.value
    }
  } catch (err) {
    console.error(err)
  } finally {
    loading.value = false
  }
}

const filterUsers = () => {
  const q = search.value.toLowerCase()
  let result = users.value.filter(u => 
    u.nom.toLowerCase().includes(q) || 
    u.prenom.toLowerCase().includes(q) ||
    (u.email && u.email.toLowerCase().includes(q))
  )
  
  if (roleFilter.value === 'admin') {
    result = result.filter(u => u.administrateur === 'true')
  } else if (roleFilter.value === 'user') {
    result = result.filter(u => u.administrateur !== 'true')
  } else if (roleFilter.value === 'premium') {
    result = result.filter(u => u.premium === 'true')
  }
  
  filtered.value = result
}

const openCreateModal = () => {
  editing.value = false
  error.value = ''
  form.value = {
    nom: '',
    prenom: '',
    email: '',
    telephone: '',
    password: '',
    administrateur: false,
    premium: false
  }
  showModal.value = true
}

const editUser = (user) => {
  editing.value = true
  editId.value = user.id_utilisateur
  error.value = ''
  form.value = {
    nom: user.nom,
    prenom: user.prenom,
    email: user.email,
    telephone: user.telephone || '',
    administrateur: user.administrateur === 'true',
    premium: user.premium === 'true'
  }
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  error.value = ''
}

const saveUser = async () => {
  error.value = ''
  const token = localStorage.getItem('token')
  
  try {
    let url = 'http://localhost:6090/auth/users'
    let method = 'POST'
    let body = { ...form.value }

    if (editing.value) {
      url = `${url}/${editId.value}`
      method = 'PUT'
      delete body.password
    }

    const response = await fetch(url, {
      method,
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${token}`
      },
      body: JSON.stringify(body)
    })

    if (response.ok) {
      showModal.value = false
      loadUsers()
    } else {
      const data = await response.json()
      error.value = data.message || 'Erreur lors de l\'opération'
    }
  } catch (err) {
    error.value = 'Erreur de connexion au serveur'
  }
}

const deleteUser = async (id) => {
  if (!confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')) return

  const token = localStorage.getItem('token')
  
  try {
    const response = await fetch(`http://localhost:6090/auth/users/${id}`, {
      method: 'DELETE',
      headers: { 'Authorization': `Bearer ${token}` }
    })

    if (response.ok) loadUsers()
  } catch (err) {
    console.error(err)
  }
}

onMounted(() => {
  loadUsers()
})
</script>
