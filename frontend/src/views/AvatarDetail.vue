<template>
  <div class="avatar-detail-page">
    <div class="container">
      <div class="back-button-wrapper">
        <router-link :to="isOwner ? '/my-avatars' : '/avatar'" class="btn-back">
          ← Retour {{ isOwner ? 'à mes avatars' : 'à la galerie' }}
        </router-link>
      </div>

      <div v-if="loading" class="loading">
        <div class="spinner"></div>
        <p>Chargement des détails...</p>
      </div>
      <div v-else-if="error" class="error-state">
        <span class="error-icon">⚠️</span>
        <p>{{ error }}</p>
        <router-link to="/avatar" class="btn-retry">Retour à la galerie</router-link>
      </div>

      <div v-else-if="avatar" class="avatar-detail-card">
        <div class="companion-header">
          <div class="companion-image">
            <img v-if="avatar.image" :src="`/avatars/${avatar.image}`" class="avatar-image-detail" alt="Avatar" />
            <div v-else class="avatar-icon-large">🦊</div>
          </div>
        </div>

        <div class="card-body-dark">
          <div class="info-row">
            <div class="info-icon-wrapper">
              <span class="info-icon">👥</span>
            </div>
            <div class="info-text">
              <span class="info-label-white">{{ isOwner ? 'Votre compagnon :' : 'Compagnon :' }}</span>
              <span class="info-value-yellow">{{ avatar.nom }}</span>
            </div>
          </div>

          <div class="info-row">
            <div class="info-icon-wrapper">
              <span class="info-icon">📊</span>
            </div>
            <div class="info-text">
              <span class="info-label-white">Niveau:</span>
              <span class="info-value-white">{{ avatar.niveau_actuel }} - {{ avatar.nom_niveau }}</span>
            </div>
          </div>

          <div class="info-row">
            <div class="info-icon-wrapper">
              <span class="info-icon">📈</span>
            </div>
            <div class="info-text">
              <span class="info-label-white">Progression vers niveau {{ avatar.niveau_actuel + 1 }}:</span>
              <span class="info-value-yellow">{{ avatar.current_points }} / {{ getNextLevelPoints }} Points</span>
            </div>
          </div>

          <div class="info-row">
            <div class="info-icon-wrapper">
              <span class="info-icon">📅</span>
            </div>
            <div class="info-text">
              <span class="info-label-white">Depuis le:</span>
              <span class="info-value-yellow">{{ formatDate(avatar.created_at) }}</span>
            </div>
          </div>

          <div v-if="isOwner" class="action-button-wrapper">
            <button @click="openLevelUpModal" class="btn-level-up" :disabled="!canLevelUp">
              🚀 Monter de niveau
            </button>
          </div>
          
          <div v-else class="info-message">
            <span class="info-icon-msg">🔒</span>
            <p>Ceci n'est pas votre avatar. Seul le propriétaire peut le personnaliser.</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Level Up Modal -->
    <LevelUpModal
      :show="showLevelUpModal"
      :user-id="Number(route.params.userId)"
      :avatar-name="avatar?.nom"
      :avatar-image="avatar?.image"
      :current-level="avatar?.niveau_actuel"
      :current-points="avatar?.current_points"
      :required-points="getNextLevelPoints"
      @close="showLevelUpModal = false"
      @success="handleLevelUpSuccess"
    />
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuth } from '../composables/useAuth'
import LevelUpModal from '../components/LevelUpModal.vue'

export default {
  name: 'AvatarDetail',
  components: {
    LevelUpModal
  },
  setup() {
    const route = useRoute()
    const router = useRouter()
    const { getUserId, initAuth } = useAuth()
    
    initAuth()
    
    const currentUserId = getUserId()
    const avatar = ref(null)
    const loading = ref(true)
    const error = ref(null)
    const showLevelUpModal = ref(false)
    const levels = ref([])
    
    const loadLevels = async () => {
      try {
        const response = await fetch('http://localhost:6090/avatar/levels')
        if (!response.ok) throw new Error('Erreur chargement niveaux')
        
        const data = await response.json()
        levels.value = Array.isArray(data) ? data : (data.levels || [])
        console.log('[AVATAR_DETAIL] Niveaux chargés:', levels.value.length)
      } catch (err) {
        console.error('[AVATAR_DETAIL] Erreur niveaux:', err)
      }
    }
    
    const loadAvatarDetails = async () => {
      try {
        loading.value = true
        error.value = null
        const userId = route.params.userId
        
        console.log('[AVATAR DETAIL] Chargement avatar user ID:', userId)
        
        const response = await fetch(`http://localhost:6090/avatar/users/${userId}/avatar`)
        
        if (!response.ok) {
          if (response.status === 404) {
            throw new Error('Utilisateur n\'a pas d\'avatar')
          }
          throw new Error(`Erreur HTTP: ${response.status}`)
        }
        
        const data = await response.json()
        console.log('[AVATAR DETAIL] Data reçue:', data)
        
        if (data.avatar) {
          avatar.value = data.avatar
        } else {
          throw new Error('Cet utilisateur n\'a pas encore sélectionné d\'avatar')
        }
      } catch (err) {
        console.error('[AVATAR DETAIL] Erreur:', err)
        error.value = err.message || 'Impossible de charger les détails de l\'avatar'
      } finally {
        loading.value = false
      }
    }
    
    const getNextLevelPoints = computed(() => {
      if (!avatar.value || levels.value.length === 0) return 100
      const nextLevelId = avatar.value.niveau_actuel + 1
      const nextLevel = levels.value.find(l => l.id_niveau === nextLevelId)
      return nextLevel ? nextLevel.points : avatar.value.current_points
    })
    
    const canLevelUp = computed(() => {
      if (!avatar.value) return false
      return avatar.value.current_points >= getNextLevelPoints.value && 
             avatar.value.niveau_actuel < 5
    })
    
    const isOwner = computed(() => {
      if (!currentUserId) return false
      return Number(route.params.userId) === Number(currentUserId)
    })
    
    const formatDate = (dateString) => {
      if (!dateString) return 'N/A'
      const date = new Date(dateString)
      return date.toLocaleDateString('fr-FR', { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
      })
    }

    const openLevelUpModal = () => {
      console.log('[AVATAR_DETAIL] Ouverture modale Level Up')
      showLevelUpModal.value = true
    }

    const handleLevelUpSuccess = async () => {
      console.log('[AVATAR_DETAIL] Level Up réussi, rechargement...')
      showLevelUpModal.value = false
      await loadAvatarDetails()
    }
    
    onMounted(async () => {
      await loadLevels()
      await loadAvatarDetails()
    })
    
    return {
      avatar,
      loading,
      error,
      showLevelUpModal,
      levels,
      getNextLevelPoints,
      canLevelUp,
      isOwner,
      formatDate,
      openLevelUpModal,
      handleLevelUpSuccess,
      route
    }
  }
}
</script>

<style scoped>
.avatar-detail-page {
  min-height: 100vh;
  background: var(--avatar-gradient);
  padding: 2rem 0;
}

.avatar-detail-card {
  background: var(--avatar-text-white);
  border-radius: var(--avatar-radius-xl);
  overflow: hidden;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
  max-width: 800px;
  margin: 0 auto;
}

.companion-header {
  background: var(--avatar-gradient);
  padding: var(--avatar-spacing-xxl) var(--avatar-spacing-lg);
  text-align: center;
  position: relative;
}

.companion-image {
  display: inline-block;
}

.avatar-image-detail {
  width: 250px;
  height: 250px;
  object-fit: contain;
  animation: float 3s ease-in-out infinite;
  filter: drop-shadow(0 6px 12px rgba(0, 0, 0, 0.3));
}

@keyframes float {
  0%, 100% {
    transform: translateY(0px);
  }
  50% {
    transform: translateY(-10px);
  }
}

.card-body-dark {
  background: var(--avatar-text-white);
  padding: var(--avatar-spacing-xxl) var(--avatar-spacing-lg);
}

.info-row {
  display: flex;
  align-items: center;
  gap: var(--avatar-spacing-md);
  padding: var(--avatar-spacing-md);
  margin-bottom: var(--avatar-spacing-sm);
  background: rgba(102, 126, 234, 0.08);
  border-radius: var(--avatar-radius-md);
  transition: all var(--avatar-transition-normal);
}

.info-row:hover {
  background: rgba(102, 126, 234, 0.12);
  transform: translateY(-2px);
}

.info-row:last-of-type {
  margin-bottom: 0;
}

.info-icon-wrapper {
  flex-shrink: 0;
  width: 50px;
  height: 50px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: var(--avatar-gradient);
  border-radius: var(--avatar-radius-md);
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.info-icon {
  font-size: 1.5rem;
}

.info-text {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.info-label-white {
  color: var(--avatar-text-medium);
  font-size: 0.85rem;
  font-weight: 500;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.info-value-white {
  color: var(--avatar-text-dark);
  font-size: 1.1rem;
  font-weight: 700;
}

.info-value-yellow {
  color: var(--avatar-primary);
  font-size: 1.1rem;
  font-weight: 700;
}

.action-button-wrapper {
  margin-top: var(--avatar-spacing-xl);
  text-align: center;
  display: flex;
  gap: var(--avatar-spacing-md);
  justify-content: center;
  flex-wrap: wrap;
}

.btn-level-up {
  flex: 1;
  min-width: 200px;
  max-width: 400px;
  padding: var(--avatar-spacing-md) var(--avatar-spacing-lg);
  background: var(--avatar-gradient);
  color: var(--avatar-text-white);
  border: none;
  border-radius: var(--avatar-radius-md);
  cursor: pointer;
  font-size: 1.1rem;
  font-weight: 600;
  transition: all var(--avatar-transition-normal);
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}

.btn-level-up:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: var(--avatar-shadow-primary);
}

.btn-level-up:disabled {
  background: linear-gradient(135deg, #95a5a6 0%, #7f8c8d 100%);
  cursor: not-allowed;
  opacity: 0.6;
  box-shadow: none;
}

.info-message {
  margin-top: var(--avatar-spacing-lg);
  padding: var(--avatar-spacing-md);
  background: rgba(255, 193, 7, 0.1);
  border: 2px solid rgba(255, 193, 7, 0.3);
  border-radius: var(--avatar-radius-md);
  text-align: center;
  color: #f39c12;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: var(--avatar-spacing-sm);
}

.info-icon-msg {
  font-size: 2.5rem;
}

.info-message p {
  margin: 0;
  font-weight: 600;
  font-size: 1rem;
  line-height: 1.5;
  color: var(--avatar-text-medium);
}

@media (max-width: 768px) {
  .card-body-dark {
    padding: var(--avatar-spacing-lg) var(--avatar-spacing-md);
  }
  
  .info-row {
    padding: var(--avatar-spacing-sm);
  }
  
  .info-icon-wrapper {
    width: 40px;
    height: 40px;
  }
  
  .info-icon {
    font-size: 1.25rem;
  }
  
  .info-label-white,
  .info-value-white,
  .info-value-yellow {
    font-size: 0.95rem;
  }

  .action-button-wrapper {
    flex-direction: column;
  }

  .btn-level-up {
    max-width: 100%;
  }
}
</style>
