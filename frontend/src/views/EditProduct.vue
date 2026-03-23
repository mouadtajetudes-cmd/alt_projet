<template>
  <div class="edit-product-page">
    <div class="container">
      <div class="page-header">
        <h1>✏️ Modifier l'annonce</h1>
        <button class="btn-back" @click="goBack">
          Retour
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
              v-model.trim="formData.nom"
              type="text"
              required
              class="form-input"
            />
          </div>

          <div class="form-group">
            <label for="description">Description *</label>
            <textarea
              id="description"
              v-model.trim="formData.description"
              rows="5"
              class="form-input"
              required
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
              <label for="quantite">Quantité *</label>
              <input
                id="quantite"
                v-model.number="formData.quantite"
                type="number"
                min="0"
                required
                class="form-input"
              />
            </div>
          </div>

          <div class="form-group">
            <label for="categorie">Catégorie *</label>
            <select
              id="categorie"
              v-model.number="formData.id_categorie"
              class="form-input"
              required
            >
              <option value="">Choisir une catégorie</option>
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
              <option value="reserve">Réservé</option>
              <option value="indisponible">Indisponible</option>
              <option value="vendu">Vendu</option>
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
    const productId = Number(route.params.id)

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

    const API_BASE = 'http://localhost:6090/marketplace'

    const validateForm = () => {
      if (!formData.value.nom?.trim()) {
        submitError.value = "Le nom de l'annonce est obligatoire"
        return false
      }

      if (!formData.value.description?.trim()) {
        submitError.value = 'La description est obligatoire'
        return false
      }

      if (Number(formData.value.prix) < 0) {
        submitError.value = 'Le prix est invalide'
        return false
      }

      if (Number(formData.value.quantite) < 0) {
        submitError.value = 'La quantité est invalide'
        return false
      }

      if (!formData.value.id_categorie) {
        submitError.value = 'La catégorie est obligatoire'
        return false
      }

      return true
    }

    const loadProduct = async () => {
      try {
        loading.value = true
        error.value = null

        const response = await axios.get(`${API_BASE}/products/${productId}`)
        const product = response?.data?.data || response?.data

        formData.value = {
          nom: product?.nom || '',
          description: product?.description || '',
          prix: Number(product?.prix || 0),
          quantite: Number(product?.quantite || 0),
          id_categorie: product?.id_categorie || '',
          statut: product?.statut || 'disponible'
        }
      } catch (err) {
        console.error('Erreur chargement produit:', err)
        error.value =
          err?.response?.data?.message ||
          'Impossible de charger le produit'
      } finally {
        loading.value = false
      }
    }

    const loadCategories = async () => {
      try {
        const response = await axios.get(`${API_BASE}/categories`)
        categories.value = response?.data?.data || []
      } catch (err) {
        console.error('Erreur chargement catégories:', err)
      }
    }

    const handleSubmit = async () => {
      submitError.value = null
      success.value = null

      if (!validateForm()) return

      submitting.value = true

      try {
        const productData = {
          nom: formData.value.nom.trim(),
          description: formData.value.description.trim(),
          prix: Number(formData.value.prix || 0),
          quantite: Number(formData.value.quantite || 0),
          id_categorie: Number(formData.value.id_categorie),
          statut: formData.value.statut || 'disponible'
        }

        await axios.put(`${API_BASE}/products/${productId}`, productData)

        success.value = 'Annonce modifiée avec succès !'

        setTimeout(() => {
          router.push({ name: 'Marketplace' })
        }, 1200)
      } catch (err) {
        console.error('Erreur modification:', err)
        submitError.value =
          err?.response?.data?.message ||
          'Erreur lors de la modification'
      } finally {
        submitting.value = false
      }
    }

    const handleDelete = async () => {
      submitError.value = null
      deleting.value = true

      try {
        await axios.delete(`${API_BASE}/products/${productId}`)
        router.push({ name: 'Marketplace' })
      } catch (err) {
        console.error('Erreur suppression:', err)
        submitError.value =
          err?.response?.data?.message ||
          'Erreur lors de la suppression'
        showDeleteConfirm.value = false
      } finally {
        deleting.value = false
      }
    }

    const goBack = () => {
      router.push({ name: 'Marketplace' })
    }

    onMounted(async () => {
      await loadCategories()
      await loadProduct()
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
  background:
    radial-gradient(circle at top left, rgba(99, 102, 241, 0.10), transparent 28%),
    linear-gradient(180deg, #f7f8fc 0%, #f1f4fb 100%);
  padding: 2.5rem 0 3rem;
}

.container {
  max-width: 880px;
  margin: 0 auto;
  padding: 0 1.5rem;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
}

.page-header h1 {
  margin: 0;
  color: #0f172a;
  font-size: 2rem;
  font-weight: 800;
}

.btn-back {
  border: none;
  border-radius: 14px;
  padding: 0.85rem 1.2rem;
  background: white;
  color: #334155;
  font-weight: 700;
  cursor: pointer;
  border: 1px solid #e5e7eb;
}

.form-container,
.loading,
.alert-error {
  background: rgba(255,255,255,0.92);
  border: 1px solid #e7edf6;
  border-radius: 24px;
  box-shadow: 0 16px 40px rgba(15, 23, 42, 0.07);
}

.form-container {
  padding: 1.6rem;
}

.loading {
  text-align: center;
  padding: 3rem 1.5rem;
}

.spinner {
  width: 48px;
  height: 48px;
  border: 4px solid #e5e7eb;
  border-top-color: #6366f1;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto 0.9rem;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.form-group {
  margin-bottom: 1rem;
}

.form-group label {
  display: block;
  margin-bottom: 0.45rem;
  color: #334155;
  font-weight: 700;
}

.form-input {
  width: 100%;
  border: 1px solid #dbe3ef;
  background: white;
  border-radius: 14px;
  padding: 0.9rem 1rem;
  font-size: 0.96rem;
  color: #0f172a;
  transition: all 0.2s ease;
}

.form-input:focus {
  outline: none;
  border-color: #8b5cf6;
  box-shadow: 0 0 0 4px rgba(139, 92, 246, 0.10);
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
}

.alert {
  border-radius: 16px;
  padding: 1rem 1.1rem;
  font-weight: 700;
  margin-bottom: 1rem;
}

.alert-error {
  color: #dc2626;
  padding: 1rem 1.1rem;
}

.alert-success {
  background: #ecfdf5;
  color: #059669;
  border: 1px solid #d1fae5;
}

.form-actions {
  display: flex;
  justify-content: space-between;
  gap: 1rem;
  margin-top: 1.2rem;
  padding-top: 1rem;
  border-top: 1px solid #e5e7eb;
}

.actions-right {
  display: flex;
  gap: 0.85rem;
}

.btn {
  border: none;
  border-radius: 14px;
  padding: 0.9rem 1.2rem;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-secondary {
  background: #eef2f7;
  color: #475569;
}

.btn-primary {
  color: white;
  background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
  box-shadow: 0 10px 24px rgba(99, 102, 241, 0.24);
}

.btn-danger {
  background: #fef2f2;
  color: #dc2626;
}

.btn:hover {
  transform: translateY(-2px);
}

.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(15, 23, 42, 0.35);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 1rem;
  z-index: 1000;
}

.modal {
  background: white;
  border-radius: 22px;
  padding: 1.5rem;
  width: 100%;
  max-width: 430px;
  box-shadow: 0 20px 50px rgba(15, 23, 42, 0.18);
}

.modal h3 {
  margin: 0 0 0.75rem;
  color: #0f172a;
}

.modal p {
  color: #64748b;
  line-height: 1.6;
}

.modal-actions {
  display: flex;
  justify-content: flex-end;
  gap: 0.75rem;
  margin-top: 1.25rem;
}

@media (max-width: 768px) {
  .form-row {
    grid-template-columns: 1fr;
  }

  .form-actions,
  .actions-right {
    flex-direction: column;
  }
}
</style>