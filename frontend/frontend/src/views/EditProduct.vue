<template>
  <div class="edit-product-page">
    <div class="container">
      <div class="page-header">
        <h1>Modifier l'annonce</h1>
        <button class="btn-back" @click="goBack">
          ← Retour
        </button>
      </div>

      <div v-if="loading" class="loading">
        <div class="spinner"></div>
        <p>Chargement...</p>
      </div>

      <div v-else-if="error" class="alert alert-error">
        {{ error }}
      </div>

      <div v-else class="form-container">
        <form @submit.prevent="handleSubmit">
          <div class="form-group">
            <label for="nom">Nom de l'annonce *</label>
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
              rows="5"
              class="form-input"
            ></textarea>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="prix">Prix (€) *</label>
              <input
                id="prix"
                v-model.number="formData.prix"
                type="number"
                step="0.01"
                min="0"
                required
                class="form-input"
              />
            </div>

            <div class="form-group">
              <label for="quantite">Quantité</label>
              <input
                id="quantite"
                v-model.number="formData.quantite"
                type="number"
                min="0"
                class="form-input"
              />
            </div>
          </div>

          <div class="form-group">
            <label for="categorie">Catégorie</label>
            <select
              id="categorie"
              v-model.number="formData.id_categorie"
              class="form-input"
            >
              <option value="">Aucune catégorie</option>
              <option
                v-for="category in categories"
                :key="category.id_categorie"
                :value="category.id_categorie"
              >
                {{ category.nom }}
              </option>
            </select>
          </div>

          <div class="form-group">
            <label for="statut">Statut</label>
            <select
              id="statut"
              v-model="formData.statut"
              class="form-input"
            >
              <option value="disponible">Disponible</option>
              <option value="vendu">Vendu</option>
              <option value="reserve">Réservé</option>
            </select>
          </div>

          <div v-if="submitError" class="alert alert-error">
            {{ submitError }}
          </div>

          <div v-if="success" class="alert alert-success">
            {{ success }}
          </div>

          <div class="form-actions">
            <button type="button" class="btn btn-danger" @click="showDeleteConfirm = true">
              🗑️ Supprimer
            </button>
            <div class="actions-right">
              <button type="button" class="btn btn-secondary" @click="goBack">
                Annuler
              </button>
              <button type="submit" class="btn btn-primary" :disabled="submitting">
                {{ submitting ? 'Enregistrement...' : 'Enregistrer' }}
              </button>
            </div>
          </div>
        </form>
      </div>

      <!-- Modal confirmation suppression -->
      <div v-if="showDeleteConfirm" class="modal-overlay" @click="showDeleteConfirm = false">
        <div class="modal" @click.stop>
          <h3>Confirmer la suppression</h3>
          <p>Êtes-vous sûr de vouloir supprimer ce produit ? Cette action est irréversible.</p>
          <div class="modal-actions">
            <button class="btn btn-secondary" @click="showDeleteConfirm = false">
              Annuler
            </button>
            <button class="btn btn-danger" @click="handleDelete" :disabled="deleting">
              {{ deleting ? 'Suppression...' : 'Supprimer' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import axios from 'axios'

export default {
  name: 'EditProduct',
  setup() {
    const router = useRouter()
    const route = useRoute()
    const productId = route.params.id

    const loading = ref(true)
    const error = ref(null)
    const submitting = ref(false)
    const submitError = ref(null)
    const success = ref(null)
    const categories = ref([])
    const showDeleteConfirm = ref(false)
    const deleting = ref(false)

    const formData = ref({
      nom: '',
      description: '',
      prix: 0,
      quantite: 0,
      id_categorie: '',
      statut: 'disponible'
    })

    const API_BASE = 'http://localhost:6082'

    const loadProduct = async () => {
      try {
        const response = await axios.get(`${API_BASE}/products/${productId}`)
        const product = response.data || response.data
        
        formData.value = {
          nom: product.nom,
          description: product.description || '',
          prix: product.prix,
          quantite: product.quantite || 0,
          id_categorie: product.id_categorie || '',
          statut: product.statut
        }
      } catch (err) {
        console.error('Erreur chargement produit:', err)
        error.value = 'Impossible de charger le produit'
      } finally {
        loading.value = false
      }
    }

    const loadCategories = async () => {
      try {
        const response = await axios.get(`${API_BASE}/categories`)
        categories.value = response.data || []
      } catch (err) {
        console.error('Erreur chargement catégories:', err)
      }
    }

    const handleSubmit = async () => {
      submitError.value = null
      success.value = null
      submitting.value = true

      try {
        const productData = {
          nom: formData.value.nom,
          description: formData.value.description,
          prix: formData.value.prix,
          quantite: formData.value.quantite,
          statut: formData.value.statut
        }

        if (formData.value.id_categorie) {
          productData.id_categorie = formData.value.id_categorie
        }

        await axios.put(`${API_BASE}/products/${productId}`, productData)

        success.value = 'Produit modifié avec succès !'
        
        setTimeout(() => {
          router.push({ name: 'Marketplace' })
        }, 1500)
      } catch (err) {
        console.error('Erreur modification:', err)
        submitError.value = err.response?.data?.message || 'Erreur lors de la modification'
      } finally {
        submitting.value = false
      }
    }

    const handleDelete = async () => {
      deleting.value = true

      try {
        await axios.delete(`${API_BASE}/products/${productId}`)
        router.push({ name: 'Marketplace' })
      } catch (err) {
        console.error('Erreur suppression:', err)
        submitError.value = 'Erreur lors de la suppression'
        showDeleteConfirm.value = false
      } finally {
        deleting.value = false
      }
    }

    const goBack = () => {
      router.push({ name: 'Marketplace' })
    }

    onMounted(() => {
      loadProduct()
      loadCategories()
    })

    return {
      formData,
      categories,
      loading,
      error,
      submitting,
      submitError,
      success,
      showDeleteConfirm,
      deleting,
      handleSubmit,
      handleDelete,
      goBack
    }
  }
}
</script>

<style scoped>
.edit-product-page {
  min-height: 100vh;
  background: #f5f7fa;
  padding: 2rem 0;
}

.container {
  max-width: 800px;
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

.btn-back {
  background: white;
  color: #495057;
  border: 1px solid #dee2e6;
  padding: 0.625rem 1.25rem;
  border-radius: 6px;
  font-weight: 500;
  font-size: 0.875rem;
  cursor: pointer;
  transition: all 0.15s ease;
}

.btn-back:hover {
  background: #f8f9fa;
  border-color: #adb5bd;
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

.form-container {
  background: white;
  padding: 2rem;
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
}

.form-group {
  margin-bottom: 1.5rem;
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

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1.5rem;
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

.form-actions {
  display: flex;
  justify-content: space-between;
  margin-top: 2rem;
  padding-top: 1.5rem;
  border-top: 1px solid #dee2e6;
}

.actions-right {
  display: flex;
  gap: 1rem;
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

.btn-danger {
  background: #dc3545;
  color: white;
  border: 1px solid #dc3545;
}

.btn-danger:hover:not(:disabled) {
  background: #c82333;
  border-color: #c82333;
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
  max-width: 400px;
  width: 90%;
}

.modal h3 {
  margin: 0 0 1rem 0;
  font-size: 1.25rem;
  color: #212529;
}

.modal p {
  margin: 0 0 1.5rem 0;
  color: #6c757d;
}

.modal-actions {
  display: flex;
  gap: 1rem;
  justify-content: flex-end;
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

  .form-container {
    padding: 1.5rem;
  }

  .form-row {
    grid-template-columns: 1fr;
  }

  .form-actions {
    flex-direction: column;
  }

  .actions-right {
    flex-direction: column;
    width: 100%;
  }

  .btn {
    width: 100%;
  }
}
</style>
