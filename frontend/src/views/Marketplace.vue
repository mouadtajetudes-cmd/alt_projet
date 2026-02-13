<template>
  <div class="marketplace-page">
    <div class="container">
      <h1>Marketplace</h1>
      
      <div v-if="loading" class="loading">Chargement des produits...</div>
      
      <div v-else class="products-grid">
        <div v-for="product in products" :key="product.id_produit" class="product-card">
          <div class="product-image">🛍️</div>
          <h3>{{ product.nom }}</h3>
          <p class="description">{{ product.description }}</p>
          <p class="price">{{ product.prix }} €</p>
          <button class="btn">Voir détails</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'

export default {
  name: 'Marketplace',
  setup() {
    const products = ref([])
    const loading = ref(true)
    
    const loadProducts = async () => {
      try {
        const response = await fetch('http://localhost:6086/products')
        const data = await response.json()
        products.value = data
      } catch (error) {
        console.error('Erreur chargement produits:', error)
      } finally {
        loading.value = false
      }
    }
    
    onMounted(() => {
      loadProducts()
    })
    
    return {
      products,
      loading
    }
  }
}
</script>

<style scoped>
.marketplace-page {
  padding: 2rem 0;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 1rem;
}

h1 {
  margin-bottom: 2rem;
  color: #333;
}

.loading {
  text-align: center;
  padding: 3rem;
  color: #666;
}

.products-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  gap: 2rem;
}

.product-card {
  background: white;
  padding: 1.5rem;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  transition: transform 0.2s;
}

.product-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.product-image {
  font-size: 4rem;
  text-align: center;
  margin-bottom: 1rem;
}

.product-card h3 {
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
</style>
