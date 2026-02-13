<template>
  <div class="levels-page">
    <div class="container">
      <div class="header">
        <h1>🏆 Système de Niveaux</h1>
        <p class="subtitle">Progressez et débloquez de nouvelles récompenses</p>
      </div>

      <div class="current-progress-card">
        <div class="progress-header">
          <div class="level-badge">
            <span class="badge-icon">⭐</span>
            <span class="badge-label">Niveau Actuel</span>
            <span class="badge-value">{{ currentLevel }}</span>
          </div>
          <div class="points-display">
            <span class="points-icon">💎</span>
            <span class="points-value">{{ currentPoints }} points</span>
          </div>
        </div>

        <div class="progress-bar-container">
          <div class="progress-info">
            <span>Progression vers le niveau {{ nextLevel }}</span>
            <span class="progress-percentage">{{ progressPercentage }}%</span>
          </div>
          <div class="progress-bar">
            <div 
              class="progress-fill" 
              :style="{ width: progressPercentage + '%' }"
            ></div>
          </div>
          <div class="progress-details">
            <span>{{ currentPoints }} / {{ nextLevelPoints }} points</span>
            <span class="points-remaining">{{ pointsRemaining }} points restants</span>
          </div>
        </div>
      </div>

      <div v-if="loading" class="loading">
        <div class="spinner"></div>
        <p>Chargement des niveaux...</p>
      </div>

      <div v-else-if="error" class="error-state">
        <span class="error-icon">⚠️</span>
        <p>{{ error }}</p>
        <button @click="loadLevels" class="btn-retry">Réessayer</button>
      </div>

      <div v-else class="levels-table-card">
        <h2 class="table-title">📊 Tous les Niveaux</h2>
        
        <div class="table-wrapper">
          <table class="levels-table">
            <thead>
              <tr>
                <th>Badge</th>
                <th>Niveau</th>
                <th>Nom</th>
                <th>Points Requis</th>
                <th>Statut</th>
              </tr>
            </thead>
            <tbody>
              <tr 
                v-for="level in levels" 
                :key="level.id_niveau"
                :class="{ 
                  'current-level': level.id_niveau === currentLevel,
                  'unlocked': level.id_niveau < currentLevel,
                  'locked': level.id_niveau > currentLevel
                }"
              >
                <td class="badge-cell">
                  <span class="level-badge-icon" :class="getBadgeClass(level.numero)">
                    {{ getBadgeEmoji(level.numero) }}
                  </span>
                </td>
                <td class="level-number">
                  <strong>Niveau {{ level.id_niveau }}</strong>
                </td>
                <td class="level-name">{{ level.nom }}</td>
                <td class="points-required">
                  <span class="points-badge">💎 {{ level.points }} pts</span>
                </td>
                <td class="status-cell">
                  <span 
                    class="status-badge" 
                    :class="getStatusClass(level.id_niveau)"
                  >
                    {{ getStatusLabel(level.id_niveau) }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="levels-summary">
          <div class="summary-item">
            <span class="summary-icon">🎯</span>
            <span class="summary-label">Niveaux débloqués</span>
            <span class="summary-value">{{ unlockedLevels }} / {{ levels.length }}</span>
          </div>
          <div class="summary-item">
            <span class="summary-icon">🚀</span>
            <span class="summary-label">Prochain objectif</span>
            <span class="summary-value">{{ nextLevelPoints }} points</span>
          </div>
          <div class="summary-item">
            <span class="summary-icon">⚡</span>
            <span class="summary-label">Total points</span>
            <span class="summary-value">{{ currentPoints }} points</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue'

export default {
  name: 'Levels',
  setup() {
    const levels = ref([])
    const loading = ref(true)
    const error = ref(null)
    
    // TODO: Récupérer les points actuels de l'utilisateur 
    const currentPoints = ref(0) 
    const currentLevel = ref(1)  

    const loadLevels = async () => {
      try {
        loading.value = true
        error.value = null
        console.log('[LEVELS] Chargement des niveaux depuis http://localhost:6090/avatar/levels')
        
        const response = await fetch('http://localhost:6090/avatar/levels')
        
        if (!response.ok) {
          throw new Error(`Erreur HTTP: ${response.status}`)
        }
        
        const data = await response.json()
        console.log('[LEVELS] Réponse API:', data)
        
        if (Array.isArray(data)) {
          levels.value = data
          console.log('[LEVELS] Niveaux chargés:', data.length)
        } else if (data.levels && Array.isArray(data.levels)) {
          levels.value = data.levels
          console.log('[LEVELS] Niveaux chargés:', data.levels.length)
        } else {
          console.error('[LEVELS] Format de réponse invalide:', data)
          levels.value = []
        }

        updateCurrentLevel()
        
      } catch (err) {
        console.error('[LEVELS] Erreur chargement niveaux:', err)
        error.value = 'Impossible de charger les niveaux. Vérifiez que le service est démarré.'
      } finally {
        loading.value = false
      }
    }

    const updateCurrentLevel = () => {
      if (!Array.isArray(levels.value) || levels.value.length === 0) return
      
      for (let i = levels.value.length - 1; i >= 0; i--) {
        if (currentPoints.value >= levels.value[i].points) {
          currentLevel.value = levels.value[i].id_niveau
          break
        }
      }
    }

    const nextLevel = computed(() => {
      return currentLevel.value + 1
    })

    const nextLevelPoints = computed(() => {
      if (!Array.isArray(levels.value)) return currentPoints.value
      const next = levels.value.find(l => l.id_niveau === nextLevel.value)
      return next ? next.points : currentPoints.value
    })

    const pointsRemaining = computed(() => {
      return Math.max(0, nextLevelPoints.value - currentPoints.value)
    })

    const progressPercentage = computed(() => {
      if (!Array.isArray(levels.value) || levels.value.length === 0) return 0
      
      const currentLevelData = levels.value.find(l => l.id_niveau === currentLevel.value)
      const currentLevelPoints = currentLevelData ? currentLevelData.points : 0
      
      const pointsInCurrentLevel = currentPoints.value - currentLevelPoints
      const pointsNeededForNext = nextLevelPoints.value - currentLevelPoints
      
      if (pointsNeededForNext === 0) return 100
      
      return Math.min(100, Math.max(0, (pointsInCurrentLevel / pointsNeededForNext) * 100)).toFixed(1)
    })

    const unlockedLevels = computed(() => {
      if (!Array.isArray(levels.value)) return 0
      return levels.value.filter(l => l.id_niveau <= currentLevel.value).length
    })

    const getBadgeEmoji = (levelNumber) => {
      const badges = {
        1: '🥉',
        2: '🥈',
        3: '🥇',
        4: '🏅',
        5: '🎖️',
        6: '👑',
        7: '💎',
        8: '⭐',
        9: '🌟',
        10: '✨'
      }
      return badges[levelNumber] || '🎯'
    }

    const getBadgeClass = (levelNumber) => {
      if (levelNumber === currentLevel.value) return 'current'
      if (levelNumber < currentLevel.value) return 'unlocked'
      return 'locked'
    }

    const getStatusLabel = (levelNumber) => {
      if (levelNumber === currentLevel.value) return '✓ Actuel'
      if (levelNumber < currentLevel.value) return '✓ Débloqué'
      return '🔒 Verrouillé'
    }

    const getStatusClass = (levelNumber) => {
      if (levelNumber === currentLevel.value) return 'current'
      if (levelNumber < currentLevel.value) return 'unlocked'
      return 'locked'
    }

    onMounted(() => {
      loadLevels()
    })

    return {
      levels,
      loading,
      error,
      currentPoints,
      currentLevel,
      nextLevel,
      nextLevelPoints,
      pointsRemaining,
      progressPercentage,
      unlockedLevels,
      loadLevels,
      getBadgeEmoji,
      getBadgeClass,
      getStatusLabel,
      getStatusClass
    }
  }
}
</script>

<style scoped>
.levels-page {
  min-height: 100vh;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  padding: 2rem 0;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 1.5rem;
}

.header {
  text-align: center;
  margin-bottom: 3rem;
  color: white;
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

.current-progress-card {
  background: white;
  border-radius: 20px;
  padding: 2rem;
  margin-bottom: 2rem;
  box-shadow: 0 10px 40px rgba(0,0,0,0.15);
}

.progress-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
  flex-wrap: wrap;
  gap: 1rem;
}

.level-badge {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  padding: 1rem 1.5rem;
  border-radius: 15px;
  color: white;
  box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
}

.badge-icon {
  font-size: 2rem;
}

.badge-label {
  font-size: 0.9rem;
  opacity: 0.9;
}

.badge-value {
  font-size: 1.8rem;
  font-weight: 700;
  margin-left: 0.5rem;
}

.points-display {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
  padding: 1rem 1.5rem;
  border-radius: 15px;
  color: white;
  box-shadow: 0 4px 15px rgba(245, 87, 108, 0.3);
}

.points-icon {
  font-size: 1.5rem;
}

.points-value {
  font-size: 1.3rem;
  font-weight: 700;
}

.progress-bar-container {
  background: rgba(102, 126, 234, 0.05);
  border-radius: 15px;
  padding: 1.5rem;
}

.progress-info {
  display: flex;
  justify-content: space-between;
  margin-bottom: 0.75rem;
  font-weight: 600;
  color: #333;
}

.progress-percentage {
  color: #667eea;
  font-size: 1.1rem;
}

.progress-bar {
  height: 30px;
  background: #e9ecef;
  border-radius: 15px;
  overflow: hidden;
  box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);
  margin-bottom: 0.75rem;
}

.progress-fill {
  height: 100%;
  background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
  border-radius: 15px;
  transition: width 0.8s ease;
  box-shadow: 0 2px 8px rgba(102, 126, 234, 0.4);
}

.progress-details {
  display: flex;
  justify-content: space-between;
  font-size: 0.9rem;
  color: #666;
}

.points-remaining {
  color: #f5576c;
  font-weight: 600;
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

.levels-table-card {
  background: white;
  border-radius: 20px;
  padding: 2rem;
  box-shadow: 0 10px 40px rgba(0,0,0,0.15);
}

.table-title {
  font-size: 1.8rem;
  color: #333;
  margin-bottom: 1.5rem;
  font-weight: 700;
}

.table-wrapper {
  overflow-x: auto;
  margin-bottom: 2rem;
}

.levels-table {
  width: 100%;
  border-collapse: collapse;
}

.levels-table thead {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
}

.levels-table th {
  padding: 1rem;
  text-align: left;
  font-weight: 600;
  font-size: 0.95rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.levels-table tbody tr {
  border-bottom: 1px solid #e9ecef;
  transition: all 0.3s ease;
}

.levels-table tbody tr:hover {
  background: rgba(102, 126, 234, 0.05);
  transform: scale(1.01);
}

.levels-table tbody tr.current-level {
  background: rgba(102, 126, 234, 0.1);
  border-left: 4px solid #667eea;
}

.levels-table tbody tr.unlocked {
  opacity: 0.8;
}

.levels-table tbody tr.locked {
  opacity: 0.5;
}

.levels-table td {
  padding: 1.25rem 1rem;
}

.badge-cell {
  text-align: center;
}

.level-badge-icon {
  font-size: 2rem;
  display: inline-block;
  transition: transform 0.3s ease;
}

.level-badge-icon.current {
  animation: pulse 2s ease-in-out infinite;
}

.level-badge-icon.unlocked {
  filter: grayscale(0);
}

.level-badge-icon.locked {
  filter: grayscale(1);
  opacity: 0.5;
}

@keyframes pulse {
  0%, 100% { transform: scale(1); }
  50% { transform: scale(1.2); }
}

.level-number {
  color: #333;
  font-size: 1rem;
}

.level-name {
  color: #666;
  font-size: 1rem;
}

.points-required {
  font-weight: 600;
}

.points-badge {
  display: inline-block;
  padding: 0.5rem 1rem;
  background: rgba(102, 126, 234, 0.1);
  border-radius: 20px;
  color: #667eea;
  font-size: 0.9rem;
}

.status-cell {
  text-align: center;
}

.status-badge {
  display: inline-block;
  padding: 0.5rem 1rem;
  border-radius: 20px;
  font-size: 0.85rem;
  font-weight: 600;
}

.status-badge.current {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
}

.status-badge.unlocked {
  background: #d4edda;
  color: #155724;
}

.status-badge.locked {
  background: #f8d7da;
  color: #721c24;
}

.levels-summary {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1.5rem;
  margin-top: 2rem;
  padding-top: 2rem;
  border-top: 2px solid #e9ecef;
}

.summary-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.5rem;
  padding: 1.5rem;
  background: rgba(102, 126, 234, 0.05);
  border-radius: 15px;
  transition: all 0.3s ease;
}

.summary-item:hover {
  background: rgba(102, 126, 234, 0.1);
  transform: translateY(-3px);
}

.summary-icon {
  font-size: 2rem;
}

.summary-label {
  font-size: 0.9rem;
  color: #666;
  font-weight: 500;
}

.summary-value {
  font-size: 1.3rem;
  color: #667eea;
  font-weight: 700;
}

@media (max-width: 768px) {
  .header h1 {
    font-size: 2rem;
  }
  
  .progress-header {
    flex-direction: column;
  }
  
  .level-badge,
  .points-display {
    width: 100%;
    justify-content: center;
  }
  
  .table-wrapper {
    overflow-x: scroll;
  }
  
  .levels-table {
    min-width: 600px;
  }
  
  .levels-summary {
    grid-template-columns: 1fr;
  }
}
</style>
