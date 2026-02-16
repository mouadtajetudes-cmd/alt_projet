<template>
<<<<<<< HEAD
  <div class="home">
    <div class="container">
      <div class="hero">
        <h1>Bienvenue sur ALT Platform</h1>
        <p>Plateforme sociale avec chat en temps réel, marketplace, avatars et réseau social</p>
      </div>
      
      <div class="features">
        <div class="feature-card">
          <div class="icon">💬</div>
          <h3>Chat Temps Réel</h3>
          <p>Discutez instantanément avec WebSocket</p>
          <router-link to="/chat" class="btn">Accéder au Chat</router-link>
        </div>
        
        <div class="feature-card">
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
        </div>
=======
  <div class="min-h-screen bg-gradient-to-br from-gray-50 via-blue-50 to-purple-50">
    <div class="relative overflow-hidden">
      <div class="absolute inset-0 bg-gradient-to-r from-primary/10 to-purple-500/10"></div>

      <div class="relative max-w-7xl mx-auto px-4 py-20">
        <div class="text-center">
          <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-primary to-primary-light rounded-2xl shadow-lg mb-8 animate-fade-in">
            <span class="text-4xl font-bold text-white">A</span>
          </div>

          <h1 class="text-5xl md:text-6xl font-bold text-dark mb-6 animate-slide-down">
            Bienvenue sur <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary to-purple-600">Alt Platform</span>
          </h1>

          <p class="text-xl text-dark-muted max-w-2xl mx-auto mb-10 animate-fade-in" style="animation-delay: 0.2s">
            La plateforme moderne pour connecter, échanger et collaborer avec votre communauté en temps réel
          </p>

          <div class="flex flex-wrap items-center justify-center gap-4 animate-fade-in" style="animation-delay: 0.4s">
            <router-link 
              v-if="!isAuthenticated"
              to="/register"
              class="flex items-center gap-2 px-8 py-4 bg-gradient-to-r from-primary to-primary-light text-white font-semibold rounded-full shadow-lg hover:-translate-y-1 hover:shadow-xl transition-all duration-200"
            >
              <font-awesome-icon icon="user-plus" />
              <span>Commencer gratuitement</span>
            </router-link>
            
            <router-link 
              v-if="!isAuthenticated"
              to="/login"
              class="flex items-center gap-2 px-8 py-4 bg-white text-primary font-semibold rounded-full shadow-md hover:-translate-y-1 hover:shadow-lg transition-all duration-200"
            >
              <font-awesome-icon icon="sign-in-alt" />
              <span>Se connecter</span>
            </router-link>
            
            <router-link 
              v-if="isAuthenticated"
              to="/chat"
              class="flex items-center gap-2 px-8 py-4 bg-gradient-to-r from-primary to-primary-light text-white font-semibold rounded-full shadow-lg hover:-translate-y-1 hover:shadow-xl transition-all duration-200"
            >
              <font-awesome-icon icon="comments" />
              <span>Ouvrir le Chat</span>
            </router-link>
          </div>
        </div>
      </div>
    </div>

    <div v-if="isAuthenticated" class="max-w-7xl mx-auto px-4 py-16">
      <div class="text-center mb-12">
        <h2 class="text-3xl font-bold text-dark mb-4">Votre activité</h2>
        <p class="text-dark-muted max-w-2xl mx-auto">Aperçu rapide de vos statistiques</p>
      </div>
      
      <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
        <BaseCard
          title="Utilisateurs"
          subtitle="Total inscrits"
          icon="users"
          icon-bg-class="bg-gradient-to-br from-purple-500 to-purple-600"
        >
          <div class="text-3xl font-bold text-purple-600">
            {{ stats.users }}
          </div>
        </BaseCard>

        <BaseCard
          title="Groupes"
          subtitle="Créés aujourd'hui"
          icon="layer-group"
          icon-bg-class="bg-gradient-to-br from-green-500 to-teal-500"
        >
          <div class="text-3xl font-bold text-green-600">
            {{ stats.groups }}
          </div>
        </BaseCard>

        <BaseCard
          title="Messages"
          subtitle="Total échangés"
          icon="comments"
          icon-bg-class="bg-gradient-to-br from-blue-500 to-blue-600"
        >
          <div class="text-3xl font-bold text-blue-600">
            {{ stats.messages }}
          </div>
        </BaseCard>

        <BaseCard
          title="En ligne"
          subtitle="Utilisateurs actifs"
          icon="circle"
          icon-bg-class="bg-gradient-to-br from-orange-500 to-red-500"
        >
          <div class="text-3xl font-bold text-orange-600">
            {{ stats.online }}
          </div>
        </BaseCard>
      </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 py-16">
      <div class="text-center mb-12">
        <h2 class="text-3xl font-bold text-dark mb-4">Fonctionnalités principales</h2>
        <p class="text-dark-muted max-w-2xl mx-auto">Tout ce dont vous avez besoin pour communiquer efficacement</p>
      </div>
      
      <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
        <router-link 
          v-for="feature in features"
          :key="feature.path"
          :to="feature.path"
          class="block"
        >
          <BaseCard
            :title="feature.title"
            :subtitle="feature.description"
            :icon="feature.icon"
            :icon-bg-class="feature.bgColor"
            :icon-color-class="feature.iconColor"
            clickable
          />
        </router-link>
      </div>
    </div>

    <div class="bg-gradient-to-r from-primary to-purple-600 py-16">
      <div class="max-w-7xl mx-auto px-4">
        <div class="grid md:grid-cols-3 gap-8 text-center text-white">
          <div class="animate-fade-in">
            <div class="flex items-center justify-center mb-2">
              <font-awesome-icon icon="users" class="text-4xl mb-2" />
            </div>
            <div class="text-4xl font-bold mb-2">{{ stats.users }}+</div>
            <div class="text-white/80">Utilisateurs actifs</div>
          </div>
          
          <div class="animate-fade-in" style="animation-delay: 0.1s">
            <div class="flex items-center justify-center mb-2">
              <font-awesome-icon icon="comments" class="text-4xl mb-2" />
            </div>
            <div class="text-4xl font-bold mb-2">{{ stats.messages }}+</div>
            <div class="text-white/80">Messages échangés</div>
          </div>
          
          <div class="animate-fade-in" style="animation-delay: 0.2s">
            <div class="flex items-center justify-center mb-2">
              <font-awesome-icon icon="user-circle" class="text-4xl mb-2" />
            </div>
            <div class="text-4xl font-bold mb-2">{{ stats.online }}</div>
            <div class="text-white/80">En ligne maintenant</div>
          </div>
        </div>
      </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 py-20">
      <div class="bg-gradient-to-r from-primary to-purple-600 rounded-3xl p-12 text-center text-white shadow-2xl">
        <h2 class="text-3xl md:text-4xl font-bold mb-4">Prêt à commencer ?</h2>
        <p class="text-xl text-white/90 mb-8 max-w-2xl mx-auto">
          Rejoignez notre communauté et profitez d'une expérience de communication moderne et intuitive
        </p>
        
        <router-link 
          v-if="!isAuthenticated"
          to="/register"
          class="inline-flex items-center gap-2 px-8 py-4 bg-white text-primary font-semibold rounded-full shadow-lg hover:-translate-y-1 hover:shadow-xl transition-all duration-200"
        >
          <font-awesome-icon icon="user-plus" />
          <span>Créer un compte</span>
        </router-link>
        
        <router-link 
          v-if="isAuthenticated"
          to="/users"
          class="inline-flex items-center gap-2 px-8 py-4 bg-white text-primary font-semibold rounded-full shadow-lg hover:-translate-y-1 hover:shadow-xl transition-all duration-200"
        >
          <font-awesome-icon icon="users" />
          <span>Voir les utilisateurs</span>
        </router-link>
>>>>>>> origin/groupe/develop
      </div>
    </div>
  </div>
</template>

<<<<<<< HEAD
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
=======
<script setup>
import { ref, computed, onMounted } from 'vue'
import BaseCard from '@/components/BaseCard.vue'

const isAuthenticated = computed(() => {
  return !!localStorage.getItem('token')
})

const features = [
  {
    path: '/chat',
    icon: 'comments',
    iconColor: 'text-blue-600',
    bgColor: 'bg-blue-50',
    title: 'Chat en temps réel',
    description: 'Messagerie instantanée avec WebSocket pour des conversations fluides'
  },
  {
    path: '/groups',
    icon: 'layer-group',
    iconColor: 'text-green-600',
    bgColor: 'bg-green-50',
    title: 'Groupes',
    description: 'Créez et gérez des groupes de travail collaboratifs'
  },
  {
    path: '/users',
    icon: 'users',
    iconColor: 'text-purple-600',
    bgColor: 'bg-purple-50',
    title: 'Gestion d\'utilisateurs',
    description: 'Gérez et interagissez avec tous les membres de la communauté'
  },
  {
    path: '/profile',
    icon: 'user-circle',
    iconColor: 'text-orange-600',
    bgColor: 'bg-orange-50',
    title: 'Profil personnalisé',
    description: 'Personnalisez votre profil et vos informations'
  }
]

const stats = ref({
  users: 0,
  groups: 0,
  messages: 5000,
  online: 0
})

const loadStats = async () => {
  if (!isAuthenticated.value) return
  
  const token = localStorage.getItem('token')
  
  try {
    const usersResponse = await fetch('http://localhost:6090/auth/users', {
      headers: { 'Authorization': `Bearer ${token}` }
    })
    if (usersResponse.ok) {
      const users = await usersResponse.json()
      stats.value.users = users.length
    }
    
    const groupsResponse = await fetch('http://localhost:6090/auth/groups', {
      headers: { 'Authorization': `Bearer ${token}` }
    })
    if (groupsResponse.ok) {
      const groups = await groupsResponse.json()
      stats.value.groups = groups.length
    }
  } catch (err) {
    console.error('Error loading stats:', err)
  }
}

onMounted(() => {
  loadStats()
  
  setInterval(() => {
    stats.value.online = Math.floor(Math.random() * 50) + 30
  }, 5000)
})
</script>

>>>>>>> origin/groupe/develop
