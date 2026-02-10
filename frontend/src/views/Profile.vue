<template>
  <div class="profile-page">
    <div class="container">
      <div class="profile-card">
        <h2>Mon Profil</h2>
        
        <div v-if="loading" class="loading">Chargement...</div>
        
        <div v-else-if="user">
          <div v-if="!editing" class="profile-info">
            <div class="info-item">
              <label>Nom</label>
              <p>{{ user.nom }}</p>
            </div>
            
            <div class="info-item">
              <label>Prénom</label>
              <p>{{ user.prenom }}</p>
            </div>
            
            <div class="info-item">
              <label>Email</label>
              <p>{{ user.email }}</p>
            </div>
            
            <div class="info-item">
              <label>Créé le</label>
              <p>{{ formatDate(user.created_at) }}</p>
            </div>
            
            <div class="actions">
              <button class="btn-primary" @click="editing = true">Modifier</button>
              <button class="btn-secondary" @click="logout">Déconnexion</button>
            </div>
          </div>
          
          <form v-else @submit.prevent="updateProfile" class="profile-form">
            <div class="form-group">
              <label>Nom</label>
              <input v-model="editForm.nom" type="text" required>
            </div>
            
            <div class="form-group">
              <label>Prénom</label>
              <input v-model="editForm.prenom" type="text" required>
            </div>
            
            <div class="form-group">
              <label>Email</label>
              <input v-model="editForm.email" type="email" required>
            </div>
            
            <p v-if="error" class="error">{{ error }}</p>
            <p v-if="success" class="success">{{ success }}</p>
            
            <div class="actions">
              <button type="submit" class="btn-primary" :disabled="saving">
                {{ saving ? 'Enregistrement...' : 'Enregistrer' }}
              </button>
              <button type="button" class="btn-secondary" @click="cancelEdit">Annuler</button>
            </div>
          </form>
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
      editForm: {
        nom: '',
        prenom: '',
        email: ''
      }
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
      
      console.log('[PROFILE] Token:', token)
      console.log('[PROFILE] User data:', userData)
      console.log('[PROFILE] User ID:', userData.id)
      
      if (!token || !userData.id) {
        this.error = 'Session expirée, veuillez vous reconnecter'
        this.$router.push('/login')
        return
      }
      
      try {
        const response = await fetch(`http://localhost:6090/auth/users/${userData.id}`, {
          headers: {
            'Authorization': `Bearer ${token}`
          }
        })
        
        if (response.ok) {
          this.user = await response.json()
          this.editForm = {
            nom: this.user.nom,
            prenom: this.user.prenom,
            email: this.user.email
          }
        } else {
          this.error = 'Impossible de charger le profil'
        }
      } catch (err) {
        this.error = 'Erreur de connexion'
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
          body: JSON.stringify(this.editForm)
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
      this.editForm = {
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
      const date = new Date(dateString)
      return date.toLocaleDateString('fr-FR')
    }
  }
}
</script>

<style scoped>
.profile-page {
  padding: 3rem 0;
}

.container {
  max-width: 600px;
  margin: 0 auto;
  padding: 0 1rem;
}

.profile-card {
  background: white;
  padding: 2rem;
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.profile-card h2 {
  margin-bottom: 2rem;
  text-align: center;
  color: #333;
}

.loading {
  text-align: center;
  padding: 2rem;
  color: #666;
}

.profile-info {
  margin-bottom: 1rem;
}

.info-item {
  margin-bottom: 1.5rem;
}

.info-item label {
  display: block;
  font-weight: 600;
  color: #555;
  margin-bottom: 0.5rem;
  font-size: 0.875rem;
  text-transform: uppercase;
}

.info-item p {
  font-size: 1.1rem;
  color: #333;
  padding: 0.5rem 0;
}

.form-group {
  margin-bottom: 1rem;
}

.form-group label {
  display: block;
  margin-bottom: 0.5rem;
  color: #333;
  font-weight: 500;
}

.form-group input {
  width: 100%;
  padding: 0.75rem;
  border: 2px solid #e0e0e0;
  border-radius: 4px;
  font-size: 1rem;
}

.form-group input:focus {
  outline: none;
  border-color: #0d6efd;
}

.actions {
  display: flex;
  gap: 1rem;
  margin-top: 2rem;
}

.btn-primary, .btn-secondary {
  flex: 1;
  padding: 0.75rem;
  border: none;
  border-radius: 4px;
  font-size: 1rem;
  cursor: pointer;
  transition: background 0.2s;
}

.btn-primary {
  background: #0d6efd;
  color: white;
}

.btn-primary:hover {
  background: #0b5ed7;
}

.btn-primary:disabled {
  background: #6c757d;
  cursor: not-allowed;
}

.btn-secondary {
  background: #6c757d;
  color: white;
}

.btn-secondary:hover {
  background: #5a6268;
}

.error {
  margin-top: 1rem;
  padding: 0.75rem;
  background: #f8d7da;
  color: #721c24;
  border-radius: 4px;
  text-align: center;
}

.success {
  margin-top: 1rem;
  padding: 0.75rem;
  background: #d4edda;
  color: #155724;
  border-radius: 4px;
  text-align: center;
}
</style>
