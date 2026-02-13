<template>
  <div id="app" class="min-h-screen bg-gray-50">
    <Navbar />
    
    <main>
      <router-view />
    </main>
    
    <footer>
      <div class="container">
        <p>&copy; 2026 ALT Platform - Tous droits réservés</p>
      </div>
    </footer>
  </div>
</template>

<script setup>
import Navbar from './components/Navbar.vue'
</script>

<style>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

#app {
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen',
    'Ubuntu', 'Cantarell', 'Fira Sans', 'Droid Sans', 'Helvetica Neue',
    sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}

main {
  min-height: calc(100vh - 160px);
  padding: 2rem 0;
}

footer {
  background: #f8f9fa;
  padding: 2rem 0;
  text-align: center;
  border-top: 1px solid #dee2e6;
}

footer .container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 1rem;
}

footer p {
  margin: 0;
  color: #6c757d;
}
</style>
