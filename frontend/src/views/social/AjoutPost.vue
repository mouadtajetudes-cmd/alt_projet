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