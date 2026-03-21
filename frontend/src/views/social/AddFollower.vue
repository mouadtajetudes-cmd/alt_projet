<template>
  <div>
<button 
  @click="toggleFollow"
  :disabled="loading"
  :class="['follow-btn', { following: isFollowing }]"
>
  {{ isFollowing ? 'Abonné' : 'Suivre' }}
</button>    <p v-if="error" class="error">{{ error }}</p>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { API } from '../../shared/config/api'
import { useRouter } from 'vue-router'

const props = defineProps({
  targetUserId: { type: Number, required: true },
  currentUserId: { type: Number, required: true }
})
const router = useRouter()
const isFollowing = ref(false)
const loading = ref(false)
const error = ref(null)
const token=localStorage.getItem('token')

const checkIsFollowing = async () => {
  try {
    const res = await axios.get(`${API.SOCIAL}/users/${props.currentUserId}/following/${props.targetUserId}`)
    isFollowing.value = res.data.isFollowing
  } catch (err) {
    console.error(err)
    error.value = 'Impossible de récupérer le statut de suivi.'
  }
}

const toggleFollow = async () => {
  loading.value = true
  error.value = null
  if(!token){
    router.push('/login')
    return
  }
  try {
    if (!isFollowing.value) {
await axios.post(
        `${API.SOCIAL}/users/${props.currentUserId}/following`,
        {
          follower_id: props.currentUserId,
          following_id: props.targetUserId
        },
        {
          headers: {
          'Authorization': 'Bearer ' + token
        }
        }

      )

      isFollowing.value = true
    } else {
      await axios.delete(
  `${API.SOCIAL}/users/${props.currentUserId}/following/${props.targetUserId}`,{
    headers: {
          'Authorization': 'Bearer ' + token
        }
  }
)
      isFollowing.value = false
    }
  } catch (err) {
    console.error(err)
    error.value = 'Erreur lors du suivi / désabonnement.'
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  checkIsFollowing()
})
</script>

<style scoped>
.follow-btn {
  padding: 6px 14px;
  border-radius: 6px;
  border: none;
  background-color: #f03bf6; 
  color: white;
  font-weight: bold;
  cursor: pointer;
  transition: all 0.2s ease;
}

.follow-btn.following {
  background-color: white; 
  color: #f03bf6;
}

.follow-btn:hover {
  opacity: 0.8;
}

.follow-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}
.error {
  color: #ef4444;
  margin-top: 6px;
  font-size: 13px;
}
</style>