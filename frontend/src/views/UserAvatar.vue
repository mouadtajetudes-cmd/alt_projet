<template>
  <div class="user-avatar-page">
    <div class="container">
      <div class="header">
        <router-link to="/avatar" class="btn-back">
          ← Retour à la galerie
        </router-link>
        <h1>🎭 Mes Avatars</h1>
        <p class="subtitle">Découvrez vos compagnons et leur progression</p>
      </div>

      <div class="quick-link-card">
        <div class="quick-link-content">
          <span class="quick-link-icon">🏆</span>
          <div class="quick-link-text">
            <h3>Système de Niveaux</h3>
            <p>Découvrez tous les niveaux disponibles et les points requis</p>
          </div>
        </div>
        <router-link to="/levels" class="btn-view-levels">
          Voir les niveaux →
        </router-link>
      </div>

      <div v-if="loading" class="loading">
        <div class="spinner"></div>
        <p>Chargement de vos avatars...</p>
      </div>

      <div v-else-if="error" class="error-state">
        <span class="error-icon">⚠️</span>
        <p>{{ error }}</p>
        <button @click="loadUserAvatars" class="btn-retry">Réessayer</button>
      </div>

      <div v-else-if="avatars.length === 0" class="empty-state">
        <span class="empty-icon">🎭</span>
        <h2>Aucun avatar trouvé</h2>
        <p>Vous n'avez pas encore de compagnon.</p>
        <router-link to="/avatar" class="btn-browse">
          Parcourir la galerie
        </router-link>
      </div>

      <div v-else class="avatars-section">
        <div class="section-header">
          <h2>Vos Compagnons ({{ avatars.length }})</h2>
        </div>
        
        <div class="avatars-grid">
          <div
            v-for="avatar in avatars" 
            :key="avatar.id_avatar"
            class="avatar-card"
          >
            <div class="card-header">
              <div class="avatar-icon">{{ avatar.image || '🦊' }}</div>
              <div class="level-badge">
                <span class="level-icon">⭐</span>
                <span class="level-text">Niveau {{ getCurrentLevel(avatar) }}</span>
              </div>
            </div>
            
            <div class="card-body">
              <h3 class="avatar-name">{{ avatar.nom }}</h3>
              <div class="avatar-stats">
                <div class="stat-item">
                  <span class="stat-icon">💎</span>
                  <span class="stat-value">{{ getCurrentPoints(avatar) }} pts</span>
                </div>
                <div class="stat-item">
                  <span class="stat-icon">📅</span>
                  <span class="stat-value">{{ formatDate(avatar.created_at) }}</span>
                </div>
              </div>

              <div class="progress-section">
                <div class="progress-info">
                  <span class="progress-label">Progression vers niveau {{ getNextLevel(avatar) }}</span>
                  <span class="progress-percentage">{{ getProgressPercentage(avatar) }}%</span>
                </div>
                <div class="progress-bar">
                  <div 
                    class="progress-fill" 
                    :style="{ width: getProgressPercentage(avatar) + '%' }"
                  ></div>
                </div>
                <div class="progress-details">
                  <span>{{ getPointsRemaining(avatar) }} points restants</span>
                </div>
              </div>

              <div class="level-info">
                <span class="level-name">{{ getCurrentLevelName(avatar) }}</span>
              </div>
            </div>
            
            <div class="card-footer">
              <router-link :to="`/avatar/${avatar.id_avatar}`" class="btn-details">
                Voir les détails →
              </router-link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuth } from '../composables/useAuth'

export default {
  name: 'UserAvatar',
  setup() {
    const router = useRouter()
    const { getUserId, initAuth, isAuthenticated } = useAuth()
    
    initAuth()
    
    const avatars = ref([])
    const levels = ref([])
    const loading = ref(true)
    const error = ref(null)
    
    const userId = getUserId()
    
    if (!userId || !isAuthenticated.value) {
      console.log('[USER_AVATAR] Utilisateur non connecté, redirection vers login')
      router.push('/login')
      return {
        avatars,
        levels,
        loading,
        error
      }
    }

    const loadLevels = async () => {
      try {
        const response = await fetch('http://localhost:6090/avatar/levels')
        if (!response.ok) {
          throw new Error(`Erreur HTTP: ${response.status}`)
        }
        const data = await response.json()
        
        if (Array.isArray(data)) {
          levels.value = data
        } else if (data.levels && Array.isArray(data.levels)) {
          levels.value = data.levels
        } else {
          levels.value = []
        }
        
        console.log('[USER_AVATAR] Niveaux chargés:', levels.value.length)
      } catch (err) {
        console.error('[USER_AVATAR] Erreur chargement niveaux:', err)
      }
    }

    const loadUserAvatars = async () => {
      try {
        loading.value = true
        error.value = null
        console.log(`[USER_AVATAR] Chargement des avatars de l'utilisateur ${userId}`)
        
        const response = await fetch(`http://localhost:6090/avatar/users/${userId}/avatars`)
        
        if (!response.ok) {
          if (response.status === 404) {
            avatars.value = []
            return
          }
          throw new Error(`Erreur HTTP: ${response.status}`)
        }
        
        const data = await response.json()
        console.log('[USER_AVATAR] Réponse API:', data)
        
        if (Array.isArray(data)) {
          avatars.value = data
        } else if (data.avatars && Array.isArray(data.avatars)) {
          avatars.value = data.avatars
        } else {
          avatars.value = []
        }
        
        console.log('[USER_AVATAR] Avatars chargés:', avatars.value.length)
        
      } catch (err) {
        console.error('[USER_AVATAR] Erreur chargement avatars:', err)
        error.value = 'Impossible de charger vos avatars. Vérifiez que le service est démarré.'
      } finally {
        loading.value = false
      }
    }

    const getCurrentPoints = (avatar) => {
      return avatar.points || 0
    }

    const getCurrentLevel = (avatar) => {
      const points = getCurrentPoints(avatar)
      if (levels.value.length === 0) return 1
      
      for (let i = levels.value.length - 1; i >= 0; i--) {
        if (points >= levels.value[i].points) {
          return levels.value[i].id_niveau
        }
      }
      return 1
    }

    const getCurrentLevelName = (avatar) => {
      const levelId = getCurrentLevel(avatar)
      const level = levels.value.find(l => l.id_niveau === levelId)
      return level ? level.nom : 'Débutant'
    }

    const getNextLevel = (avatar) => {
      return getCurrentLevel(avatar) + 1
    }

    const getNextLevelPoints = (avatar) => {
      const nextLevelId = getNextLevel(avatar)
      const nextLevel = levels.value.find(l => l.id_niveau === nextLevelId)
      return nextLevel ? nextLevel.points : getCurrentPoints(avatar)
    }

    const getPointsRemaining = (avatar) => {
      const current = getCurrentPoints(avatar)
      const next = getNextLevelPoints(avatar)
      return Math.max(0, next - current)
    }

    const getProgressPercentage = (avatar) => {
      if (levels.value.length === 0) return 0
      
      const currentLevelId = getCurrentLevel(avatar)
      const currentLevelData = levels.value.find(l => l.id_niveau === currentLevelId)
      const currentLevelPoints = currentLevelData ? currentLevelData.points : 0
      
      const points = getCurrentPoints(avatar)
      const nextPoints = getNextLevelPoints(avatar)
      
      const pointsInCurrentLevel = points - currentLevelPoints
      const pointsNeededForNext = nextPoints - currentLevelPoints
      
      if (pointsNeededForNext === 0) return 100
      
      return Math.min(100, Math.max(0, (pointsInCurrentLevel / pointsNeededForNext) * 100)).toFixed(0)
    }

    const formatDate = (dateString) => {
      if (!dateString) return 'Récent'
      const date = new Date(dateString)
      const options = { day: 'numeric', month: 'short', year: 'numeric' }
      return date.toLocaleDateString('fr-FR', options)
    }

    onMounted(async () => {
      await loadLevels()
      await loadUserAvatars()
    })

    return {
      avatars,
      loading,
      error,
      userId,
      loadUserAvatars,
      getCurrentLevel,
      getCurrentLevelName,
      getCurrentPoints,
      getNextLevel,
      getPointsRemaining,
      getProgressPercentage,
      formatDate
    }
  }
}
</script>

<style scoped>
.user-avatar-page {
  min-height: 100vh;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  padding: 2rem 0;
}

.container {
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 1.5rem;
}

.header {
  text-align: center;
  margin-bottom: 2rem;
  color: white;
  position: relative;
}

.btn-back {
  position: absolute;
  left: 0;
  top: 0;
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1.5rem;
  background: rgba(255, 255, 255, 0.2);
  color: white;
  border-radius: 12px;
  text-decoration: none;
  font-weight: 600;
  transition: all 0.3s ease;
  backdrop-filter: blur(10px);
}

.btn-back:hover {
  background: rgba(255, 255, 255, 0.3);
  transform: translateX(-5px);
}

.header h1 {
  font-size: 2.5rem;
  margin-bottom: 0.5rem;
  font-weight: 700;
  text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
}

.subtitle {
  font-size: 1.1rem;
  opacity: 0.9;
}

.quick-link-card {
  background: white;
  border-radius: 16px;
  padding: 1.5rem;
  margin-bottom: 2rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 1.5rem;
  box-shadow: 0 8px 32px rgba(0,0,0,0.1);
  transition: all 0.3s ease;
}

.quick-link-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 12px 40px rgba(0,0,0,0.15);
}

.quick-link-content {
  display: flex;
  align-items: center;
  gap: 1rem;
  flex: 1;
}

.quick-link-icon {
  font-size: 3rem;
}

.quick-link-text h3 {
  margin: 0 0 0.25rem 0;
  color: #333;
  font-size: 1.3rem;
}

.quick-link-text p {
  margin: 0;
  color: #666;
  font-size: 0.9rem;
}

.btn-view-levels {
  padding: 0.875rem 1.75rem;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border-radius: 12px;
  text-decoration: none;
  font-weight: 600;
  transition: all 0.3s ease;
  white-space: nowrap;
  box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
}

.btn-view-levels:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
}

.loading {
  text-align: center;
  padding: 4rem 2rem;
  color: white;
}

.spinner {
  width: 50px;
  height: 50px;
  border: 4px solid rgba(255,255,255,0.3);
  border-top-color: white;
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
}

.error-icon {
  font-size: 4rem;
  display: block;
  margin-bottom: 1rem;
}

.btn-retry {
  margin-top: 1.5rem;
  padding: 0.875rem 2rem;
  background: #667eea;
  color: white;
  border: none;
  border-radius: 12px;
  cursor: pointer;
  font-size: 1rem;
  font-weight: 600;
  transition: all 0.3s ease;
}

.btn-retry:hover {
  background: #5568d3;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.empty-state {
  text-align: center;
  padding: 4rem 2rem;
  background: white;
  border-radius: 16px;
  color: #666;
}

.empty-icon {
  font-size: 5rem;
  display: block;
  margin-bottom: 1rem;
  opacity: 0.5;
}

.empty-state h2 {
  color: #333;
  margin-bottom: 0.5rem;
}

.btn-browse {
  display: inline-block;
  margin-top: 1.5rem;
  padding: 0.875rem 2rem;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border-radius: 12px;
  text-decoration: none;
  font-weight: 600;
  transition: all 0.3s ease;
}

.btn-browse:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
}

.avatars-section {
  background: white;
  border-radius: 20px;
  padding: 2rem;
  box-shadow: 0 10px 40px rgba(0,0,0,0.15);
}

.section-header {
  margin-bottom: 2rem;
}

.section-header h2 {
  font-size: 1.8rem;
  color: #333;
  font-weight: 700;
}

.avatars-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
  gap: 1.5rem;
}

.avatar-card {
  background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
  border-radius: 16px;
  overflow: hidden;
  transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.avatar-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 12px 32px rgba(102, 126, 234, 0.3);
}

.card-header {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  padding: 2rem 1.5rem 1rem;
  position: relative;
  text-align: center;
}

.avatar-icon {
  font-size: 5rem;
  margin-bottom: 0.5rem;
  filter: drop-shadow(0 4px 8px rgba(0,0,0,0.2));
  animation: float 3s ease-in-out infinite;
}

@keyframes float {
  0%, 100% { transform: translateY(0px); }
  50% { transform: translateY(-10px); }
}

.level-badge {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  background: rgba(255, 255, 255, 0.2);
  backdrop-filter: blur(10px);
  padding: 0.5rem 1rem;
  border-radius: 20px;
  color: white;
  font-weight: 600;
}

.level-icon {
  font-size: 1.2rem;
}

.card-body {
  padding: 1.5rem;
  background: white;
}

.avatar-name {
  font-size: 1.4rem;
  font-weight: 700;
  color: #333;
  margin-bottom: 1rem;
  text-align: center;
}

.avatar-stats {
  display: flex;
  justify-content: space-around;
  margin-bottom: 1.5rem;
  padding: 1rem;
  background: rgba(102, 126, 234, 0.05);
  border-radius: 12px;
}

.stat-item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.stat-icon {
  font-size: 1.3rem;
}

.stat-value {
  font-weight: 600;
  color: #667eea;
}

.progress-section {
  margin-bottom: 1rem;
}

.progress-info {
  display: flex;
  justify-content: space-between;
  margin-bottom: 0.5rem;
  font-size: 0.9rem;
  font-weight: 600;
  color: #666;
}

.progress-percentage {
  color: #667eea;
}

.progress-bar {
  height: 20px;
  background: #e9ecef;
  border-radius: 10px;
  overflow: hidden;
  box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);
  margin-bottom: 0.5rem;
}

.progress-fill {
  height: 100%;
  background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
  border-radius: 10px;
  transition: width 0.8s ease;
  box-shadow: 0 2px 8px rgba(102, 126, 234, 0.4);
}

.progress-details {
  text-align: center;
  font-size: 0.85rem;
  color: #f5576c;
  font-weight: 600;
}

.level-info {
  text-align: center;
  padding: 0.75rem;
  background: rgba(102, 126, 234, 0.1);
  border-radius: 10px;
}

.level-name {
  font-weight: 600;
  color: #667eea;
  font-size: 1rem;
}

.card-footer {
  padding: 0 1.5rem 1.5rem;
  background: white;
}

.btn-details {
  display: block;
  width: 100%;
  padding: 0.875rem;
  background: white;
  color: #667eea;
  border: 2px solid #667eea;
  border-radius: 12px;
  cursor: pointer;
  font-size: 0.95rem;
  font-weight: 600;
  text-align: center;
  text-decoration: none;
  transition: all 0.3s ease;
}

.btn-details:hover {
  background: #667eea;
  color: white;
  transform: translateY(-2px);
}

@media (max-width: 768px) {
  .header h1 {
    font-size: 2rem;
    margin-top: 3rem;
  }
  
  .btn-back {
    position: static;
    display: inline-flex;
    margin-bottom: 1rem;
  }
  
  .quick-link-card {
    flex-direction: column;
    text-align: center;
  }
  
  .quick-link-content {
    flex-direction: column;
    text-align: center;
  }
  
  .avatars-grid {
    grid-template-columns: 1fr;
  }
}
</style>
