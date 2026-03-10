<template>
  <div class="product-card">
    <div class="product-image">
      <img v-if="product.image" :src="product.image" :alt="product.nom" class="card-img" />
      <div v-else class="placeholder">🛍️</div>

      <span v-if="product.statut === 'vendu'" class="badge vendu">Vendu</span>
      <span v-else-if="Number(product.quantite) === 0" class="badge rupture">Rupture</span>
    </div>

    <div class="product-info">
      <h3>{{ product.nom }}</h3>
      <p class="description">{{ truncDesc }}</p>
      <p class="category">{{ product.categorie || 'Non catégorisé' }}</p>

      <div class="footer">
        <span class="price">{{ formatPrice(product.prix) }}</span>
        <button @click="goDetails" class="btn-primary">Voir</button>
      </div>

      <div class="actions">
        <button @click.stop="contact" class="btn-contact">💬 Contacter</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const props = defineProps({
  product: {
    type: Object,
    required: true
  }
})

const pid = computed(() => Number(props.product?.id_produit ?? props.product?.id ?? 0))

const truncDesc = computed(() => {
  const text = props.product?.description ?? ''
  if (text.length <= 60) return text || 'Aucune description'
  return text.substring(0, 60) + '...'
})

const goDetails = () => {
  if (!pid.value) return

  router.push({
    name: 'ProductDetails',
    params: { id: pid.value }
  })
}

const contact = () => {
  if (props.product?.id_utilisateur) {
    router.push({
      name: 'Chat',
      params: { userId: props.product.id_utilisateur }
    })
    return
  }

  alert('Impossible de contacter le vendeur pour le moment.')
}

const formatPrice = (p) =>
  new Intl.NumberFormat('fr-FR', {
    style: 'currency',
    currency: 'EUR'
  }).format(Number(p || 0))
</script>

<style scoped>
.product-card {
  border: 1px solid #ddd;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  transition: transform 0.2s, box-shadow 0.2s;
  background: white;
}

.product-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.product-image {
  position: relative;
  width: 100%;
  height: 200px;
  background: #f5f5f5;
  display: flex;
  align-items: center;
  justify-content: center;
}

.card-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.placeholder {
  font-size: 60px;
}

.badge {
  position: absolute;
  top: 10px;
  right: 10px;
  padding: 5px 10px;
  border-radius: 4px;
  font-size: 12px;
  font-weight: bold;
  color: white;
}

.badge.vendu {
  background: #dc3545;
}

.badge.rupture {
  background: #ffc107;
  color: #333;
}

.product-info {
  padding: 15px;
}

.product-info h3 {
  margin: 0 0 8px 0;
  font-size: 16px;
  font-weight: bold;
  color: #222;
}

.description {
  color: #666;
  font-size: 13px;
  margin: 8px 0;
  min-height: 34px;
}

.category {
  color: #999;
  font-size: 12px;
  margin: 5px 0;
}

.footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin: 12px 0;
  gap: 10px;
}

.price {
  font-size: 18px;
  font-weight: bold;
  color: #007bff;
}

.btn-primary {
  padding: 6px 12px;
  background: #007bff;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 13px;
}

.btn-primary:hover {
  background: #0056b3;
}

.actions {
  display: flex;
  gap: 8px;
}

.btn-contact {
  flex: 1;
  padding: 8px;
  background: #28a745;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 12px;
}

.btn-contact:hover {
  background: #218838;
}
</style>