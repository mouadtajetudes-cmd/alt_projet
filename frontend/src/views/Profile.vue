<template>
  <div class="page">
    <div class="container">
      <div class="card">
        <h2>👤 Mon Profil</h2>
        
        <div v-if="loading">Chargement...</div>
        
        <div v-else-if="user">
          <div v-if="!editing" class="info">
            <div class="item">
              <strong>Nom:</strong> {{ user.nom }}
            </div>
            <div class="item">
              <strong>Prénom:</strong> {{ user.prenom }}
            </div>
            <div class="item">
              <strong>Email:</strong> {{ user.email }}
            </div>
            <div class="item">
              <strong>Téléphone:</strong> {{ user.telephone || 'Non renseigné' }}
            </div>
            <div class="item">
              <strong>Statut:</strong>
              <span :class="isAdmin ? 'badge-admin' : 'badge-user'">
                {{ isAdmin ? 'Administrateur' : 'Utilisateur' }}
              </span>
              <span v-if="isPremium" class="badge-premium">Premium</span>
            </div>
            <div class="item">
              <strong>Créé le:</strong> {{ formatDate(user.created_at) }}
            </div>
            
            <div class="actions">
              <button @click="editing = true" class="btn-primary">Modifier</button>
              <router-link to="/users" class="btn">Utilisateurs</router-link>
              <router-link to="/chat" class="btn">Chat</router-link>
              <button @click="logout" class="btn-danger">Déconnexion</button>
            </div>
          </div>

          <div v-else>
            <form @submit.prevent="updateProfile">
              <input v-model="form.nom" placeholder="Nom" required>
              <input v-model="form.prenom" placeholder="Prénom" required>
              <input v-model="form.email" type="email" placeholder="Email" required>
              
              <button type="submit" class="btn-primary" :disabled="saving">
                {{ saving ? 'Enregistrement...' : 'Enregistrer' }}
              </button>
              <button type="button" @click="cancelEdit" class="btn">Annuler</button>
              
              <p v-if="error" class="error">{{ error }}</p>
              <p v-if="success" class="success">{{ success }}</p>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Profile',
  data() {
    return {
      user: null,
      loading: true,
      editing: false,
      saving: false,
      error: '',
      success: '',
      form: {
        nom: '',
        prenom: '',
        email: ''
      }
    }
  },
  computed: {
    isAdmin() {
      if (!this.user) return false
      return this.user.administrateur === 'true'
    },
    isPremium() {
      if (!this.user) return false
      return this.user.premium === 'true'
    }
  },
  mounted() {
    this.loadProfile()
  },
  methods: {
    async loadProfile() {
      this.loading = true
      const token = localStorage.getItem('token')
      const userData = JSON.parse(localStorage.getItem('user') || '{}')
      
      if (!token || !userData.id) {
        this.$router.push('/login')
        return
      }
      
      try {
        const response = await fetch(`http://localhost:6090/auth/users/${userData.id}`, {
          headers: { 'Authorization': `Bearer ${token}` }
        })
        
        if (response.ok) {
          this.user = await response.json()
          this.form = {
            nom: this.user.nom,
            prenom: this.user.prenom,
            email: this.user.email
          }
        }
      } catch (err) {
        this.error = 'Erreur de chargement'
      } finally {
        this.loading = false
      }
    },
    
    async updateProfile() {
      this.error = ''
      this.success = ''
      this.saving = true
      
      const token = localStorage.getItem('token')
      const userData = JSON.parse(localStorage.getItem('user') || '{}')
      
      try {
        const response = await fetch(`http://localhost:6090/auth/users/${userData.id}`, {
          method: 'PUT',
          headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`
          },
          body: JSON.stringify(this.form)
        })
        
        if (response.ok) {
          this.user = await response.json()
          this.success = 'Profil mis à jour'
          this.editing = false
          localStorage.setItem('user', JSON.stringify(this.user))
        } else {
          this.error = 'Erreur lors de la mise à jour'
        }
      } catch (err) {
        this.error = 'Impossible de mettre à jour'
      } finally {
        this.saving = false
      }
    },
    
    cancelEdit() {
      this.editing = false
      this.error = ''
      this.success = ''
      this.form = {
        nom: this.user.nom,
        prenom: this.user.prenom,
        email: this.user.email
      }
    },
    
    logout() {
      localStorage.removeItem('token')
      localStorage.removeItem('user')
      this.$router.push('/login')
    },
    
    formatDate(dateString) {
      if (!dateString) return 'Non disponible'
      const date = new Date(dateString)
      if (isNaN(date.getTime())) return 'Non disponible'
      return date.toLocaleDateString('fr-FR')
    }
  }
}
</script>

<style scoped>
.page {
  min-height: 100vh;
  background: #f5f7fa;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 2rem 1rem;
}

.container {
  width: 100%;
  max-width: 600px;
}

.card {
  background: white;
  padding: 2.5rem;
  border-radius: 12px;
  box-shadow: 0 2px 12px rgba(0,0,0,0.08);
}

.card h2 {
  text-align: center;
  color: #333;
  margin-bottom: 2rem;
}

.info {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.item {
  padding: 0.75rem;
  background: #f5f7fa;
  border-radius: 8px;
}

.item strong {
  color: #666;
  display: block;
  margin-bottom: 0.25rem;
  font-size: 0.85rem;
}

.badge-admin {
  background: #FF6B35;
  color: white;
  padding: 0.25rem 0.75rem;
  border-radius: 12px;
  font-size: 0.85rem;
  margin-right: 0.5rem;
}

.badge-user {
  background: #e0e0e0;
  color: #666;
  padding: 0.25rem 0.75rem;
  border-radius: 12px;
  font-size: 0.85rem;
  margin-right: 0.5rem;
}

.badge-premium {
  background: #FFD700;
  color: #333;
  padding: 0.25rem 0.75rem;
  border-radius: 12px;
  font-size: 0.85rem;
}

.actions {
  display: flex;
  gap: 0.5rem;
  margin-top: 1.5rem;
  flex-wrap: wrap;
}

form {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

input {
  padding: 0.75rem;
  border: 2px solid #e0e0e0;
  border-radius: 8px;
  font-size: 1rem;
}

input:focus {
  outline: none;
  border-color: #2196F3;
}

.btn {
  padding: 0.75rem 1rem;
  background: #f5f7fa;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  text-decoration: none;
  color: #333;
  text-align: center;
}

.btn:hover {
  background: #e0e0e0;
}

.btn-primary {
  padding: 0.75rem 1rem;
  background: #2196F3;
  color: white;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 500;
}

.btn-primary:hover {
  background: #1976D2;
}

.btn-primary:disabled {
  background: #ccc;
  cursor: not-allowed;
}

.btn-danger {
  padding: 0.75rem 1rem;
  background: #f44336;
  color: white;
  border: none;
  border-radius: 8px;
  cursor: pointer;
}

.btn-danger:hover {
  background: #d32f2f;
}

.error {
  padding: 0.75rem;
  background: #ffebee;
  color: #c62828;
  border-radius: 8px;
  font-size: 0.9rem;
}

.success {
  padding: 0.75rem;
  background: #e8f5e9;
  color: #2e7d32;
  border-radius: 8px;
  font-size: 0.9rem;
}
</style>
