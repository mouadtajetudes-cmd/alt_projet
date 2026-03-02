<template>
  <div class="social-page">
    <div class="container">

          <div class="header">
        <div class="search-bar">
          <img src="../assets/images/chercher.png" >
          <input v-model="search" type="text" placeholder="Rechercher un post..." />
         </div>
         <div><AjoutPost/></div>
         

      </div>
      
      <div v-if="loading" class="loading">Chargement des posts...</div>
      
      <div v-else-if="error" class="error">{{ error }}</div>
      
      <div v-else class="posts-list">
        <div v-for="post in filteredPosts" :key="post.id_post" class="post-card">
          <div class="header-card">
              <div class="post-user">
            <div class="image-user">
              <img src="../assets/images/default.jpeg" >
            </div>
             <span>{{ post.nom }} {{ post.prenom }}</span>
          </div>
          <p class="post-meta">  Publié le {{ new Date(post.date_publication).toLocaleDateString() }}</p>
          </div>
          <!-- <h3>{{ post.titre }}</h3> -->
 <div class="post-content">
    <div class="media-wrapper">
          <p v-if="post.type === 'text'" class="post-text">{{ post.description }}</p>

          <img v-else-if="post.type === 'image'" :src="post.description"  class="post-media" />

    <video v-else-if="post.type === 'video'" controls class="post-media">
      <source :src="post.description" type="video/mp4" />
      Votre navigateur ne supporte pas la vidéo.
    </video>

    </div>
  </div>         
<div class="post-actions">
  <Like :post="post" :userId="currentId"/>
  <button @click="toggleComments(post.id_post)">
    <img src="../assets/images/commentaire.png" class="commentImg"> 
  </button>
</div>

<div v-if="post.showComments" class="comments-zone">
  <Comment :postId="post.id_post"/>
  <AjoutComment :postId="post.id_post"/>
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
import AjoutPost from './social/AjoutPost.vue'
import Comment from './social/Comment.vue'
import AjoutComment from './social/AjoutComment.vue'
import Like from './social/Like.vue'


const posts = ref([])
const loading = ref(true)
const error = ref(null)
const search = ref('')
const currentId=1;

const loadPosts = async () => {
  try {
    const response = await axios.get(`${config}/posts`)
   posts.value = (response.data.data || []).map(post => ({
  ...post,
  showComments: false
}))
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
const filteredPosts = computed(() => {
  return posts.value.filter(post =>
    post.titre?.toLowerCase().includes(search.value.toLowerCase()) ||
    post.description?.toLowerCase().includes(search.value.toLowerCase())
  )
})
const toggleComments = (postId) => {
  const post = posts.value.find(p => p.id_post === postId)
  if (post) {
    post.showComments = !post.showComments
  }
}
</script>

<style scoped>
.social-page {
   display: flex;
   padding: 2rem 0; }
 .container {
   max-width: 800px;
    max-height: 40%;
     margin: 0 auto;
     padding: 0 1rem; } 
.header {
  width: 100%;
  display: flex;
  flex-direction: column;
  margin-bottom: 1rem;
}
.search-bar {
  position: relative;
  width: 70%;
  max-width: 500px;
}

.search-bar img {
  position: absolute;
  left: 12px;
  top: 50%;
  transform: translateY(-50%);
  width: 18px;
  opacity: 0.6;
}

.search-bar input {
  width: 100%;
  padding: 0.6rem 1rem 0.6rem 35px;
  border-radius: 25px;
  border: 1px solid #ddd;
}
.search-bar input:focus {
  border-color: #354b6c;
  box-shadow: 0 0 0 3px rgba(13,110,253,0.15);
}
   .posts-list { 
    display: grid; 
    grid-template-rows:repeat(auto-fit, minmax(100px, auto)); 
     gap: 1.5rem; } 
   .post-card { 
    background: #f4f4f4;
    padding: 1rem; border-radius: 8px;
     box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      }
 .post-user { 
  display: flex;
  align-items: center;
   gap: 0.5rem; 
   }
.image-user img
 {
       
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
  background-color:white; 
  padding: 5rem;
   line-height: 1; }
.media-wrapper { width: 100%; 
height: 200px;
 border-radius: 12px;
 overflow: hidden; }
.post-media:hover { 
  transform: scale(1.02);
 } 
video.post-media {
   background: rgb(212, 206, 206); 
   }
.post-actions {
 display: flex; 
gap: 1rem; } 
.post-actions button { border: none;
 cursor: pointer;
  transition: background 0.2s; }
.commentImg{ 
  width: 20px;
 height: 20px; }
 .comments-zone{ 
  margin-top: 10px;
  display: flex; 
  flex-direction: column;
   gap: 8px; }
   </style>
