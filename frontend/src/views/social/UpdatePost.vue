<template>
  <div class="update-post">
    <button class="modal-close" @click="closeModal">&times;</button>
    <h2>Modifier le post</h2>

    <textarea
      v-model="description"
      placeholder="Modifier le texte du post..."
      rows="4"
      class="text-area"
    ></textarea>

    <label class="file-upload">
  <input type="file" @change="handleFile" accept="image/*,video/*" />
  📁 Choisir une image ou vidéo
</label>

    <div v-if="previewUrl" class="preview">
      <img v-if="isImage" :src="previewUrl" alt="Preview" class="preview-img" />
      <video v-else controls class="preview-video">
        <source :src="previewUrl" type="video/mp4" />
      </video>
    </div>

    <button @click="updatePost" :disabled="loading" class="btn">
      {{ loading ? 'Mise à jour...' : 'Modifier' }}
    </button>

    <p v-if="error" class="error">{{ error }}</p>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import axios from 'axios'
import { API } from '../../shared/config/api'

const props = defineProps({
  post: { type: Object, required: true }
})

const emit = defineEmits(['close', 'updated'])

const description = ref(props.post.description || '')
const file = ref(null)
const previewUrl = ref(props.post.media_url || null)
const loading = ref(false)
const error = ref('')
const isDraft = ref(true)

const isImage = computed(() => {
  if (!file.value) return false
  return file.value.type.startsWith('image/')
})

const handleFile = (event) => {
  const selectedFile = event.target.files[0]
  if (!selectedFile) return

  file.value = selectedFile
  previewUrl.value = URL.createObjectURL(selectedFile)
}

watch(() => props.post, (newPost) => {
  description.value = newPost.description || ''
  previewUrl.value = newPost.media_url || null
  file.value = null
})

const closeModal = () => {
  emit('close')
}

const updatePost = async () => {

  if (!description.value && !file.value) {
    error.value = 'Vous devez fournir un texte ou un fichier'
    return
  }

  loading.value = true
  error.value = ''

  try {

    const formData = new FormData()
    formData.append('description', description.value)
    formData.append('_method', 'PUT')
    formData.append('is_draft', isDraft.value)

    if (file.value) {
      formData.append('file', file.value)
    }

    const response = await axios.post(
      `${API.SOCIAL}/posts/${props.post.id_post}`,
      formData,
      {
        headers: {
          Authorization: 'Bearer ' + localStorage.getItem('token')
        }
      }
    )

    emit('updated', {
  ...props.post,
  description: description.value,
  media_url: previewUrl.value
})
    emit('close')

  } catch (err) {

    error.value = err.response?.data?.error || 'Erreur lors de la mise à jour'
    console.error(err)

  } finally {

    loading.value = false

  }

}
</script>
<style scoped>
.update-post {
  max-width: 520px;
  width: 90%;                
  max-height: 90vh;         
  overflow-y: auto;          
  background: white;
  border-radius: 18px;
  padding: 28px;
  border: 1px solid #e6e8f0;
  box-shadow: 0 10px 28px rgba(0,0,0,0.08);
  display: flex;
  flex-direction: column;
  gap: 18px;
  transition: transform 0.25s ease, box-shadow 0.25s ease;
}

.update-post:hover {
  transform: translateY(-3px);
  box-shadow: 0 18px 35px rgba(0,0,0,0.12);
}

.update-post h2 {
  text-align: center;
  font-weight: 700;
  color: #1f305b;
  letter-spacing: 0.3px;
}
.update-post {
  max-height: 90vh; 
  overflow-y: auto;
}

@media (max-width:600px) {
  .update-post {
    margin: 30px 10px;
    padding: 20px;
  }

  .preview-video {
    max-width: 100%;
  }
}
.text-area {
  width: 100%;
  resize: none;
  border-radius: 12px;
  background: #f7f9fc;
  padding: 12px;
  font-size: 0.95rem;
  border: 1px solid #e2e6f0;
  color: #374151;
  transition: all 0.2s ease;
}

.text-area:focus {
  outline: none;
  border-color: #2948ff;
  background: white;
  box-shadow: 0 0 0 3px rgba(41,72,255,0.15);
}


.file-upload {
  display: flex;
  justify-content: center;
  align-items: center;
  border: 2px dashed #cfd6ff;
  padding: 14px;
  border-radius: 12px;
  cursor: pointer;
  font-size: 0.9rem;
  color: #3a4bb3;
  background: #f5f7ff;
  transition: all 0.2s ease;
}

.file-upload:hover {
  background: #eef1ff;
  border-color: #4b63ff;
}

.file-upload input {
  display: none;
}


.preview {
  display: flex;
  justify-content: center;
  margin-top: 10px;
}

.preview-img {
  max-width: 100%;
  width: 20%;
  border-radius: 12px;
  box-shadow: 0 6px 18px rgba(0,0,0,0.15);
}

.preview-video {
  max-width: 80%;
  border-radius: 12px;
  box-shadow: 0 6px 18px rgba(0,0,0,0.15);
}



.btn {
  padding: 12px;
  border-radius: 12px;
  border: none;
  background: linear-gradient(135deg,#1a2dff,#5b73ff);
  color: white;
  font-weight: 600;
  cursor: pointer;
  font-size: 0.95rem;
  transition: all 0.25s ease;
}

.btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 14px rgba(41,72,255,0.3);
}

.btn:disabled {
  background: #a5accf;
  cursor: not-allowed;
  box-shadow: none;
}


.error {
  color: #e03131;
  font-weight: 500;
  text-align: center;
}
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  background: rgba(0,0,0,0.4);
  display: flex;
  /* justify-content: center; */
  align-items: center;
  z-index: 1000;
}
.modal-close:hover {
  color: #ef4444;
}


@media (max-width:600px) {

.update-post {
  margin: 30px 10px;
  padding: 20px;
}

.preview-video {
  max-width: 100%;
}

}</style>