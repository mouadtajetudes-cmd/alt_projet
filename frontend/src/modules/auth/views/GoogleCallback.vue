<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50">
    <div class="text-center">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary mx-auto mb-4"></div>
      <p class="text-gray-600">Finalisation de la connexion Google...</p>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue'

onMounted(() => {
  const urlParams = new URLSearchParams(window.location.search)
  const code = urlParams.get('code')
  const state = urlParams.get('state')
  
  if (code && window.opener) {
    window.opener.postMessage(
      {
        type: 'google-auth-callback',
        code,
        state
      },
      window.location.origin
    )
    
    setTimeout(() => {
      window.close()
    }, 500)
  } else {
    window.location.href = '/login'
  }
})
</script>
