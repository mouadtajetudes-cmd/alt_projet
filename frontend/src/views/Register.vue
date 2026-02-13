<template>
  <div class="register-page">
    <div class="container">
      <div class="register-card">
        <h2>Inscription</h2>
        
        <form @submit.prevent="handleRegister">
          <div class="form-group">
            <label>Nom</label>
            <input v-model="nom" type="text" required placeholder="Votre nom">
          </div>

          <div class="form-group">
            <label>Prénom</label>
            <input v-model="prenom" type="text" required placeholder="Votre prénom">
          </div>
          
          <div class="form-group">
            <label>Email</label>
            <input v-model="email" type="email" required placeholder="votre@email.com">
          </div>
          
          <div class="form-group">
            <label>Mot de passe</label>
            <input v-model="password" type="password" required placeholder="••••••••">
          </div>

          <div class="form-group">
            <label>Confirmer mot de passe</label>
            <input v-model="confirmPassword" type="password" required placeholder="••••••••">
          </div>
          
          <button type="submit" class="btn-primary" :disabled="loading">
            {{ loading ? 'Inscription...' : "S'inscrire" }}
          </button>
          
          <p v-if="error" class="error">{{ error }}</p>
          <p v-if="success" class="success">{{ success }}</p>
          
          <p class="login-link">
            Déjà un compte ? <router-link to="/login">Se connecter</router-link>
          </p>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Register',
  data() {
    return {
      nom: '',
      prenom: '',
      email: '',
      password: '',
      confirmPassword: '',
      error: '',
      success: '',
      loading: false
    }
  },
  methods: {
    async handleRegister() {
      this.error = ''
      this.success = ''
      
      if (this.password.length < 8) {
        this.error = 'Le mot de passe doit contenir au moins 8 caractères'
        return
      }
      
      if (this.password !== this.confirmPassword) {
        this.error = 'Les mots de passe ne correspondent pas'
        return
      }
      
      this.loading = true
      
      try {
        const response = await fetch('http://localhost:6090/auth/auth/register', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({
            nom: this.nom,
            prenom: this.prenom,
            email: this.email,
            password: this.password
          })
        })
        
        const data = await response.json()
        
        if (response.ok) {
          this.success = 'Inscription réussie ! Redirection...'
          setTimeout(() => this.$router.push('/login'), 1500)
        } else {
          this.error = data.message || 'Erreur lors de l\'inscription'
        }
      } catch (err) {
        this.error = 'Impossible de créer le compte'
      } finally {
        this.loading = false
      }
    }
  }
}
</script>

<style scoped>
.register-page {
  padding: 3rem 0;
}

.container {
  max-width: 500px;
  margin: 0 auto;
  padding: 0 1rem;
}

.register-card {
  background: white;
  padding: 2rem;
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.register-card h2 {
  margin-bottom: 1.5rem;
  text-align: center;
  color: #333;
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

.btn-primary {
  width: 100%;
  padding: 0.75rem;
  background: #0d6efd;
  color: white;
  border: none;
  border-radius: 4px;
  font-size: 1rem;
  cursor: pointer;
  transition: background 0.2s;
}

.btn-primary:hover {
  background: #0b5ed7;
}

.btn-primary:disabled {
  background: #6c757d;
  cursor: not-allowed;
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

.login-link {
  margin-top: 1.5rem;
  text-align: center;
  color: #666;
}

.login-link a {
  color: #0d6efd;
  text-decoration: none;
  font-weight: 500;
}

.login-link a:hover {
  text-decoration: underline;
}
</style>
