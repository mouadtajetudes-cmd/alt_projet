<template>
  <div id="app">
    <nav class="navbar">
      <div class="container">
        <router-link to="/" class="navbar-brand">ALT Platform</router-link>
        <div class="navbar-menu">
          <router-link to="/">Accueil</router-link>
          <router-link to="/chat">Chat</router-link>
          <router-link to="/marketplace">Marketplace</router-link>
          <router-link to="/social">Social</router-link>
          <router-link to="/avatar">Avatar</router-link>
          <router-link to="/login">Connexion</router-link>
        </div>
      </div>
    </nav>
    
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

<script>
export default {
  name: 'App'
}
</script>

<style scoped>
.navbar {
  background: #0d6efd;
  padding: 1rem 0;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.navbar .container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 1rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.navbar-brand {
  color: white;
  font-size: 1.5rem;
  font-weight: bold;
  text-decoration: none;
}

.navbar-menu {
  display: flex;
  gap: 2rem;
}

.navbar-menu a {
  color: white;
  text-decoration: none;
  transition: opacity 0.2s;
}

.navbar-menu a:hover,
.navbar-menu a.router-link-active {
  opacity: 0.8;
  border-bottom: 2px solid white;
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

footer p {
  margin: 0;
  color: #6c757d;
}
</style>
