<template>
  <div class="create-avatar-page">
    <div v-if="!isAdmin && !loading" class="unauthorized-overlay">
      <div class="unauthorized-card">
        <div class="unauthorized-icon">⛔</div>
        <h2>Accès refusé</h2>
        <p>Seuls les administrateurs peuvent créer des avatars.</p>
        <router-link to="/avatar" class="btn-back-home">
          ← Retour à la galerie
        </router-link>
      </div>
    </div>
    
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

      if (!isAdmin.value) {
        error.value = 'Accès refusé : droits administrateur requis'
        return
      }

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
      handleSubmit,
      isAdmin
    }
  }
}
</script>

<style scoped>
/* === Styles Spécifiques à CreateAvatar.vue === */

.create-avatar-page {
  min-height: 100vh;
  background: var(--avatar-gradient);
  padding: var(--avatar-spacing-xl) 0;
}

/* === Overlay Non Autorisé === */
.unauthorized-overlay {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: var(--avatar-spacing-xl);
}

.unauthorized-card {
  background: var(--avatar-bg-card);
  border-radius: var(--avatar-radius-card);
  padding: 3rem 2rem;
  text-align: center;
  max-width: 500px;
  box-shadow: var(--avatar-shadow-xl);
  animation: fadeInScale 0.4s ease-out;
}

.unauthorized-icon {
  font-size: 5rem;
  margin-bottom: var(--avatar-spacing-lg);
}

.unauthorized-card h2 {
  color: var(--avatar-error);
  font-size: 2rem;
  margin-bottom: var(--avatar-spacing-md);
  font-weight: 700;
}

.unauthorized-card p {
  color: var(--avatar-text-secondary);
  font-size: 1.1rem;
  margin-bottom: var(--avatar-spacing-xl);
  line-height: 1.6;
}

.btn-back-home {
  display: inline-flex;
  align-items: center;
  gap: var(--avatar-spacing-sm);
  padding: var(--avatar-spacing-md) var(--avatar-spacing-xl);
  background: var(--avatar-gradient);
  color: white;
  border-radius: var(--avatar-radius-btn);
  text-decoration: none;
  font-weight: 700;
  transition: all 0.3s ease;
  box-shadow: var(--avatar-shadow-btn);
}

.btn-back-home:hover {
  transform: translateY(-3px);
  box-shadow: var(--avatar-shadow-btn-hover);
}

/* === Animation Spécifique === */
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

/* === Media Queries === */
@media (max-width: 768px) {
  .create-avatar-page {
    padding: var(--avatar-spacing-md) 0;
  }

  .unauthorized-card {
    padding: var(--avatar-spacing-xl) var(--avatar-spacing-md);
  }

  .unauthorized-icon {
    font-size: 4rem;
  }

  .unauthorized-card h2 {
    font-size: 1.5rem;
  }

  .unauthorized-card p {
    font-size: 1rem;
  }
}
</style>
