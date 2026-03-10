<template>
  <div class="marketplace-page">
    <div class="container">
      <div class="page-header">
        <div>
          <p class="page-kicker">Marketplace</p>
          <h1>Trouvez ce qu’il vous faut</h1>
        </div>

        <div class="header-actions">
          <button class="btn btn-primary" @click="goToCreate">
            Ajouter une annonce
          </button>
          <button class="btn btn-secondary" @click="goToMyProducts">
            Mes annonces
          </button>
        </div>
      </div>

      <div class="filters-section">
        <div class="search-bar">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Rechercher une annonce..."
            @input="handleSearch"
            class="search-input"
          />
          <button class="search-btn" @click="loadProducts">Rechercher</button>
        </div>

        <div class="filters">
          <select
            v-model="selectedCategory"
            @change="applyFilters"
            class="filter-select"
          >
            <option value="">Toutes les catégories</option>
            <option
              v-for="category in categories"
              :key="category.id_categorie"
              :value="category.id_categorie"
            >
              {{ category.nom }}
            </option>
          </select>

          <div class="price-filter">
            <input
              v-model.number="priceMin"
              type="number"
              placeholder="Prix min"
              class="price-input"
              min="0"
            />
            <span class="price-separator">—</span>
            <input
              v-model.number="priceMax"
              type="number"
              placeholder="Prix max"
              class="price-input"
              min="0"
            />
          </div>

          <button class="btn btn-dark" @click="applyFilters">Appliquer</button>
          <button class="btn btn-ghost" @click="resetFilters">Réinitialiser</button>
        </div>
      </div>

      <div class="results-info" v-if="!loading && !error">
        <p>
          {{ totalProducts }} annonce{{ totalProducts > 1 ? 's' : '' }}
          trouvée{{ totalProducts > 1 ? 's' : '' }}
        </p>
      </div>

      <div v-if="loading" class="loading">
        <div class="spinner"></div>
        <p>Chargement des annonces...</p>
      </div>

      <div v-else-if="error" class="error-message">
        <p>{{ error }}</p>
        <button class="btn btn-primary" @click="loadProducts">Réessayer</button>
      </div>

      <div v-else-if="products.length > 0" class="products-grid">
        <ProductCard
          v-for="(product, idx) in products"
          :key="product.id_produit || idx"
          :product="product"
        />
      </div>

      <div v-else class="no-products">
        <p>Aucun produit trouvé</p>
      </div>

      <div v-if="totalPages > 1" class="pagination">
        <button
          class="page-btn"
          :disabled="currentPage === 1"
          @click="changePage(currentPage - 1)"
        >
          Précédent
        </button>

        <span class="page-info">
          Page {{ currentPage }} sur {{ totalPages }}
        </span>

        <button
          class="page-btn"
          :disabled="currentPage === totalPages"
          @click="changePage(currentPage + 1)"
        >
          Suivant
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import ProductCard from '../components/ProductCard.vue'

export default {
  name: 'Marketplace',
  components: { ProductCard },
  setup() {
    const router = useRouter()

    const API_BASE = 'http://localhost:6090/marketplace'
    const FILES_BASE = 'http://localhost:3001'
    const itemsPerPage = 12

    const products = ref([])
    const categories = ref([])
    const loading = ref(false)
    const error = ref(null)

    const searchQuery = ref('')
    const selectedCategory = ref('')
    const priceMin = ref(null)
    const priceMax = ref(null)

    const currentPage = ref(1)
    const totalProducts = ref(0)
    const totalPages = ref(1)

    const goToCreate = () => router.push('/marketplace/create')
    const goToMyProducts = () => router.push('/marketplace/my-products')

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
        (Array.isArray(p?.medias) && p.medias.length
          ? (p.medias[0]?.media_url ?? p.medias[0]?.url)
          : null)

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

    const loadCategories = async () => {
      try {
        const response = await axios.get(`${API_BASE}/categories`)
        categories.value = response?.data?.data || []
      } catch (err) {
        console.error('Erreur chargement catégories:', err)
      }
    }

    const loadProducts = async () => {
      try {
        loading.value = true
        error.value = null

        const params = {
          limit: itemsPerPage,
          page: currentPage.value
        }

        if (searchQuery.value && searchQuery.value.trim() !== '') {
          params.search = searchQuery.value.trim()
        }

        if (selectedCategory.value !== '' && selectedCategory.value !== null) {
          params.category_id = selectedCategory.value
        }

        if (priceMin.value !== null && priceMin.value !== '') {
          params.min_price = priceMin.value
        }

        if (priceMax.value !== null && priceMax.value !== '') {
          params.max_price = priceMax.value
        }

    const response = await axios.get(`${API_BASE}/products`, { params })
    const json = response.data || {}

    console.log('🔍 Réponse API complète:', json)  // ← À ajouter

    const items = json.data ?? json.products ?? json.produits ?? []

    console.log(' Items trouvés:', items)  // ← À ajouter

      products.value = (Array.isArray(items) ? items : []).map((p, idx) =>
        normalizeProduct(p, idx)
        )

      totalProducts.value = Number(
        json.count ??
        json.total ??
        json.totalProducts ??
        products.value.length
        )

        totalPages.value = Math.max(1, Math.ceil(totalProducts.value / itemsPerPage))
      } catch (err) {
        console.error('Erreur loadProducts:', err)
        error.value =
          err?.response?.data?.message ||
          err?.message ||
          'Erreur lors du chargement des annonces'
      } finally {
        loading.value = false
      }
    }

    let searchTimeout = null
    const handleSearch = () => {
      clearTimeout(searchTimeout)
      searchTimeout = setTimeout(() => {
        currentPage.value = 1
        loadProducts()
      }, 400)
    }

    const applyFilters = () => {
      currentPage.value = 1
      loadProducts()
    }

    const changePage = (page) => {
      currentPage.value = page
      loadProducts()
      window.scrollTo({ top: 0, behavior: 'smooth' })
    }

    const resetFilters = () => {
      searchQuery.value = ''
      selectedCategory.value = ''
      priceMin.value = null
      priceMax.value = null
      currentPage.value = 1
      loadProducts()
    }

    onMounted(async () => {
      await loadCategories()
      await loadProducts()
    })

    return {
      products,
      categories,
      loading,
      error,
      searchQuery,
      selectedCategory,
      priceMin,
      priceMax,
      currentPage,
      totalPages,
      totalProducts,
      handleSearch,
      loadProducts,
      applyFilters,
      changePage,
      resetFilters,
      goToCreate,
      goToMyProducts
    }
  }
}
</script>

<style scoped>
.marketplace-page {
  min-height: 100vh;
  background:
    radial-gradient(circle at top left, rgba(102, 126, 234, 0.08), transparent 28%),
    radial-gradient(circle at top right, rgba(118, 75, 162, 0.08), transparent 24%),
    #f6f7fb;
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
  margin-bottom: 2rem;
  flex-wrap: wrap;
}

.page-kicker {
  font-size: 0.9rem;
  font-weight: 700;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: #7c3aed;
  margin-bottom: 0.35rem;
}

.page-header h1 {
  font-size: 2.4rem;
  color: #121826;
  margin: 0;
  line-height: 1.1;
  letter-spacing: -0.03em;
}

.header-actions {
  display: flex;
  gap: 0.9rem;
  flex-wrap: wrap;
}

.btn {
  border: none;
  border-radius: 14px;
  padding: 0.82rem 1.2rem;
  font-size: 0.95rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.22s ease;
}

.btn:hover {
  transform: translateY(-1px);
}

.btn-primary {
  background: linear-gradient(135deg, #3b82f6, #2563eb);
  color: white;
  box-shadow: 0 10px 22px rgba(59, 130, 246, 0.22);
}

.btn-primary:hover {
  box-shadow: 0 14px 28px rgba(59, 130, 246, 0.28);
}

.btn-secondary {
  background: white;
  color: #1f2937;
  border: 1px solid #e7eaf3;
  box-shadow: 0 8px 18px rgba(15, 23, 42, 0.06);
}

.btn-dark {
  background: #111827;
  color: white;
}

.btn-ghost {
  background: #eef2f7;
  color: #475569;
}

.filters-section {
  background: rgba(255, 255, 255, 0.88);
  backdrop-filter: blur(8px);
  padding: 1.35rem;
  border-radius: 22px;
  box-shadow: 0 16px 40px rgba(15, 23, 42, 0.08);
  border: 1px solid rgba(255, 255, 255, 0.8);
  margin-bottom: 1.5rem;
}

.search-bar {
  display: flex;
  gap: 0.75rem;
  margin-bottom: 1rem;
}

.search-input {
  flex: 1;
  padding: 0.95rem 1rem;
  border: 1px solid #dbe2ea;
  border-radius: 14px;
  font-size: 0.98rem;
  background: #fbfcfe;
  color: #111827;
  transition: all 0.2s ease;
}

.search-input:focus,
.filter-select:focus,
.price-input:focus {
  outline: none;
  border-color: #9aa8ff;
  box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.12);
  background: white;
}

.search-btn {
  background: linear-gradient(135deg, #3b82f6, #2563eb);
  color: white;
  border: none;
  padding: 0.95rem 1.25rem;
  border-radius: 14px;
  font-size: 0.95rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.22s ease;
}

.search-btn:hover {
  transform: translateY(-1px);
}

.filters {
  display: flex;
  gap: 0.9rem;
  flex-wrap: wrap;
  align-items: center;
}

.filter-select,
.price-input {
  padding: 0.85rem 1rem;
  border: 1px solid #dbe2ea;
  border-radius: 14px;
  font-size: 0.95rem;
  background: #fbfcfe;
  color: #111827;
}

.filter-select {
  min-width: 220px;
}

.price-filter {
  display: flex;
  gap: 0.65rem;
  align-items: center;
}

.price-input {
  width: 130px;
}

.price-separator {
  color: #64748b;
  font-weight: 600;
}

.results-info {
  margin-bottom: 1.3rem;
}

.results-info p {
  color: #4b5563;
  font-size: 0.96rem;
  font-weight: 500;
}

.loading {
  text-align: center;
  padding: 4rem 0;
  color: #475569;
}

.spinner {
  width: 48px;
  height: 48px;
  border: 4px solid #e5e7eb;
  border-top-color: #667eea;
  border-radius: 50%;
  animation: spin 0.9s linear infinite;
  margin: 0 auto 1rem;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.error-message,
.no-products {
  text-align: center;
  padding: 3rem;
  background: white;
  border-radius: 22px;
  box-shadow: 0 16px 36px rgba(15, 23, 42, 0.08);
  border: 1px solid #edf1f6;
}

.error-message p {
  color: #dc3545;
  font-size: 1.05rem;
  margin-bottom: 1rem;
}

.no-products p {
  font-size: 1.08rem;
  color: #64748b;
  margin: 0;
}

.products-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(285px, 1fr));
  gap: 1.6rem;
  margin-bottom: 2.5rem;
}

.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 1rem;
  margin-top: 1rem;
}

.page-btn {
  background: white;
  color: #334155;
  border: 1px solid #dbe2ea;
  padding: 0.8rem 1.15rem;
  border-radius: 12px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
}

.page-btn:hover:not(:disabled) {
  transform: translateY(-1px);
  border-color: #bcc7d8;
}

.page-btn:disabled {
  opacity: 0.45;
  cursor: not-allowed;
}

.page-info {
  font-weight: 600;
  color: #475569;
}

@media (max-width: 768px) {
  .marketplace-page {
    padding: 1.5rem 0 2rem;
  }

  .page-header {
    flex-direction: column;
    align-items: stretch;
  }

  .page-header h1 {
    font-size: 2rem;
  }

  .header-actions {
    width: 100%;
    flex-direction: column;
  }

  .header-actions .btn {
    width: 100%;
  }

  .search-bar {
    flex-direction: column;
  }

  .filters {
    flex-direction: column;
    align-items: stretch;
  }

  .filter-select,
  .price-filter,
  .price-input,
  .btn-dark,
  .btn-ghost {
    width: 100%;
  }

  .price-filter {
    flex-direction: column;
  }

  .products-grid {
    grid-template-columns: 1fr;
  }

  .pagination {
    flex-direction: column;
    gap: 0.75rem;
  }
}
</style>