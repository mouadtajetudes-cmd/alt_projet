<template>
  <div class="product-detail-page">
    <div class="container">
      <nav class="breadcrumb">
        <router-link to="/marketplace">Retour à la marketplace</router-link>
      </nav>

      <div v-if="loading" class="loading">
        <div class="spinner"></div>
        <p>Chargement...</p>
      </div>

      <div v-else-if="error" class="error-message">
        <p>❌ {{ error }}</p>
        <router-link to="/marketplace" class="btn-back">Retour</router-link>
      </div>

      <div v-else-if="product" class="product-detail">
        <div class="product-main">
          <div class="product-gallery">
            <div class="main-image">
              <img v-if="currentImage" :src="currentImage" :alt="product.nom" />
              <div v-else class="placeholder-image">🛍️</div>
            </div>

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

          <div class="product-info">
            <h1 class="product-title">{{ product.nom }}</h1>

            <div class="product-price-section">
              <span class="product-price">{{ formatPrice(product.prix) }}</span>

              <span v-if="product.statut === 'vendu'" class="badge badge-sold">
                Vendu
              </span>
              <span
                v-else-if="Number(product.quantite) === 0"
                class="badge badge-outofstock"
              >
                Rupture
              </span>
              <span v-else class="badge badge-available">
                Disponible
              </span>
            </div>

            <div class="product-meta">
              <div class="meta-item">
                <strong>Catégorie:</strong>
                <span> {{ product.categorie_nom || product.categorie || `Catégorie #${product.id_categorie}` }}</span>              </div>

              <div
                class="meta-item"
                v-if="product.quantite !== null && product.quantite !== undefined"
              >
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
              <button class="btn-contact" @click="contactSeller">
                💬 Contacter le vendeur
              </button>

              <button class="btn-share" @click="shareProduct">
                🔗 Partager
              </button>
            </div>
          </div>
        </div>

        <div v-if="similarProducts.length > 0" class="similar-products">
          <h2>Produits similaires</h2>
          <div class="products-grid">
            <ProductCard
              v-for="item in similarProducts"
              :key="item.id_produit"
              :product="item"
            />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import axios from 'axios'
import ProductCard from '../components/ProductCard.vue'

export default {
  name: 'ProductDetails',
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
    const FILES_BASE = 'http://localhost:3001'

    const resolveImageUrl = (raw) => {
      if (!raw || typeof raw !== 'string') return null
      if (raw.startsWith('http://') || raw.startsWith('https://')) return raw
      if (raw.startsWith('/')) return `${FILES_BASE}${raw}`
      return `${FILES_BASE}/${raw}`
    }

    const buildImagesArray = (p) => {
      const images = []

      if (Array.isArray(p?.images)) {
        p.images.forEach((img) => {
          const url = resolveImageUrl(typeof img === 'string' ? img : img?.url)
          if (url) images.push(url)
        })
      }

      if (Array.isArray(p?.media_urls)) {
        p.media_urls.forEach((img) => {
          const url = resolveImageUrl(img)
          if (url) images.push(url)
        })
      }

      if (Array.isArray(p?.medias)) {
        p.medias.forEach((media) => {
          const url = resolveImageUrl(
            media?.url || media?.media_url || media?.chemin || null
          )
          if (url) images.push(url)
        })
      }

      const singleImage =
        resolveImageUrl(
          p?.image_url ||
          p?.media_url ||
          p?.image ||
          null
        )

      if (singleImage) {
        images.unshift(singleImage)
      }

      return [...new Set(images.filter(Boolean))]
    }

    const normalizeProduct = (p) => {
      const normalizedImages = buildImagesArray(p)

      return {
        ...p,
        id_produit: Number(p?.id_produit ?? p?.id ?? 0),
        prix: Number(p?.prix ?? 0),
        quantite:
          p?.quantite !== null && p?.quantite !== undefined
            ? Number(p.quantite)
            : null,
        categorie_nom:
          p?.categorie_nom ||
          p?.categorie ||
          'Non catégorisé',
        images: normalizedImages,
        image: normalizedImages.length > 0 ? normalizedImages[0] : null
      }
    }

    const loadProduct = async () => {
      loading.value = true
      error.value = null
      product.value = null
      currentImage.value = null
      similarProducts.value = []

      try {
        const productId = Number(route.params.id)

        if (!productId) {
          throw new Error('ID produit invalide')
        }

        const response = await axios.get(`${API_BASE}/products/${productId}`)
        const rawProduct = response?.data?.data || response?.data

        if (!rawProduct) {
          throw new Error('Produit introuvable')
        }

        product.value = normalizeProduct(rawProduct)

        if (product.value.images.length > 0) {
          currentImage.value = product.value.images[0]
        }

        if (product.value.id_categorie) {
          await loadSimilarProducts(
            product.value.id_categorie,
            product.value.id_produit
          )
        }
      } catch (err) {
        console.error('Erreur chargement produit:', err)
        error.value =
          err?.response?.data?.message ||
          err?.message ||
          'Produit introuvable'
      } finally {
        loading.value = false
      }
    }

    const loadSimilarProducts = async (categoryId, excludeProductId) => {
      try {
        const response = await axios.get(`${API_BASE}/products`, {
          params: {
            category_id: categoryId,
            limit: 4
          }
        })

        similarProducts.value = (response?.data?.data || [])
          .filter((p) => Number(p.id_produit) !== Number(excludeProductId))
          .slice(0, 4)
          .map((p) => {
            const normalized = normalizeProduct(p)
            return {
              ...normalized,
              image: normalized.images.length > 0 ? normalized.images[0] : null
            }
          })
      } catch (err) {
        console.error('Erreur chargement produits similaires:', err)
      }
    }

    const formatPrice = (price) => {
      return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR'
      }).format(Number(price || 0))
    }

    const formatDate = (dateString) => {
      if (!dateString) return 'Date inconnue'

      const date = new Date(dateString)

      if (Number.isNaN(date.getTime())) {
        return 'Date inconnue'
      }

      return date.toLocaleDateString('fr-FR', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      })
    }

    const contactSeller = () => {
      if (product.value?.id_utilisateur) {
        router.push({
          name: 'Chat',
          params: { userId: product.value.id_utilisateur }
        })
        return
      }

      alert('Impossible de contacter le vendeur pour le moment.')
    }

    const shareProduct = async () => {
      try {
        const url = window.location.href
        await navigator.clipboard.writeText(url)
        alert('Lien copié dans le presse-papiers !')
      } catch (e) {
        alert('Impossible de copier le lien.')
      }
    }

    onMounted(loadProduct)

    watch(
      () => route.params.id,
      () => {
        loadProduct()
      }
    )

    return {
      product,
      similarProducts,
      loading,
      error,
      currentImage,
      formatPrice,
      formatDate,
      contactSeller,
      shareProduct
    }
  }
}
</script>

<style scoped>
.product-detail-page {
  min-height: 100vh;
  background:
    radial-gradient(circle at top left, rgba(99, 102, 241, 0.10), transparent 28%),
    linear-gradient(180deg, #f7f8fc 0%, #f1f4fb 100%);
  padding: 2.5rem 0 3rem;
}

.container {
  max-width: 1280px;
  margin: 0 auto;
  padding: 0 1.5rem;
}

.breadcrumb {
  margin-bottom: 1.5rem;
}

.breadcrumb a {
  color: #6366f1;
  text-decoration: none;
  font-weight: 700;
}

.loading,
.error-message {
  background: rgba(255,255,255,0.92);
  border: 1px solid #e7edf6;
  border-radius: 24px;
  box-shadow: 0 16px 40px rgba(15, 23, 42, 0.07);
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

.error-message p {
  color: #dc2626;
  margin-bottom: 1rem;
  font-weight: 700;
}

.btn-back {
  display: inline-block;
  text-decoration: none;
  border-radius: 14px;
  padding: 0.9rem 1.2rem;
  background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
  color: white;
  font-weight: 700;
}

.product-detail {
  background: rgba(255,255,255,0.92);
  border: 1px solid #e7edf6;
  border-radius: 28px;
  box-shadow: 0 16px 40px rgba(15, 23, 42, 0.07);
  padding: 1.6rem;
}

.product-main {
  display: grid;
  grid-template-columns: 1.05fr 1fr;
  gap: 1.8rem;
  margin-bottom: 2.2rem;
}

.product-gallery {
  position: sticky;
  top: 1.5rem;
  height: fit-content;
}

.main-image {
  width: 100%;
  height: 520px;
  background: #f8fafc;
  border: 1px solid #e7edf6;
  border-radius: 24px;
  overflow: hidden;
  margin-bottom: 0.9rem;
}

.main-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.placeholder-image {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 6rem;
  color: #cbd5e1;
}

.thumbnails {
  display: flex;
  gap: 0.75rem;
  overflow-x: auto;
}

.thumbnail {
  width: 84px;
  height: 84px;
  border: 2px solid #e2e8f0;
  border-radius: 16px;
  overflow: hidden;
  cursor: pointer;
  flex-shrink: 0;
  transition: all 0.2s ease;
}

.thumbnail:hover,
.thumbnail.active {
  border-color: #8b5cf6;
  transform: translateY(-2px);
}

.thumbnail img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.product-info {
  padding-top: 0.2rem;
}

.product-title {
  margin: 0 0 1rem;
  font-size: 2.3rem;
  line-height: 1.08;
  font-weight: 800;
  color: #0f172a;
}

.product-price-section {
  display: flex;
  align-items: center;
  gap: 1rem;
  flex-wrap: wrap;
  margin-bottom: 1.4rem;
}

.product-price {
  font-size: 2.4rem;
  font-weight: 800;
  color: #2563eb;
}

.badge {
  border-radius: 999px;
  padding: 0.55rem 1rem;
  font-size: 0.82rem;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0.04em;
}

.badge-available {
  background: #dcfce7;
  color: #15803d;
}

.badge-sold {
  background: #fee2e2;
  color: #b91c1c;
}

.badge-outofstock {
  background: #fef3c7;
  color: #b45309;
}

.product-meta {
  background: #f8fafc;
  border: 1px solid #e7edf6;
  border-radius: 20px;
  padding: 1.2rem 1.25rem;
  margin-bottom: 1.5rem;
}

.meta-item {
  display: flex;
  justify-content: space-between;
  gap: 1rem;
  padding: 0.9rem 0;
  border-bottom: 1px solid #e5e7eb;
}

.meta-item:last-child {
  border-bottom: none;
}

.meta-item strong {
  color: #334155;
}

.meta-item span {
  color: #0f172a;
  text-align: right;
  font-weight: 600;
}

.product-description {
  margin-bottom: 1.5rem;
}

.product-description h3 {
  margin: 0 0 0.8rem;
  font-size: 1.35rem;
  font-weight: 800;
  color: #0f172a;
}

.product-description p {
  margin: 0;
  color: #64748b;
  line-height: 1.75;
  white-space: pre-line;
}

.product-actions {
  display: flex;
  gap: 0.9rem;
  flex-wrap: wrap;
}

.product-actions button {
  flex: 1;
  min-width: 190px;
  border: none;
  border-radius: 16px;
  padding: 1rem 1.25rem;
  font-size: 0.96rem;
  font-weight: 800;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-contact {
  color: white;
  background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
  box-shadow: 0 10px 24px rgba(99, 102, 241, 0.24);
}

.btn-share {
  background: #eef2f7;
  color: #475569;
}

.btn-contact:hover,
.btn-share:hover {
  transform: translateY(-2px);
}

.similar-products {
  margin-top: 2.5rem;
  padding-top: 2rem;
  border-top: 1px solid #e5e7eb;
}

.similar-products h2 {
  margin: 0 0 1.25rem;
  font-size: 1.7rem;
  font-weight: 800;
  color: #0f172a;
}

.products-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 1.4rem;
}

@media (max-width: 992px) {
  .product-main {
    grid-template-columns: 1fr;
  }

  .product-gallery {
    position: relative;
    top: auto;
  }

  .main-image {
    height: 380px;
  }

  .product-title {
    font-size: 1.9rem;
  }

  .product-price {
    font-size: 2rem;
  }
}

@media (max-width: 576px) {
  .main-image {
    height: 280px;
  }

  .meta-item {
    flex-direction: column;
    align-items: flex-start;
  }

  .meta-item span {
    text-align: left;
  }

  .product-actions button {
    min-width: 100%;
  }
}
</style>