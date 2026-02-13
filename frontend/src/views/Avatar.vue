<template>
  <div class="avatar-page">
    <div class="container">
      <h1>Galerie des Avatars</h1>
      
      <div v-if="loading" class="loading">Chargement des avatars...</div>
      
      <div v-else class="avatars-grid">
        <div v-for="avatar in avatars" :key="avatar.id_avatar" class="avatar-card">
          <div class="avatar-icon">🎭</div>
          <h3>{{ avatar.nom }}</h3>
          <p>Niveau {{ avatar.niveau }}</p>
          <p class="points">Points: {{ avatar.points }}</p>
          <button class="btn">Sélectionner</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'

export default {
  name: 'Avatar',
  setup() {
    const avatars = ref([])
    const loading = ref(true)
    
    const loadAvatars = async () => {
      try {
        const response = await fetch('http://localhost:6085/avatars')
        const data = await response.json()
        avatars.value = data
      } catch (error) {
        console.error('Erreur chargement avatars:', error)
      } finally {
        loading.value = false
      }
    }
    
    onMounted(() => {
      loadAvatars()
    })
    
    return {
      avatars,
      loading
    }
  }
}
</script>

<style scoped>
.avatar-page {
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

.avatars-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 2rem;
}

.avatar-card {
  background: white;
  padding: 1.5rem;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  text-align: center;
  transition: transform 0.2s;
}

.avatar-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.avatar-icon {
  font-size: 4rem;
  margin-bottom: 1rem;
}

.avatar-card h3 {
  margin-bottom: 0.5rem;
  color: #333;
}

.avatar-card p {
  color: #666;
  margin-bottom: 0.5rem;
}

.points {
  font-size: 0.9rem;
  color: #0d6efd;
  font-weight: 500;
}

.btn {
  width: 100%;
  padding: 0.75rem;
  background: #0d6efd;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  margin-top: 1rem;
  transition: background 0.2s;
}

.btn:hover {
  background: #0b5ed7;
}
</style>
