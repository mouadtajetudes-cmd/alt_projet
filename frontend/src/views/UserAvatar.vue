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
/* === Styles Spécifiques à UserAvatar.vue === */

.user-avatar-page {
  min-height: 100vh;
  background: var(--avatar-gradient);
  padding: var(--avatar-spacing-lg) 0;
}

/* Header avec position relative pour le bouton back absolu */
.header {
  text-align: center;
  margin-bottom: var(--avatar-spacing-lg);
  color: var(--avatar-text-white);
  position: relative;
}

/* Bouton back avec backdrop-filter (version spécifique) */
.btn-back {
  position: absolute;
  left: 0;
  top: 0;
  display: inline-flex;
  align-items: center;
  gap: var(--avatar-spacing-xs);
  padding: 0.75rem var(--avatar-spacing-md);
  background: rgba(255, 255, 255, 0.2);
  color: var(--avatar-text-white);
  border-radius: var(--avatar-radius-md);
  text-decoration: none;
  font-weight: 600;
  transition: all var(--avatar-transition-normal);
  backdrop-filter: blur(10px);
}

.btn-back:hover {
  background: rgba(255, 255, 255, 0.3);
  transform: translateX(-5px);
}

/* Carte de lien rapide vers les niveaux */
.quick-link-card {
  background: var(--avatar-text-white);
  border-radius: var(--avatar-radius-lg);
  padding: var(--avatar-spacing-md);
  margin-bottom: var(--avatar-spacing-lg);
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: var(--avatar-spacing-md);
  box-shadow: var(--avatar-shadow-md);
  transition: all var(--avatar-transition-normal);
}

.quick-link-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
}

.quick-link-content {
  display: flex;
  align-items: center;
  gap: var(--avatar-spacing-sm);
  flex: 1;
}

.quick-link-icon {
  font-size: 3rem;
}

.quick-link-text h3 {
  margin: 0 0 0.25rem 0;
  color: var(--avatar-text-dark);
  font-size: 1.3rem;
}

.quick-link-text p {
  margin: 0;
  color: var(--avatar-text-medium);
  font-size: 0.9rem;
}

.btn-view-levels {
  padding: 0.875rem 1.75rem;
  background: var(--avatar-gradient);
  color: var(--avatar-text-white);
  border-radius: var(--avatar-radius-md);
  text-decoration: none;
  font-weight: 600;
  transition: all var(--avatar-transition-normal);
  white-space: nowrap;
  box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
}

.btn-view-levels:hover {
  transform: translateY(-2px);
  box-shadow: var(--avatar-shadow-primary);
}

/* Bouton de navigation vers la galerie */
.btn-browse {
  display: inline-block;
  margin-top: var(--avatar-spacing-md);
  padding: 0.875rem var(--avatar-spacing-lg);
  background: var(--avatar-gradient);
  color: var(--avatar-text-white);
  border-radius: var(--avatar-radius-md);
  text-decoration: none;
  font-weight: 600;
  transition: all var(--avatar-transition-normal);
}

.btn-browse:hover {
  transform: translateY(-2px);
  box-shadow: var(--avatar-shadow-primary);
}

/* Section contenant les avatars */
.avatars-section {
  background: var(--avatar-text-white);
  border-radius: var(--avatar-radius-xl);
  padding: var(--avatar-spacing-lg);
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
}

.section-header {
  margin-bottom: var(--avatar-spacing-lg);
}

.section-header h2 {
  font-size: 1.8rem;
  color: var(--avatar-text-dark);
  font-weight: 700;
}

/* Grille des avatars utilisateur */
.avatars-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
  gap: var(--avatar-spacing-md);
}

/* Carte avatar avec gradient spécifique */
.avatar-card {
  background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
  border-radius: var(--avatar-radius-lg);
  overflow: hidden;
  transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
  box-shadow: var(--avatar-shadow-sm);
}

.avatar-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 12px 32px rgba(102, 126, 234, 0.3);
}

/* Header de carte avec gradient et padding spécifiques */
.card-header {
  background: var(--avatar-gradient);
  padding: var(--avatar-spacing-lg) var(--avatar-spacing-md) var(--avatar-spacing-sm);
  position: relative;
  text-align: center;
}

/* Icône avatar avec taille et animation spécifiques */
.avatar-icon {
  font-size: 5rem;
  margin-bottom: var(--avatar-spacing-xs);
  filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.2));
  animation: float 3s ease-in-out infinite;
}

/* Badge de niveau */
.level-badge {
  display: inline-flex;
  align-items: center;
  gap: var(--avatar-spacing-xs);
  background: rgba(255, 255, 255, 0.2);
  backdrop-filter: blur(10px);
  padding: var(--avatar-spacing-xs) var(--avatar-spacing-sm);
  border-radius: 20px;
  color: var(--avatar-text-white);
  font-weight: 600;
}

.level-icon {
  font-size: 1.2rem;
}

/* Corps de carte avec padding spécifique */
.card-body {
  padding: var(--avatar-spacing-md);
  background: var(--avatar-text-white);
}

/* Nom d'avatar avec styles spécifiques */
.avatar-name {
  font-size: 1.4rem;
  font-weight: 700;
  color: var(--avatar-text-dark);
  margin-bottom: var(--avatar-spacing-sm);
  text-align: center;
}

/* Statistiques de l'avatar */
.avatar-stats {
  display: flex;
  justify-content: space-around;
  margin-bottom: var(--avatar-spacing-md);
  padding: var(--avatar-spacing-sm);
  background: rgba(102, 126, 234, var(--avatar-opacity-overlay));
  border-radius: var(--avatar-radius-md);
}

.stat-item {
  display: flex;
  align-items: center;
  gap: var(--avatar-spacing-xs);
}

.stat-icon {
  font-size: 1.3rem;
}

.stat-value {
  font-weight: 600;
  color: var(--avatar-primary);
}

/* Section de progression */
.progress-section {
  margin-bottom: var(--avatar-spacing-sm);
}

.progress-info {
  display: flex;
  justify-content: space-between;
  margin-bottom: var(--avatar-spacing-xs);
  font-size: 0.9rem;
  font-weight: 600;
  color: var(--avatar-text-medium);
}

.progress-percentage {
  color: var(--avatar-primary);
}

.progress-bar {
  height: 20px;
  background: #e9ecef;
  border-radius: 10px;
  overflow: hidden;
  box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);
  margin-bottom: var(--avatar-spacing-xs);
}

.progress-fill {
  height: 100%;
  background: var(--avatar-gradient);
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

/* Information de niveau */
.level-info {
  text-align: center;
  padding: 0.75rem;
  background: rgba(102, 126, 234, 0.1);
  border-radius: 10px;
}

.level-name {
  font-weight: 600;
  color: var(--avatar-primary);
  font-size: 1rem;
}

/* Footer de carte avec padding spécifique */
.card-footer {
  padding: 0 var(--avatar-spacing-md) var(--avatar-spacing-md);
  background: var(--avatar-text-white);
}

/* Media queries spécifiques à cette vue */
@media (max-width: 768px) {
  .header h1 {
    font-size: 2rem;
    margin-top: var(--avatar-spacing-xxl);
  }
  
  .btn-back {
    position: static;
    display: inline-flex;
    margin-bottom: var(--avatar-spacing-sm);
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
