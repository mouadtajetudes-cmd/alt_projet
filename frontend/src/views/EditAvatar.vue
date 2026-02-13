<template>
  <div class="edit-avatar-page">
    <div class="container">
      <div class="back-button-wrapper">
        <router-link :to="`/avatar/${avatarId}`" class="btn-back">
          ← Retour aux détails
        </router-link>
      </div>

      <div v-if="initialLoading" class="loading">
        <div class="spinner"></div>
        <p>Chargement de l'avatar...</p>
      </div>

      <div v-else-if="loadError" class="error-state">
        <span class="error-icon">⚠️</span>
        <p>{{ loadError }}</p>
        <router-link to="/avatar" class="btn-back-gallery">
          Retour à la galerie
        </router-link>
      </div>

      <div v-else class="avatar-form-card">
        <div class="avatar-preview">
          <div class="avatar-display">
            <div class="avatar-icon-3d">{{ formData.image || '🦊' }}</div>
          </div>
          <div class="preview-name">{{ formData.nom || 'Compagnon' }}</div>
        </div>

        <form @submit.prevent="handleSubmit" class="avatar-form">
          <div class="form-header">
            <h2>✏️ Modifier l'avatar</h2>
            <p class="form-subtitle">Personnalisez votre compagnon</p>
          </div>

          <div class="form-field">
            <div class="field-icon">📝</div>
            <div class="field-content">
              <label class="field-label">Nom :</label>
              <input 
                v-model="formData.nom" 
                type="text" 
                class="field-input" 
                placeholder="Nom du compagnon"
                maxlength="255"
                required
              >
              <span class="field-hint">{{ formData.nom.length }}/255 caractères</span>
            </div>
          </div>

          <div class="form-field">
            <div class="field-icon">🎨</div>
            <div class="field-content">
              <label class="field-label">Icône :</label>
              <select v-model="formData.image" class="field-select" required>
                <option value="">Sélectionner...</option>
                <option value="🦊">🦊 Renard</option>
                <option value="🐱">🐱 Chat</option>
                <option value="🐶">🐶 Chien</option>
                <option value="🐺">🐺 Loup</option>
                <option value="🐻">🐻 Ours</option>
                <option value="🐰">🐰 Lapin</option>
                <option value="🐼">🐼 Panda</option>
                <option value="🦁">🦁 Lion</option>
                <option value="🦅">🦅 Aigle</option>
                <option value="🐉">🐉 Dragon</option>
                <option value="🦄">🦄 Licorne</option>
                <option value="🐢">🐢 Tortue</option>
              </select>
            </div>
          </div>

          <div v-if="error" class="error-message">
            ⚠️ {{ error }}
          </div>

          <div v-if="success" class="success-message">
            ✓ {{ success }}
          </div>

          <div class="form-actions">
            <router-link :to="`/avatar/${avatarId}`" class="btn-cancel">
              Annuler
            </router-link>
            <button 
              type="submit" 
              class="btn-save" 
              :disabled="saving || !isFormValid || !hasChanges"
            >
              <span v-if="saving" class="spinner-small"></span>
              <span v-else>💾 Enregistrer</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'

export default {
  name: 'EditAvatar',
  setup() {
    const router = useRouter()
    const route = useRoute()
    
    const avatarId = computed(() => route.params.id)
    
    const initialLoading = ref(true)
    const saving = ref(false)
    const loadError = ref(null)
    const error = ref(null)
    const success = ref(null)
    
    const originalData = ref(null)
    const formData = ref({
      nom: '',
      image: ''
    })

    const loadAvatar = async () => {
      try {
        initialLoading.value = true
        loadError.value = null
        
        console.log(`[EDIT_AVATAR] Chargement de l'avatar ${avatarId.value}`)
        
        const response = await fetch(`http://localhost:6090/avatar/avatars/${avatarId.value}`)
        
        if (!response.ok) {
          if (response.status === 404) {
            throw new Error('Avatar introuvable')
          }
          throw new Error(`Erreur HTTP: ${response.status}`)
        }
        
        const data = await response.json()
        console.log('[EDIT_AVATAR] Avatar chargé:', data)
        
        originalData.value = {
          nom: data.nom || '',
          image: data.image || '🦊'
        }
        
        formData.value = {
          nom: data.nom || '',
          image: data.image || '🦊'
        }
        
      } catch (err) {
        console.error('[EDIT_AVATAR] Erreur chargement:', err)
        loadError.value = err.message || 'Impossible de charger l\'avatar'
      } finally {
        initialLoading.value = false
      }
    }

    const isFormValid = computed(() => {
      return formData.value.nom.trim().length >= 1 && formData.value.image
    })

    const hasChanges = computed(() => {
      if (!originalData.value) return false
      
      return formData.value.nom !== originalData.value.nom ||
             formData.value.image !== originalData.value.image
    })

    const handleSubmit = async () => {
      if (!isFormValid.value || !hasChanges.value) {
        error.value = 'Aucune modification à enregistrer'
        return
      }

      try {
        saving.value = true
        error.value = null
        success.value = null

        console.log('[EDIT_AVATAR] Envoi des modifications:', formData.value)

        const response = await fetch(`http://localhost:6090/avatar/avatars/${avatarId.value}`, {
          method: 'PUT',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({
            nom: formData.value.nom,
            image: formData.value.image
          })
        })

        if (!response.ok) {
          const errorData = await response.json()
          throw new Error(errorData.message || `Erreur HTTP: ${response.status}`)
        }

        const result = await response.json()
        console.log('[EDIT_AVATAR] Avatar modifié:', result)

        success.value = 'Avatar modifié avec succès !'
        
        originalData.value = {
          nom: formData.value.nom,
          image: formData.value.image
        }
        
        setTimeout(() => {
          router.push(`/avatar/${avatarId.value}`)
        }, 1500)

      } catch (err) {
        console.error('[EDIT_AVATAR] Erreur sauvegarde:', err)
        error.value = err.message || 'Impossible de modifier l\'avatar'
      } finally {
        saving.value = false
      }
    }

    onMounted(() => {
      loadAvatar()
    })

    return {
      avatarId,
      initialLoading,
      saving,
      loadError,
      error,
      success,
      formData,
      isFormValid,
      hasChanges,
      handleSubmit
    }
  }
}
</script>

<style scoped>
.edit-avatar-page {
  min-height: 100vh;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  padding: 2rem 0;
}

.container {
  max-width: 800px;
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
  color: #667eea;
  border-radius: 12px;
  text-decoration: none;
  font-weight: 600;
  transition: all 0.3s ease;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.btn-back:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(0,0,0,0.15);
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

.btn-back-gallery {
  display: inline-block;
  margin-top: 1.5rem;
  padding: 0.875rem 2rem;
  background: #667eea;
  color: white;
  border-radius: 12px;
  text-decoration: none;
  font-weight: 600;
  transition: all 0.3s ease;
}

.btn-back-gallery:hover {
  background: #5568d3;
  transform: translateY(-2px);
}

.avatar-form-card {
  background: linear-gradient(180deg, #2c3e50 0%, #34495e 100%);
  border-radius: 20px;
  overflow: hidden;
  box-shadow: 0 20px 60px rgba(0,0,0,0.3);
}

.avatar-preview {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  padding: 3rem 2rem;
  text-align: center;
  position: relative;
  overflow: hidden;
}

.avatar-preview::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120"><path d="M0,60 Q300,0 600,60 T1200,60 L1200,120 L0,120 Z" fill="%232c3e50"/></svg>') no-repeat bottom;
  background-size: 100% 60px;
  pointer-events: none;
}

.avatar-display {
  margin-bottom: 1.5rem;
  position: relative;
  z-index: 1;
}

.avatar-icon-3d {
  font-size: 8rem;
  filter: drop-shadow(0 10px 30px rgba(0,0,0,0.3));
  animation: float 3s ease-in-out infinite;
}

@keyframes float {
  0%, 100% { transform: translateY(0px) scale(1); }
  50% { transform: translateY(-20px) scale(1.05); }
}

.preview-name {
  font-size: 1.8rem;
  font-weight: 700;
  color: white;
  text-shadow: 2px 2px 8px rgba(0,0,0,0.3);
  position: relative;
  z-index: 1;
}

.avatar-form {
  padding: 2.5rem;
}

.form-header {
  text-align: center;
  margin-bottom: 2rem;
}

.form-header h2 {
  color: white;
  font-size: 1.8rem;
  margin-bottom: 0.5rem;
}

.form-subtitle {
  color: rgba(255, 255, 255, 0.7);
  font-size: 1rem;
  margin: 0;
}

.form-field {
  display: flex;
  align-items: flex-start;
  gap: 1rem;
  margin-bottom: 1.5rem;
  background: rgba(255,255,255,0.05);
  padding: 1.25rem;
  border-radius: 12px;
  transition: all 0.3s ease;
}

.form-field:hover {
  background: rgba(255,255,255,0.08);
}

.field-icon {
  font-size: 1.5rem;
  background: rgba(255,255,255,0.1);
  border-radius: 8px;
  padding: 0.5rem;
  flex-shrink: 0;
}

.field-content {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.field-label {
  color: white;
  font-weight: 600;
  font-size: 0.95rem;
}

.field-select,
.field-input,
.field-textarea {
  width: 100%;
  padding: 0.875rem 1rem;
  background: white;
  border: 2px solid rgba(255,255,255,0.2);
  border-radius: 10px;
  font-size: 1rem;
  color: #2c3e50;
  font-weight: 500;
  transition: all 0.3s ease;
}

.field-select:focus,
.field-input:focus,
.field-textarea:focus {
  outline: none;
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.2);
}

.field-textarea {
  resize: vertical;
  min-height: 80px;
  font-family: inherit;
}

.field-hint {
  color: rgba(255,255,255,0.6);
  font-size: 0.85rem;
}

.error-message {
  background: rgba(220, 53, 69, 0.15);
  border: 2px solid #dc3545;
  color: #ff6b6b;
  padding: 1rem;
  border-radius: 12px;
  margin-bottom: 1.5rem;
  font-weight: 600;
  text-align: center;
}

.success-message {
  background: rgba(40, 167, 69, 0.15);
  border: 2px solid #28a745;
  color: #51cf66;
  padding: 1rem;
  border-radius: 12px;
  margin-bottom: 1.5rem;
  font-weight: 600;
  text-align: center;
  animation: slideDown 0.4s ease-out;
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

.form-actions {
  margin-top: 2rem;
  display: flex;
  gap: 1rem;
}

.btn-cancel {
  flex: 1;
  padding: 1.25rem;
  background: rgba(255, 255, 255, 0.1);
  color: white;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-radius: 12px;
  font-size: 1.1rem;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.3s ease;
  text-align: center;
  text-decoration: none;
  display: flex;
  align-items: center;
  justify-content: center;
}

.btn-cancel:hover {
  background: rgba(255, 255, 255, 0.2);
  border-color: rgba(255, 255, 255, 0.5);
}

.btn-save {
  flex: 2;
  padding: 1.25rem 3rem;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border: none;
  border-radius: 12px;
  font-size: 1.1rem;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
}

.btn-save:hover:not(:disabled) {
  transform: translateY(-3px);
  box-shadow: 0 10px 30px rgba(102, 126, 234, 0.5);
}

.btn-save:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none;
}

.spinner-small {
  width: 20px;
  height: 20px;
  border: 3px solid rgba(255,255,255,0.3);
  border-top-color: white;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}

@media (max-width: 768px) {
  .avatar-form {
    padding: 1.5rem;
  }
  
  .form-field {
    flex-direction: column;
    align-items: center;
  }
  
  .field-icon {
    font-size: 2rem;
  }
  
  .avatar-icon-3d {
    font-size: 6rem;
  }
  
  .preview-name {
    font-size: 1.5rem;
  }
  
  .form-actions {
    flex-direction: column;
  }
}
</style>
