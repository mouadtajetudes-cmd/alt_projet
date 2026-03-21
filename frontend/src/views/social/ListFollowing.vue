<template>
  <div class="user-connections">
    <div class="tabs">
    <div class="tab-content">
      <div v-if="loading" class="loading">Chargement...</div>
        <div v-if="following.length === 0" class="empty">Vous ne suivez personne</div>
        <div v-else class="list">
          <div v-for="user in following" :key="user.id_utilisateur" class="user-card">
            <img :src="getAvatar(user)" class="avatar" />
            <span class="user-name">{{ user.nom }} {{ user.prenom }}</span>
            <AddFollower :targetUserId="user.id_utilisateur" :currentUserId="currentUserId" />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { API } from '../../shared/config/api'
import AddFollower from './AddFollower.vue'

const props = defineProps({
  userId: { type: Number, required: true },
  currentUserId: { type: Number, required: true }
})

const following = ref([])
const followingCount = ref(0)
const loading = ref(true)
const emit = defineEmits(['update-count'])

const getAvatar = (user) => {
  return user.id_avatar
    ? `${API.AVATAR}/avatars/${user.id_avatar}`
    : 'https://placehold.co/40x40/eeeeee/6b7280?text=Avatar'
}


const loadFollowing = async () => {
  try {
    const res = await axios.get(`${API.SOCIAL}/users/${props.userId}/following`, {
      headers: {
        'Authorization': 'Bearer ' + localStorage.getItem('token')
      }
    })
    following.value = res.data.data || res.data || []
    followingCount.value = following.value.length
    emit('update-count', followingCount.value)
  } catch (err) {
    console.error(err)
    following.value = []
  }
}

const loadAll = async () => {
  loading.value = true
  await Promise.all([loadFollowing()])
  loading.value = false
}

onMounted(() => {
  loadAll()
})
</script>

<style scoped>
.user-connections {
  width: 100%;
  margin: 0 auto;
  font-family: 'Inter', sans-serif;
}

.loading, .empty {
  text-align: center;
  color: #6b7280;
  padding: 16px 0;
  font-size: 14px;
}

.list {
  max-height: 300px; 
  overflow-y: auto;
}

.user-card {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  padding: 8px 12px;
  border-bottom: 1px solid #f3f4f6;
  border-radius: 12px;
  transition: background 0.2s ease;
}

.user-card:hover {
  background: #f9fafb;
}

.avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  object-fit: cover;
}

.user-name {
  flex: 1;
  font-weight: 500;
  color: #111827;
  margin-left: 8px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.user-card AddFollower {
  margin-left: 8px;
}

.list::-webkit-scrollbar {
  width: 6px;
}

.list::-webkit-scrollbar-thumb {
  background-color: rgba(0,0,0,0.2);
  border-radius: 3px;
}

@media (max-width: 480px) {
  .user-connections {
    max-width: 100%;
  }
  .avatar {
    width: 32px;
    height: 32px;
  }
}
</style>