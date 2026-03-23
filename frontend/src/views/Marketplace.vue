<template>
  <div class="marketplace-page">
    <div class="container">
      <div class="page-header">
        <div>
          <p class="page-kicker">Marketplace</p>
          <h1>Trouvez ce qu'il vous faut</h1>
        </div>
        <div class="header-actions">
          <button class="btn btn-primary" @click="goToCreate">
            Ajouter une annonce
          </button>
        </div>
      </div>

      <div class="filters-section">
        <div class="search-bar">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Rechercher un produit..."
            @input="handleSearch"
            class="search-input"
          />
          <button class="search-btn" @click="loadProducts">Rechercher</button>
        </div>

        <div class="filters">
          <select v-model="selectedCategory" @change="loadProducts" class="filter-select">
            <option value="">Toutes les categories</option>
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
            <span class="price-separator">-</span>
            <input
              v-model.number="priceMax"
              type="number"
              placeholder="Prix max"
              class="price-input"
              min="0"
            />
          </div>

          <button class="btn btn-dark" @click="loadProducts">Appliquer</button>
          <button class="btn btn-ghost" @click="resetFilters">Reinitialiser</button>
        </div>
      </div>

      <div class="results-info" v-if="!loading">
        <p>
          {{ totalProducts }} produit{{ totalProducts > 1 ? 's' : '' }}
          trouve{{ totalProducts > 1 ? 's' : '' }}
        </p>
      </div>

      <div v-if="loading" class="loading">
        <div class="spinner"></div>
        <p>Chargement des produits...</p>
      </div>

      <div v-else-if="error" class="error-message">
        <p>{{ error }}</p>
        <button class="btn btn-primary" @click="loadProducts">Reessayer</button>
      </div>

      <div v-else-if="products.length > 0" class="products-grid">
        <ProductCard
          v-for="product in products"
          :key="product.id_produit"
          :product="product"
        />
      </div>

      <div v-else class="no-products">
        <p>Aucun produit trouve</p>
      </div>

      <div v-if="totalPages > 1" class="pagination">
        <button
          class="page-btn"
          :disabled="currentPage === 1"
          @click="changePage(currentPage - 1)"
        >
          Precedent
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
  components: {
    ProductCard,
  },
  setup() {
    const router = useRouter()
    const products = ref([])
    const categories = ref([])
    const loading = ref(true)
    const error = ref(null)

    const searchQuery = ref('')
    const selectedCategory = ref('')
    const priceMin = ref(null)
    const priceMax = ref(null)

    const currentPage = ref(1)
    const itemsPerPage = 12
    const totalProducts = ref(0)
    const totalPages = ref(1)

    const API_BASE = 'http://localhost:6090/marketplace'

    const loadCategories = async () => {
      try {
        const response = await axios.get(`${API_BASE}/categories`)
        categories.value = response.data.data || []
      } catch (err) {
        console.error('Erreur chargement categories:', err)
      }
    }

    const loadProducts = async () => {
      loading.value = true
      error.value = null

      try {
        let url = `${API_BASE}/products?limit=${itemsPerPage}&page=${currentPage.value}`

        if (searchQuery.value) {
          url += `&search=${encodeURIComponent(searchQuery.value)}`
        }
        if (selectedCategory.value) {
          url += `&categorie=${selectedCategory.value}`
        }
        if (priceMin.value !== null && priceMin.value !== '') {
          url += `&prix_min=${priceMin.value}`
        }
        if (priceMax.value !== null && priceMax.value !== '') {
          url += `&prix_max=${priceMax.value}`
        }

        const response = await axios.get(url)
        products.value = response.data.data || []
        totalProducts.value = response.data.count || products.value.length
        totalPages.value = Math.max(1, Math.ceil(totalProducts.value / itemsPerPage))
      } catch (err) {
        console.error('Erreur chargement produits:', err)
        error.value = 'Impossible de charger les produits. Verifiez que le serveur est demarre.'
      } finally {
        loading.value = false
      }
    }

    let searchTimeout
    const handleSearch = () => {
      clearTimeout(searchTimeout)
      searchTimeout = setTimeout(() => {
        currentPage.value = 1
        loadProducts()
      }, 500)
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

    const goToCreate = () => router.push('/marketplace/create')

    onMounted(() => {
      loadCategories()
      loadProducts()
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
      changePage,
      resetFilters,
      goToCreate,
    }
  },
}
</script>

<style scoped src="../assets/styles/marketplace-wiem.css"></style>
