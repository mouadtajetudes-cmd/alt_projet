<template>
  <div class="avatar-page">
    <div class="container">
      <div class="header">
        <h1>🎭 Galerie des Avatars</h1>
        <p class="subtitle">Découvrez tous les avatars disponibles</p>
        
        <router-link 
          v-if="isAdmin" 
          to="/avatar/create" 
          class="btn-admin-create"
        >
          ➕ Créer un avatar
        </router-link>
      </div>
      
      <div v-if="errorMessage" class="alert-error">
        <span class="alert-icon">⚠️</span>
        {{ errorMessage }}
      </div>
      
      <div class="filters-section">
        <div class="search-box">
          <span class="search-icon">🔍</span>
          <input 
            v-model="searchQuery"
            type="text" 
            placeholder="Rechercher un avatar par nom..."
            class="search-input"
          >
          <button 
            v-if="searchQuery" 
            @click="searchQuery = ''"
            class="clear-btn"
          >
            ✕
          </button>
        </div>
        
        <div class="results-count">
          {{ filteredAvatars.length }} avatar(s) trouvé(s)
        </div>
      </div>
      
      <div v-if="loading" class="loading">
        <div class="spinner"></div>
        <p>Chargement des avatars...</p>
      </div>
      <div v-else-if="error" class="error-state">
        <span class="error-icon">⚠️</span>
        <p>{{ error }}</p>
        <button @click="loadAvatars" class="btn-retry">Réessayer</button>
      </div>
      <div v-else-if="filteredAvatars.length === 0" class="empty-state">
        <span class="empty-icon">🔍</span>
        <p>Aucun avatar trouvé</p>
        <p class="empty-hint">Essayez de modifier vos filtres</p>
      </div>
      
      <div v-else class="avatars-grid">
        <div
          v-for="avatar in filteredAvatars" 
          :key="avatar.id_avatar"
          class="avatar-card"
        >
          <div class="card-header">
            <div class="avatar-icon">🎭</div>
          </div>
          
          <div class="card-body">
            <h3 class="avatar-name">{{ avatar.nom }}</h3>
            <p class="avatar-description">Prêt à évoluer avec vous</p>
          </div>
          
          <div class="card-footer">
            <button @click="chooseAvatar(avatar)" class="btn-choose">
              ✓ Choisir cet avatar
            </button>
            <router-link :to="`/avatar/${avatar.id_avatar}`" class="btn-details">
              Voir les détails →
            </router-link>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuth } from '../composables/useAuth'

export default {
  name: 'Avatar',
  setup() {
    const router = useRouter()
    const route = useRoute()
    const { isAdmin, isAuthenticated, getUserId, initAuth, user } = useAuth()
    
    const avatars = ref([])
    const loading = ref(true)
    const error = ref(null)
    const searchQuery = ref('')
    const errorMessage = ref(null)
    
    initAuth()
    
    console.log('[AVATAR] User après initAuth:', user.value)
    console.log('[AVATAR] isAdmin:', isAdmin.value)
    console.log('[AVATAR] isAuthenticated:', isAuthenticated.value)
    
    if (route.query.error === 'admin-required') {
      errorMessage.value = '⛔ Accès refusé : seuls les administrateurs peuvent créer des avatars.'
      setTimeout(() => errorMessage.value = null, 5000)
    } else if (route.query.error === 'login-required') {
      errorMessage.value = '🔒 Veuillez vous connecter pour accéder à cette page.'
      setTimeout(() => errorMessage.value = null, 5000)
    }
    
    const loadAvatars = async () => {
      try {
        loading.value = true
        error.value = null
        console.log('[AVATAR] Chargement des avatars depuis http://localhost:6090/avatar/avatars')
        
        const response = await fetch('http://localhost:6090/avatar/avatars')
        
        if (!response.ok) {
          throw new Error(`Erreur HTTP: ${response.status}`)
        }
        
        const data = await response.json()
        console.log('[AVATAR] Avatars chargés:', data.length)
        avatars.value = data
      } catch (err) {
        console.error('[AVATAR] Erreur chargement avatars:', err)
        error.value = 'Impossible de charger les avatars. Vérifiez que le service est démarré.'
      } finally {
        loading.value = false
      }
    }
    
    const filteredAvatars = computed(() => {
      let filtered = avatars.value
      
      if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase().trim()
        filtered = filtered.filter(avatar => 
          avatar.nom.toLowerCase().includes(query)
        )
      }
      
      return filtered
    })
    
    const chooseAvatar = async (avatar) => {
      console.log('[AVATAR] Avatar choisi:', avatar.nom)
      
      try {
        // Récupérer l'ID utilisateur depuis l'auth
        const userId = getUserId()
        
        if (!userId) {
          alert('Veuillez vous connecter pour choisir un avatar.')
          router.push('/login')
          return
        }
        
        const response = await fetch('http://localhost:6090/avatar/avatars', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({
            nom: avatar.nom,
            image: avatar.image,
            id_utilisateur: userId
          })
        })
        
        if (!response.ok) {
          throw new Error(`Erreur HTTP: ${response.status}`)
        }
        
        const result = await response.json()
        console.log('[AVATAR] Avatar assigné:', result)
        
        alert(`Vous avez choisi "${avatar.nom}" ! Votre compagnon commence son aventure à 0 points.`)
        
        // Rediriger vers mes avatars
        router.push('/my-avatars')
        
      } catch (err) {
        console.error('[AVATAR] Erreur lors du choix:', err)
        alert('Une erreur est survenue. Veuillez réessayer.')
      }
    }
    
    onMounted(() => {
      loadAvatars()
    })
    
    return {
      avatars,
      loading,
      error,
      searchQuery,
      filteredAvatars,
      loadAvatars,
      chooseAvatar,
      isAdmin,
      errorMessage
    }
  }
}
</script>

<style scoped>
@import '@/assets/styles/avatar-variables.css';
@import '@/assets/styles/avatar-animations.css';
@import '@/assets/styles/avatar-common.css';

/* === Styles Spécifiques à Avatar.vue === */

.avatar-page {
  min-height: 100vh;
  background: var(--avatar-gradient);
  padding: 2rem 0;
}

.filters-section {
  background: white;
  border-radius: 16px;
  padding: 1.5rem;
  margin-bottom: 2rem;
  box-shadow: 0 8px 32px rgba(0,0,0,0.1);
  display: grid;
  grid-template-columns: 1fr auto;
  gap: 1.5rem;
  align-items: center;
}

.search-box {
  position: relative;
  display: flex;
  align-items: center;
}

.search-icon {
  position: absolute;
  left: 1rem;
  font-size: 1.2rem;
  opacity: 0.5;
}

.search-input {
  width: 100%;
  padding: 0.875rem 3rem 0.875rem 3rem;
  border: 2px solid #e0e0e0;
  border-radius: 12px;
  font-size: 1rem;
  transition: all 0.3s ease;
}

.search-input:focus {
  outline: none;
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.clear-btn {
  position: absolute;
  right: 1rem;
  background: none;
  border: none;
  font-size: 1.2rem;
  cursor: pointer;
  color: #999;
  padding: 0.25rem;
  transition: color 0.2s;
}

.clear-btn:hover {
  color: #333;
}

.results-count {
  font-weight: 600;
  color: var(--avatar-primary);
  font-size: 0.95rem;
  padding: 0.75rem 1.25rem;
  background: rgba(102, 126, 234, 0.1);
  border-radius: var(--avatar-radius-md);
  white-space: nowrap;
}

.empty-state {
  text-align: center;
  padding: var(--avatar-spacing-huge) var(--avatar-spacing-lg);
  background: var(--avatar-text-white);
  border-radius: var(--avatar-radius-lg);
  color: var(--avatar-text-medium);
}

.empty-icon {
  font-size: 4rem;
  display: block;
  margin-bottom: var(--avatar-spacing-sm);
  opacity: 0.5;
}

.empty-hint {
  margin-top: var(--avatar-spacing-xs);
  font-size: 0.9rem;
  color: var(--avatar-text-light);
}

.avatars-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 1.5rem;
}

@media (min-width: 768px) {
  .avatars-grid {
    grid-template-columns: repeat(3, 1fr);
  }
}

@media (min-width: 1200px) {
  .avatars-grid {
    grid-template-columns: repeat(4, 1fr);
  }
}

.avatar-card {
  display: block;
  background: var(--avatar-text-white);
  border-radius: var(--avatar-radius-lg);
  overflow: hidden;
  box-shadow: var(--avatar-shadow-sm);
  transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
  cursor: default;
  text-decoration: none;
  color: inherit;
}

.avatar-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 12px 32px rgba(102, 126, 234, 0.3);
}

.avatar-description {
  text-align: center;
  color: var(--avatar-text-light);
  font-size: 0.9rem;
  margin: 0;
}

.btn-choose {
  width: 100%;
  padding: 1rem;
  background: var(--avatar-gradient);
  color: var(--avatar-text-white);
  border: none;
  border-radius: var(--avatar-radius-md);
  cursor: pointer;
  font-size: 1rem;
  font-weight: 600;
  text-align: center;
  transition: all var(--avatar-transition-normal);
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.btn-choose:hover {
  transform: translateY(-2px);
  box-shadow: var(--avatar-shadow-primary);
}

.btn-admin-create {
  display: inline-flex;
  align-items: center;
  gap: var(--avatar-spacing-xs);
  padding: 0.875rem 1.75rem;
  background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
  color: var(--avatar-text-white);
  border-radius: var(--avatar-radius-md);
  text-decoration: none;
  font-weight: 700;
  font-size: 1rem;
  margin-top: var(--avatar-spacing-md);
  transition: all var(--avatar-transition-normal);
  box-shadow: 0 4px 16px rgba(40, 167, 69, 0.3);
}

.btn-admin-create:hover {
  transform: translateY(-3px);
  box-shadow: 0 6px 24px rgba(40, 167, 69, 0.4);
}

.alert-error {
  background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
  color: var(--avatar-text-white);
  padding: var(--avatar-spacing-sm) var(--avatar-spacing-md);
  border-radius: var(--avatar-radius-md);
  margin-bottom: var(--avatar-spacing-lg);
  display: flex;
  align-items: center;
  gap: 0.75rem;
  font-weight: 600;
  box-shadow: 0 4px 16px rgba(220, 53, 69, 0.3);
  animation: slideDown var(--avatar-transition-slow) ease-out;
}

@media (max-width: 768px) {
  .filters-section {
    grid-template-columns: 1fr;
  }
  
  .avatars-grid {
    grid-template-columns: 1fr;
  }
}
</style>
