<template>
  <div class="social-page">
    <div class="container">

      <div class="header">
        <div class="search-bar">
          <img src="../assets/images/chercher.png" alt="Search">
          <input v-model="search" type="text" placeholder="Rechercher un post..." />
        </div>
        <AjoutPost/>
      </div>
      <div v-if="loading" class="loading">Chargement des posts...</div>
      <div v-else-if="error" class="error">{{ error }}</div>

      <!-- POSTS -->
      <div v-else class="posts-list">
        <div v-for="post in visibleFilteredPosts" :key="post.id_post" class="post-card">

          <div class="header-card">
            <div class="post-user">
              <button class="image-user" @click="goToUserPosts(post.id_utilisateur)">
                <img :src="getUserAvatar(post)"  >
              </button>
              <div>
                <span>{{ post.nom }} {{ post.prenom }}</span>
               <p class="post-meta">
              Publié le {{ formatDate(post.date_publication) }}
            </p>
              </div>
              
            </div>
           
            <div class="post-follow">
              <AddFollower :targetUserId="post.id_utilisateur" :currentUserId="currentId" />
            </div>
          </div>

          <div class="media-wrapper">
            <p v-if="post.media_type === 'text'" class="post-text">
              <span>{{ post.titre }}</span>
            </p>

            <img v-else-if="post.media_type === 'image'" :src="post.media_url" class="post-media" />
            <video v-else-if="post.media_type === 'video'" controls class="post-media">
              <source :src="post.media_url" type="video/mp4">
            </video>

            <span>{{ post.description }}</span>
          </div>

          <!-- ACTIONS -->
          <div class="post-s">
            <Like :post="post" :userId="currentId" />
            <button @click="openComments(post)" class="comment-btn">
              <img src="../assets/images/commentaire.png" class="commentImg" alt="Commentaire">
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
import { ref, onMounted, onUnmounted, computed } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";
import AjoutPost from "./social/AjoutPost.vue";
import Comment from "./social/Comment.vue";
import AjoutComment from "./social/AjoutComment.vue";
import Like from "./social/Like.vue";
import { API } from "../shared/config/api";
import AddFollower from "./social/AddFollower.vue";
import defaultAvatar from '@/assets/images/default.jpeg'

const posts = ref([]);
const loading = ref(true);
const error = ref(null);

const search = ref('');
const visiblePosts = ref(5);

const showCommentsModal = ref(false);
const selectedPost = ref(null);
const router = useRouter();

// Utilisateur authentifié
const user = JSON.parse(localStorage.getItem('user') || '{}');
const currentId = user.id_utilisateur ? Number(user.id_utilisateur) : null;

const openComments = (post) => {
  selectedPost.value = post;
  showCommentsModal.value = true;
};

const closeComments = () => {
  showCommentsModal.value = false;
  selectedPost.value = null;
};

const getUserAvatar = (post) => {
  if (post.banner_url) return post.banner_url;
  if (post.avatar_url) return post.avatar_url;
  return defaultAvatar;
};

const loadPosts = async () => {
  try {
    const response = await axios.get(`${API.SOCIAL}/posts`);
    posts.value = response.data.data || [];
  } catch(err) {
    console.error(err);
    error.value = "Impossible de charger les posts";
  } finally {
    loading.value = false;
  }
};

const filteredPosts = computed(() => {
  const query = search.value.toLowerCase();
  return posts.value.filter(post =>
    (post.titre || "").toLowerCase().includes(query) ||
    (post.description || "").toLowerCase().includes(query)
  );
});

const visibleFilteredPosts = computed(() => filteredPosts.value.slice(0, visiblePosts.value));

const handleScroll = () => {
  if (window.innerHeight + window.scrollY >= document.body.offsetHeight - 100) {
    visiblePosts.value += 5;
  }
};

const goToUserPosts = (userId) => {
  router.push(`/posts/user/${userId}`);
};
const formatDate = (dateStr) => {
  if (!dateStr) return ''
  const date = new Date(dateStr)
  const day = String(date.getDate()).padStart(2, '0')
  const month = String(date.getMonth() + 1).padStart(2, '0') 
  const year = String(date.getFullYear()).slice(-2) 
  return `${day}-${month}-${year}`}

onMounted(() => {
  loadPosts();
  window.addEventListener("scroll", handleScroll);
});

onUnmounted(() => {
  window.removeEventListener("scroll", handleScroll);
});
</script>

<style scoped>

.social-page{
  display:flex;
  justify-content:center;
  padding:2.5rem 0;
  min-height: 100vh;
}

.container{
  width:100%;
  max-width:760px;
  padding:0 1rem;
  transition: transform 0.3s ease;
}

.container:hover {
  transform: translateY(-2px);
}


.header{
  display:flex;
  flex-direction:column;
  gap:1rem;
  padding:1.2rem;
  margin-bottom:2rem;
}


.search-bar{
  position:relative;
  width:100%;
}

.search-bar img{
  position:absolute;
  left:12px;
  top:50%;
  transform:translateY(-50%);
  width:20px;
  opacity:0.6;
}

.search-bar input{
  width:100%;
  padding:0.6rem 1rem 0.6rem 35px;
  border-radius:30px;
  background:white;
}

.search-bar input:focus{
  outline:none;
  border-color:#4a6cf7;
  background:white;
  box-shadow:0 0 0 3px rgba(74,108,247,0.15);
}


.posts-list{
  display:flex;
  flex-direction:column;
  gap:1.5rem;
}


.post-card{
  background: rgba(255,255,255,0.95);
  border-radius:16px;
  padding:1.15rem;
  box-shadow:0 8px 24px rgba(23, 40, 63, 0.08);
  border: 1px solid rgba(141, 157, 216, 0.2);
  transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
  animation: card-in 0.5s ease both;
}

.post-card:hover{
  transform:translateY(-5px) scale(1.005);
  box-shadow:0 14px 30px rgba(23, 40, 63, 0.16);
  border-color: rgba(81, 103, 196, 0.35);
}

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
.post-follow {
  margin-top: 6px; 
}
button.image-user{
  border:none;
  background:none;
  padding:0;
  cursor:pointer;
}

.image-user img{
  width:42px;
  height:42px;
  border-radius:50%;
  object-fit:cover;
  }

.post-user span{
  font-weight:700;
  color:#1f305b;
}

.post-meta{
  font-size:0.6rem;
  color:#5f6f9a;
  font-weight:500;
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


.post-actions{
  display:flex;
  align-items:center;
  gap:1rem;
  margin-top:0.8rem;
}

.post-actions button{
  background:#ffffff;
  border:none;
  padding:0.4rem 0.8rem;
  border-radius:8px;
  cursor:pointer;
}

.post-actions button:hover{
  background:#dbe6ff;
}
.post-s{
  display:flex;
  align-items:center;
  gap:12px;
  margin-top:0.8rem;
}

.comment-btn{
  background: transparent;
  border: none;
  padding: 6px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  transition: background 0.2s ease, transform 0.15s ease;
}

.comment-btn:hover{
  background: rgba(0,0,0,0.06);
  transform: scale(1.1);
}

.commentImg{
  width: 22px;
  height: 22px;
  display:block;
}
@keyframes card-in {
  0% {
    opacity: 0;
    transform: translateY(12px) scale(0.98);
  }
  60% {
    opacity: 1;
    transform: translateY(-2px) scale(1.001);
  }
  100% {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

@keyframes fade-in {
  0% { opacity: 0; transform: translateY(8px); }
  100% { opacity: 1; transform: translateY(0); }
}

.post-media,
.post-text {
  animation: fade-in 0.5s ease 0.15s both;
}


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
  width:420px;
  max-height:520px;
  overflow-y:auto;
  border-radius:14px;
  padding:20px;
  box-shadow:0 16px 40px rgba(20, 34, 56, 0.18);
  border: 1px solid rgba(119, 136, 195, 0.2);
  animation: modal-in 0.35s ease both;
}

@keyframes modal-in {
  0% {
    opacity: 0;
    transform: scale(0.94) translateY(12px);
  }
  100% {
    opacity: 1;
    transform: scale(1) translateY(0);
  }
}

.close-btn{
  border:none;
  background:none;
  font-size:18px;
  cursor:pointer;
  float:right;
}


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