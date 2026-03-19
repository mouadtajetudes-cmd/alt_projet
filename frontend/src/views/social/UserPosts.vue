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

    <!-- ONGLETS -->
    <div class="tabs">

      <button
        :class="{ active: activeTab === 'posts' }"
        @click="activeTab = 'posts'"
      >
        Publications
      </button>

      <button
        :class="{ active: activeTab === 'followers' }"
        @click="activeTab = 'followers'"
      >
        {{ followersCount }} Followers
      </button>

      <button
        :class="{ active: activeTab === 'following' }"
        @click="activeTab = 'following'"
      >
       {{ followingCount }} Following
      </button>

    </div>

    <div v-if="activeTab === 'posts'">

      <h2>Publications</h2>
      <hr />

      <div v-if="posts.length === 0" class="empty">
        Aucun post trouvé
      </div>

      <div class="posts-grid">

        <div
          v-for="post in posts"
          :key="post.id_post"
          class="post"
        >
            <!-- v-if="isOwner(post)" -->

          <button
            @click="deletePost(post.id_post)"
            class="delete-btn"
          >
            supprimer
          </button>

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
                 <Like :post="post" :userId="currentId" />
            </span>

            <span class="date">
              {{ post.date_publication }}
            </span>
          </div>

        </div>

      </div>

    </div>

    <div v-else-if="activeTab === 'followers'">
      <ListFollowers
        :userId="userId"
        :currentUserId="currentUserId"
        @update-count="followersCount = $event"
      />
    </div>

    <div v-else-if="activeTab === 'following'">
      <ListFollowing
        :userId="userId"
        :currentUserId="currentUserId"
        @update-count="followingCount = $event"
      />
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute } from 'vue-router'
import axios from 'axios'
import { API } from '@/shared/config/api'
import ListFollowers from './ListFollowers.vue'
import ListFollowing from './ListFollowing.vue'
import Like from './Like.vue'

const route = useRoute()
const userId = Number(route.params.id)
const posts = ref([])
const userProfile = ref(null)
const loading = ref(true)
const activeTab = ref('posts')
const currentUserId = Number(localStorage.getItem('userId')) || null
const followersCount = ref(0)
const followingCount = ref(0)

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
    const res = await axios.get(`${API.SOCIAL}/posts/user/${userId}`)
    posts.value = Array.isArray(res.data) ? res.data : []
  } catch (err) {
    console.error('Erreur récupération posts :', err)
    posts.value = []
  } finally {
    loading.value = false
  }
}
// supprimer un post
const deletePost = async (postId) => {
  if (!confirm("Voulez-vous vraiment supprimer ce post ?")) return

  try {
    await axios.delete(`${API.SOCIAL}/posts/${postId}`)

    // retirer le post de la liste
    posts.value = posts.value.filter(post => post.id_post !== postId)

  } catch (err) {
    console.error("Erreur suppression post :", err)
  }
}


const isOwner = (post) => {
  return currentUserId && post.id_utilisateur === currentUserId
}
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
.tabs{
  display:flex;
  gap:10px;
  margin:20px 0;
  border-bottom:1px solid #e5e7eb;
}

.tabs button{
  padding:10px 18px;
  border:none;
  background:transparent;
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
  color:#0659f4;
  border-bottom:2px solid #0659f4;
}

.user-posts {
  width: 100%;
  margin: 40px auto;
  padding: 0 18px;
  font-family: 'Inter', 'Segoe UI', Roboto, sans-serif;
  color: #1f2937;
}

.loading,
.no-user {
  text-align: center;
  padding: 18px 12px;
  background: #eff6ff;
  border: 1px solid #bfdbfe;
  border-radius: 10px;
  margin-bottom: 20px;
  color: #1e40af;
}

.user-profile {
  border-radius: 16px;
  overflow: hidden;
  margin-bottom: 20px;
  background: #fff;
}

.banner {
  height: 140px;
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
}

.profile-body {
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 14px;
}

.avatar {
  width: 85px;
  height: 85px;
  border-radius: 50%;
  object-fit: cover;
  border: 3px solid #fff;
  box-shadow: 0 8px 18px rgba(0, 0, 0, 0.15);
}

.profile-info h3 {
  margin: 0;
  font-size: 21px;
  color: #1f2937;
}

.username {
  margin: 4px 0;
  color: #4b5563;
  font-size: 14px;
}

.meta {
  margin: 0;
  color: #6b7280;
  font-size: 13px;
}

h2 {
  font-size: 20px;
  margin-bottom: 14px;
  color: #111827;
}
.posts-grid{
  display:grid;
  grid-template-columns: repeat(auto-fit, minmax(80px, 1fr));
  gap:20px;
  margin-top:25px;
}
.post {
  position:relative;
  background:white;
  border-radius:14px;
  overflow:hidden;
  box-shadow:0 6px 18px rgba(0,0,0,0.08);
  border: 1px solid #dbeafe;
  padding: 18px;
  margin-bottom: 18px;
  transition: transform 0.24s ease, box-shadow 0.24s ease;
  backdrop-filter: blur(6px);
}

.post:hover {
  transform: translateY(-3px) scale(1.003);
  box-shadow: 0 14px 28px rgba(15, 23, 42, 0.16);
}

.post-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 10px;
}

.post-header h4 {
  margin: 0;
  font-size: 18px;
  color: #111827;
}

.post-author {
  color: #6b7280;
  font-size: 12px;
}

.post-header {
  display: flex;
  justify-content: space-between;
  align-items: baseline;
  margin-bottom: 10px;
}

.post-header h4 {
  margin: 0;
  font-size: 18px;
  color: #111827;
}

.post-author {
  color: #6b7280;
  font-size: 12px;
}

.description{
  /* padding:10px 12px; */
  font-size:10px;
  color:#374151;
}
.media-container{
  width:100%;
  height:50px;
  overflow:hidden;
}

.media{
  width:100%;
  object-fit:cover;
}
.date {
  font-size: 6px;
  color: #6b7280;
  margin-top: 12px;
}
.post-footer{
  display:flex;
  justify-content:space-between;
  padding:10px 12px;
  color:#6b7280;
}
/* supprimer */
.post-actions{
  display:flex;
  justify-content:flex-end;
}

.delete-btn{
  position:absolute;
  top:8px;
  right:8px;
  background:rgba(0,0,0,0.5);
  border:none;
  color:white;
  width:32px;
  height:32px;
  border-radius:50%;
  cursor:pointer;
  display:flex;
  align-items:center;
  justify-content:center;
  transition:0.2s;
}

.delete-btn:hover{
  color:#dc2626;
  transform:scale(1.1);
}
  /* Media queries responsive */
@media (max-width:900px){
  .posts-grid{
    grid-template-columns:repeat(auto-fit, minmax(150px, 1fr));
  }
}

@media (max-width:500px){
  .posts-grid{
    grid-template-columns:1fr;
  }
}
</style>