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
    
    <div v-else class="container">
      <div class="back-button-wrapper">
        <router-link to="/avatar" class="btn-back">
          ← Retour à la galerie
        </router-link>
      </div>

      <div class="avatar-form-card">
        <div class="companion-header">
          <div class="companion-mascot">
            <img src="/avatars/avatar.png" alt="Assistant" class="mascot-image" />
          </div>
        </div>

        <div class="card-body-dark">
          <h2 class="form-title">✨ Créer un nouvel avatar</h2>
          <p class="form-subtitle">Définissez un nouveau compagnon avec ses 5 évolutions</p>

          <form @submit.prevent="handleSubmit" class="avatar-form">
            <div class="form-section">
              <h3 class="section-title">
                <span class="section-icon">🖼️</span>
                Image de l'avatar
              </h3>
              <div class="upload-container">
                <input 
                  type="file" 
                  @change="handleImageUpload"
                  accept="image/png,image/jpeg,image/jpg"
                  class="file-input"
                  id="avatar-image"
                  ref="imageInput"
                  required
                >
                <label for="avatar-image" class="upload-label">
                  <div v-if="imagePreview" class="upload-preview">
                    <img :src="imagePreview" alt="Aperçu" />
                  </div>
                  <div v-else class="upload-placeholder">
                    <span class="upload-icon">📁</span>
                    <span class="upload-text">Cliquer pour choisir une image</span>
                  </div>
                </label>
                <span v-if="imageFile" class="file-name">{{ imageFile.name }}</span>
              </div>
            </div>

            <div class="form-section">
              <h3 class="section-title">
                <span class="section-icon">📝</span>
                Nom de l'avatar
              </h3>
              <input 
                v-model="formData.nom" 
                type="text" 
                class="field-input" 
                placeholder="Ex: Rex le Fidèle"
                maxlength="50"
                required
              >
              <span class="field-hint">{{ formData.nom?.length || 0 }}/50 caractères</span>
            </div>

            <div class="form-section">
              <h3 class="section-title">
                <span class="section-icon">📄</span>
                Description
              </h3>
              <textarea 
                v-model="formData.description" 
                class="field-textarea" 
                placeholder="Décrivez ce compagnon..."
                maxlength="200"
                rows="3"
              ></textarea>
              <span class="field-hint">{{ formData.description?.length || 0 }}/200 caractères</span>
            </div>

            <div class="form-section">
              <h3 class="section-title">
                <span class="section-icon">⭐</span>
                Évolutions (5 niveaux)
              </h3>
              <p class="section-description">Définissez les surnoms pour chaque niveau d'évolution</p>
              
              <div class="versions-grid">
                <div v-for="level in 5" :key="level" class="version-card">
                  <div class="version-header">
                    <span class="version-icon">{{ getLevelIcon(level) }}</span>
                    <span class="version-title">Niveau {{ level }}</span>
                  </div>
                  <input 
                    v-model="formData.versions[level - 1]" 
                    type="text" 
                    class="version-input" 
                    :placeholder="getLevelPlaceholder(level)"
                    maxlength="50"
                    required
                  >
                </div>
              </div>
            </div>

            <div v-if="error" class="error-message">
              ⚠️ {{ error }}
            </div>

            <div v-if="success" class="success-message">
              ✓ {{ success }}
            </div>

            <div class="form-actions">
              <button 
                type="button"
                @click="router.push('/avatar')"
                class="btn-cancel"
                :disabled="loading"
              >
                Annuler
              </button>
              <button 
                type="submit" 
                class="btn-save" 
                :disabled="loading || !isFormValid"
              >
                <span v-if="loading" class="spinner-small"></span>
                <span v-else>💾 Créer l'avatar</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuth } from '../composables/useAuth'

export default {
  name: 'CreateAvatar',
  setup() {
    const router = useRouter()
    const { user, isAdmin } = useAuth()
    
    const loading = ref(false)
    const error = ref('')
    const success = ref('')
    const imageFile = ref(null)
    const imagePreview = ref('')
    const imageInput = ref(null)
    
    const formData = ref({
      nom: '',
      description: '',
      versions: ['', '', '', '', '']
    })

    const isFormValid = computed(() => {
      return formData.value.nom.trim().length > 0 &&
             imageFile.value !== null &&
             formData.value.versions.every(v => v.trim().length > 0) &&
             !loading.value
    })

    const handleImageUpload = (event) => {
      const file = event.target.files[0]
      if (file) {
        imageFile.value = file
        const reader = new FileReader()
        reader.onload = (e) => {
          imagePreview.value = e.target.result
        }
        reader.readAsDataURL(file)
      }
    }

    const getLevelIcon = (level) => {
      const icons = ['🌱', '🌿', '🌳', '⭐', '🏆']
      return icons[level - 1]
    }

    const getLevelPlaceholder = (level) => {
      const placeholders = ['Jeune', 'Ami', 'Fidèle', 'Héroïque', 'Légendaire']
      return `Ex: ${placeholders[level - 1]}`
    }

    const handleSubmit = async () => {
      if (!isAdmin.value) {
        error.value = 'Seuls les administrateurs peuvent créer des avatars'
        return
      }

      if (!isFormValid.value) {
        error.value = 'Veuillez remplir tous les champs obligatoires'
        return
      }

      loading.value = true
      error.value = ''
      success.value = ''
      
      try {
        const token = localStorage.getItem('auth_token')
        
        const formDataToSend = new FormData()
        formDataToSend.append('nom', formData.value.nom)
        formDataToSend.append('image', imageFile.value)
        if (formData.value.description) {
          formDataToSend.append('description', formData.value.description)
        }
        formDataToSend.append('versions', JSON.stringify(formData.value.versions))

        console.log('[CREATE AVATAR] Envoi du formulaire avec fichier')

        const response = await fetch('http://localhost:6090/avatar/avatars', {
          method: 'POST',
          headers: {
            'Authorization': `Bearer ${token}`
          },
          body: formDataToSend
        })

        if (!response.ok) {
          const errorData = await response.json()
          console.error('[CREATE AVATAR] Erreur détaillée:', errorData)
          throw new Error(errorData.message || errorData.error || 'Erreur lors de la création')
        }

        const result = await response.json()
        console.log('[CREATE AVATAR] Avatar créé:', result)

        success.value = 'Avatar créé avec succès!'
        
        setTimeout(() => {
          router.push('/avatar')
        }, 2000)
        
      } catch (err) {
        console.error('Erreur lors de la création:', err)
        error.value = err.message || 'Erreur lors de la création de l\'avatar'
      } finally {
        loading.value = false
      }
    }

    return {
      router,
      formData,
      loading,
      error,
      success,
      imageFile,
      imagePreview,
      imageInput,
      isFormValid,
      handleSubmit,
      handleImageUpload,
      getLevelIcon,
      getLevelPlaceholder,
      isAdmin
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
  border-radius: 24px;
  padding: 3rem 2rem;
  text-align: center;
  max-width: 500px;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
  animation: fadeInScale 0.4s ease-out;
}

.unauthorized-icon {
  font-size: 5rem;
  margin-bottom: 1.5rem;
}

.unauthorized-card h2 {
  color: #dc2626;
  font-size: 2rem;
  margin-bottom: 1rem;
  font-weight: 700;
}

.unauthorized-card p {
  color: #6b7280;
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
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}

.btn-back-home:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 20px rgba(102, 126, 234, 0.5);
}

.container {
  max-width: 900px;
  margin: 0 auto;
  padding: 0 1.5rem;
}

.back-button-wrapper {
  margin-bottom: 1.5rem;
}

.btn-back {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1.5rem;
  background: rgba(255, 255, 255, 0.2);
  backdrop-filter: blur(10px);
  color: white;
  border-radius: 12px;
  text-decoration: none;
  font-weight: 600;
  transition: all 0.3s ease;
  border: 1px solid rgba(255, 255, 255, 0.3);
}

.btn-back:hover {
  background: rgba(255, 255, 255, 0.3);
  transform: translateX(-5px);
}

.avatar-form-card {
  border-radius: 24px;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
  overflow: hidden;
  animation: slideUp 0.5s ease-out;
  background: var(--avatar-gradient);
}

.companion-header {
  background: var(--avatar-gradient);
  padding: 2rem 1.5rem;
  text-align: center;
}

.companion-mascot {
  display: inline-block;
  position: relative;
}

.mascot-image {
  width: 250px;
  height: 250px;
  object-fit: contain;
  animation: float 3s ease-in-out infinite;
  filter: drop-shadow(0 6px 12px rgba(0, 0, 0, 0.3));
}

@keyframes float {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-10px); }
}

.card-body-dark {
  background: var(--avatar-gradient);
  padding: 2rem;
}

.form-title {
  color: white;
  font-size: 1.8rem;
  font-weight: 700;
  margin-bottom: 0.5rem;
  text-align: center;
}

.form-subtitle {
  color: rgba(255, 255, 255, 0.8);
  font-size: 1rem;
  margin-bottom: 2rem;
  text-align: center;
}

.avatar-form {
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

.form-section {
  background: white;
  border-radius: 16px;
  padding: 1.5rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.section-title {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 1.2rem;
  font-weight: 700;
  color: #1f2937;
  margin-bottom: 1rem;
  padding-bottom: 0.75rem;
  border-bottom: 2px solid #f3f4f6;
}

.section-icon {
  font-size: 1.5rem;
}

.section-description {
  color: #6b7280;
  margin-bottom: 1.5rem;
  font-size: 0.95rem;
}

.upload-container {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.file-input {
  display: none;
}

.upload-label {
  display: block;
  width: 100%;
  height: 200px;
  border: 3px dashed #d1d5db;
  border-radius: 12px;
  cursor: pointer;
  transition: all 0.3s ease;
  overflow: hidden;
}

.upload-label:hover {
  border-color: #667eea;
  background: rgba(102, 126, 234, 0.05);
}

.upload-preview {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 100%;
  height: 100%;
  background: #f9fafb;
}

.upload-preview img {
  max-width: 100%;
  max-height: 100%;
  object-fit: contain;
}

.upload-placeholder {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  height: 100%;
  gap: 0.75rem;
}

.upload-icon {
  font-size: 3rem;
}

.upload-text {
  color: #6b7280;
  font-weight: 600;
  font-size: 1rem;
}

.file-name {
  color: #6b7280;
  font-size: 0.875rem;
  text-align: center;
  font-weight: 500;
}

.field-input {
  width: 100%;
  padding: 0.875rem 1rem;
  border: 2px solid #e5e7eb;
  border-radius: 12px;
  font-size: 1rem;
  transition: all 0.3s ease;
  background: white;
  color: #1f2937;
}

.field-input:focus {
  outline: none;
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.field-textarea {
  width: 100%;
  padding: 0.875rem 1rem;
  border: 2px solid #e5e7eb;
  border-radius: 12px;
  font-size: 1rem;
  font-family: inherit;
  resize: vertical;
  transition: all 0.3s ease;
  background: white;
  color: #1f2937;
}

.field-textarea:focus {
  outline: none;
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.field-hint {
  display: block;
  margin-top: 0.5rem;
  font-size: 0.875rem;
  color: #9ca3af;
}

.versions-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1rem;
}

.version-card {
  background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
  border-radius: 16px;
  padding: 1.25rem;
  border: 2px solid rgba(102, 126, 234, 0.2);
  transition: all 0.3s ease;
}

.version-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
  border-color: rgba(102, 126, 234, 0.5);
}

.version-header {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  margin-bottom: 1rem;
  padding-bottom: 0.75rem;
  border-bottom: 2px solid rgba(0, 0, 0, 0.05);
}

.version-icon {
  font-size: 1.8rem;
}

.version-title {
  font-weight: 700;
  font-size: 1.1rem;
  color: #1f2937;
}

.version-input {
  width: 100%;
  padding: 0.875rem 1rem;
  border: 2px solid rgba(102, 126, 234, 0.3);
  border-radius: 10px;
  font-size: 1rem;
  transition: all 0.3s ease;
  background: white;
}

.version-input:focus {
  outline: none;
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.15);
}

.error-message {
  background: #fee2e2;
  color: #991b1b;
  padding: 1rem;
  border-radius: 12px;
  border-left: 4px solid #dc2626;
  font-weight: 600;
}

.success-message {
  background: #d1fae5;
  color: #065f46;
  padding: 1rem;
  border-radius: 12px;
  border-left: 4px solid #10b981;
  font-weight: 600;
}

.form-actions {
  display: flex;
  gap: 1rem;
  justify-content: flex-end;
}

.btn-cancel {
  padding: 0.875rem 1.75rem;
  background: #e5e7eb;
  color: #4b5563;
  border: none;
  border-radius: 12px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.3s ease;
}

.btn-cancel:hover:not(:disabled) {
  background: #d1d5db;
  transform: translateY(-2px);
}

.btn-cancel:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.btn-save {
  padding: 0.875rem 1.75rem;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border: none;
  border-radius: 12px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}

.btn-save:hover:not(:disabled) {
  transform: translateY(-3px);
  box-shadow: 0 8px 20px rgba(102, 126, 234, 0.5);
}

.btn-save:disabled {
  opacity: 0.5;
  cursor: not-allowed;
  transform: none;
}

.spinner-small {
  display: inline-block;
  width: 1rem;
  height: 1rem;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-top-color: white;
  border-radius: 50%;
  animation: spin 0.6s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
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

@keyframes slideUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
@media (max-width: 768px) {
  .create-avatar-page {
    padding: 1rem 0;
  }

  .container {
    padding: 0 1rem;
  }

  .mascot-image {
    width: 120px;
    height: 120px;
  }

  .card-body-dark {
    padding: 1.5rem;
  }

  .form-title {
    font-size: 1.5rem;
  }

  .versions-grid {
    grid-template-columns: 1fr;
  }

  .form-actions {
    flex-direction: column;
  }

  .btn-cancel,
  .btn-save {
    width: 100%;
    justify-content: center;
  }
}
</style>
