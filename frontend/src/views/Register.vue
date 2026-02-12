<template>
  <div class="page">
    <div class="card">
      <h2>✍️ Inscription</h2>
      
      <form @submit.prevent="handleRegister">
        <input v-model="nom" type="text" required placeholder="Nom">
        <input v-model="prenom" type="text" required placeholder="Prénom">
        <input v-model="email" type="email" required placeholder="Email">
        <input v-model="password" type="password" required placeholder="Mot de passe (8 car. min)">
        <input v-model="confirmPassword" type="password" required placeholder="Confirmer mot de passe">
        
        <button type="submit" :disabled="loading">
          {{ loading ? 'Inscription...' : "S'inscrire" }}
        </button>
        
        <p v-if="error" class="error">{{ error }}</p>
        <p v-if="success" class="success">{{ success }}</p>
        
        <p class="link">
          Déjà un compte ? <router-link to="/login">Se connecter</router-link>
        </p>
      </form>
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
.page {
  min-height: 100vh;
  background: #f5f7fa;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 2rem 1rem;
}

.card {
  background: white;
  padding: 2.5rem;
  border-radius: 12px;
  box-shadow: 0 2px 12px rgba(0,0,0,0.08);
  width: 100%;
  max-width: 400px;
}

.card h2 {
  text-align: center;
  color: #333;
  margin-bottom: 2rem;
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

button {
  padding: 0.75rem;
  background: #2196F3;
  color: white;
  border: none;
  border-radius: 8px;
  font-size: 1rem;
  cursor: pointer;
  font-weight: 500;
}

button:hover {
  background: #1976D2;
}

button:disabled {
  background: #ccc;
  cursor: not-allowed;
}

.error {
  padding: 0.75rem;
  background: #ffebee;
  color: #c62828;
  border-radius: 8px;
  text-align: center;
  font-size: 0.9rem;
}

.success {
  padding: 0.75rem;
  background: #e8f5e9;
  color: #2e7d32;
  border-radius: 8px;
  text-align: center;
  font-size: 0.9rem;
}

.link {
  text-align: center;
  color: #666;
  font-size: 0.9rem;
}

.link a {
  color: #2196F3;
  text-decoration: none;
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
