<template>
  <div class="likePost">
    <button @click="toggleLike(post.id_post)">
      <img :src="liked ? likedImg : likeImg" class="likeImg" />
    </button>
    <span class="count">{{ likesCount }}</span>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import axios from 'axios'
import { API } from '../../shared/config/api'

import likeImg from '../../assets/images/coeur.png'
import likedImg from '../../assets/images/coeur2.png'

const props = defineProps({
  post: { type: Object, required: true },
  userId: { type: Number, required: true }
})

const liked = ref(false)
const likesCount = ref(0)


const checkLiked = async () => {
  if (!props.post) return

  try {
    const res = await axios.get(
      `${API.SOCIAL}/posts/${props.post.id_post}/liked/${props.userId}`
    )

    liked.value = res.data.liked

  } catch (err) {
    console.error("Erreur check like:", err)
  }
}


const loadLikesCount = async () => {
  try {
    const res = await axios.get(`${API.SOCIAL}/posts/${props.post.id_post}/likes/count`)
    likesCount.value = res.data.count
    console.log(res.data.count)
  } catch (err) {
    console.error("Erreur count likes:", err)
  }
}
const toggleLike = async (postId) => {

  try {

    if (liked.value) {

      await axios.delete(`${API.SOCIAL}/posts/${postId}/likes`, {
        data: { id_utilisateur: props.userId }
      })

      liked.value = false

    } else {

      await axios.post(`${API.SOCIAL}/posts/${postId}/likes`, {
        id_utilisateur: props.userId
      })

      liked.value = true

    }

    await loadLikesCount()

  } catch (err) {
    console.error("Erreur like/unlike:", err)
  }
}


onMounted(() => {
  checkLiked()
  loadLikesCount()
})


watch(() => props.post, () => {
  checkLiked()
  loadLikesCount()
})

</script>

<style scoped>

.likePost{
  display: flex;
  align-items: center;
  gap: 6px;
}

.likePost button{
  border: none;
  background: none;
  cursor: pointer;
}

.likeImg {
  width: 20px;
  height: 20px;
  transition: transform 0.2s;
}

.likeImg:hover{
  transform: scale(1.2);
}

.count{
  font-size: 14px;
  color: #444;
}

</style>