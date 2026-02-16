<template>
  <div class="categories-page">
    <div class="container">
      <div class="page-header">
        <h1>🏷️ Gestion des catégories</h1>
        <button class="btn-create" @click="showCreateModal = true">
          ➕ Ajouter une catégorie
        </button>
      </div>

      <div v-if="loading" class="loading">
        <div class="spinner"></div>
        <p>Chargement...</p>
      </div>

      <div v-else-if="error" class="alert alert-error">
        {{ error }}
      </div>

      <div v-else-if="categories.length > 0" class="categories-grid">
        <div
          v-for="category in categories"
          :key="category.id_categorie"
          class="category-card"
        >
          <div class="category-header">
            <h3>{{ category.nom }}</h3>
            <button class="btn-edit" @click="editCategory(category)">
              ✏️
            </button>
          </div>
          <p class="category-description">
            {{ category.description || 'Aucune description' }}
          </p>
        </div>
      </div>

      <div v-else class="no-categories">
        <p>Aucune catégorie</p>
        <button class="btn-create" @click="showCreateModal = true">
          ➕ Créer la première catégorie
        </button>
      </div>

      <!-- Modal création/édition -->
      <div v-if="showCreateModal || showEditModal" class="modal-overlay" @click="closeModals">
        <div class="modal" @click.stop>
          <h3>{{ isEditing ? 'Modifier' : 'Créer' }} une catégorie</h3>
          
          <form @submit.prevent="handleSubmit">
            <div class="form-group">
              <label for="nom">Nom *</label>
              <input
                id="nom"
                v-model="formData.nom"
                type="text"
                required
                class="form-input"
              />
            </div>

            <div class="form-group">
              <label for="description">Description</label>
              <textarea
                id="description"
                v-model="formData.description"
                rows="3"
                class="form-input"
              ></textarea>
            </div>

            <div v-if="submitError" class="alert alert-error">
              {{ submitError }}
            </div>

            <div v-if="submitSuccess" class="alert alert-success">
              {{ submitSuccess }}
            </div>

            <div class="modal-actions">
              <button type="button" class="btn btn-secondary" @click="closeModals">
                Annuler
              </button>
              <button type="submit" class="btn btn-primary" :disabled="submitting">
                {{ submitting ? 'Enregistrement...' : 'Enregistrer' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'

export default {
  name: 'Categories',
  setup() {
    const categories = ref([])
    const loading = ref(true)
    const error = ref(null)
    const showCreateModal = ref(false)
    const showEditModal = ref(false)
    const submitting = ref(false)
    const submitError = ref(null)
    const submitSuccess = ref(null)
    const editingCategoryId = ref(null)

    const formData = ref({
      nom: '',
      description: ''
    })

    const API_BASE = 'http://localhost:6082'

    const isEditing = computed(() => editingCategoryId.value !== null)

    const loadCategories = async () => {
      try {
        const response = await axios.get(`${API_BASE}/categories`)
        categories.value = response.data || []
      } catch (err) {
        console.error('Erreur chargement catégories:', err)
        error.value = 'Impossible de charger les catégories'
      } finally {
        loading.value = false
      }
    }

    const editCategory = (category) => {
      editingCategoryId.value = category.id_categorie
      formData.value = {
        nom: category.nom,
        description: category.description || ''
      }
      showEditModal.value = true
    }

    const closeModals = () => {
      showCreateModal.value = false
      showEditModal.value = false
      editingCategoryId.value = null
      formData.value = { nom: '', description: '' }
      submitError.value = null
      submitSuccess.value = null
    }

    const handleSubmit = async () => {
      submitError.value = null
      submitSuccess.value = null
      submitting.value = true

      try {
        const categoryData = {
          nom: formData.value.nom,
          description: formData.value.description
        }

        if (isEditing.value) {
          await axios.put(`${API_BASE}/categories/${editingCategoryId.value}`, categoryData)
          submitSuccess.value = 'Catégorie modifiée !'
        } else {
          await axios.post(`${API_BASE}/categories`, categoryData)
          submitSuccess.value = 'Catégorie créée !'
        }

        await loadCategories()
        
        setTimeout(() => {
          closeModals()
        }, 1500)
      } catch (err) {
        console.error('Erreur soumission:', err)
        submitError.value = err.response?.data?.message || 'Une erreur est survenue'
      } finally {
        submitting.value = false
      }
    }

    onMounted(() => {
      loadCategories()
    })

    return {
      categories,
      loading,
      error,
      showCreateModal,
      showEditModal,
      isEditing,
      formData,
      submitting,
      submitError,
      submitSuccess,
      editCategory,
      closeModals,
      handleSubmit
    }
  }
}
</script>

<style scoped>
.categories-page {
  min-height: 100vh;
  background: #f5f7fa;
  padding: 2rem 0;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 2rem;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}

.page-header h1 {
  font-size: 1.75rem;
  font-weight: 600;
  color: #212529;
  margin: 0;
}

.btn-create {
  background: #0d6efd;
  color: white;
  border: 1px solid #0d6efd;
  padding: 0.625rem 1.25rem;
  border-radius: 6px;
  font-weight: 500;
  font-size: 0.875rem;
  cursor: pointer;
  transition: all 0.15s ease;
}

.btn-create:hover {
  background: #0b5ed7;
  border-color: #0b5ed7;
}

.loading {
  text-align: center;
  padding: 4rem 0;
  background: white;
  border-radius: 12px;
}

.spinner {
  width: 40px;
  height: 40px;
  border: 3px solid #e9ecef;
  border-top: 3px solid #0d6efd;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
  margin: 0 auto 1rem;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.categories-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 1.5rem;
}

.category-card {
  background: white;
  border-radius: 12px;
  padding: 1.5rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
  transition: box-shadow 0.15s ease;
}

.category-card:hover {
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.category-header {
  display: flex;
  justify-content: space-between;
  align-items: start;
  margin-bottom: 0.75rem;
}

.category-header h3 {
  font-size: 1.125rem;
  font-weight: 600;
  color: #212529;
  margin: 0;
}

.btn-edit {
  background: white;
  border: 1px solid #dee2e6;
  padding: 0.375rem 0.75rem;
  border-radius: 6px;
  cursor: pointer;
  font-size: 1rem;
  transition: all 0.15s ease;
}

.btn-edit:hover {
  background: #f8f9fa;
  border-color: #adb5bd;
}

.category-description {
  color: #6c757d;
  font-size: 0.875rem;
  margin: 0;
}

.no-categories {
  text-align: center;
  padding: 4rem 2rem;
  background: white;
  border-radius: 12px;
}

.no-categories p {
  color: #6c757d;
  margin-bottom: 1.5rem;
}

.alert {
  padding: 0.875rem 1rem;
  border-radius: 6px;
  margin-bottom: 1rem;
  font-size: 0.875rem;
}

.alert-error {
  background: #f8d7da;
  color: #721c24;
  border: 1px solid #f5c6cb;
}

.alert-success {
  background: #d4edda;
  color: #155724;
  border: 1px solid #c3e6cb;
}

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal {
  background: white;
  padding: 2rem;
  border-radius: 12px;
  max-width: 500px;
  width: 90%;
}

.modal h3 {
  margin: 0 0 1.5rem 0;
  font-size: 1.25rem;
  color: #212529;
}

.form-group {
  margin-bottom: 1.25rem;
}

.form-group label {
  display: block;
  font-weight: 500;
  color: #495057;
  margin-bottom: 0.5rem;
  font-size: 0.875rem;
}

.form-input {
  width: 100%;
  padding: 0.625rem 0.875rem;
  border: 1px solid #dee2e6;
  border-radius: 6px;
  font-size: 0.9rem;
  transition: border-color 0.15s ease;
}

.form-input:focus {
  outline: none;
  border-color: #0d6efd;
  box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.1);
}

.modal-actions {
  display: flex;
  gap: 1rem;
  justify-content: flex-end;
  margin-top: 1.5rem;
}

.btn {
  padding: 0.625rem 1.5rem;
  border: none;
  border-radius: 6px;
  font-size: 0.875rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.15s ease;
}

.btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.btn-secondary {
  background: white;
  color: #495057;
  border: 1px solid #dee2e6;
}

.btn-secondary:hover:not(:disabled) {
  background: #f8f9fa;
  border-color: #adb5bd;
}

.btn-primary {
  background: #0d6efd;
  color: white;
  border: 1px solid #0d6efd;
}

.btn-primary:hover:not(:disabled) {
  background: #0b5ed7;
  border-color: #0b5ed7;
}

@media (max-width: 768px) {
  .container {
    padding: 0 1rem;
  }

  .page-header {
    flex-direction: column;
    gap: 1rem;
    align-items: flex-start;
  }

  .btn-create {
    width: 100%;
  }

  .categories-grid {
    grid-template-columns: 1fr;
  }

  .modal {
    padding: 1.5rem;
  }

  .modal-actions {
    flex-direction: column;
  }

  .btn {
    width: 100%;
  }
}
</style>
