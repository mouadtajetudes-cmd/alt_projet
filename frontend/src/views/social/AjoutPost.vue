<template></template>
<script setup>
import { ref } from 'vue'
import axios from "axios"

const API_BASE = 'http://localhost:6090/social'

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

    await axios.post(`${API_BASE}/posts`, formData, {
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
</script>