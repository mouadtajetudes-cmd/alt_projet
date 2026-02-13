<template>
  <div class="create-avatar-page">
    <!-- TODO: Réactiver quand authentification sera prête -->
    <!-- <div v-if="!isAdmin && !loading" class="unauthorized-overlay">
      <div class="unauthorized-card">
        <div class="unauthorized-icon">⛔</div>
        <h2>Accès refusé</h2>
        <p>Seuls les administrateurs peuvent créer des avatars.</p>
        <router-link to="/avatar" class="btn-back-home">
          ← Retour à la galerie
        </router-link>
      </div>
    </div> -->
    
    <div class="container">
      <div class="back-button-wrapper">
        <router-link to="/avatar" class="btn-back">
          ← Retour à la galerie
        </router-link>
      </div>

      <div class="avatar-form-card">
        <div class="avatar-preview">
          <div class="avatar-display">
            <div class="avatar-icon-3d">{{ formData.icon || '🦊' }}</div>
          </div>
          <div class="preview-name">{{ formData.nom || 'Nouveau compagnon' }}</div>
        </div>

        <form @submit.prevent="handleSubmit" class="avatar-form">
          <div class="form-field">
            <div class="field-icon">👥</div>
            <div class="field-content">
              <label class="field-label">Type de compagnon :</label>
              <select v-model="formData.type" class="field-select" required>
                <option value="">Sélectionner...</option>
                <option value="fox">Fox 🦊</option>
                <option value="cat">Chat 🐱</option>
                <option value="dog">Chien 🐶</option>
                <option value="wolf">Loup 🐺</option>
                <option value="bear">Ours 🐻</option>
                <option value="rabbit">Lapin 🐰</option>
                <option value="panda">Panda 🐼</option>
                <option value="lion">Lion 🦁</option>
              </select>
            </div>
          </div>

          <div class="form-field">
            <div class="field-icon">💎</div>
            <div class="field-content">
              <label class="field-label">Tête :</label>
              <select v-model="formData.tete" class="field-select">
                <option value="">Aucun</option>
                <option value="couronne">👑 Couronne</option>
                <option value="chapeau">🎩 Chapeau</option>
                <option value="casquette">🧢 Casquette</option>
                <option value="bandeau">🎀 Bandeau</option>
              </select>
            </div>
          </div>

          <div class="form-field">
            <div class="field-icon">🔄</div>
            <div class="field-content">
              <label class="field-label">Lunettes :</label>
              <select v-model="formData.lunettes" class="field-select">
                <option value="non">Non</option>
                <option value="soleil">🕶️ Lunettes de soleil</option>
                <option value="vue">👓 Lunettes de vue</option>
                <option value="tech">🥽 Lunettes tech</option>
              </select>
            </div>
          </div>

          <div class="form-field">
            <div class="field-icon">👕</div>
            <div class="field-content">
              <label class="field-label">Haut :</label>
              <select v-model="formData.haut" class="field-select">
                <option value="">Aucun</option>
                <option value="sc-grise">SC-Grise</option>
                <option value="t-shirt">T-Shirt</option>
                <option value="chemise">Chemise</option>
                <option value="sweat">Sweat</option>
                <option value="veste">Veste</option>
              </select>
            </div>
          </div>

          <div class="form-field">
            <div class="field-icon">📝</div>
            <div class="field-content">
              <label class="field-label">Nom :</label>
              <input 
                v-model="formData.nom" 
                type="text" 
                class="field-input" 
                placeholder="Nouveau item"
                maxlength="30"
                required
              >
              <span class="field-hint">{{ formData.nom.length }}/30 caractères</span>
            </div>
          </div>

          <div class="form-field">
            <div class="field-icon">📄</div>
            <div class="field-content">
              <label class="field-label">Description :</label>
              <textarea 
                v-model="formData.description" 
                class="field-textarea" 
                placeholder="Décrivez votre compagnon..."
                maxlength="200"
                rows="3"
              ></textarea>
              <span class="field-hint">{{ formData.description.length }}/200 caractères</span>
            </div>
          </div>

          <div v-if="error" class="error-message">
            ⚠️ {{ error }}
          </div>

          <!-- Success Message -->
          <div v-if="success" class="success-message">
            ✓ {{ success }}
          </div>

          <div class="form-actions">
            <button 
              type="submit" 
              class="btn-save" 
              :disabled="loading || !isFormValid"
            >
              <span v-if="loading" class="spinner-small"></span>
              <span v-else>💾 Sauvegarder</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, watch, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuth } from '../composables/useAuth'

export default {
  name: 'CreateAvatar',
  setup() {
    const router = useRouter()
    const { isAdmin, getUserId, initAuth } = useAuth()
    const loading = ref(false)
    const error = ref(null)
    const success = ref(null)
    
    initAuth()
    
    const formData = ref({
      type: '',
      tete: '',
      lunettes: 'non',
      haut: '',
      nom: '',
      description: '',
      icon: '🦊'
    })

    const typeToIcon = {
      fox: '🦊',
      cat: '🐱',
      dog: '🐶',
      wolf: '🐺',
      bear: '🐻',
      rabbit: '🐰',
      panda: '🐼',
      lion: '🦁'
    }

    watch(() => formData.value.type, (newType) => {
      if (newType && typeToIcon[newType]) {
        formData.value.icon = typeToIcon[newType]
      }
    })

    const isFormValid = computed(() => {
      return formData.value.type && formData.value.nom.trim().length >= 3
    })

    const handleSubmit = async () => {
      if (!isFormValid.value) {
        error.value = 'Veuillez remplir tous les champs obligatoires'
        return
      }

      // TODO: Réactiver quand authentification sera prête
      // if (!isAdmin.value) {
      //   error.value = 'Accès refusé : droits administrateur requis'
      //   return
      // }

      try {
        loading.value = true
        error.value = null
        success.value = null

        const userId = getUserId()
        if (!userId) {
          error.value = 'Vous devez être connecté pour créer un avatar'
          return
        }

        const avatarData = {
          nom: formData.value.nom,
          image: formData.value.icon,
          id_utilisateur: userId
        }

        console.log('[CREATE AVATAR] Envoi des données:', avatarData)

        const response = await fetch('http://localhost:6090/avatar/avatars', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify(avatarData)
        })

        if (!response.ok) {
          throw new Error(`Erreur HTTP: ${response.status}`)
        }

        const result = await response.json()
        console.log('[CREATE AVATAR] Avatar créé:', result)

        success.value = 'Avatar créé avec succès !'
        
        setTimeout(() => {
          router.push('/avatar')
        }, 1500)

      } catch (err) {
        console.error('[CREATE AVATAR] Erreur:', err)
        error.value = 'Impossible de créer l\'avatar. Vérifiez que le service est démarré.'
      } finally {
        loading.value = false
      }
    }

    return {
      formData,
      loading,
      error,
      success,
      isFormValid,
      handleSubmit
      // TODO: Réactiver quand authentification sera prête
      // isAdmin
    }
  }
}
</script>

<style scoped>
.create-avatar-page {
  min-height: 100vh;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  padding: 2rem 0;
}

.unauthorized-overlay {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 2rem;
}

.unauthorized-card {
  background: white;
  border-radius: 20px;
  padding: 3rem 2rem;
  text-align: center;
  max-width: 500px;
  box-shadow: 0 20px 60px rgba(0,0,0,0.3);
  animation: fadeInScale 0.4s ease-out;
}

.unauthorized-icon {
  font-size: 5rem;
  margin-bottom: 1.5rem;
}

.unauthorized-card h2 {
  color: #dc3545;
  font-size: 2rem;
  margin-bottom: 1rem;
  font-weight: 700;
}

.unauthorized-card p {
  color: #666;
  font-size: 1.1rem;
  margin-bottom: 2rem;
  line-height: 1.6;
}

.btn-back-home {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 1rem 2rem;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border-radius: 12px;
  text-decoration: none;
  font-weight: 700;
  transition: all 0.3s ease;
  box-shadow: 0 4px 16px rgba(102, 126, 234, 0.3);
}

.btn-back-home:hover {
  transform: translateY(-3px);
  box-shadow: 0 6px 24px rgba(102, 126, 234, 0.4);
}

@keyframes fadeInScale {
  from {
    opacity: 0;
    transform: scale(0.9);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
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
}

.form-actions {
  margin-top: 2rem;
  text-align: center;
}

.btn-save {
  width: 100%;
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
}

.spinner-small {
  width: 20px;
  height: 20px;
  border: 3px solid rgba(255,255,255,0.3);
  border-top-color: white;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
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
}
</style>
