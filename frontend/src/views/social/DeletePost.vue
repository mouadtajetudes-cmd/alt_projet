<template>
    <div v-for="post in posts" :key="post.id_post" class="post">

  <div v-if="isOwner(post)" class="post-actions">
    <button @click="deletePost(post.id_post)" class="delete-btn">
      Supprimer
    </button>
  </div>
    </div>

</template>
<script setup>
const deletePost = async (postId) => {
  if (!confirm("Voulez-vous vraiment supprimer ce post ?")) return

  try {
    await axios.delete(`${API.SOCIAL}/posts/${postId}`)

    // retirer le post de la liste
    posts.value = posts.value.filter(post => post.id_post !== postId)

  } catch (err) {
    console.error("Erreur suppression post :", err)
  }
}
</script>
<style scoped>
.post-actions {
  display: flex;
  justify-content: flex-end;
  margin-bottom: 8px;
}

.delete-btn {
  background: #ef4444;
  color: white;
  border: none;
  padding: 6px 10px;
  border-radius: 6px;
  cursor: pointer;
  font-size: 12px;
}

.delete-btn:hover {
  background: #dc2626;
}
</style>