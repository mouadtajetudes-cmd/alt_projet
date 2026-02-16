<template>
  <div class="marketplace-page">
    <div class="container">
      <div class="page-header">
        <h1>🛒 Marketplace</h1>
        <button class="btn-create" @click="goToCreate">
          ➕ Ajouter une annonce
        </button>
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
          <button class="search-btn" @click="loadProducts">🔍</button>
        </div>

        <div class="filters">
          <select v-model="selectedCategory" @change="loadProducts" class="filter-select">
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
            <span>-</span>
            <input
              v-model.number="priceMax"
              type="number"
              placeholder="Prix max"
              class="price-input"
              min="0"
            />
          </div>

          <button class="btn-filter" @click="loadProducts">Appliquer</button>
          <button class="btn-reset" @click="resetFilters">Réinitialiser</button>
        </div>
      </div>

      <div class="results-info" v-if="!loading">
        <p>{{ totalProducts }} annonce{{ totalProducts > 1 ? 's' : '' }} trouvée{{ totalProducts > 1 ? 's' : '' }}</p>
      </div>

      <div v-if="loading" class="loading">
        <div class="spinner"></div>
        <p>Chargement des annonces...</p>
      </div>

      <div v-else-if="error" class="error-message">
        <p> {{ error }}</p>
        <button class="btn-retry" @click="loadProducts">Réessayer</button>
      </div>

      <div v-else-if="products.length > 0" class="products-grid">
        <ProductCard
          v-for="product in products"
          :key="product.id_produit"
          :product="product"
        />
      </div>

      <div v-else class="no-products">
        <p> Aucun produit trouvé</p>
        <button class="btn-create" @click="goToCreate">
           Créer la première annonce
        </button>
      </div>

      <div v-if="totalPages > 1" class="pagination">
        <button
          class="page-btn"
          :disabled="currentPage === 1"
          @click="changePage(currentPage - 1)"
        >
          ‹ Précédent
        </button>
        
        <span class="page-info">
          Page {{ currentPage }} sur {{ totalPages }}
        </span>
        
        <button
          class="page-btn"
          :disabled="currentPage === totalPages"
          @click="changePage(currentPage + 1)"
        >
          Suivant ›
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
    ProductCard
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
    
<<<<<<< HEAD
  
=======
>>>>>>> origin/groupe/develop
    const currentPage = ref(1)
    const itemsPerPage = 12
    const totalProducts = ref(0)
    const totalPages = ref(1)

    const API_BASE = 'http://localhost:6090/marketplace'

<<<<<<< HEAD

=======
>>>>>>> origin/groupe/develop
    const loadCategories = async () => {
      try {
        const response = await axios.get(`${API_BASE}/categories`)
        categories.value = response.data || []
      } catch (err) {
        console.error('Erreur chargement catégories:', err)
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
        products.value = response.data || []
        totalProducts.value = response.data.count || products.value.length
        totalPages.value = Math.ceil(totalProducts.value / itemsPerPage)
      } catch (err) {
        console.error('Erreur chargement produits:', err)
        error.value = 'Impossible de charger les produits. Vérifiez que le serveur est démarré.'
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

    const goToCreate = () => {
<<<<<<< HEAD
      router.push({ name: 'CreateProduct' })
    }

    const goToCategories = () => {
      router.push({ name: 'Categories' })
=======
      alert('Fonctionnalité de création à venir !')
>>>>>>> origin/groupe/develop
    }

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
      goToCreate
    }
  }
}
</script>

<style scoped>
.marketplace-page {
  min-height: 100vh;
  background: #f8f9fa;
  padding: 2rem 0;
}

.container {
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 1.5rem;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}

.page-header h1 {
  font-size: 2.5rem;
  color: #212529;
  margin: 0;
}

.btn-create {
  background: #28a745;
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-create:hover {
  background: #218838;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
}

.filters-section {
  background: white;
  padding: 1.5rem;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  margin-bottom: 2rem;
}

.search-bar {
  display: flex;
  gap: 0.5rem;
  margin-bottom: 1rem;
}

.search-input {
  flex: 1;
  padding: 0.75rem 1rem;
  border: 2px solid #dee2e6;
  border-radius: 8px;
  font-size: 1rem;
  transition: border-color 0.2s;
}

.search-input:focus {
  outline: none;
  border-color: #0d6efd;
}

.search-btn {
  background: #0d6efd;
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  font-size: 1.2rem;
  cursor: pointer;
  transition: all 0.2s;
}

.search-btn:hover {
  background: #0b5ed7;
}

.filters {
  display: flex;
  gap: 1rem;
  flex-wrap: wrap;
  align-items: center;
}

.filter-select {
  padding: 0.65rem 1rem;
  border: 2px solid #dee2e6;
  border-radius: 8px;
  font-size: 0.95rem;
  background: white;
  cursor: pointer;
  min-width: 200px;
}

.price-filter {
  display: flex;
  gap: 0.5rem;
  align-items: center;
}

.price-input {
  width: 120px;
  padding: 0.65rem 1rem;
  border: 2px solid #dee2e6;
  border-radius: 8px;
  font-size: 0.95rem;
}

.btn-filter {
  background: #0d6efd;
  color: white;
  border: none;
  padding: 0.65rem 1.5rem;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-filter:hover {
  background: #0b5ed7;
}

.btn-reset {
  background: #6c757d;
  color: white;
  border: none;
  padding: 0.65rem 1.5rem;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-reset:hover {
  background: #5a6268;
}

.results-info {
  margin-bottom: 1.5rem;
  color: #6c757d;
  font-size: 0.95rem;
}

.loading {
  text-align: center;
  padding: 4rem 0;
}

.spinner {
  width: 50px;
  height: 50px;
  border: 4px solid #f3f3f3;
  border-top: 4px solid #0d6efd;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto 1rem;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.error-message {
  text-align: center;
  padding: 3rem;
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.error-message p {
  color: #dc3545;
  font-size: 1.1rem;
  margin-bottom: 1rem;
}

.btn-retry {
  background: #0d6efd;
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
}

.products-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 2rem;
  margin-bottom: 3rem;
}

.no-products {
  text-align: center;
  padding: 4rem;
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.no-products p {
  font-size: 1.3rem;
  color: #6c757d;
  margin-bottom: 1.5rem;
}

.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 1.5rem;
  margin-top: 3rem;
}

.page-btn {
  background: white;
  color: #0d6efd;
  border: 2px solid #0d6efd;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
}

.page-btn:hover:not(:disabled) {
  background: #0d6efd;
  color: white;
}

.page-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.page-info {
  font-weight: 600;
  color: #495057;
}

@media (max-width: 768px) {
  .page-header {
    flex-direction: column;
    gap: 1rem;
    align-items: stretch;
  }

  .page-header h1 {
    font-size: 2rem;
  }

  .filters {
    flex-direction: column;
    align-items: stretch;
  }

  .filter-select,
  .price-filter {
    width: 100%;
  }

  .products-grid {
    grid-template-columns: 1fr;
  }

  .pagination {
    flex-direction: column;
    gap: 1rem;
  }
<<<<<<< HEAD
  .product-card h3 {
=======
}
.product-card h3 {
>>>>>>> origin/groupe/develop
  margin-bottom: 0.5rem;
  color: #333;
}

.description {
  color: #666;
  margin-bottom: 1rem;
  font-size: 0.9rem;
}

.price {
  font-size: 1.5rem;
  font-weight: bold;
  color: #0d6efd;
  margin-bottom: 1rem;
}

.btn {
  width: 100%;
  padding: 0.75rem;
  background: #0d6efd;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  transition: background 0.2s;
}

.btn:hover {
  background: #0b5ed7;
  }
}
</style>


