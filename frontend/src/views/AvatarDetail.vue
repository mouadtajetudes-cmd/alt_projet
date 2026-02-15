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
            <div class="avatar-icon-large">{{ avatar.image || '🦊' }}</div>
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
              <span class="info-value-white">{{ avatar.niveau }}</span>
            </div>
          </div>

          <div class="info-row">
            <div class="info-icon-wrapper">
              <span class="info-icon">📈</span>
            </div>
            <div class="info-text">
              <span class="info-label-white">Niveau suivant dans:</span>
              <span class="info-value-yellow">{{ calculatePointsToNextLevel() }} Points</span>
            </div>
          </div>

          <div class="info-row">
            <div class="info-icon-wrapper">
              <span class="info-icon">👤</span>
            </div>
            <div class="info-text">
              <span class="info-label-white">{{ isOwner ? 'Nom de votre compagnon:' : 'Surnom:' }}</span>
              <span class="info-value-yellow">{{ avatar.surnom || 'Non défini' }}</span>
            </div>
          </div>

          <div class="info-row">
            <div class="info-icon-wrapper">
              <span class="info-icon">⭐</span>
            </div>
            <div class="info-text">
              <span class="info-label-white">Points Actuels:</span>
              <span class="info-value-yellow">{{ avatar.points }} Points</span>
            </div>
          </div>

          <div class="info-row">
            <div class="info-icon-wrapper">
              <span class="info-icon">💎</span>
            </div>
            <div class="info-text">
              <span class="info-label-white">Premium:</span>
              <span class="info-value-yellow">{{ isPremium ? 'Oui' : 'Non' }}</span>
            </div>
          </div>

          <div v-if="isOwner" class="action-button-wrapper">
            <button @click="customizeAvatar" class="btn-customize">
              ✏️ Personnaliser
            </button>
            <button @click="openLevelUpModal" class="btn-level-up" :disabled="!canLevelUp">
              🚀 Level Up
            </button>
          </div>
          
          <div v-else class="info-message">
            <span class="info-icon-msg">🔒</span>
            <p>Ceci n'est pas votre avatar. Seul le propriétaire peut le personnaliser.</p>
          </div>
        </div>
      </div>
    </div>

    <LevelUpModal
      :show="showLevelUpModal"
      :avatar-id="avatar?.id_avatar"
      :version-id="avatar?.id_avatar_version"
      :avatar-name="avatar?.nom"
      :avatar-image="avatar?.image || '🦊'"
      :current-level="currentLevel"
      :current-points="currentPoints"
      :required-points="requiredPointsForNextLevel"
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
    const isPremium = ref(false)
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
        const avatarId = route.params.id
        
        console.log('[AVATAR DETAIL] Chargement avatar ID:', avatarId)
        
        const response = await fetch(`http://localhost:6090/avatar/avatars/${avatarId}`)
        
        if (!response.ok) {
          if (response.status === 404) {
            throw new Error('Avatar non trouvé')
          }
          throw new Error(`Erreur HTTP: ${response.status}`)
        }
        
        const data = await response.json()
        console.log('[AVATAR DETAIL] Avatar chargé:', data)
        avatar.value = data
      } catch (err) {
        console.error('[AVATAR DETAIL] Erreur:', err)
        error.value = err.message || 'Impossible de charger les détails de l\'avatar'
      } finally {
        loading.value = false
      }
    }
    
    const currentLevel = computed(() => {
      if (!avatar.value || levels.value.length === 0) return 1
      const points = currentPoints.value
      
      for (let i = levels.value.length - 1; i >= 0; i--) {
        if (points >= levels.value[i].points) {
          return levels.value[i].id_niveau
        }
      }
      return 1
    })

    const currentPoints = computed(() => {
      return avatar.value?.points || 0
    })

    const requiredPointsForNextLevel = computed(() => {
      if (levels.value.length === 0) return 100
      const nextLevelId = currentLevel.value + 1
      const nextLevel = levels.value.find(l => l.id_niveau === nextLevelId)
      return nextLevel ? nextLevel.points : currentPoints.value
    })

    const canLevelUp = computed(() => {
      return currentPoints.value >= requiredPointsForNextLevel.value && 
             currentLevel.value < 5 // Niveau max = 5
    })
    
    const isOwner = computed(() => {
      if (!avatar.value || !currentUserId) return false
      return avatar.value.id_utilisateur && currentUserId == avatar.value.id_utilisateur
    })

    const calculatePointsToNextLevel = () => {
      return Math.max(0, requiredPointsForNextLevel.value - currentPoints.value)
    }
    
    const customizeAvatar = () => {
      console.log('[AVATAR DETAIL] Redirection vers édition avatar:', avatar.value.id_avatar)
      router.push(`/avatar/${avatar.value.id_avatar}/edit`)
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
      isPremium,
      showLevelUpModal,
      currentLevel,
      currentPoints,
      requiredPointsForNextLevel,
      canLevelUp,
      isOwner,
      calculatePointsToNextLevel,
      customizeAvatar,
      openLevelUpModal,
      handleLevelUpSuccess
    }
  }
}
</script>

<style scoped>
.avatar-detail-page {
  min-height: 100vh;
  background: #f5f5f5;
  padding: 2rem 0;
}

.container {
  max-width: 700px;
  margin: 0 auto;
  padding: 0 1.5rem;
}

.back-button-wrapper {
  margin-bottom: 2rem;
}

.btn-back {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1.5rem;
  background: white;
  color: #333;
  text-decoration: none;
  border-radius: 12px;
  font-weight: 600;
  transition: all 0.3s ease;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.btn-back:hover {
  background: #f8f9fa;
  transform: translateX(-5px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.loading {
  text-align: center;
  padding: 4rem 2rem;
  background: white;
  border-radius: 16px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.spinner {
  width: 50px;
  height: 50px;
  border: 4px solid #e9ecef;
  border-top-color: #667eea;
  border-radius: 50%;
  margin: 0 auto 1rem;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.error-state {
  text-align: center;
  padding: 4rem 2rem;
  background: white;
  border-radius: 16px;
  color: #dc3545;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.error-icon {
  font-size: 4rem;
  display: block;
  margin-bottom: 1rem;
}

.btn-retry {
  display: inline-block;
  margin-top: 1.5rem;
  padding: 0.875rem 2rem;
  background: #667eea;
  color: white;
  text-decoration: none;
  border-radius: 12px;
  font-weight: 600;
  transition: all 0.3s ease;
}

.btn-retry:hover {
  background: #5568d3;
  transform: translateY(-2px);
}

.avatar-detail-card {
  background: white;
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 4px 16px rgba(0,0,0,0.1);
}

.companion-header {
  background: linear-gradient(180deg, #6b8e94 0%, #4a6670 100%);
  padding: 3rem 2rem 2rem;
  text-align: center;
  position: relative;
}

.companion-image {
  display: inline-block;
}

.avatar-icon-large {
  font-size: 10rem;
  filter: drop-shadow(0 8px 16px rgba(0,0,0,0.3));
  animation: float 3s ease-in-out infinite;
}

@keyframes float {
  0%, 100% { transform: translateY(0px); }
  50% { transform: translateY(-15px); }
}

.card-body-dark {
  background: linear-gradient(180deg, #2c3e50 0%, #1a252f 100%);
  padding: 2.5rem 2rem;
}

.info-row {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1rem 0;
  border-bottom: 1px solid rgba(255,255,255,0.1);
}

.info-row:last-of-type {
  border-bottom: none;
}

.info-icon-wrapper {
  flex-shrink: 0;
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(255,255,255,0.1);
  border-radius: 8px;
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
  color: white;
  font-size: 0.95rem;
  font-weight: 500;
}

.info-value-white {
  color: white;
  font-size: 1.1rem;
  font-weight: 700;
}

.info-value-yellow {
  color: #f9ca24;
  font-size: 1.1rem;
  font-weight: 700;
}

.action-button-wrapper {
  margin-top: 2rem;
  text-align: center;
  display: flex;
  gap: 1rem;
  justify-content: center;
  flex-wrap: wrap;
}

.btn-customize {
  flex: 1;
  min-width: 180px;
  max-width: 300px;
  padding: 1rem 2rem;
  background: #95a5a6;
  color: white;
  border: none;
  border-radius: 12px;
  cursor: pointer;
  font-size: 1.1rem;
  font-weight: 600;
  transition: all 0.3s ease;
  box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}

.btn-customize:hover {
  background: #7f8c8d;
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(0,0,0,0.3);
}

.btn-level-up {
  flex: 1;
  min-width: 180px;
  max-width: 300px;
  padding: 1rem 2rem;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border: none;
  border-radius: 12px;
  cursor: pointer;
  font-size: 1.1rem;
  font-weight: 600;
  transition: all 0.3s ease;
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}

.btn-level-up:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
}

.btn-level-up:disabled {
  background: linear-gradient(135deg, #95a5a6 0%, #7f8c8d 100%);
  cursor: not-allowed;
  opacity: 0.6;
  box-shadow: none;
}

.btn-customize:active {
  transform: translateY(0);
}

.info-message {
  margin-top: 1.5rem;
  padding: 1.25rem;
  background: rgba(255, 193, 7, 0.15);
  border: 2px solid rgba(255, 193, 7, 0.5);
  border-radius: 12px;
  text-align: center;
  color: #ffc107;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.75rem;
}

.info-icon-msg {
  font-size: 2.5rem;
}

.info-message p {
  margin: 0;
  font-weight: 600;
  font-size: 1rem;
  line-height: 1.5;
}

@media (max-width: 768px) {
  .avatar-icon-large {
    font-size: 7rem;
  }
  
  .card-body-dark {
    padding: 2rem 1.5rem;
  }
  
  .info-row {
    padding: 0.875rem 0;
  }
  
  .info-icon-wrapper {
    width: 35px;
    height: 35px;
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

  .btn-customize,
  .btn-level-up {
    max-width: 100%;
  }
}
</style>
