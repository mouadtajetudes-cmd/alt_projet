<template>
  <div class="comments-section">

    <div v-if="loading" class="loading">
      Chargement des commentaires...
    </div>

    <div v-else-if="error" class="error">
      {{ error }}
    </div>

    <div v-else class="comments-list">

      <div
        v-for="comment in comments"
        :key="comment.id_commentaire"
        class="comment-card"
        :class="{ 'my-comment': isMyComment(comment) }"
      >

        <div class="comment-wrapper">

          <div class="comment-user" v-if="!isMyComment(comment)">
            <img
              :src="comment.banner_url ? comment.banner_url : defaultAvatar"
              class="user-avatar"
            />
            <div class="comment-author">
              <span>{{ comment.user_nom }} {{ comment.user_prenom }}</span>
            </div>
          </div>

          <div class="comment-contexte">

  

  <p class="comment-text">
    {{ comment.details }}
  </p>

  <span class="comment-date">
    {{ formatDate(comment.created_at) }}
  </span>

</div>
        </div>

      </div>

    </div>

  </div>
</template>

<script setup>

import { ref, onMounted, watch } from 'vue'
import axios from 'axios'
import { API } from '../../shared/config/api'

const props = defineProps({
  postId: {
    type: Number,
    required: true
  }
})

const comments = ref([])
const loading = ref(true)
const error = ref(null)

const defaultAvatar=()=>{
  return 'https://placehold.co/40x40/eeeeee/6b7280?text=Avatar'
}

const user = JSON.parse(localStorage.getItem('user') || '{}')
const userId = user.id_utilisateur

const isMyComment = (comment) => {
  return comment.id_utilisateur === userId
}

const loadComments = async () => {

  loading.value = true
  error.value = null

  try {

    const response = await axios.get(`${API.SOCIAL}/posts/${props.postId}/comments`)

    comments.value = response.data.data || []

  } catch (err) {

    console.error(err)
    error.value = "Impossible de charger les commentaires"

  } finally {

    loading.value = false

  }

}

watch(() => props.postId, () => {
  loadComments()
})
const formatDate = (dateStr) => {
  if (!dateStr) return ''
  const date = new Date(dateStr)
  const day = String(date.getDate()).padStart(2, '0')
  const month = String(date.getMonth() + 1).padStart(2, '0') 
  const year = String(date.getFullYear()).slice(-2) 
  return `${day}-${month}-${year}`}


onMounted(loadComments)

</script>

<style scoped>

.comments-section{
  margin-top:1rem;
  display:flex;
  flex-direction:column;
  gap:20px;
}
.comment-list{
  display:flex;
  flex-direction:column;
  gap:4px;
}

.comment-card{
  display:flex;
  width:100%;
}

.my-comment{
  justify-content:flex-end;
}

.comment-wrapper{
 display: flex;
    gap: 8px;
    flex-direction: column;
  }

.my-comment .comment-wrapper{
  flex-direction:row-reverse;
}

.comment-user{
  display:flex;
}

.comment-user span{
  margin-left:6px;
  font-size:0.8rem;
  font-weight:600;
  color:#374151;
}
.comment-author{
  font-size:0.6rem;
  font-weight:600;
  color:#374151;
  display:block;
  margin-bottom:2px;
}

.my-comment .comment-author{
  color:#e0e7ff;
}

.user-avatar{
  width:32px;
  height:32px;
  border-radius:50%;
  object-fit:cover;
  border:2px solid #e5e7eb;
}

.comment-contexte{
  background: #f3f4f6;
    padding: 10px 12px;
    margin-left: 2em;
    margin-bottom: 0.5em;
    border-radius: 4px 14px;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
}

.my-comment .comment-contexte{
  background:#3b82f6;
  color:white;
  border-radius:14px 14px 4px 14px;
}

.comment-text{
  font-size:smaller;
  margin:0;
  line-height:1.4;
}

.comment-date{
  display:block;
  margin-top:4px;
  font-size:0.5rem;
  opacity:0.7;
}

.loading{
  text-align:center;
  color:#6b7280;
  font-size:0.9rem;
}

.error{
  text-align:center;
  color:#ef4444;
  font-size:0.9rem;
}
</style>