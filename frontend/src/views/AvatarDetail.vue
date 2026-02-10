<template>
  <div class="avatar-detail-page">
    <div class="container">
      <div class="back-button-wrapper">
        <router-link to="/avatar" class="btn-back">
          ← Retour à la galerie
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
            <div class="avatar-icon-large">🎭</div>
          </div>
        </div>

        <div class="card-body-dark">
          <div class="info-row">
            <div class="info-icon-wrapper">
              <span class="info-icon">👥</span>
            </div>
            <div class="info-text">
              <span class="info-label-white">Votre compagnon :</span>
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
              <span class="info-label-white">Nom de votre compagnon:</span>
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

          <div class="action-button-wrapper">
            <button @click="customizeAvatar" class="btn-customize">
              ✏️ Personnaliser
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'

export default {
  name: 'AvatarDetail',
  setup() {
    const route = useRoute()
    const router = useRouter()
    const avatar = ref(null)
    const loading = ref(true)
    const error = ref(null)
    const isPremium = ref(false)
    
    const loadAvatarDetails = async () => {
      try {
        loading.value = true
        error.value = null
        const avatarId = route.params.id
        
        console.log('[AVATAR DETAIL] Chargement avatar ID:', avatarId)
        
        const response = await fetch(`http://localhost:6083/avatars/${avatarId}`)
        
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
    
    const calculatePointsToNextLevel = () => {
      if (!avatar.value) return 0
      
      const levelThresholds = {
        1: 100,   // Débutant → Intermédiaire
        2: 250,   // Intermédiaire → Avancé
        3: 500,   // Avancé → Expert
        4: 1000,  // Expert → Maître
        5: 1000   // Maître (niveau max)
      }
      
      const currentLevel = avatar.value.niveau || 1
      const currentPoints = avatar.value.points || 0
      const nextLevelThreshold = levelThresholds[currentLevel] || 1000
      
      return Math.max(0, nextLevelThreshold - currentPoints)
    }
    
    const customizeAvatar = () => {
      console.log('[AVATAR DETAIL] Personnaliser avatar:', avatar.value.id_avatar)
    }
    
    onMounted(() => {
      loadAvatarDetails()
    })
    
    return {
      avatar,
      loading,
      error,
      isPremium,
      calculatePointsToNextLevel,
      customizeAvatar
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
}

.btn-customize {
  width: 100%;
  max-width: 400px;
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

.btn-customize:active {
  transform: translateY(0);
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
}
</style>
