<template>
  <div class="comment-form">
    <textarea
      v-model="newComment"
      placeholder="Écrire un commentaire..."
      class="comment-input"
      rows="2"
    ></textarea>
    <button
      @click="submitComment"
      :disabled="!newComment.trim()"
      class="comment-btn"
    >
      Envoyer
    </button>
  </div>
</template>
<script setup>
import { ref } from 'vue';
import { config } from '../../conf';
import axios from 'axios';
const newComment =ref('')
const props = defineProps({
  postId: {
    type: Number,
    required: true
  }
})
const userId=3
const comments= ref([])

const submitComment = async () => {
  if (!newComment.value.trim()) return 
  try {
    const res = await axios.post(`${config}/posts/${props.postId}/comments`, {
      id_utilisateur:userId,
      details: newComment.value
    })

    comments.value.push(res.data)

    newComment.value = ''
  } catch (err) {
    console.error("Erreur ajout commentaire:", err)
  }
}
</script>
<style scoped>
.comment-form {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  margin-top: 0.5rem;
  max-width: 80%;
}

.comment-input {
  width: 100%;
  padding: 0.7rem 1rem;
  border-radius: 12px;
  border: 1px solid #ccc;
  resize: none;
  font-size: 0.95rem;
  background-color: #f8f9fa;
  transition: border 0.2s, box-shadow 0.2s;
}

.comment-input:focus {
  outline: none;
  border-color: #007bff;
  box-shadow: 0 0 5px rgba(0, 123, 255, 0.3);
}

.comment-btn {
  align-self: flex-end;
  padding: 0.5rem 1.2rem;
  border: none;
  border-radius: 8px;
  background-color: #131920;
  color: white;
  font-weight: 500;
  cursor: pointer;
  transition: background 0.2s, transform 0.1s;
}

.comment-btn:hover {
  background-color: #13191e;
  transform: scale(1.03);
}

.comment-btn:disabled {
  background-color: #c0c0c0;
  cursor: not-allowed;
}
</style>