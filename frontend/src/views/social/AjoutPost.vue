<template>
  <div class="create-post">
    <form @submit.prevent="submitPost">
      <div class="form-group">
        <textarea
          v-model="post.description"
          placeholder="Écrivez votre message ici..."
          rows="3"
        ></textarea>
      </div>
      <div class="form-group">
        <input
          type="file"
          ref="fileInput"
          @change="onFileChange"
          accept="image/*,video/*"
          style="display:none;"
        />
      </div>
      <div class="btn-form">
        <button type="button" @click="$refs.fileInput.click()" class="upload"><img src="../../assets/images/upload.png"/></button>
         <span v-if="post.file" class="file-name">{{ post.file.name }}</span>
        <button type="submit" class="publiek">Publier</button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { API } from '../../shared/config/api'

const post = ref({
  description: '',
  file: null
})

const onFileChange = (event) => {
  post.value.file = event.target.files[0] || null
}

const getCurrentUserId = () => {
  const storedUser = localStorage.getItem('user')

  if (!storedUser) return null

  try {
    const user = JSON.parse(storedUser)
    return Number(user?.id_utilisateur ?? user?.id ?? null)
  } catch {
    return null
  }
}

const getPostType = () => {
  if (!post.value.file) return 'text'
  if (post.value.file.type.startsWith('image/')) return 'image'
  if (post.value.file.type.startsWith('video/')) return 'video'
  return null
}

const submitPost = async () => {
  try {
    const idUtilisateur = getCurrentUserId()
    const type = getPostType()

    if (!idUtilisateur) {
      throw new Error('Utilisateur introuvable')
    }

    if (!type) {
      throw new Error('Type de fichier non pris en charge')
    }

    if (!post.value.description?.trim() && !post.value.file) {
      throw new Error('Le post est vide')
    }

    const formData = new FormData()
    formData.append("id_utilisateur", String(idUtilisateur))
    formData.append("type", type)
    formData.append("description", post.value.description?.trim() || "")
    if (post.value.file) {
      formData.append("file", post.value.file)
    }

    const response = await fetch(`${API.SOCIAL}/posts`, {
      method: "POST",
      body: formData
    })

    const data = await response.json()

    if (!response.ok || data?.status === 'error') {
      throw new Error(data?.message || 'Erreur lors de la création du post')
    }

    post.value.description = ''
    post.value.file = null

  } catch (err) {
    console.error(err)
    alert(err.message || 'Erreur lors de la création du post')
  }
}
</script>

<style scoped>
.create-post {
  background: #f8f9fa;
  padding: 1.5rem;
  border-radius: 12px;
  max-width: 100%;
  margin: 2rem auto;
  box-shadow: 0 2px 3px rgba(0,0,0,0.1);
}


.form-group {
  margin-bottom: 1rem;
}

textarea {
  width: 100%;
  padding: 0.5rem;
  border: none;
  font-size: 0.5rem;
}

.publiek {
  background: #0d6efd;
  color: white;
  border: none;
  padding: 0.5rem 1rem;
  border-radius: 6px;
  cursor: pointer;
}

.publiek:hover {
  background: #0b5ed7;
}

.upload {
  background: none;
  border: none;
  cursor: pointer;
  padding: 0.3rem;
}

.upload img {
  width: 20px;
  height: 20px;
}

.btn-form {
  display: flex;
  justify-content: space-between;
  align-items: center;
}
</style>
