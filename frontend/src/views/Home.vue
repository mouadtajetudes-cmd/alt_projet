<template>
  <div class="home">
    <div class="hero">
      <h1>🦊 ALT Platform</h1>
      <p>Connectez-vous et échangez avec votre communauté</p>
    </div>
    
    <div class="container">
      <div class="cards">
        <router-link to="/login" class="card">
          <span class="icon">🔐</span>
          <h3>Connexion</h3>
        </router-link>

        <router-link to="/chat" class="card">
          <span class="icon">💬</span>
          <h3>Chat</h3>
        </router-link>

        <router-link to="/users" class="card">
          <span class="icon">👥</span>
          <h3>Utilisateurs</h3>
        </router-link>

        <router-link to="/profile" class="card">
          <span class="icon">👤</span>
          <h3>Profil</h3>
        </router-link>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Home'
}
</script>

<style scoped>
.home {
  min-height: 100vh;
  background: #f5f7fa;
}

.hero {
  background: linear-gradient(135deg, #2196F3 0%, #1976D2 100%);
  color: white;
  text-align: center;
  padding: 4rem 2rem;
}

.hero h1 {
  font-size: 2rem;
  margin-bottom: 0.5rem;
}

.hero p {
  opacity: 0.9;
}

.container {
  max-width: 900px;
  margin: -2rem auto 0;
  padding: 0 1rem 3rem;
}

.cards {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1.5rem;
}

.card {
  background: white;
  padding: 2rem;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.08);
  text-align: center;
  text-decoration: none;
  color: #333;
  transition: all 0.2s;
}

.card:hover {
  transform: translateY(-4px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.12);
}

.icon {
  font-size: 3rem;
  display: block;
  margin-bottom: 0.5rem;
}

.card h3 {
  color: #2196F3;
  font-size: 1.1rem;
}
</style>
