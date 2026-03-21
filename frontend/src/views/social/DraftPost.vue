<template>
  <div class="drafts">
    <div v-if="drafts.length === 0" class="empty">Aucun brouillon trouvé</div>
    
    <div v-for="draft in drafts" :key="draft.id_post" class="draft-post">
      <p>{{ draft.description || 'Pas de texte' }}</p>
      
      <div v-if="draft.media_url">
        <img v-if="draft.media_type === 'image'" :src="draft.media_url" />
        <video v-else-if="draft.media_type === 'video'" controls :src="draft.media_url"></video>
      </div>

      <div class="buttons">
        <button @click="openModal(draft)">  
             <svg xmlns="http://www.w3.org/2000/svg" fill="white" viewBox="0 0 24 24">
      <path d="M3 17.25V21h3.75l11-11.03-3.75-3.75L3 17.25zM20.71 7.04a1 1 0 0 0 0-1.41l-2.34-2.34a1 1 0 0 0-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
    </svg>

        </button>
        <button @click="deleteDraft(draft.id_post)">
       <svg xmlns="http://www.w3.org/2000/svg" fill="white" viewBox="0 0 24 24">
      <path d="M3 6h18v2H3V6zm2 3h14l-1 12H6L5 9zm4 2v8h2v-8H9zm4 0v8h2v-8h-2z"/>
     </svg>
        </button>
        <button @click="publishDraft(draft.id_post)">Publier</button>
      </div>
    </div>
  </div>
<div v-if="showModal" class="modal-overlay" @click="closeModal">
  <UpdatePost
    :post="selectedDraft"
    @close="closeModal"
    @updated="onUpdated"
    @click.stop
  />
</div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import axios from 'axios'
import { API } from '@/shared/config/api'
import UpdatePost from './UpdatePost.vue'


const { userId } = defineProps({
  userId: { type: Number, required: true }
})

const drafts = ref([])

const showModal = ref(false)
const selectedDraft = ref(null)

const openModal = (draft) => {
  selectedDraft.value = draft
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  selectedDraft.value = null
}

const onUpdated = (updatedDraft) => {
  drafts.value = drafts.value.map(d => 
    d.id_post === updatedDraft.id_post ? updatedDraft : d
  )
}

const loadDrafts = async () => {
  try {
    const res = await axios.get(`${API.SOCIAL}/users/${userId}/drafts`, {
      headers: { 'Authorization': 'Bearer ' + localStorage.getItem('token') }
    })
    drafts.value = res.data.data || []
  } catch (err) {
    console.error("Impossible de récupérer les brouillons", err)
  }
}

onMounted(loadDrafts)
watch(() => userId, loadDrafts)


const deleteDraft = async (postId) => {
  try {
    await axios.delete(`${API.SOCIAL}/posts/${postId}`, {
      headers: { 'Authorization': 'Bearer ' + localStorage.getItem('token') }
    })
    drafts.value = drafts.value.filter(d => d.id_post !== postId)
  } catch (err) {
    console.error("Erreur suppression brouillon :", err)
  }
}

const publishDraft = async (postId) => {
  try {

    await axios.post(`${API.SOCIAL}/posts/${postId}/publish`, {}, {
      headers: { 
        Authorization: 'Bearer ' + localStorage.getItem('token') 
      }
    })

    drafts.value = drafts.value.filter(d => d.id_post !== postId)

  } catch (err) {
    console.error("Erreur publication brouillon :", err)
  }
}
</script>

<style>
.drafts {
  max-width: 700px;
  margin: 30px auto;
  padding: 0 16px;
  font-family: 'Inter', 'Segoe UI', Roboto, sans-serif;
}

.draft-post {
  background: #ffffff;
  border-radius: 14px;
  padding: 16px;
  margin-bottom: 18px;
  box-shadow: 0 6px 18px rgba(0,0,0,0.08);
  border: 1px solid #e5e7eb;
  display: flex;
  flex-direction: column;
  gap: 12px;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.draft-post:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 24px rgba(0,0,0,0.12);
}

.draft-post p {
  margin: 0;
  font-size: 15px;
  color: #374151;
  line-height: 1.4;
}

.draft-post img,
.draft-post video {
  width: 100%;
  max-height: 350px;
  object-fit: cover;
  border-radius: 10px;
}

.buttons {
  display: flex;
  justify-content: flex-end;
  gap: 8px;
}

.buttons button {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 36px;
  height: 36px;
  border-radius: 8px;
  border: none;
  cursor: pointer;
  transition: all 0.2s ease;
}

.buttons svg {
  width: 16px;
  height: 16px;
}

.buttons button:nth-child(1) {
  background: #3b82f6;
}

.buttons button:nth-child(1):hover {
  background: #2563eb;
}

.buttons button:nth-child(2) {
  background: #ef4444;
}

.buttons button:nth-child(2):hover {
  background: #dc2626;
}

.buttons button:nth-child(3) {
  width: auto;
  padding: 0 14px;
  background: #16a34a;
  color: white;
  font-size: 13px;
  font-weight: 600;
}

.buttons button:nth-child(3):hover {
  background: #15803d;
}

.empty {
  text-align: center;
  padding: 20px;
  background: #f9fafb;
  border-radius: 10px;
  color: #6b7280;
  border: 1px dashed #d1d5db;
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

</style>