<<<<<<< HEAD
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
import { config } from '../../conf'


const idUtilisateur = 2

const post = ref({
  description: '',
  file: null
})

const onFileChange = (event) => {
  post.value.file = event.target.files[0] || null
}

const submitPost = async () => {
  try {
    const formData = new FormData()
    formData.append("id_utilisateur", String(idUtilisateur))
    formData.append("description", post.value.description || "")
    if (post.value.file) {
      formData.append("file", post.value.file)
    }

    const response = await fetch(`${config}/posts`, {
      method: "POST",
      body: formData
    })

    const data = await response.json()
    console.log(data)

    post.value.description = ''
    post.value.file = null

  } catch (err) {
    console.error(err)
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
=======
<template></template>
<script setup>
import { ref } from 'vue'
import axios from "axios"
import { config } from '../conf'
const defaultPost = {
  titre: '',
  description: '',
  image: null
}

const post = ref(defaultPost)

const submitPost = async () => {
  try {
    const formData = new FormData()
    formData.append('titre', post.value.titre)
    formData.append('description', post.value.description)
    if (post.value.image) {
      formData.append('image', post.value.image)
    }

    await axios.post(`${config}/posts`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })
    alert('Post créé avec succès!')
  } catch (error) {
    console.error('Erreur lors de la création du post:', error)
    alert('Erreur lors de la création du post')
  }
}

    const submitPost = async () => {
      try {
        const formData = new FormData()
        formData.append('titre', title.value)
        formData.append('description', description.value)
        if (image.value) {
          formData.append('image', image.value)
        }

        await axios.post(`${config}/posts`, formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        })
        alert('Post créé avec succès!')
      } catch (error) {
        console.error('Erreur lors de la création du post:', error)
        alert('Erreur lors de la création du post')
      }
    }

    return { title, description, image, submitPost }
  }
}
</script>
>>>>>>> 12cf330f2b803327b9789fc239e81dd5bfbec9a9
