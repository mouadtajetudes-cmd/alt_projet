<template>
  <div class="page">
    <div class="card">
      <h2>🔐 Connexion</h2>
      
      <form @submit.prevent="handleLogin">
        <input v-model="email" type="email" required placeholder="Email">
        <input v-model="password" type="password" required placeholder="Mot de passe">
        
        <button type="submit" :disabled="loading">
          {{ loading ? 'Connexion...' : 'Se connecter' }}
        </button>
        
        <p v-if="error" class="error">{{ error }}</p>
        
        <p class="link">
          Pas de compte ? <router-link to="/register">S'inscrire</router-link>
        </p>
      </form>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Login',
  data() {
    return {
      email: '',
      password: '',
      error: '',
      loading: false
    }
  },
  methods: {
    async handleLogin() {
      this.error = ''
      this.loading = true
      
      try {
        const response = await fetch('http://localhost:6090/auth/auth/login', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({
            email: this.email,
            password: this.password
          })
        })
        
        const data = await response.json()
        
        if (response.ok) {
          localStorage.setItem('token', data.token)
          localStorage.setItem('user', JSON.stringify(data.user))
          this.$router.push('/chat')
        } else {
          this.error = data.message || 'Email ou mot de passe incorrect'
        }
      } catch (err) {
        this.error = 'Impossible de se connecter'
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

.link {
  text-align: center;
  color: #666;
  font-size: 0.9rem;
}

.link a {
  color: #2196F3;
  text-decoration: none;
}
</style>
