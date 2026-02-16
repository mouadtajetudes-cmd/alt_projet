<template>
  <div class="social-page">
    <div class="container">
      <div class="header">
        <h1>Feed Social</h1>
        <button class="btn-create">Nouveau Post</button>
      </div>
      
      <div v-if="loading" class="loading">Chargement des posts...</div>
      
      <div v-else class="posts-list">
        <div v-for="post in posts" :key="post.id_post" class="post-card">
          <h3>{{ post.title }}</h3>
          <p>{{ post.content }}</p>
          <div class="post-actions">
            <button>👍 Réactions</button>
            <button>💬 Commentaires</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'

export default {
  name: 'Social',
  setup() {
    const posts = ref([])
    const loading = ref(true)
    
    const loadPosts = async () => {
      try {
        const response = await fetch('http://localhost:6087/posts')
        const data = await response.json()
        posts.value = data
      } catch (error) {
        console.error('Erreur chargement posts:', error)
      } finally {
        loading.value = false
      }
    }
    
    onMounted(() => {
      loadPosts()
    })
    
    return {
      posts,
      loading
    }
  }
}
</script>

<style scoped>
.social-page {
  padding: 2rem 0;
}

.container {
  max-width: 800px;
  margin: 0 auto;
  padding: 0 1rem;
}

.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}

h1 {
  color: #333;
  margin: 0;
}

.btn-create {
  background: #0d6efd;
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 4px;
  cursor: pointer;
}

.loading {
  text-align: center;
  padding: 3rem;
  color: #666;
}

.posts-list {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.post-card {
  background: white;
  padding: 1.5rem;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.post-card h3 {
  margin-bottom: 0.75rem;
  color: #333;
}

.post-card p {
  color: #666;
  line-height: 1.6;
  margin-bottom: 1rem;
}

.post-actions {
  display: flex;
  gap: 1rem;
}

.post-actions button {
  background: #f8f9fa;
  border: none;
  padding: 0.5rem 1rem;
  border-radius: 4px;
  cursor: pointer;
  transition: background 0.2s;
}

.post-actions button:hover {
  background: #e9ecef;
}
</style>
