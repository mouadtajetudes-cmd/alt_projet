<template>
  <div class="product-card" @click="viewDetails">
    <div class="product-image">
      <img v-if="product.image_url" :src="product.image_url" :alt="product.nom" />
      <div v-else class="placeholder-image">🛍️</div>
      <span v-if="product.statut === 'vendu'" class="badge badge-sold">Vendu</span>
      <span v-else-if="product.quantite === 0" class="badge badge-outofstock">Rupture</span>
    </div>
    
    <div class="product-content">
      <h3 class="product-title">{{ product.nom }}</h3>
      <p class="product-description">{{ truncatedDescription }}</p>
      
      <div class="product-footer">
        <span class="product-price">{{ formatPrice(product.prix) }}</span>
        <button class="btn-primary" @click.stop="viewDetails">
          Voir détails
        </button>
      </div>
      
      <div class="product-meta">
        <span class="category">{{ product.categorie_nom || 'Non catégorisé' }}</span>
        <span class="stock" v-if="product.quantite > 0">
          Stock: {{ product.quantite }}
        </span>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ProductCard',
  props: {
    product: {
      type: Object,
      required: true
    }
  },
  computed: {
    truncatedDescription() {
      if (!this.product.description) return 'Aucune description'
      return this.product.description.length > 100
        ? this.product.description.substring(0, 100) + '...'
        : this.product.description
    }
  },
  methods: {
    formatPrice(price) {
      return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR'
      }).format(price)
    },
    viewDetails() {
      this.$router.push({ name: 'ProductDetail', params: { id: this.product.id_produit } })
    }
  }
}
</script>

<style scoped>
.product-card {
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  overflow: hidden;
  transition: all 0.3s ease;
  cursor: pointer;
}

.product-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
}

.product-image {
  position: relative;
  width: 100%;
  height: 220px;
  background: #f8f9fa;
  overflow: hidden;
}

.product-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s ease;
}

.product-card:hover .product-image img {
  transform: scale(1.1);
}

.placeholder-image {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 5rem;
  color: #dee2e6;
}

.badge {
  position: absolute;
  top: 12px;
  right: 12px;
  padding: 6px 14px;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 700;
  text-transform: uppercase;
}

.badge-sold {
  background: #dc3545;
  color: white;
}

.badge-outofstock {
  background: #ffc107;
  color: #000;
}

.product-content {
  padding: 1.25rem;
}

.product-title {
  font-size: 1.1rem;
  font-weight: 700;
  color: #212529;
  margin: 0 0 0.75rem 0;
  min-height: 2.5rem;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.product-description {
  color: #6c757d;
  font-size: 0.9rem;
  line-height: 1.5;
  margin-bottom: 1rem;
  min-height: 3rem;
}

.product-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 0.75rem;
}

.product-price {
  font-size: 1.5rem;
  font-weight: 800;
  color: #0d6efd;
}

.btn-primary {
  background: #0d6efd;
  color: white;
  border: none;
  padding: 0.5rem 1.25rem;
  border-radius: 8px;
  font-size: 0.9rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-primary:hover {
  background: #0b5ed7;
  transform: scale(1.05);
}

.product-meta {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-top: 0.75rem;
  border-top: 1px solid #e9ecef;
  font-size: 0.85rem;
}

.category {
  color: #0d6efd;
  font-weight: 600;
}

.stock {
  color: #28a745;
  font-weight: 500;
}

@media (max-width: 768px) {
  .product-image {
    height: 180px;
  }
  
  .product-price {
    font-size: 1.25rem;
  }
  
  .btn-primary {
    font-size: 0.8rem;
    padding: 0.4rem 1rem;
  }
}
</style>
