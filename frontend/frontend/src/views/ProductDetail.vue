<template>
  <div class="product-detail-page">
    <div class="container">
      <!-- Navigation breadcrumb -->
      <nav class="breadcrumb">
        <router-link to="/marketplace">← Retour à la marketplace</router-link>
      </nav>

      <!-- Chargement -->
      <div v-if="loading" class="loading">
        <div class="spinner"></div>
        <p>Chargement du produit...</p>
      </div>

      <!-- Erreur -->
      <div v-else-if="error" class="error-message">
        <p>❌ {{ error }}</p>
        <router-link to="/marketplace" class="btn-back">Retour</router-link>
      </div>

      <!-- Détails du produit -->
      <div v-else-if="product" class="product-detail">
        <!-- Section principale -->
        <div class="product-main">
          <!-- Galerie d'images -->
          <div class="product-gallery">
            <div class="main-image">
              <img
                v-if="currentImage"
                :src="currentImage"
                :alt="product.nom"
              />
              <div v-else class="placeholder-image">🛍️</div>
            </div>
            
            <!-- Miniatures -->
            <div v-if="product.images && product.images.length > 1" class="thumbnails">
              <div
                v-for="(image, index) in product.images"
                :key="index"
                class="thumbnail"
                :class="{ active: currentImage === image }"
                @click="currentImage = image"
              >
                <img :src="image" :alt="`${product.nom} ${index + 1}`" />
              </div>
            </div>
          </div>

          <!-- Informations produit -->
          <div class="product-info">
            <h1 class="product-title">{{ product.nom }}</h1>
            
            <div class="product-price-section">
              <span class="product-price">{{ formatPrice(product.prix) }}</span>
              <span v-if="product.statut === 'vendu'" class="badge badge-sold">Vendu</span>
              <span v-else-if="product.quantite === 0" class="badge badge-outofstock">Rupture de stock</span>
              <span v-else class="badge badge-available">Disponible</span>
            </div>

            <div class="product-meta">
              <div class="meta-item">
                <strong>Catégorie:</strong>
                <span>{{ product.categorie_nom || 'Non catégorisé' }}</span>
              </div>
              <div class="meta-item" v-if="product.quantite !== null">
                <strong>Stock:</strong>
                <span>{{ product.quantite }} unité(s)</span>
              </div>
              <div class="meta-item">
                <strong>Publié le:</strong>
                <span>{{ formatDate(product.date_publication) }}</span>
              </div>
            </div>

            <div class="product-description">
              <h3>Description</h3>
              <p>{{ product.description || 'Aucune description disponible.' }}</p>
            </div>

            <div class="product-actions">
              <button
                v-if="product.statut !== 'vendu' && product.quantite > 0"
                class="btn-add-cart"
                @click="addToCart"
              >
                🛒 Ajouter au panier
              </button>
              <button class="btn-contact">
                💬 Contacter le vendeur
              </button>
              <button class="btn-share" @click="shareProduct">
                🔗 Partager
              </button>
            </div>
          </div>
        </div>

        <!-- Section produits similaires -->
        <div v-if="similarProducts.length > 0" class="similar-products">
          <h2>Produits similaires</h2>
          <div class="products-grid">
            <ProductCard
              v-for="product in similarProducts"
              :key="product.id_produit"
              :product="product"
            />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import axios from 'axios'
import ProductCard from '../components/ProductCard.vue'

export default {
  name: 'ProductDetail',
  components: {
    ProductCard
  },
  setup() {
    const route = useRoute()
    const router = useRouter()
    
    const product = ref(null)
    const similarProducts = ref([])
    const loading = ref(true)
    const error = ref(null)
    const currentImage = ref(null)

    const API_BASE = 'http://localhost:6090/marketplace'

    // Charger le produit
    const loadProduct = async () => {
      loading.value = true
      error.value = null

      try {
        const productId = route.params.id
        const response = await axios.get(`${API_BASE}/products/${productId}`)
        
        product.value = response.data.data

        // Initialiser l'image actuelle
        if (product.value.images && product.value.images.length > 0) {
          currentImage.value = product.value.images[0]
        } else if (product.value.image_url) {
          currentImage.value = product.value.image_url
        }

        // Charger les produits similaires
        if (product.value.id_categorie) {
          loadSimilarProducts(product.value.id_categorie, product.value.id_produit)
        }
      } catch (err) {
        console.error('Erreur chargement produit:', err)
        error.value = 'Produit introuvable'
      } finally {
        loading.value = false
      }
    }

    // Charger les produits similaires (même catégorie)
    const loadSimilarProducts = async (categoryId, excludeProductId) => {
      try {
        const response = await axios.get(
          `${API_BASE}/products?categorie=${categoryId}&limit=4`
        )
        
        // Exclure le produit actuel et limiter à 4
        similarProducts.value = (response.data.data || [])
          .filter(p => p.id_produit !== excludeProductId)
          .slice(0, 4)
      } catch (err) {
        console.error('Erreur chargement produits similaires:', err)
      }
    }

    // Formater le prix
    const formatPrice = (price) => {
      return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR'
      }).format(price)
    }

    // Formater la date
    const formatDate = (dateString) => {
      if (!dateString) return 'Date inconnue'
      const date = new Date(dateString)
      return date.toLocaleDateString('fr-FR', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      })
    }

    // Ajouter au panier
    const addToCart = () => {
      // À implémenter avec Pinia store (Vendredi)
      alert(`"${product.value.nom}" ajouté au panier !`)
    }

    // Partager le produit
    const shareProduct = () => {
      const url = window.location.href
      navigator.clipboard.writeText(url).then(() => {
        alert('Lien copié dans le presse-papiers !')
      })
    }

    onMounted(() => {
      loadProduct()
    })

    return {
      product,
      similarProducts,
      loading,
      error,
      currentImage,
      formatPrice,
      formatDate,
      addToCart,
      shareProduct
    }
  }
}
</script>

<style scoped>
.product-detail-page {
  min-height: 100vh;
  background: #f8f9fa;
  padding: 2rem 0;
}

.container {
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 1.5rem;
}

.breadcrumb {
  margin-bottom: 2rem;
}

.breadcrumb a {
  color: #0d6efd;
  text-decoration: none;
  font-weight: 500;
  transition: color 0.2s;
}

.breadcrumb a:hover {
  color: #0b5ed7;
  text-decoration: underline;
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
}

.error-message p {
  color: #dc3545;
  font-size: 1.2rem;
  margin-bottom: 1.5rem;
}

.btn-back {
  display: inline-block;
  background: #0d6efd;
  color: white;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  text-decoration: none;
  font-weight: 600;
}

.product-detail {
  background: white;
  border-radius: 12px;
  padding: 2rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.product-main {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 3rem;
  margin-bottom: 4rem;
}

.product-gallery {
  position: sticky;
  top: 2rem;
  height: fit-content;
}

.main-image {
  width: 100%;
  height: 500px;
  background: #f8f9fa;
  border-radius: 12px;
  overflow: hidden;
  margin-bottom: 1rem;
}

.main-image img {
  width: 100%;
  height: 100%;
  object-fit: contain;
}

.placeholder-image {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 8rem;
  color: #dee2e6;
}

.thumbnails {
  display: flex;
  gap: 0.75rem;
  overflow-x: auto;
}

.thumbnail {
  width: 80px;
  height: 80px;
  border: 2px solid #dee2e6;
  border-radius: 8px;
  overflow: hidden;
  cursor: pointer;
  transition: all 0.2s;
  flex-shrink: 0;
}

.thumbnail:hover,
.thumbnail.active {
  border-color: #0d6efd;
  transform: scale(1.05);
}

.thumbnail img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.product-info {
  padding-top: 1rem;
}

.product-title {
  font-size: 2rem;
  font-weight: 700;
  color: #212529;
  margin: 0 0 1.5rem 0;
}

.product-price-section {
  display: flex;
  align-items: center;
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.product-price {
  font-size: 2.5rem;
  font-weight: 800;
  color: #0d6efd;
}

.badge {
  padding: 0.5rem 1rem;
  border-radius: 20px;
  font-size: 0.85rem;
  font-weight: 700;
  text-transform: uppercase;
}

.badge-available {
  background: #28a745;
  color: white;
}

.badge-sold {
  background: #dc3545;
  color: white;
}

.badge-outofstock {
  background: #ffc107;
  color: #000;
}

.product-meta {
  background: #f8f9fa;
  padding: 1.5rem;
  border-radius: 8px;
  margin-bottom: 2rem;
}

.meta-item {
  display: flex;
  justify-content: space-between;
  padding: 0.5rem 0;
  border-bottom: 1px solid #dee2e6;
}

.meta-item:last-child {
  border-bottom: none;
}

.meta-item strong {
  color: #495057;
}

.meta-item span {
  color: #212529;
}

.product-description {
  margin-bottom: 2rem;
}

.product-description h3 {
  font-size: 1.3rem;
  font-weight: 700;
  color: #212529;
  margin-bottom: 1rem;
}

.product-description p {
  color: #495057;
  line-height: 1.8;
  font-size: 1.05rem;
}

.product-actions {
  display: flex;
  gap: 1rem;
  flex-wrap: wrap;
}

.product-actions button {
  flex: 1;
  min-width: 200px;
  padding: 1rem 1.5rem;
  border: none;
  border-radius: 8px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-add-cart {
  background: #28a745;
  color: white;
}

.btn-add-cart:hover {
  background: #218838;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
}

.btn-contact {
  background: #0d6efd;
  color: white;
}

.btn-contact:hover {
  background: #0b5ed7;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
}

.btn-share {
  background: #6c757d;
  color: white;
}

.btn-share:hover {
  background: #5a6268;
  transform: translateY(-2px);
}

.similar-products {
  margin-top: 4rem;
  padding-top: 2rem;
  border-top: 2px solid #dee2e6;
}

.similar-products h2 {
  font-size: 1.8rem;
  font-weight: 700;
  color: #212529;
  margin-bottom: 2rem;
}

.products-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 2rem;
}

@media (max-width: 992px) {
  .product-main {
    grid-template-columns: 1fr;
    gap: 2rem;
  }

  .product-gallery {
    position: relative;
    top: auto;
  }

  .main-image {
    height: 400px;
  }

  .product-title {
    font-size: 1.6rem;
  }

  .product-price {
    font-size: 2rem;
  }

  .product-actions button {
    min-width: 100%;
  }
}

@media (max-width: 576px) {
  .main-image {
    height: 300px;
  }

  .product-detail {
    padding: 1.5rem;
  }

  .products-grid {
    grid-template-columns: 1fr;
  }
}
</style>
