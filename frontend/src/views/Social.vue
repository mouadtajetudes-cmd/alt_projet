<template>
<div class="social-page">

<div class="container">

<div class="header">

<div class="search-bar">
<img src="../assets/images/chercher.png">
<input v-model="search" type="text" placeholder="Rechercher un post..." />
</div>

<AjoutPost/>

</div>


<div v-if="loading" class="loading">
Chargement des posts...
</div>

<div v-else-if="error" class="error">
{{ error }}
</div>


<div v-else class="posts-list">

<div
v-for="post in visibleFilteredPosts"
:key="post.id_post"
class="post-card"
>

<div class="header-card">

<div class="post-user">

<div class="image-user">
<img src="../assets/images/default.jpeg">
</div>

<span>{{ post.nom }} {{ post.prenom }}</span>

</div>

<p class="post-meta">
Publié le {{ new Date(post.date_publication).toLocaleDateString() }}
</p>

</div>


<div class="media-wrapper">

<p v-if="post.media_type === 'text'" class="post-text">
  <span >{{ post.titre  }}</span>
</p>

<!-- IMAGE -->
<img
   v-else-if="post.media_type === 'image'"
  :src="post.media_url"
  class="post-media"
/>

<!-- VIDEO -->
<video
  v-else="post.media_type === 'video'"
  controls
  class="post-media"
>
  <source :src="post.media_url" type="video/mp4">
</video>
  <span>{{ post.description }}</span>

</div>



<div class="post-actions">

<Like :post="post" :userId="currentId"/>

<button @click="openComments(post)">
<img src="../assets/images/commentaire.png" class="commentImg">
</button>

</div>

</div>

</div>

</div>


<div v-if="showCommentsModal" class="comments-modal">

<div class="comments-content">

<button class="close-btn" @click="closeComments">✖</button>

<h3>Commentaires</h3>

<Comment v-if="selectedPost" :postId="selectedPost.id_post"/>

<AjoutComment v-if="selectedPost" :postId="selectedPost.id_post"/>

</div>

</div>

</div>
</template>

<script setup>

import { ref, onMounted, onUnmounted, computed } from "vue"
import axios from "axios"

import AjoutPost from "./social/AjoutPost.vue"
import Comment from "./social/Comment.vue"
import AjoutComment from "./social/AjoutComment.vue"
import Like from "./social/Like.vue"

import { API } from "../shared/config/api"

const posts = ref([])
const loading = ref(true)
const error = ref(null)

const search = ref('')
const visiblePosts = ref(5)

const showCommentsModal = ref(false)
const selectedPost = ref(null)

const currentId = ref(Number(localStorage.getItem("userId")) || 1)

const openComments = (post)=>{
selectedPost.value = post
showCommentsModal.value = true
}

const closeComments = ()=>{
showCommentsModal.value = false
selectedPost.value = null
}

const loadPosts = async ()=>{

try{

const response = await axios.get(`${API.SOCIAL}/posts`)

posts.value = response.data.data || []

}catch(err){

console.error(err)
error.value="Impossible de charger les posts"

}finally{

loading.value=false

}

}

const filteredPosts = computed(()=>{

const query = search.value.toLowerCase()

return posts.value.filter(post =>
(post.titre || "").toLowerCase().includes(query) ||
(post.description || "").toLowerCase().includes(query)
)

})

const visibleFilteredPosts = computed(()=>{
return filteredPosts.value.slice(0, visiblePosts.value)
})

const handleScroll = ()=>{

if(
window.innerHeight + window.scrollY >=
document.body.offsetHeight - 100
){
visiblePosts.value +=5
}

}

onMounted(()=>{

loadPosts()

window.addEventListener("scroll", handleScroll)

})

onUnmounted(()=>{

window.removeEventListener("scroll", handleScroll)

})

</script>
<style scoped>

.social-page{
  display:flex;
  justify-content:center;
  padding:2rem 0;
  background:#f5f7fb;
}

.container{
  width:100%;
  max-width:700px;
  padding:0 1rem;
}

/* HEADER */

.header{
  display:flex;
  flex-direction:column;
  gap:1rem;
  background:#f5f7fb;
  padding:1rem;
  border-radius:15px;
  margin-bottom:2rem;
}

/* SEARCH */

.search-bar{
  position:relative;
  width:100%;
}

.search-bar img{
  position:absolute;
  left:12px;
  top:50%;
  transform:translateY(-50%);
  width:18px;
  opacity:0.6;
}

.search-bar input{
  width:100%;
  padding:0.6rem 1rem 0.6rem 35px;
  border-radius:30px;
  border:1px solid #ddd;
  background:#f9f9f9;
}

.search-bar input:focus{
  outline:none;
  border-color:#4a6cf7;
  background:white;
  box-shadow:0 0 0 3px rgba(74,108,247,0.15);
}

/* POSTS */

.posts-list{
  display:flex;
  flex-direction:column;
  gap:1.5rem;
}

/* CARD */

.post-card{
  background:white;
  border-radius:15px;
  padding:1rem;
  box-shadow:0 5px 15px rgba(0,0,0,0.08);
  transition:0.2s;
}

.post-card:hover{
  transform:translateY(-3px);
  box-shadow:0 10px 25px rgba(0,0,0,0.1);
}

/* HEADER POST */

.header-card{
  display:flex;
  justify-content:space-between;
  align-items:center;
  margin-bottom:1rem;
}

.post-user{
  display:flex;
  align-items:center;
  gap:0.6rem;
}

.image-user img{
  width:42px;
  height:42px;
  border-radius:50%;
  border:2px solid #eee;
}

.post-user span{
  font-weight:600;
  color:#333;
}

.post-meta{
  font-size:0.8rem;
  color:#777;
}

/* MEDIA */

.media-wrapper{
  width:100%;
  border-radius:12px;
  overflow:hidden;
}

.post-media{
  width:100%;
  max-height:350px;
  object-fit:cover;
  border-radius:12px;
}

.post-text{
  background:#f1f4ff;
  padding:2rem;
  border-radius:12px;
  text-align:center;
  font-size:1rem;
}

/* ACTIONS */

.post-actions{
  display:flex;
  align-items:center;
  gap:1rem;
  margin-top:0.8rem;
}

.post-actions button{
  background:#f1f3f7;
  border:none;
  padding:0.4rem 0.8rem;
  border-radius:8px;
  cursor:pointer;
}

.post-actions button:hover{
  background:#e4e7ee;
}

.commentImg{
  width:20px;
}

/* COMMENTS MODAL */

.comments-modal{
  position:fixed;
  top:0;
  left:0;
  width:100%;
  height:100%;
  background: rgba(0, 0, 0, 0.02);
  display:flex;
  justify-content:center;
  align-items:center;
  z-index:1000;
}

.comments-content{
  background:white;
  width:400px;
  max-height:500px;
  overflow-y:auto;
  border-radius:10px;
  padding:20px;
  box-shadow:0 10px 30px rgba(0, 0, 0, 0);
}

.close-btn{
  border:none;
  background:none;
  font-size:18px;
  cursor:pointer;
  float:right;
}

/* TABLETTE */

@media (max-width:900px){

.container{
  max-width:600px;
}

.post-media{
  max-height:300px;
}

.post-text{
  padding:1.5rem;
}

}

/* MOBILE */

@media (max-width:600px){

.container{
  max-width:100%;
}

.header{
  padding:0.8rem;
}

.post-card{
  padding:0.8rem;
}

.image-user img{
  width:35px;
  height:35px;
}

.post-text{
  padding:1rem;
  font-size:0.9rem;
}

.post-actions{
  gap:0.5rem;
}

.post-media{
  max-height:250px;
}

}

@media (max-width:400px){

.search-bar input{
  font-size:0.8rem;
}

.post-user span{
  font-size:0.8rem;
}

.post-meta{
  display:none;
}

}

</style>