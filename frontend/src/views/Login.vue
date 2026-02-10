<template>
  <div class="login-page">
    <div class="container">
      <div class="login-card">
        <h2>Connexion</h2>
        
        <form @submit.prevent="handleLogin">
          <div class="form-group">
            <label>Email</label>
            <input v-model="email" type="email" required placeholder="votre@email.com">
          </div>
          
          <div class="form-group">
            <label>Mot de passe</label>
            <input v-model="password" type="password" required placeholder="••••••••">
          </div>
          
          <button type="submit" class="btn-primary" :disabled="loading">
            {{ loading ? 'Connexion...' : 'Se connecter' }}
          </button>
          
          <p v-if="error" class="error">{{ error }}</p>
          
          <p class="register-link">
            Pas de compte ? <router-link to="/register">S'inscrire</router-link>
          </p>
        </form>
      </div>
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
        console.log('[LOGIN] Response data:', data)
        
        if (response.ok) {
          console.log('[LOGIN] Token:', data.token)
          console.log('[LOGIN] User:', data.user)
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
.login-page {
  padding: 3rem 0;
}

.container {
  max-width: 500px;
  margin: 0 auto;
  padding: 0 1rem;
}

.login-card {
  background: white;
  padding: 2rem;
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.login-card h2 {
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

.register-link {
  margin-top: 1.5rem;
  text-align: center;
  color: #666;
}

.register-link a {
  color: #0d6efd;
  text-decoration: none;
  font-weight: 500;
}

.register-link a:hover {
  text-decoration: underline;
}
</style>
