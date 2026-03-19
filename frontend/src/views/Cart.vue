<template>
  <div class="cart-page">
    <div class="container">
      <h1>🛒 Mon Panier</h1>
      
      <div v-if="cartItems.length === 0" class="empty-cart">
        <p>Votre panier est vide</p>
        <router-link to="/marketplace" class="btn-primary">
          Continuer vos achats
        </router-link>
      </div>
      
      <div v-else class="cart-content">
        <table class="cart-table">
          <thead>
            <tr>
              <th>Produit</th>
              <th>Prix</th>
              <th>Quantité</th>
              <th>Total</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in cartItems" :key="item.id_produit">
              <td>{{ item.nom }}</td>
              <td>{{ formatPrice(item.prix) }}</td>
              <td>
                <input 
                  v-model.number="item.quantite" 
                  type="number" 
                  min="1"
                  @change="updateCart"
                  class="qty-input"
                />
              </td>
              <td>{{ formatPrice(item.prix * item.quantite) }}</td>
              <td>
                <button @click="removeFromCart(item.id_produit)" class="btn-danger">
                  ❌ Supprimer
                </button>
              </td>
            </tr>
          </tbody>
        </table>
        
        <div class="cart-summary">
          <h3>Résumé</h3>
          <p>Sous-total: <strong>{{ formatPrice(subtotal) }}</strong></p>
          <p>Shipping: <strong>{{ formatPrice(5.00) }}</strong></p>
          <h2>Total: <strong>{{ formatPrice(total) }}</strong></h2>
          <button class="btn-checkout" @click="checkout">
            Procéder au paiement
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue'

export default {
  name: 'Cart',
  setup() {
    const cartItems = ref([])
    
    onMounted(() => {
      const saved = localStorage.getItem('cart')
      if (saved) {
        cartItems.value = JSON.parse(saved)
      }
    })
    
    const subtotal = computed(() => {
      return cartItems.value.reduce((sum, item) => sum + (item.prix * item.quantite), 0)
    })
    
    const total = computed(() => subtotal.value + 5.00)
    
    const updateCart = () => {
      localStorage.setItem('cart', JSON.stringify(cartItems.value))
    }
    
    const removeFromCart = (id) => {
      cartItems.value = cartItems.value.filter(item => item.id_produit !== id)
      updateCart()
    }
    
    const formatPrice = (price) => {
      return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR'
      }).format(price)
    }
    
    const checkout = () => {
      alert('Redirection vers le paiement...')
      // À implémenter avec votre système de paiement
    }
    
    return {
      cartItems,
      subtotal,
      total,
      updateCart,
      removeFromCart,
      formatPrice,
      checkout
    }
  }
}
</script>

<style scoped>
.cart-page {
  min-height: 100vh;
  background: #f8f9fa;
  padding: 2rem 0;
}

.container {
  max-width: 1000px;
  margin: 0 auto;
  padding: 0 1rem;
}

h1 {
  font-size: 2rem;
  margin-bottom: 2rem;
  color: #212529;
}

.empty-cart {
  text-align: center;
  padding: 3rem;
  background: white;
  border-radius: 12px;
}

.empty-cart p {
  font-size: 1.2rem;
  color: #6c757d;
  margin-bottom: 2rem;
}

.btn-primary {
  display: inline-block;
  background: #0d6efd;
  color: white;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  text-decoration: none;
  font-weight: 600;
}

.btn-primary:hover {
  background: #0b5ed7;
}

.cart-content {
  background: white;
  border-radius: 12px;
  padding: 2rem;
}

.cart-table {
  width: 100%;
  border-collapse: collapse;
  margin-bottom: 2rem;
}

.cart-table th,
.cart-table td {
  padding: 1rem;
  text-align: left;
  border-bottom: 1px solid #dee2e6;
}

.cart-table th {
  background: #f8f9fa;
  font-weight: 700;
}

 qty-input {
  width: 60px;
  padding: 0.5rem;
  border: 1px solid #dee2e6;
  border-radius: 6px;
}

.btn-danger {
  background: #dc3545;
  color: white;
  border: none;
  padding: 0.5rem 1rem;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 600;
}

.btn-danger:hover {
  background: #c82333;
}

.cart-summary {
  background: #f8f9fa;
  padding: 1.5rem;
  border-radius: 8px;
  max-width: 400px;
  margin-left: auto;
}

.cart-summary h3 {
  margin-bottom: 1rem;
}

.cart-summary p {
  display: flex;
  justify-content: space-between;
  margin-bottom: 0.5rem;
  font-size: 0.95rem;
}

.cart-summary h2 {
  font-size: 1.5rem;
  color: #0d6efd;
  margin-top: 1rem;
  padding-top: 1rem;
  border-top: 2px solid #dee2e6;
}

.btn-checkout {
  width: 100%;
  background: #28a745;
  color: white;
  border: none;
  padding: 1rem;
  border-radius: 8px;
  font-size: 1rem;
  font-weight: 700;
  cursor: pointer;
  margin-top: 1.5rem;
}

.btn-checkout:hover {
  background: #218838;
}
</style>
