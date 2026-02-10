<template>
  <div class="home">
    <div class="container">
      <div class="hero">
        <h1>Bienvenue sur ALT Platform</h1>
        <p>Plateforme sociale avec chat en temps réel</p>
      </div>
      
      <div class="features">
        <div class="feature-card">
          <div class="icon">🔐</div>
          <h3>Connexion</h3>
          <p>Accédez à votre compte</p>
          <router-link to="/login" class="btn">Se connecter</router-link>
        </div>

        <div class="feature-card">
          <div class="icon">💬</div>
          <h3>Chat Temps Réel</h3>
          <p>Discutez instantanément</p>
          <router-link to="/chat" class="btn">Accéder au Chat</router-link>
        </div>
        
        <!-- <div class="feature-card">
          <div class="icon">🛒</div>
          <h3>Marketplace</h3>
          <p>Découvrez et achetez des produits</p>
          <router-link to="/marketplace" class="btn">Voir Produits</router-link>
        </div>
        
        <div class="feature-card">
          <div class="icon">👥</div>
          <h3>Réseau Social</h3>
          <p>Partagez vos posts et commentaires</p>
          <router-link to="/social" class="btn">Feed Social</router-link>
        </div>
        
        <div class="feature-card">
          <div class="icon">🎭</div>
          <h3>Avatars</h3>
          <p>Personnalisez votre profil</p>
          <router-link to="/avatar" class="btn">Mes Avatars</router-link>
        </div> -->
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
  padding: 2rem 0;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 1rem;
}

.hero {
  text-align: center;
  padding: 3rem 0;
  background: #0d6efd;
  color: white;
  border-radius: 8px;
  margin-bottom: 3rem;
}

.hero h1 {
  font-size: 2.5rem;
  margin-bottom: 1rem;
}

.hero p {
  font-size: 1.2rem;
  opacity: 0.9;
}

.features {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 2rem;
}

.feature-card {
  background: white;
  padding: 2rem;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  text-align: center;
  transition: transform 0.2s;
}

.feature-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.icon {
  font-size: 3rem;
  margin-bottom: 1rem;
}

.feature-card h3 {
  margin-bottom: 0.5rem;
  color: #333;
}

.feature-card p {
  color: #666;
  margin-bottom: 1rem;
}

.btn {
  display: inline-block;
  background: #0d6efd;
  color: white;
  padding: 0.5rem 1.5rem;
  border-radius: 4px;
  text-decoration: none;
  transition: background 0.2s;
}

.btn:hover {
  background: #0b5ed7;
}
</style>
