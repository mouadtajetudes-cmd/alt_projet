<template>
  <div class="cart-page">
    <div class="container">
      <div class="page-header">
        <h1>🛒 Mon panier</h1>
        <router-link to="/marketplace" class="btn-back">
          ← Continuer mes achats
        </router-link>
      </div>

      <!-- Panier vide -->
      <div v-if="cartStore.items.length === 0" class="empty-cart">
        <div class="empty-icon">🛒</div>
        <h2>Votre panier est vide</h2>
        <p>Découvrez nos produits et ajoutez-les à votre panier</p>
        <router-link to="/marketplace" class="btn-primary">
          Voir les produits
        </router-link>
      </div>

      <!-- Liste produits -->
      <div v-else class="cart-content">
        <div class="cart-items">
          <div
            v-for="item in cartStore.items"
            :key="item.id_produit"
            class="cart-item"
          >
            <div class="item-image">
              <img
                v-if="item.image_url"
                :src="item.image_url"
                :alt="item.nom"
              />
              <div v-else class="placeholder">🛍️</div>
            </div>

            <div class="item-info">
              <h3>{{ item.nom }}</h3>
              <p class="item-description">{{ item.description }}</p>
              <p class="item-price">{{ formatPrice(item.prix) }} / unité</p>
            </div>

            <div class="item-quantity">
              <button
                class="qty-btn"
                @click="cartStore.updateQuantity(item.id_produit, item.quantity - 1)"
                :disabled="item.quantity <= 1"
              >
                -
              </button>
              <input
                type="number"
                :value="item.quantity"
                @change="handleQuantityChange(item.id_produit, $event)"
                min="1"
                :max="item.quantite"
                class="qty-input"
              />
              <button
                class="qty-btn"
                @click="cartStore.updateQuantity(item.id_produit, item.quantity + 1)"
                :disabled="item.quantity >= item.quantite"
              >
                +
              </button>
            </div>

            <div class="item-total">
              <p class="total-price">{{ formatPrice(item.prix * item.quantity) }}</p>
              <button
                class="btn-remove"
                @click="cartStore.removeFromCart(item.id_produit)"
              >
                🗑️ Retirer
              </button>
            </div>
          </div>
        </div>

        <!-- Résumé commande -->
        <div class="cart-summary">
          <h2>Récapitulatif</h2>
          
          <div class="summary-line">
            <span>Sous-total ({{ cartStore.itemCount }} article{{ cartStore.itemCount > 1 ? 's' : '' }})</span>
            <span>{{ formatPrice(cartStore.totalPrice) }}</span>
          </div>
          
          <div class="summary-line">
            <span>Frais de livraison</span>
            <span>Gratuit</span>
          </div>
          
          <div class="summary-line total">
            <span><strong>Total</strong></span>
            <span><strong>{{ formatPrice(cartStore.totalPrice) }}</strong></span>
          </div>

          <button class="btn-checkout" @click="handleCheckout">
            Passer la commande
          </button>

          <button class="btn-clear" @click="handleClearCart">
            Vider le panier
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { useCartStore } from '../stores/cart'
import { useRouter } from 'vue-router'

export default {
  name: 'Cart',
  setup() {
    const cartStore = useCartStore()
    const router = useRouter()

    const formatPrice = (price) => {
      return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR'
      }).format(price)
    }

    const handleQuantityChange = (productId, event) => {
      const newQuantity = parseInt(event.target.value)
      if (newQuantity > 0) {
        cartStore.updateQuantity(productId, newQuantity)
      }
    }

    const handleCheckout = () => {
      // Simuler la commande
      alert(`Commande de ${cartStore.itemCount} article(s) pour ${formatPrice(cartStore.totalPrice)} confirmée !`)
      cartStore.clearCart()
      router.push('/marketplace')
    }

    const handleClearCart = () => {
      if (confirm('Êtes-vous sûr de vouloir vider le panier ?')) {
        cartStore.clearCart()
      }
    }

    return {
      cartStore,
      formatPrice,
      handleQuantityChange,
      handleCheckout,
      handleClearCart
    }
  }
}
</script>

<style scoped>
.cart-page {
  min-height: 100vh;
  background: #f5f7fa;
  padding: 2rem 0;
}

.container {
  max-width: 1200px;
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
  text-decoration: none;
  transition: all 0.15s ease;
}

.btn-back:hover {
  background: #f8f9fa;
  border-color: #adb5bd;
}

.empty-cart {
  text-align: center;
  background: white;
  padding: 4rem 2rem;
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
}

.empty-icon {
  font-size: 5rem;
  margin-bottom: 1rem;
  opacity: 0.3;
}

.empty-cart h2 {
  font-size: 1.5rem;
  color: #212529;
  margin-bottom: 0.5rem;
}

.empty-cart p {
  color: #6c757d;
  margin-bottom: 2rem;
}

.btn-primary {
  display: inline-block;
  background: #0d6efd;
  color: white;
  border: 1px solid #0d6efd;
  padding: 0.75rem 2rem;
  border-radius: 6px;
  font-weight: 500;
  text-decoration: none;
  transition: all 0.15s ease;
}

.btn-primary:hover {
  background: #0b5ed7;
  border-color: #0b5ed7;
}

.cart-content {
  display: grid;
  grid-template-columns: 1fr 400px;
  gap: 2rem;
  align-items: start;
}

.cart-items {
  background: white;
  border-radius: 12px;
  padding: 1.5rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
}

.cart-item {
  display: grid;
  grid-template-columns: 100px 1fr auto auto;
  gap: 1.5rem;
  padding: 1.5rem 0;
  border-bottom: 1px solid #dee2e6;
  align-items: center;
}

.cart-item:last-child {
  border-bottom: none;
}

.item-image {
  width: 100px;
  height: 100px;
  border-radius: 8px;
  overflow: hidden;
  background: #f8f9fa;
  display: flex;
  align-items: center;
  justify-content: center;
}

.item-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.placeholder {
  font-size: 2rem;
  opacity: 0.3;
}

.item-info h3 {
  font-size: 1.125rem;
  font-weight: 600;
  color: #212529;
  margin: 0 0 0.5rem 0;
}

.item-description {
  color: #6c757d;
  font-size: 0.875rem;
  margin: 0 0 0.5rem 0;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.item-price {
  color: #495057;
  font-weight: 500;
  margin: 0;
}

.item-quantity {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.qty-btn {
  width: 32px;
  height: 32px;
  border: 1px solid #dee2e6;
  background: white;
  border-radius: 4px;
  cursor: pointer;
  font-size: 1.125rem;
  transition: all 0.15s ease;
}

.qty-btn:hover:not(:disabled) {
  background: #f8f9fa;
  border-color: #adb5bd;
}

.qty-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.qty-input {
  width: 60px;
  height: 32px;
  text-align: center;
  border: 1px solid #dee2e6;
  border-radius: 4px;
  font-size: 0.9rem;
}

.item-total {
  text-align: right;
}

.total-price {
  font-size: 1.125rem;
  font-weight: 600;
  color: #0d6efd;
  margin: 0 0 0.5rem 0;
}

.btn-remove {
  background: none;
  border: none;
  color: #dc3545;
  font-size: 0.875rem;
  cursor: pointer;
  padding: 0.25rem 0;
}

.btn-remove:hover {
  text-decoration: underline;
}

.cart-summary {
  background: white;
  border-radius: 12px;
  padding: 1.5rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
  position: sticky;
  top: 2rem;
}

.cart-summary h2 {
  font-size: 1.25rem;
  font-weight: 600;
  color: #212529;
  margin: 0 0 1.5rem 0;
  padding-bottom: 1rem;
  border-bottom: 1px solid #dee2e6;
}

.summary-line {
  display: flex;
  justify-content: space-between;
  margin-bottom: 1rem;
  color: #495057;
  font-size: 0.9rem;
}

.summary-line.total {
  font-size: 1.125rem;
  color: #212529;
  padding-top: 1rem;
  border-top: 1px solid #dee2e6;
  margin-top: 1rem;
}

.btn-checkout {
  width: 100%;
  background: #0d6efd;
  color: white;
  border: 1px solid #0d6efd;
  padding: 0.875rem;
  border-radius: 6px;
  font-weight: 600;
  font-size: 1rem;
  cursor: pointer;
  margin-top: 1.5rem;
  transition: all 0.15s ease;
}

.btn-checkout:hover {
  background: #0b5ed7;
  border-color: #0b5ed7;
}

.btn-clear {
  width: 100%;
  background: white;
  color: #6c757d;
  border: 1px solid #dee2e6;
  padding: 0.625rem;
  border-radius: 6px;
  font-weight: 500;
  font-size: 0.875rem;
  cursor: pointer;
  margin-top: 0.75rem;
  transition: all 0.15s ease;
}

.btn-clear:hover {
  background: #f8f9fa;
  border-color: #adb5bd;
}

@media (max-width: 992px) {
  .cart-content {
    grid-template-columns: 1fr;
  }

  .cart-summary {
    position: static;
  }
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

  .cart-item {
    grid-template-columns: 80px 1fr;
    gap: 1rem;
  }

  .item-image {
    width: 80px;
    height: 80px;
  }

  .item-quantity,
  .item-total {
    grid-column: 2;
  }

  .item-quantity {
    justify-content: flex-start;
  }

  .item-total {
    text-align: left;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
}
</style>
