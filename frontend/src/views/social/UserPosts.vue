<template>
  <div class="user-posts">

    <div v-if="loading" class="loading">
      Chargement en cours…
    </div>

    <div v-else-if="userExists" class="user-profile">

      <div
        class="banner"
        :style="user.banner_url
          ? { backgroundImage: `url(${user.banner_url})` } : {}"></div>

      <div class="profile-body">

        <img class="avatar" :src="avatarUrl" alt="Avatar" />

        <div class="profile-info">
          <h3>{{ user.nom || 'Utilisateur' }} {{ user.prenom || '' }}</h3>

          <p class="username">
            {{ usernameHandle }}
          </p>

          <p class="bio" v-if="user.bio">
            {{ user.bio }}
          </p>

          <p class="meta">
            {{ posts.length }} publication{{ posts.length > 1 ? 's' : '' }}
            •
            {{ totalLikes }} like{{ totalLikes > 1 ? 's' : '' }}
          </p>
        </div>

      </div>
    </div>

    <div v-else class="no-user">
      Utilisateur non trouvé
    </div>

    <div class="tabs">

      <button
        :class="{ active: activeTab === 'posts' }"
        @click="activeTab = 'posts'"
      >
        Publications
      </button>

      <button
        v-if="isProfileOwner"
        :class="{ active: activeTab === 'followers' }"
        @click="activeTab = 'followers'"
      >
        {{ followersCount }} Followers
      </button>

      <button
        v-if="isProfileOwner"
        :class="{ active: activeTab === 'following' }"
        @click="activeTab = 'following'"
      >
       {{ followingCount }} Following
      </button>
      <button 
       v-if="isProfileOwner"
       :class="{ active: activeTab === 'draft' }" @click="activeTab = 'draft'">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M12 20h9"></path>
        <path d="M16.5 3.5a2.121 2.121 0 1 1 3 3L7 19l-4 1 1-4 12.5-12.5z"></path>
      </svg>
        Brouillon
      </button>

    </div>

    <div v-if="activeTab === 'posts'">

      <h2>Publications</h2>
      <hr />

      <div v-if="posts.length === 0" class="empty">
        Aucun post trouvé
      </div>

      <div class="posts-grid">

        <div v-for="post in posts" :key="post.id_post" class="post" >

<div class="post-actions" v-if="isOwner(post)">
  <button @click="deletePost(post.id_post)" class="delete-btn">
    <svg xmlns="http://www.w3.org/2000/svg" fill="white" viewBox="0 0 24 24">
      <path d="M3 6h18v2H3V6zm2 3h14l-1 12H6L5 9zm4 2v8h2v-8H9zm4 0v8h2v-8h-2z"/>
    </svg>
  </button>
  <button @click="openEditModal(post)" class="edit-btn">
    <svg xmlns="http://www.w3.org/2000/svg" fill="white" viewBox="0 0 24 24">
      <path d="M3 17.25V21h3.75l11-11.03-3.75-3.75L3 17.25zM20.71 7.04a1 1 0 0 0 0-1.41l-2.34-2.34a1 1 0 0 0-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
    </svg>
  </button>
</div>

          <div class="media-container">

            <img
              v-if="post.media_type === 'image' && post.media_url"
              :src="post.media_url"
              class="media"
            />

            <video
              v-if="post.media_type === 'video' && post.media_url"
              :src="post.media_url"
              class="media"
              controls
            ></video>

          </div>

          <p class="description">
            {{ post.description || 'Pas de description.' }}
          </p>

          <div class="post-footer">
            <span class="likes">
              {{ post.likes_count || 0 }} like{{ (post.likes_count || 0) > 1 ? 's' : '' }}
            </span>

            <span class="date">
              {{ formatDate(post.date_publication) }}
            </span>
          </div>

        </div>

      </div>

    </div>
    <div v-else-if="activeTab === 'followers'" > 
      <ListFollowers
        v-if="currentUserId === userId"
        :userId="userId"
        :currentUserId="currentUserId"
        @update-count="followersCount = $event"
      />
    </div>

    <div v-else-if="activeTab === 'following'"  >
      <ListFollowing
        v-if="currentUserId === userId"
        :userId="userId"
        :currentUserId="currentUserId"
        @update-count="followingCount = $event"
      />
    </div>
    <div v-else-if="activeTab === 'draft'">
  <h2>Brouillons</h2>
  <hr />
    <DraftPost :userId="userId" />

    </div>
  </div>

<div v-if="showEditModal" class="modal-overlay" @click="closeModal">
  <UpdatePost
    :post="postToEdit"
    @close="closeModal"
    @updated="onUpdated"
    @click.stop
  />
</div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import axios from 'axios'
import { API } from '@/shared/config/api'
import ListFollowers from './ListFollowers.vue'
import ListFollowing from './ListFollowing.vue'
import DraftPost from './DraftPost.vue'
import UpdatePost from './UpdatePost.vue';


const route = useRoute()
const router=useRouter()
const userId = Number(route.params.id)
const posts = ref([])
const userProfile = ref(null)
const loading = ref(true)
const activeTab = ref('posts')
const userAuth = JSON.parse(localStorage.getItem('user') || '{}')
const currentUserId = userAuth.id_utilisateur || null
const followersCount = ref(0)
const followingCount = ref(0)
const showEditModal = ref(false)
const postToEdit = ref(null)

const openEditModal = (post) => {
  postToEdit.value = { ...post }  
  showEditModal.value = true
}

const closeEditModal = () => {
  showEditModal.value = false
  postToEdit.value = null
}

const handlePostUpdated = (updatedPost) => {
  const index = posts.value.findIndex(p => p.id_post === updatedPost.id_post)
  if (index !== -1) posts.value[index] = { ...updatedPost }
}
const goToUpdate = (postId) => {
  router.push({ name: 'UpdatePost', params: { id: postId } })
}

const user = computed(() => {
  if (userProfile.value) return userProfile.value
  if (posts.value.length) {
    const first = posts.value[0]
    return {
      nom: first.nom,
      prenom: first.prenom,
      banner_url: first.banner_url,
      email: first.email,
      id_avatar: first.id_avatar || null,
      bio: first.bio || null
    }
  }
  return {}
})

const userExists = computed(() => !!user.value && (user.value.nom || user.value.prenom || user.value.email))

const totalLikes = computed(() => {
  return posts.value.reduce((sum, post) => {
    const count = Number(post.likes_count || 0)
    return sum + (Number.isFinite(count) ? count : 0)
  }, 0)
})

const avatarUrl = computed(() => {
  if (user.value?.id_avatar) return `${API.AVATAR}/avatars/${user.value.id_avatar}`
  if (user.value?.avatar_url) return user.value.avatar_url
  if (user.value?.profile_url) return user.value.profile_url
  return 'https://placehold.co/120x120/eeeeee/6b7280?text=Avatar'
})

const usernameHandle = computed(() => {
  if (user.value?.username) return '@' + user.value.username
  if (user.value?.email) return '@' + user.value.email.split('@')[0]
  return 'Nom d\'utilisateur indisponible'
})

const loadUser = async () => {
  try {
    const res = await axios.get(`${API.AUTH}/users/${userId}`)
    userProfile.value = res.data
  } catch (err) {
    console.warn('Impossible de récupérer l\'utilisateur, fallback aux données de post si disponibles.', err)
  }
}

const loadUserPosts = async () => {
  try {
    const res = await axios.get(`${API.SOCIAL}/posts/user/${userId}`, {
      headers: {
        'Authorization': 'Bearer ' + localStorage.getItem('token')
      }
    })
    posts.value = Array.isArray(res.data) ? res.data : []
  } catch (err) {
    console.error('Erreur récupération posts :', err)
    posts.value = []
  } finally {
    loading.value = false
  }
}
const deletePost = async (postId) => {
  if (!confirm("Voulez-vous vraiment supprimer ce post ?")) return

  try {
    await axios.delete(`${API.SOCIAL}/posts/${postId}`, {
      headers: {
        'Authorization': 'Bearer ' + localStorage.getItem('token')
      }
    })

    posts.value = posts.value.filter(post => post.id_post !== postId)
    console.log('token'+localStorage.getItem('token'))

  } catch (err) {
    console.error("Erreur suppression post :", err)
  }
}

const isProfileOwner = computed(() => currentUserId === userId)
const isOwner = (post) => {
  return currentUserId && post.id_utilisateur === currentUserId
}
const formatDate = (dateStr) => {
  if (!dateStr) return ''
  const date = new Date(dateStr)
  const day = String(date.getDate()).padStart(2, '0')
  const month = String(date.getMonth() + 1).padStart(2, '0') 
  const year = String(date.getFullYear()).slice(-2) 
  return `${day}-${month}-${year}`}

onMounted(() => {
  if (userId) {
    loadUser()
    loadUserPosts()
  } else {
    loading.value = false
  }
})
</script>

<style scoped>
.user-posts{
  max-width:1000px;
  margin:40px auto;
  padding:0 20px;
  font-family:'Inter','Segoe UI',sans-serif;
}


.loading,.no-user{
  text-align:center;
  padding:18px;
  border-radius:10px;
  background:#f3f4f6;
  color:#374151;
}


.user-profile{
  background:white;
  border-radius:18px;
  overflow:hidden;
  box-shadow:0 10px 30px rgba(0,0,0,0.08);
  margin-bottom:25px;
}

.banner{
  height:180px;
  background:linear-gradient(120deg,#3b82f6,#2563eb);
  background-size:cover;
  background-position:center;
}

.profile-body{
  display:flex;
  align-items:center;
  gap:20px;
  padding:0 20px 20px 20px;
  margin-top:-45px;
}

.avatar{
  width:90px;
  height:90px;
  border-radius:50%;
  border:4px solid white;
  object-fit:cover;
  box-shadow:0 10px 25px rgba(0,0,0,0.2);
}

.profile-info h3{
  margin:0;
  font-size:22px;
}

.username{
  color:#6b7280;
  font-size:14px;
}

.meta{
  font-size:13px;
  color:#9ca3af;
}


.tabs{
  display:flex;
  gap:12px;
  margin:25px 0;
  border-bottom:1px solid #e5e7eb;
}

.tabs button{
  display:flex;
  align-items:center;
  gap:6px;
  padding:10px 16px;
  border:none;
  background:none;
  cursor:pointer;
  font-weight:600;
  color:#6b7280;
  border-bottom:2px solid transparent;
  transition:0.2s;
}

.tabs button:hover{
  color:#111827;
}

.tabs button.active{
  color:#2563eb;
  border-bottom:2px solid #2563eb;
}

.tabs svg{
  width:18px;
  height:18px;
}


.posts-grid{
  display:grid;
  grid-template-columns:repeat(auto-fill,minmax(230px,1fr));
  gap:20px;
  margin-top: 10px;
}


.post{
  position:relative;
  background:white;
  border-radius:14px;
  overflow:hidden;
  box-shadow:0 8px 20px rgba(0,0,0,0.08);
  transition:all 0.25s ease;
}

.post:hover{
  transform:translateY(-5px);
  box-shadow:0 16px 30px rgba(0,0,0,0.15);
}


.media-container{
  width:100%;
  height:200px;
  overflow:hidden;
}

.media{
  width:100%;
  height:100%;
  object-fit:cover;
}


.description{
  padding:10px 12px;
  font-size:14px;
  color:#374151;
  min-height:40px;
}


.post-footer{
  display:flex;
  justify-content:space-between;
  align-items:center;
  padding:0 12px 12px 12px;
}

.likes{
  color:#ef4444;
  font-weight:600;
  font-size:13px;
}

.date{
  font-size:12px;
  color:#9ca3af;
}


.post-actions{
  position:absolute;
  top:8px;
  right:8px;
  display:flex;
  gap:6px;
}

.delete-btn,.edit-btn{
  width:30px;
  height:30px;
  border:none;
  border-radius:6px;
  display:flex;
  align-items:center;
  justify-content:center;
  cursor:pointer;
  transition:0.2s;
}

.delete-btn{
  background:#ef4444;
}

.edit-btn{
  background:#3b82f6;
}

.delete-btn svg,.edit-btn svg{
  width:16px;
  height:16px;
}

.delete-btn:hover{
  background:#dc2626;
  transform:scale(1.1);
}

.edit-btn:hover{
  background:#2563eb;
  transform:scale(1.1);
}
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  background: rgba(0,0,0,0.4);
  display: flex;
  justify-content: center;  
  align-items: center;     
  z-index: 1000;
  overflow-y: auto;         
}


@media (max-width:700px){

.profile-body{
flex-direction:column;
align-items:flex-start;
margin-top:-40px;
}

.posts-grid{
grid-template-columns:1fr;
}

}
</style>