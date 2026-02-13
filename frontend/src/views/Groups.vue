<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50 py-8 px-4">
    <div class="max-w-7xl mx-auto">
      <div class="bg-white rounded-2xl shadow-lg p-6 mb-6 animate-fade-in">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
          <div>
            <h1 class="text-3xl font-bold text-dark flex items-center gap-3">
              <font-awesome-icon icon="layer-group" class="text-primary" />
              Gestion des groupes
            </h1>
            <p class="text-dark-muted mt-1">{{ groups.length }} groupe(s) au total</p>
          </div>
          
          <div class="flex gap-2">
            <router-link 
              to="/users"
              class="flex items-center gap-2 px-4 py-2 bg-purple-50 text-purple-600 rounded-xl hover:bg-purple-100 transition-colors"
            >
              <font-awesome-icon icon="users" />
              <span>Utilisateurs</span>
            </router-link>
            <button 
              @click="openCreateModal"
              class="flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-primary to-primary-light text-white rounded-xl hover:shadow-lg transition-all"
            >
              <font-awesome-icon icon="plus" />
              <span>Nouveau groupe</span>
            </button>
          </div>
        </div>
      </div>

      <div class="grid lg:grid-cols-5 gap-6">
        <div class="lg:col-span-2 space-y-4">
          <div class="bg-white rounded-2xl shadow-lg p-4 animate-slide-up">
            <div class="relative">
              <font-awesome-icon icon="search" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
              <input
                v-model="searchQuery"
                @input="filterGroups"
                type="text"
                placeholder="Rechercher un groupe..."
                class="w-full pl-10 pr-4 py-3 border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all"
              />
            </div>
          </div>

          <div class="space-y-3 max-h-[calc(100vh-300px)] overflow-y-auto pr-2">
            <div v-if="loading" class="bg-white rounded-2xl shadow-lg p-8 text-center">
              <div class="flex justify-center gap-2">
                <div class="w-3 h-3 bg-primary rounded-full animate-bounce" style="animation-delay: 0ms"></div>
                <div class="w-3 h-3 bg-primary rounded-full animate-bounce" style="animation-delay: 150ms"></div>
                <div class="w-3 h-3 bg-primary rounded-full animate-bounce" style="animation-delay: 300ms"></div>
              </div>
            </div>

            <div 
              v-else
              v-for="group in filteredGroups" 
              :key="group.id_groupe"
              @click="selectGroup(group)"
              class="bg-white rounded-2xl shadow-md p-6 cursor-pointer transition-all hover:shadow-xl hover:-translate-y-1 animate-slide-up"
              :class="{'ring-2 ring-primary': selectedGroup?.id_groupe === group.id_groupe}"
            >
              <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-primary to-purple-600 flex items-center justify-center flex-shrink-0">
                  <font-awesome-icon icon="layer-group" class="text-white text-xl" />
                </div>
                <div class="flex-1 min-w-0">
                  <h3 class="font-semibold text-dark text-lg mb-1 truncate">
                    {{ group.nom }}
                  </h3>
                  <p class="text-dark-muted text-sm line-clamp-2 mb-2">
                    {{ group.description || 'Pas de description' }}
                  </p>
                  <div class="flex items-center gap-2 text-sm text-primary">
                    <font-awesome-icon icon="users" />
                    <span>{{ group.memberCount || 0 }} membre(s)</span>
                  </div>
                </div>
              </div>
            </div>

            <div v-if="!loading && filteredGroups.length === 0" class="bg-white rounded-2xl shadow-lg p-12 text-center">
              <font-awesome-icon icon="layer-group" class="text-6xl text-gray-300 mb-4" />
              <p class="text-dark-muted">Aucun groupe trouvé</p>
            </div>
          </div>
        </div>

        <div class="lg:col-span-3">
          <div class="bg-white rounded-2xl shadow-lg p-8 min-h-[500px] animate-slide-up" style="animation-delay: 0.1s">
            <div v-if="!selectedGroup" class="flex flex-col items-center justify-center h-full text-gray-400 py-20">
              <font-awesome-icon icon="layer-group" class="text-8xl mb-6" />
              <h3 class="text-xl font-semibold text-dark mb-2">Sélectionnez un groupe</h3>
              <p class="text-dark-muted">Choisissez un groupe dans la liste pour voir les détails</p>
            </div>

            <div v-else>
                      <div class="flex items-start justify-between mb-6 pb-6 border-b">
                <div class="flex-1">
                  <h2 class="text-2xl font-bold text-dark mb-2">{{ selectedGroup.nom }}</h2>
                  <p class="text-dark-muted">{{ selectedGroup.description || 'Aucune description' }}</p>
                  <div class="flex items-center gap-4 mt-3 text-sm text-dark-muted">
                    <span class="flex items-center gap-1">
                      <font-awesome-icon icon="calendar" />
                      Créé le {{ formatDate(selectedGroup.created_at) }}
                    </span>
                  </div>
                </div>
                <button 
                  @click="editGroup(selectedGroup)"
                  class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                  title="Modifier le groupe"
                >
                  <font-awesome-icon icon="edit" class="text-xl" />
                </button>
              </div>

              
              <div>
                <div class="flex items-center justify-between mb-4">
                  <h3 class="text-lg font-semibold text-dark flex items-center gap-2">
                    <font-awesome-icon icon="users" class="text-primary" />
                    Membres ({{ members.length }})
                  </h3>
                  <button 
                    @click="showAddMemberModal = true"
                    class="flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-xl hover:bg-primary-dark transition-colors text-sm"
                  >
                    <font-awesome-icon icon="user-plus" />
                    Ajouter un membre
                  </button>
                </div>

                
                <div v-if="loadingMembers" class="flex justify-center py-12">
                  <div class="flex gap-2">
                    <div class="w-3 h-3 bg-primary rounded-full animate-bounce" style="animation-delay: 0ms"></div>
                    <div class="w-3 h-3 bg-primary rounded-full animate-bounce" style="animation-delay: 150ms"></div>
                    <div class="w-3 h-3 bg-primary rounded-full animate-bounce" style="animation-delay: 300ms"></div>
                  </div>
                </div>

                
                <div v-else-if="members.length > 0" class="space-y-3">
                  <div 
                    v-for="member in members" 
                    :key="member.id_utilisateur"
                    class="flex items-center justify-between p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors"
                  >
                    <div class="flex items-center gap-3">
                      <div class="w-10 h-10 rounded-full bg-gradient-to-br from-primary to-primary-light flex items-center justify-center text-white font-semibold">
                        {{ member.nom?.[0] }}{{ member.prenom?.[0] }}
                      </div>
                      <div class="flex-1">
                        <div class="flex items-center gap-2">
                          <span class="font-semibold text-dark">{{ member.prenom }} {{ member.nom }}</span>
                          <span 
                            v-if="member.role === 'owner'" 
                            class="px-2 py-0.5 bg-gradient-to-r from-yellow-400 to-orange-400 text-white text-xs font-bold rounded-full flex items-center gap-1"
                          >
                            <font-awesome-icon icon="crown" class="text-xs" />
                            Propriétaire
                          </span>
                          <span 
                            v-else-if="member.role === 'admin'" 
                            class="px-2 py-0.5 bg-blue-500 text-white text-xs font-semibold rounded-full"
                          >
                            Admin
                          </span>
                        </div>
                        <div class="text-sm text-dark-muted">{{ member.email }}</div>
                      </div>
                    </div>
                    <button 
                      v-if="member.role !== 'owner'"
                      @click="removeMember(member.id_utilisateur)"
                      class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                      title="Retirer du groupe"
                    >
                      <font-awesome-icon icon="user-minus" />
                    </button>
                    <div v-else class="text-xs text-gray-400 px-3">
                      Non supprimable
                    </div>
                  </div>
                </div>

                
                <div v-else class="text-center py-12">
                  <font-awesome-icon icon="users" class="text-6xl text-gray-300 mb-4" />
                  <p class="text-dark-muted">Aucun membre dans ce groupe</p>
                  <button 
                    @click="showAddMemberModal = true"
                    class="mt-4 text-primary hover:underline"
                  >
                    Ajouter le premier membre
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    
    <transition name="modal">
      <div 
        v-if="showModal" 
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
        @click.self="closeModal"
      >
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md animate-slide-down">
          <div class="border-b border-gray-200 px-6 py-4 flex items-center justify-between">
            <h2 class="text-xl font-bold text-dark flex items-center gap-2">
              <font-awesome-icon :icon="editing ? 'edit' : 'plus'" class="text-primary" />
              {{ editing ? 'Modifier le groupe' : 'Nouveau groupe' }}
            </h2>
            <button 
              @click="closeModal"
              class="p-2 hover:bg-gray-100 rounded-lg transition-colors"
            >
              <font-awesome-icon icon="times" class="text-gray-600" />
            </button>
          </div>

          <form @submit.prevent="saveGroup" class="p-6 space-y-4">
            <div>
              <label class="block text-sm font-medium text-dark mb-2">Nom du groupe</label>
              <input
                v-model="form.nom"
                type="text"
                required
                placeholder="Ex: Équipe Marketing"
                class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-dark mb-2">Description</label>
              <textarea
                v-model="form.description"
                rows="3"
                placeholder="Description du groupe (optionnel)"
                class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all resize-none"
              ></textarea>
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
                :disabled="saving"
                class="flex-1 flex items-center justify-center gap-2 py-3 bg-gradient-to-r from-primary to-primary-light text-white font-semibold rounded-xl shadow-lg hover:-translate-y-0.5 hover:shadow-xl transition-all duration-200 disabled:opacity-50"
              >
                <font-awesome-icon :icon="editing ? 'check' : 'plus'" />
                <span>{{ saving ? 'Enregistrement...' : (editing ? 'Enregistrer' : 'Créer') }}</span>
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

    
    <transition name="modal">
      <div 
        v-if="showAddMemberModal" 
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
        @click.self="showAddMemberModal = false"
      >
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md animate-slide-down">
          <div class="border-b border-gray-200 px-6 py-4 flex items-center justify-between">
            <h2 class="text-xl font-bold text-dark flex items-center gap-2">
              <font-awesome-icon icon="user-plus" class="text-primary" />
              Ajouter un membre
            </h2>
            <button 
              @click="showAddMemberModal = false"
              class="p-2 hover:bg-gray-100 rounded-lg transition-colors"
            >
              <font-awesome-icon icon="times" class="text-gray-600" />
            </button>
          </div>

          <div class="p-6">
            
            <div class="mb-4">
              <div class="relative">
                <font-awesome-icon icon="search" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
                <input
                  v-model="memberSearchQuery"
                  @input="filterAvailableUsers"
                  type="text"
                  placeholder="Rechercher un utilisateur..."
                  class="w-full pl-10 pr-4 py-3 border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all"
                />
              </div>
            </div>

            
            <div class="max-h-96 overflow-y-auto space-y-2">
              <div v-if="loadingUsers" class="flex justify-center py-8">
                <div class="flex gap-2">
                  <div class="w-3 h-3 bg-primary rounded-full animate-bounce" style="animation-delay: 0ms"></div>
                  <div class="w-3 h-3 bg-primary rounded-full animate-bounce" style="animation-delay: 150ms"></div>
                  <div class="w-3 h-3 bg-primary rounded-full animate-bounce" style="animation-delay: 300ms"></div>
                </div>
              </div>

              <button
                v-else
                v-for="user in filteredAvailableUsers"
                :key="user.id_utilisateur"
                @click="addMember(user.id_utilisateur)"
                class="w-full flex items-center gap-3 p-3 hover:bg-blue-50 rounded-xl transition-colors text-left"
              >
                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-primary to-primary-light flex items-center justify-center text-white font-semibold">
                  {{ user.nom?.[0] }}{{ user.prenom?.[0] }}
                </div>
                <div class="flex-1 min-w-0">
                  <div class="font-semibold text-dark">{{ user.prenom }} {{ user.nom }}</div>
                  <div class="text-sm text-dark-muted truncate">{{ user.email }}</div>
                </div>
                <font-awesome-icon icon="plus" class="text-primary" />
              </button>

              <div v-if="!loadingUsers && filteredAvailableUsers.length === 0" class="text-center py-8 text-dark-muted">
                Aucun utilisateur disponible
              </div>
            </div>
          </div>
        </div>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import './views.css'

const router = useRouter()

const groups = ref([])
const filteredGroups = ref([])
const selectedGroup = ref(null)
const members = ref([])
const allUsers = ref([])
const availableUsers = ref([])
const filteredAvailableUsers = ref([])

const searchQuery = ref('')
const memberSearchQuery = ref('')
const loading = ref(true)
const loadingMembers = ref(false)
const loadingUsers = ref(false)
const showModal = ref(false)
const showAddMemberModal = ref(false)
const editing = ref(false)
const saving = ref(false)
const error = ref('')

const form = ref({
  nom: '',
  description: ''
})

const editId = ref(null)

const loadGroups = async () => {
  loading.value = true
  const token = localStorage.getItem('token')
  
  try {
    const response = await fetch('http://localhost:6090/auth/groups', {
      headers: { 'Authorization': `Bearer ${token}` }
    })
    
    if (response.ok) {
      groups.value = await response.json()
      
      for (const group of groups.value) {
        await loadMemberCount(group)
      }
      
      filteredGroups.value = groups.value
    }
  } catch (err) {
    console.error('Error loading groups:', err)
  } finally {
    loading.value = false
  }
}

const loadMemberCount = async (group) => {
  const token = localStorage.getItem('token')
  
  try {
    const response = await fetch(`http://localhost:6090/auth/groups/${group.id_groupe}/members`, {
      headers: { 'Authorization': `Bearer ${token}` }
    })
    
    if (response.ok) {
      const membersList = await response.json()
      group.memberCount = membersList.length
    }
  } catch (err) {
    console.error('Error loading member count:', err)
  }
}

const filterGroups = () => {
  const query = searchQuery.value.toLowerCase()
  filteredGroups.value = groups.value.filter(g => 
    g.nom.toLowerCase().includes(query) ||
    (g.description && g.description.toLowerCase().includes(query))
  )
}

const selectGroup = async (group) => {
  selectedGroup.value = group
  await loadMembers(group.id_groupe)
}

const loadMembers = async (groupId) => {
  loadingMembers.value = true
  const token = localStorage.getItem('token')
  
  try {
    const response = await fetch(`http://localhost:6090/auth/groups/${groupId}/members`, {
      headers: { 'Authorization': `Bearer ${token}` }
    })
    
    if (response.ok) {
      members.value = await response.json()
    }
  } catch (err) {
    console.error('Error loading members:', err)
  } finally {
    loadingMembers.value = false
  }
}

const loadUsers = async () => {
  loadingUsers.value = true
  const token = localStorage.getItem('token')
  
  try {
    const response = await fetch('http://localhost:6090/auth/users', {
      headers: { 'Authorization': `Bearer ${token}` }
    })
    
    if (response.ok) {
      allUsers.value = await response.json()
      updateAvailableUsers()
    }
  } catch (err) {
    console.error('Error loading users:', err)
  } finally {
    loadingUsers.value = false
  }
}

const updateAvailableUsers = () => {
  const memberIds = members.value.map(m => m.id_utilisateur)
  availableUsers.value = allUsers.value.filter(u => !memberIds.includes(u.id_utilisateur))
  filteredAvailableUsers.value = availableUsers.value
}

const filterAvailableUsers = () => {
  const query = memberSearchQuery.value.toLowerCase()
  filteredAvailableUsers.value = availableUsers.value.filter(u =>
    u.nom.toLowerCase().includes(query) ||
    u.prenom.toLowerCase().includes(query) ||
    u.email.toLowerCase().includes(query)
  )
}

const openCreateModal = () => {
  editing.value = false
  error.value = ''
  form.value = {
    nom: '',
    description: ''
  }
  showModal.value = true
}

const editGroup = (group) => {
  editing.value = true
  editId.value = group.id_groupe
  error.value = ''
  form.value = {
    nom: group.nom,
    description: group.description || ''
  }
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  error.value = ''
}

const saveGroup = async () => {
  error.value = ''
  saving.value = true
  const token = localStorage.getItem('token')
  
  try {
    let url = 'http://localhost:6090/auth/groups'
    let method = 'POST'

    if (editing.value) {
      url = `${url}/${editId.value}`
      method = 'PUT'
    }

    const response = await fetch(url, {
      method,
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${token}`
      },
      body: JSON.stringify(form.value)
    })

    if (response.ok) {
      showModal.value = false
      await loadGroups()
      
      if (editing.value && selectedGroup.value?.id_groupe === editId.value) {
        const updatedGroup = groups.value.find(g => g.id_groupe === editId.value)
        if (updatedGroup) selectedGroup.value = updatedGroup
      }
    } else {
      const data = await response.json()
      error.value = data.message || 'Erreur lors de l\'opération'
    }
  } catch (err) {
    error.value = 'Erreur de connexion au serveur'
  } finally {
    saving.value = false
  }
}

const addMember = async (userId) => {
  const token = localStorage.getItem('token')
  
  try {
    const response = await fetch(`http://localhost:6090/auth/groups/${selectedGroup.value.id_groupe}/members`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${token}`
      },
      body: JSON.stringify({ userId })
    })

    if (response.ok) {
      showAddMemberModal.value = false
      memberSearchQuery.value = ''
      await loadMembers(selectedGroup.value.id_groupe)
      await loadMemberCount(selectedGroup.value)
      updateAvailableUsers()
    }
  } catch (err) {
    console.error('Error adding member:', err)
  }
}

const removeMember = async (userId) => {
  if (!confirm('Êtes-vous sûr de vouloir retirer ce membre du groupe ?')) return
  
  const token = localStorage.getItem('token')
  
  try {
    const response = await fetch(`http://localhost:6090/auth/groups/${selectedGroup.value.id_groupe}/members/${userId}`, {
      method: 'DELETE',
      headers: { 'Authorization': `Bearer ${token}` }
    })

    if (response.ok) {
      await loadMembers(selectedGroup.value.id_groupe)
      await loadMemberCount(selectedGroup.value)
      updateAvailableUsers()
    }
  } catch (err) {
    console.error('Error removing member:', err)
  }
}

const formatDate = (dateString) => {
  if (!dateString) return 'Non disponible'
  const date = new Date(dateString)
  if (isNaN(date.getTime())) return 'Non disponible'
  return date.toLocaleDateString('fr-FR')
}

const watchAddMemberModal = computed(() => showAddMemberModal.value)
watchAddMemberModal.value // Use computed to trigger reactivity

onMounted(async () => {
  await loadGroups()
  await loadUsers()
})

const unwatchAddMemberModal = computed(() => {
  if (showAddMemberModal.value) {
    updateAvailableUsers()
  }
  return showAddMemberModal.value
})
</script>
