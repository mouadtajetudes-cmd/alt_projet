<template>
  <div class="social-page">
    <div class="container">
      <div class="header">
        <div class="search-bar">
          <input v-model="search" type="text" placeholder="Rechercher un post..." />
         </div>
         <div class="create-btn"> 
        <button  class="btn-public">Publier</button>
        <button class="btn-image">Image</button>
        <button class="btn-video">Vidéo</button>
         </div>
       
      </div>
      
      <div v-if="loading" class="loading">Chargement des posts...</div>
      
      <div v-else-if="error" class="error">{{ error }}</div>
      
      <div v-else class="posts-list">
        <div v-for="post in filteredPosts" :key="post.id_post" class="post-card">
          <div class="header-card">
              <div class="post-user">
            <div class="image-user">
              <img :src="post.image ||'/images/default.jpeg'" >
            </div>
             <span>{{ post.nom }} {{ post.prenom }}</span>
          </div>
          <p class="post-meta">  Publié le {{ new Date(post.date_publication).toLocaleDateString() }}</p>
          </div>
          <!-- <h3>{{ post.titre }}</h3> -->
 <div class="post-content">
    <div class="media-wrapper">
          <p v-if="post.type === 'text'" class="post-text">{{ post.description }}</p>

          <img v-else-if="post.type === 'image'" :src="getMediaUrl(post.description)"  class="post-media" />

    <video v-else-if="post.type === 'video'" controls class="post-media">
      <source :src="post.description" type="video/mp4" />
      Votre navigateur ne supporte pas la vidéo.
    </video>

    </div>
  </div>         
   <div class="post-actions">
            <button>👍 Réactions</button>
            <button>💬 Commentaires</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted ,computed} from 'vue'
import axios from "axios"
import { config } from '../conf'

const posts = ref([])
const loading = ref(true)
const error = ref(null)
const search = ref('')

const loadPosts = async () => {
  try {
    const response = await axios.get(`${config}/posts`)
    posts.value = response.data.data || []
    console.log("DATA API:", response.data)
    console.log("Posts après assignation:", posts.value)

  } catch (err) {
    console.error('Erreur chargement posts:', err)
    error.value = 'Impossible de charger les posts'
  } finally {
    loading.value = false

  }
}

onMounted(loadPosts)
const mediaBase='http://localhost:6085/uploads/images/';
function getMediaUrl(description) {
  if (description.startsWith('http')) {
    return description; 
  }
  return mediaBase + description;
}
const filteredPosts = computed(() => {
  return posts.value.filter(post =>
    post.titre?.toLowerCase().includes(search.value.toLowerCase()) ||
    post.description?.toLowerCase().includes(search.value.toLowerCase())
  )
})
</script>

<style scoped>
.social-page {
  padding: 2rem 0;
}

.container {
  max-width: 800px;
  max-height: 40%;
  margin: 0 auto;
  padding: 0 1rem;
}

.header {
width: auto;
height: 100px;
margin-bottom: 4em;
border-radius: 20px;
background-color: #EAEAEA;

}
.search-bar input {
  width: 98%;
  margin: 1em;
  padding: 0.6rem 1rem;
  border-radius: 25px;
  border: 1px solid #ddd;
  background: #FFFFFF;
}
.search-bar input:focus {
  border-color: #354b6c;
  background: white;
  box-shadow: 0 0 0 3px rgba(13,110,253,0.15);
}


h1 {
  color: #333;
  margin: 0;
}

.create-btn {
  display: flex;
  gap: 2rem;
  margin-left: 1em;
}
.btn-public {
  background-color: #007bff;
  color: white;
  border: none;
  padding: 0.5rem 1.5rem;
  border-radius: 4px;
  cursor: pointer;
}
.btn-image {
  background-color: #28a745;
  color: white;
  border: none;
  padding: 0.5rem 1.5rem;
  border-radius: 4px;
  cursor: pointer;
}
.btn-video {
  background-color: #dc3545;
  color: white;
  border: none;
  padding: 0.5rem 1.5rem;
  border-radius: 4px;
  cursor: pointer;}

.loading {
  text-align: center;
  padding: 3rem;
  color: #666;
}

.posts-list {
  display: grid;
  grid-template-rows: repeat(auto-fit, minmax(100px, auto));
  gap: 1.5rem;

}
.post-card {
  background: #EAEAEA;
  padding: 1rem;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
.header-card {
  display: flex;
  justify-content: space-between; 
  align-items: center;
  margin-bottom: 1rem;
}

.post-user {
  display: flex;
  align-items: center;
  gap: 0.5rem;          
}

.image-user img {
  width: 40px;
  height: 40px;
  border-radius: 50%;  
  border: 1px solid #ccc; 
}

.image-user span {
  font-size: 0.9rem;
  color: #333;
  font-weight: 500;
  margin-left: 0.5rem;
}

.post-media {
  width: 100%;
  height: 180px; 
  object-fit: cover; 
  border-radius: 12px;
  transition: transform 0.3s ease;
}

.post-content {
height: 180px;
opacity: 1;
margin-bottom: 1rem;
border-radius: 20px;

}

.post-text {
  font-size: 1rem;
  color: #333;
  text-align: center;
  background-color: rgb(167, 194, 235);
  padding: 5rem;
  line-height: 1;
}

.media-wrapper {
  width: 100%;
  height: 200px;
  border-radius: 12px;
  overflow: hidden;
}

.post-media:hover {
  transform: scale(1.02);
}

video.post-media {
  background: rgb(212, 206, 206);
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
