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
@import '@/assets/styles/avatar-variables.css';
@import '@/assets/styles/avatar-animations.css';
@import '@/assets/styles/avatar-common.css';

/* === Styles Spécifiques à EditAvatar.vue === */

/* Page avec fond gradient */
.edit-avatar-page {
  min-height: 100vh;
  background: var(--avatar-gradient);
  padding: var(--avatar-spacing-lg) 0;
}

/* Container spécifique pour EditAvatar */
.container {
  max-width: 800px;
}

/* État d'erreur de chargement spécifique */
.btn-back-gallery {
  display: inline-block;
  margin-top: var(--avatar-spacing-md);
  padding: 0.875rem var(--avatar-spacing-lg);
  background: var(--avatar-primary);
  color: var(--avatar-text-white);
  border-radius: var(--avatar-radius-md);
  text-decoration: none;
  font-weight: 600;
  transition: all var(--avatar-transition-normal);
}

.btn-back-gallery:hover {
  background: #5568d3;
  transform: translateY(-2px);
}

/* Wave SVG personnalisé pour l'aperçu */
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

/* Display de l'avatar en édition */
.avatar-display {
  margin-bottom: var(--avatar-spacing-md);
  position: relative;
  z-index: 1;
}

/* Nom de l'avatar en aperçu */
.preview-name {
  font-size: 1.8rem;
  font-weight: 700;
  color: var(--avatar-text-white);
  text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.3);
  position: relative;
  z-index: 1;
}

/* En-tête du formulaire */
.form-header {
  text-align: center;
  margin-bottom: var(--avatar-spacing-lg);
}

.form-header h2 {
  color: var(--avatar-text-white);
  font-size: 1.8rem;
  margin-bottom: var(--avatar-spacing-xs);
}

.form-subtitle {
  color: rgba(255, 255, 255, 0.7);
  font-size: 1rem;
  margin: 0;
}

/* Bouton d'annulation spécifique */
.btn-cancel {
  flex: 1;
  padding: 1.25rem;
  background: rgba(255, 255, 255, 0.1);
  color: var(--avatar-text-white);
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-radius: var(--avatar-radius-md);
  font-size: 1.1rem;
  font-weight: 700;
  cursor: pointer;
  transition: all var(--avatar-transition-normal);
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

/* Media queries spécifiques */
@media (max-width: 768px) {
  .preview-name {
    font-size: 1.5rem;
  }
}
</style>
