<template>
  <div class="comments-section">
    <div v-if="loading" class="loading">Chargement des commentaires...</div>
    <div v-else-if="error" class="error">{{ error }}</div>
    <div v-else>
      <div v-for="comment in comments" :key="comment.id_comment" class="comment-card">
<div class="comment-wrapper">
  <div class="comment-user">
    <img :src="comment.user_image ? comment.user_image : defaultAvatar" class="user-avatar" />
    <div class="user-context">
        <span>{{ comment.user_nom }} {{ comment.user_prenom }}</span>
    </div>
  </div>
  <div class="comment-contexte">
    <p class="comment-text">{{ comment.details }}</p>
    <span class="comment-date">{{ new Date(comment.created_at).toLocaleDateString() }}</span> 
 </div>
</div>
</div>
</div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import axios from 'axios'
import { config } from '../../conf'
import defaultAvatar from '../../assets/images/default.jpeg'

const props = defineProps({
  postId: {
    type: Number,
    required: true
  }
})

const comments = ref([])
const loading = ref(true)
const error = ref(null)

const loadComments = async () => {
  loading.value = true
  error.value = null
  try {
    const response = await axios.get(`${config}/posts/${props.postId}/comments`)
    comments.value = response.data.data || []
  } catch (err) {
    console.error('Erreur chargement commentaires:', err)
    error.value = 'Impossible de charger les commentaires'
  } finally {
    loading.value = false
  }
}

watch(() => props.postId, () => {
  loadComments()
})

onMounted(loadComments)
</script>

<style scoped>
.comments-section {
  margin-top: 1rem;
}
.comment-card {
  padding: 0.5rem;
  border-bottom: 1px solid #ddd;
}
.comment-wrapper {
  align-items: flex-start; 
  gap: 0.5em;
}

.comment-user {
  flex-shrink: 0;
  display: flex;
}
.comment-user span{
    margin: 0.5em;
    font-size: x-small;

}

.comment-contexte {
  background-color: rgba(40, 242, 54, 0.555);
  padding: 0.5em;
  border-radius:0 10px 10px;
  margin-left: 2em;
  margin-bottom: 1em;
  width: 50%;
}
.comment-text{
    font-size: small;
    size: 1em;
}
.comment-date{
    font-size: xx-small;
    size: 0em;
    font-family:'Courier New', Courier, monospace;

}
.user-avatar {
  width: 30px;
  height: 30px;
  border-radius: 50%;
  object-fit: cover;
  border: 1px solid #ccc;
}
</style>