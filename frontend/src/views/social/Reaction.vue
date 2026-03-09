<template>
  <button @click="toggleLike" class="like-btn">
    <img :src="liked ? heartFilled : heartEmpty" class="like-icon" />
    <span>{{ likes }}</span>
  </button>
</template>

<script setup>

import { ref, defineProps } from 'vue'
import axios from 'axios'
import { config } from '../../conf' 
const props = defineProps({
  postId: {
    type: Number,
    required: true
  },
  initialLikes: {
    type: Number,
    default: 0
  },
  initiallyLiked: {
    type: Boolean,
    default: false
  },
  userId: {
    type: Number,
    required: true
  }
})
const liked = ref(props.initiallyLiked)
const likes = ref(props.initialLikes)
const loading = ref(false)
const heartEmpty = require('../../assets/images/unlike.jpg')
const heartFilled = require('../../assets/images/like.jpg')

const toggleLike = async () => {
  if (loading.value) return
  loading.value = true

  try {
    const response = await axios.post(`${config}/posts/${props.postId}/like`, {
      user_id: props.userId,
      like: !liked.value
    })

    likes.value = response.data.likes
    liked.value = response.data.liked

  } catch (err) {
    console.error('Erreur like:', err)
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.like-btn {
  display: flex;
  align-items: center;
  gap: 0.3rem;
  border: none;
  background: #f0f2f5;
  padding: 0.3rem 0.6rem;
  border-radius: 20px;
  cursor: pointer;
  transition: background 0.2s;
}

.like-btn:hover {
  background: #e2e6ea;
}

.like-icon {
  width: 18px;
  height: 18px;
}
</style>