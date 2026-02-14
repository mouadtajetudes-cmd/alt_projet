<template>
  <teleport to="body">
    <transition name="modal">
      <div v-if="show" class="modal-overlay" @click.self="closeModal">
        <div class="modal-container">
          <div class="modal-header">
            <h2>🎉 Montée de Niveau</h2>
            <button @click="closeModal" class="btn-close">&times;</button>
          </div>

          <div class="modal-body">
            <div class="avatar-preview">
              <div class="level-transition">
                <div class="level-current">
                  <div class="level-badge">{{ currentLevel }}</div>
                  <div class="level-label">Niveau actuel</div>
                </div>
                <div class="arrow-icon">→</div>
                <div class="level-next">
                  <div class="level-badge next">{{ nextLevel }}</div>
                  <div class="level-label">Nouveau niveau</div>
                </div>
              </div>
              
              <div class="avatar-icon-big">{{ avatarImage }}</div>
              <div class="avatar-name">{{ avatarName }}</div>
            </div>

            <div class="points-info">
              <div class="info-row">
                <span class="info-label">💎 Points actuels :</span>
                <span class="info-value">{{ currentPoints }}</span>
              </div>
              <div class="info-row">
                <span class="info-label">⭐ Points requis :</span>
                <span class="info-value">{{ requiredPoints }}</span>
              </div>
              <div class="info-row" :class="{ 'sufficient': hasSufficientPoints, 'insufficient': !hasSufficientPoints }">
                <span class="info-label">{{ hasSufficientPoints ? '✓' : '⚠️' }} Statut :</span>
                <span class="info-value">{{ hasSufficientPoints ? 'Points suffisants !' : 'Points insuffisants' }}</span>
              </div>
            </div>

            <div v-if="!hasSufficientPoints" class="warning-message">
              ⚠️ Vous avez besoin de <strong>{{ missingPoints }}</strong> points supplémentaires pour passer au niveau {{ nextLevel }}.
            </div>

            <div v-if="error" class="error-message">
              ⚠️ {{ error }}
            </div>

            <div v-if="success" class="success-message">
              <div class="confetti-animation">🎊</div>
              ✓ {{ success }}
              <div class="confetti-animation">🎊</div>
            </div>
          </div>

          <div class="modal-footer">
            <button @click="closeModal" class="btn-cancel" :disabled="loading">
              Annuler
            </button>
            <button 
              @click="confirmLevelUp" 
              class="btn-confirm" 
              :disabled="!hasSufficientPoints || loading || success"
            >
              <span v-if="loading" class="spinner-small"></span>
              <span v-else-if="success">✓ Terminé</span>
              <span v-else>🚀 Monter de niveau</span>
            </button>
          </div>
        </div>
      </div>
    </transition>
  </teleport>
</template>

<script>
import { ref, computed } from 'vue'

export default {
  name: 'LevelUpModal',
  props: {
    show: {
      type: Boolean,
      default: false
    },
    avatarId: {
      type: [String, Number],
      required: true
    },
    versionId: {
      type: [String, Number],
      default: null
    },
    avatarName: {
      type: String,
      default: 'Compagnon'
    },
    avatarImage: {
      type: String,
      default: '🦊'
    },
    currentLevel: {
      type: Number,
      default: 1
    },
    currentPoints: {
      type: Number,
      default: 0
    },
    requiredPoints: {
      type: Number,
      default: 100
    }
  },
  emits: ['close', 'success'],
  setup(props, { emit }) {
    const loading = ref(false)
    const error = ref(null)
    const success = ref(null)

    const nextLevel = computed(() => props.currentLevel + 1)
    
    const hasSufficientPoints = computed(() => {
      return props.currentPoints >= props.requiredPoints
    })
    
    const missingPoints = computed(() => {
      return Math.max(0, props.requiredPoints - props.currentPoints)
    })

    const closeModal = () => {
      if (!loading.value) {
        emit('close')
        setTimeout(() => {
          error.value = null
          success.value = null
        }, 300)
      }
    }

    const confirmLevelUp = async () => {
      if (!hasSufficientPoints.value) {
        error.value = 'Points insuffisants pour monter de niveau'
        return
      }

      const targetId = props.versionId || props.avatarId
      
      if (!targetId) {
        error.value = 'ID d\'avatar manquant'
        return
      }

      try {
        loading.value = true
        error.value = null
        success.value = null

        console.log('[LEVEL_UP] Montée niveau pour versionId:', targetId, 'vers niveau:', nextLevel.value)

        const response = await fetch(`http://localhost:6090/avatar/avatar-versions/${targetId}/level-up`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({
            new_level: nextLevel.value
          })
        })

        if (!response.ok) {
          const errorData = await response.json()
          throw new Error(errorData.message || `Erreur HTTP: ${response.status}`)
        }

        const result = await response.json()
        console.log('[LEVEL_UP] Succès:', result)

        success.value = `Félicitations ! Vous êtes maintenant niveau ${nextLevel.value} ! 🎉`
        
        triggerConfetti()
        
        setTimeout(() => {
          emit('success')
          closeModal()
        }, 2000)

      } catch (err) {
        console.error('[LEVEL_UP] Erreur:', err)
        error.value = err.message || 'Impossible de monter de niveau'
      } finally {
        loading.value = false
      }
    }

    const triggerConfetti = () => {
      console.log('[LEVEL_UP] 🎊 Confettis déclenché!')
    }

    return {
      loading,
      error,
      success,
      nextLevel,
      hasSufficientPoints,
      missingPoints,
      closeModal,
      confirmLevelUp
    }
  }
}
</script>

<style scoped>
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.7);
  backdrop-filter: blur(4px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
  padding: 1rem;
}

.modal-container {
  background: linear-gradient(180deg, #2c3e50 0%, #34495e 100%);
  border-radius: 20px;
  width: 100%;
  max-width: 550px;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
  overflow: hidden;
  animation: modalSlideUp 0.3s ease-out;
}

@keyframes modalSlideUp {
  from {
    opacity: 0;
    transform: translateY(50px) scale(0.9);
  }
  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

.modal-header {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  padding: 1.5rem 2rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.modal-header h2 {
  color: white;
  margin: 0;
  font-size: 1.5rem;
}

.btn-close {
  background: rgba(255, 255, 255, 0.2);
  border: none;
  color: white;
  font-size: 2rem;
  width: 40px;
  height: 40px;
  border-radius: 50%;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;
  line-height: 1;
  padding: 0;
}

.btn-close:hover {
  background: rgba(255, 255, 255, 0.3);
  transform: rotate(90deg);
}

.modal-body {
  padding: 2rem;
}

.avatar-preview {
  text-align: center;
  margin-bottom: 2rem;
}

.level-transition {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 1.5rem;
  margin-bottom: 1.5rem;
}

.level-current,
.level-next {
  text-align: center;
}

.level-badge {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  font-size: 1.5rem;
  font-weight: 700;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 0.5rem;
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}

.level-badge.next {
  background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
  animation: pulse 1.5s ease-in-out infinite;
}

@keyframes pulse {
  0%, 100% {
    transform: scale(1);
    box-shadow: 0 4px 12px rgba(245, 87, 108, 0.4);
  }
  50% {
    transform: scale(1.1);
    box-shadow: 0 6px 20px rgba(245, 87, 108, 0.6);
  }
}

.level-label {
  color: rgba(255, 255, 255, 0.7);
  font-size: 0.85rem;
}

.arrow-icon {
  font-size: 2rem;
  color: #667eea;
}

.avatar-icon-big {
  font-size: 5rem;
  margin: 1rem 0;
  filter: drop-shadow(0 10px 30px rgba(0, 0, 0, 0.3));
}

.avatar-name {
  color: white;
  font-size: 1.5rem;
  font-weight: 700;
}

.points-info {
  background: rgba(255, 255, 255, 0.05);
  border-radius: 12px;
  padding: 1.5rem;
  margin-bottom: 1rem;
}

.info-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.75rem 0;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.info-row:last-child {
  border-bottom: none;
}

.info-label {
  color: rgba(255, 255, 255, 0.8);
  font-weight: 600;
}

.info-value {
  color: white;
  font-weight: 700;
  font-size: 1.1rem;
}

.info-row.sufficient .info-value {
  color: #51cf66;
}

.info-row.insufficient .info-value {
  color: #ff6b6b;
}

.warning-message {
  background: rgba(255, 107, 107, 0.15);
  border: 2px solid #ff6b6b;
  color: #ff6b6b;
  padding: 1rem;
  border-radius: 12px;
  margin-bottom: 1rem;
  text-align: center;
  font-weight: 600;
}

.error-message {
  background: rgba(220, 53, 69, 0.15);
  border: 2px solid #dc3545;
  color: #ff6b6b;
  padding: 1rem;
  border-radius: 12px;
  margin-bottom: 1rem;
  text-align: center;
  font-weight: 600;
}

.success-message {
  background: rgba(40, 167, 69, 0.15);
  border: 2px solid #28a745;
  color: #51cf66;
  padding: 1rem;
  border-radius: 12px;
  margin-bottom: 1rem;
  text-align: center;
  font-weight: 600;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  animation: slideDown 0.4s ease-out;
}

.confetti-animation {
  font-size: 2rem;
  animation: bounce 0.6s ease-in-out infinite;
}

@keyframes bounce {
  0%, 100% {
    transform: translateY(0);
  }
  50% {
    transform: translateY(-10px);
  }
}

@keyframes slideDown {
  from {
    opacity: 0;
    transform: translateY(-20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.modal-footer {
  padding: 1.5rem 2rem;
  background: rgba(0, 0, 0, 0.2);
  display: flex;
  gap: 1rem;
}

.btn-cancel,
.btn-confirm {
  flex: 1;
  padding: 1rem 2rem;
  border: none;
  border-radius: 12px;
  font-size: 1rem;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
}

.btn-cancel {
  background: rgba(255, 255, 255, 0.1);
  color: white;
  border: 2px solid rgba(255, 255, 255, 0.3);
}

.btn-cancel:hover:not(:disabled) {
  background: rgba(255, 255, 255, 0.2);
}

.btn-confirm {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
}

.btn-confirm:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 10px 30px rgba(102, 126, 234, 0.5);
}

.btn-cancel:disabled,
.btn-confirm:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none;
}

.spinner-small {
  width: 20px;
  height: 20px;
  border: 3px solid rgba(255, 255, 255, 0.3);
  border-top-color: white;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}

@media (max-width: 600px) {
  .modal-container {
    max-width: 100%;
    margin: 0 1rem;
  }

  .level-transition {
    flex-direction: column;
    gap: 1rem;
  }

  .arrow-icon {
    transform: rotate(90deg);
  }

  .avatar-icon-big {
    font-size: 4rem;
  }

  .modal-footer {
    flex-direction: column;
  }
}
</style>
