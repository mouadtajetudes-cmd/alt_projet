<template>
  <div class="marketplace-page">
    <div class="container">
      <div class="page-header">
        <h1>Mes annonces</h1>
        <div class="header-actions">
          <button class="btn-secondary" @click="goMarketplace">Retour marketplace</button>
          <button class="btn-create" @click="goToCreate">Ajouter une annonce</button>
        </div>
      </div>

      <div v-if="loading" class="loading">
        <div class="spinner"></div>
        <p>Chargement de mes annonces...</p>
      </div>

      <div v-else-if="error" class="error-message">
        <p>{{ error }}</p>
        <button class="btn-retry" @click="loadMyProducts">Réessayer</button>
      </div>

      <div v-else-if="products.length > 0" class="products-grid">
        <div v-for="product in products" :key="product.id_produit" class="product-card">
          <div class="product-image">
            <img v-if="product.image" :src="product.image" :alt="product.nom" />
            <div v-else class="placeholder">🛍️</div>
          </div>

          <div class="product-content">
            <h3>{{ product.nom }}</h3>
            <p class="description">{{ product.description || 'Aucune description' }}</p>
            <p class="category">{{ product.categorie || 'Non catégorisé' }}</p>
            <p class="price">{{ formatPrice(product.prix) }}</p>

            <div class="actions">
              <button class="btn-view" @click="goToDetails(product.id_produit)">Voir</button>
              <button class="btn-edit" @click="goToEdit(product.id_produit)">Modifier</button>
              <button class="btn-delete" @click="confirmDelete(product)">Supprimer</button>
            </div>
          </div>
        </div>
      </div>

      <div v-else class="no-products">
        <p>Vous n’avez encore publié aucune annonce.</p>
        <button class="btn-create" @click="goToCreate">Créer ma première annonce</button>
      </div>

      <div v-if="showDeleteModal" class="modal-overlay" @click="closeDeleteModal">
        <div class="modal" @click.stop>
          <h3>Supprimer l’annonce</h3>
          <p>
            Voulez-vous vraiment supprimer
            <strong>{{ productToDelete?.nom }}</strong> ?
          </p>
          <div class="modal-actions">
            <button class="btn-secondary" @click="closeDeleteModal">Annuler</button>
            <button class="btn-delete" :disabled="deleting" @click="deleteProduct">
              {{ deleting ? 'Suppression...' : 'Supprimer' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

const router = useRouter()

const products = ref([])
const loading = ref(false)
const error = ref(null)
const showDeleteModal = ref(false)
const productToDelete = ref(null)
const deleting = ref(false)

const API_BASE = 'http://localhost:6090/marketplace'
const FILES_BASE = 'http://localhost:3001'

const getAuthUserId = () => {
  const authUser = JSON.parse(localStorage.getItem('user') || '{}')
  return Number(
    authUser?.id_utilisateur ??
    authUser?.id ??
    authUser?.user?.id_utilisateur ??
    authUser?.user?.id ??
    35
  )
}

const resolveImageUrl = (raw) => {
  if (!raw || typeof raw !== 'string') return null
  if (raw.startsWith('http://') || raw.startsWith('https://')) return raw
  if (raw.startsWith('/')) return `${FILES_BASE}${raw}`
  return `${FILES_BASE}/${raw}`
}

const normalizeProduct = (p, idx) => {
  let id = Number(p?.id_produit ?? p?.id ?? 0)
  if (!Number.isFinite(id) || id <= 0) id = 1000000 + idx

  const rawImage =
    p?.media_url ??
    p?.image ??
    p?.image_url ??
    (Array.isArray(p?.media_urls) && p.media_urls.length ? p.media_urls[0] : null) ??
    (Array.isArray(p?.medias) && p.medias.length ? (p.medias[0]?.media_url ?? p.medias[0]?.url) : null)

  return {
    id_produit: id,
    nom: p?.nom ?? 'Sans titre',
    description: p?.description ?? '',
    prix: Number(p?.prix ?? 0),
    statut: p?.statut ?? 'disponible',
    quantite: Number(p?.quantite ?? 0),
    id_utilisateur: p?.id_utilisateur ?? null,
    id_categorie: p?.id_categorie ?? null,
    categorie: p?.categorie ?? 'Non catégorisé',
    image: resolveImageUrl(rawImage)
  }
}

const loadMyProducts = async () => {
  try {
    loading.value = true
    error.value = null

    const userId = getAuthUserId()
    const res = await axios.get(`${API_BASE}/products`, {
      params: {
        user_id: userId,
        limit: 100,
        page: 1
      }
    })

    products.value = (res?.data?.data || []).map((p, idx) => normalizeProduct(p, idx))
  } catch (err) {
    console.error('Erreur chargement mes annonces:', err)
    error.value = err?.response?.data?.message || 'Impossible de charger vos annonces'
  } finally {
    loading.value = false
  }
}

const confirmDelete = (product) => {
  productToDelete.value = product
  showDeleteModal.value = true
}

const closeDeleteModal = () => {
  if (deleting.value) return
  showDeleteModal.value = false
  productToDelete.value = null
}

const deleteProduct = async () => {
  if (!productToDelete.value) return

  try {
    deleting.value = true
    await axios.delete(`${API_BASE}/products/${productToDelete.value.id_produit}`)
    closeDeleteModal()
    await loadMyProducts()
  } catch (err) {
    console.error('Erreur suppression annonce:', err)
    alert(err?.response?.data?.message || 'Erreur lors de la suppression')
  } finally {
    deleting.value = false
  }
}

const formatPrice = (p) =>
  new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' }).format(p || 0)

const goToCreate = () => router.push('/marketplace/create')
const goMarketplace = () => router.push('/marketplace')
const goToEdit = (id) => router.push(`/marketplace/${id}/edit`)
const goToDetails = (id) => router.push(`/marketplace/${id}`)

onMounted(loadMyProducts)
</script>

<style scoped>
.my-products-page {
  min-height: 100vh;
  background:
    radial-gradient(circle at top left, rgba(99, 102, 241, 0.10), transparent 28%),
    linear-gradient(180deg, #f7f8fc 0%, #f1f4fb 100%);
  padding: 2.5rem 0 3rem;
}

.container {
  max-width: 1380px;
  margin: 0 auto;
  padding: 0 1.5rem;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
  gap: 1rem;
  margin-bottom: 1.75rem;
}

.page-header h1 {
  margin: 0;
  font-size: 2.2rem;
  font-weight: 800;
  color: #0f172a;
}

.header-actions {
  display: flex;
  gap: 0.85rem;
  flex-wrap: wrap;
}

.btn-create,
.btn-secondary,
.btn-retry {
  border: none;
  border-radius: 14px;
  padding: 0.9rem 1.2rem;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-create {
  color: white;
  background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
  box-shadow: 0 10px 24px rgba(99, 102, 241, 0.24);
}

.btn-secondary {
  color: #111827;
  background: white;
  border: 1px solid #e5e7eb;
  box-shadow: 0 8px 20px rgba(15, 23, 42, 0.06);
}

.btn-retry {
  color: white;
  background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
}

.btn-create:hover,
.btn-secondary:hover,
.btn-retry:hover {
  transform: translateY(-2px);
}

.loading,
.error-message,
.no-products {
  background: rgba(255, 255, 255, 0.9);
  border: 1px solid #e7edf6;
  border-radius: 24px;
  box-shadow: 0 16px 40px rgba(15, 23, 42, 0.06);
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

.products-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(290px, 1fr));
  gap: 1.5rem;
}

.product-card {
  background: white;
  border: 1px solid #e8edf6;
  border-radius: 22px;
  overflow: hidden;
  box-shadow: 0 16px 34px rgba(15, 23, 42, 0.07);
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.product-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 22px 40px rgba(15, 23, 42, 0.10);
}

.product-image {
  height: 220px;
  background: #f8fafc;
  display: flex;
  align-items: center;
  justify-content: center;
}

.product-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.placeholder {
  font-size: 4rem;
}

.product-content {
  padding: 1rem 1rem 1.1rem;
}

.product-content h3 {
  margin: 0 0 0.55rem;
  font-size: 1.35rem;
  font-weight: 800;
  color: #0f172a;
}

.description {
  color: #64748b;
  line-height: 1.55;
  min-height: 48px;
  margin-bottom: 0.65rem;
}

.category {
  color: #7c3aed;
  font-weight: 700;
  margin-bottom: 0.55rem;
}

.price {
  color: #2563eb;
  font-size: 1.55rem;
  font-weight: 800;
  margin-bottom: 0.9rem;
}

.actions {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 0.6rem;
}

.btn-view,
.btn-edit,
.btn-delete {
  border: none;
  border-radius: 12px;
  padding: 0.8rem 0.6rem;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-view {
  background: #eff6ff;
  color: #2563eb;
}

.btn-edit {
  background: #f5f3ff;
  color: #7c3aed;
}

.btn-delete {
  background: #fef2f2;
  color: #dc2626;
}

.btn-view:hover,
.btn-edit:hover,
.btn-delete:hover {
  transform: translateY(-1px);
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
  width: 100%;
  max-width: 430px;
  background: white;
  border-radius: 22px;
  padding: 1.5rem;
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
  .page-header {
    flex-direction: column;
    align-items: stretch;
  }

  .actions {
    grid-template-columns: 1fr;
  }
}
</style>