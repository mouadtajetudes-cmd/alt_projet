<template>
  <div class="create-post-card">
    <form @submit.prevent="submitPost" class="create-post-form">
      <div class="post-header">
        <img :src="avatarUrl" alt="Avatar" class="avatar"/>
        <textarea
          v-model="post.description"
          placeholder="Quoi de neuf ?"
          rows="3"
        ></textarea>
      </div>

      <div class="post-actions">
        <button type="button" @click="$refs.fileInput.click()" class="upload-btn">
          <img src="../../assets/images/upload.png" alt="Upload"/>
        </button>
        <input
          type="file"
          ref="fileInput"
          @change="onFileChange"
          accept="image/*,video/*"
          style="display:none;"
        />
        <span v-if="post.file" class="file-name">{{ post.file.name }}</span>
      </div>

      <div class="submit-wrapper">
        <button type="button" class="submit-btn" @click="showPopup=true" >Publier</button>
      </div>
    </form>
  </div>
  <div v-if="showPopup" class="popup-overlay">

  <div class="popup">

    <h3>Que voulez-vous faire ?</h3>

    <div class="popup-buttons">

      <button class="publish" @click="sendPost(true)">
        Publier
      </button>

      <button class="draft" @click="sendPost(false)">
        Brouillon
      </button>

      <button class="cancel" @click="showPopup = false">
        Annuler
      </button>

    </div>

  </div>

</div>
</template>

<script setup>
import { ref, computed } from 'vue'
import {useRouter} from 'vue-router'
import { API } from '../../shared/config/api'
import defaultImg from '../../assets/images/default.jpeg'

const user = JSON.parse(localStorage.getItem('user') || '{}')
const userId = user.id_utilisateur || null
const showPopup=ref(false) 
const router=useRouter() 

const post = ref({
  description: '',
  file: null
})

const avatarUrl = computed(() => user.banner_url || defaultImg)

const onFileChange = (event) => {
  post.value.file = event.target.files[0] || null
}

const sendPost = async (isDraft) => {
    showPopup.value = false
  if (!userId) {
    console.error('Utilisateur non authentifié !')
    router.push('/login')
    return
  }

  try {
    const formData = new FormData()
    formData.append("id_utilisateur", userId)
    formData.append("nom", user.nom || "")
    formData.append("prenom", user.prenom || "")
    formData.append("description", post.value.description || "")
    formData.append("is_draft",isDraft)

if (post.value.file) {
  formData.append("file", post.value.file)

  if (post.value.file.type.startsWith("image/")) {
    formData.append("type", "image")
  } else if (post.value.file.type.startsWith("video/")) {
    formData.append("type", "video")
  }
}  
  const response = await fetch(`${API.SOCIAL}/posts`, {
      method: "POST",
      body: formData
    })

    const data = await response.json()
    
    if (data.status === 'success') {
      console.log("Post créé :", data.data)
      post.value.description = ''
      post.value.file = null
    } else {
      console.error("Erreur backend :", data.message || data)
    }


  } catch (err) {
    console.error("Erreur réseau ou serveur :", err)
  }
}
</script>

<style scoped>
.create-post-card {
  background: white;
  border-radius: 16px;
  padding: 1rem;
  margin-bottom: 1.5rem;
  box-shadow: 0 8px 14px rgba(23, 40, 63, 0.08);
  border: 1px solid rgba(141, 157, 216, 0.2);
  display: flex;
  flex-direction: column;
}

.create-post-form {
  display: flex;
  flex-direction: column;
  gap: 0.8rem;
}

.post-header {
  display: flex;
  gap: 0.8rem;
}

.avatar {
  width: 42px;
  height: 42px;
  border-radius: 50%;
  object-fit: cover;
  border: none;
}

textarea {
  flex: 1;
  resize: none;
  border-radius: 12px;
  background: #fff;
  padding: 0.6rem;
  font-size: 0.9rem;
  margin-top: 1em;
  color: #374151;
  background: #f9f9f9;
  transition: border-color 0.2s, box-shadow 0.2s;
}

textarea:focus {
  outline: none;
  border-color: #0333f5;
  box-shadow: 0 0 0 3px rgba(74,108,247,0.15);
  background: #fff;
}

.post-actions {
  display: flex;
  align-items: center;
  gap: 0.8rem;
  flex-wrap: wrap;
}

.upload-btn {
  display: flex;
  align-items: center;
  gap: 0.4rem;
  padding: 0.4rem 0.8rem;
  border: none;
  border-radius: 12px;
  background: #ffffff;
  cursor: pointer;
  transition: background 0.2s;
}

.upload-btn:hover {
  background: #dbe6ff;
}

.upload-btn img {
  width: 18px;
  height: 18px;
}

.file-name {
  font-size: 0.85rem;
  color: #6b7280;
}

.submit-wrapper {
  display: flex;
  justify-content: flex-end;
}

.submit-btn {
  padding: 0.5rem 1rem;
  border-radius: 12px;
  border: none;
  background: #04165f;
  color: #fff;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.2s;
}

.submit-btn:hover {
  background: #456df0;
}
.popup-overlay{
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

.popup{
  background:white;
  padding:25px;
  border-radius:12px;
  width:300px;
  text-align:center;
  box-shadow:0 10px 30px rgba(0,0,0,0.2);
}

.popup h3{
  margin-bottom:20px;
}

.popup-buttons{
  display:flex;
  flex-direction:column;
  gap:10px;
}

.popup-buttons button{
  padding:10px;
  border:none;
  border-radius:8px;
  cursor:pointer;
  font-weight:600;
}

.publish{
  background:#0659f4;
  color:white;
}

.draft{
  background:#16a34a;
  color:white;
}

.cancel{
  background:#ef4444;
  color:white;
}

@media (max-width: 768px) {
  .post-header {
    flex-direction: column;
    align-items: flex-start;
  }

  .avatar {
    width: 35px;
    height: 35px;
  }

  textarea {
    width: 100%;
  }
}
</style>