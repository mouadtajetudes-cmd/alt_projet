<template>
  <div class="page">
    <div class="container">
      <div class="header">
        <h2>👥 Utilisateurs</h2>
        <div>
          <router-link to="/chat" class="btn">Chat</router-link>
          <router-link to="/profile" class="btn">Profil</router-link>
          <button v-if="isAdmin" @click="showModal = true" class="btn-primary">+ Créer</button>
        </div>
      </div>

      <input v-model="search" placeholder="Rechercher..." @input="filterUsers">

      <div v-if="loading">Chargement...</div>

      <div v-else class="table">
        <table>
          <thead>
            <tr>
              <th>Nom</th>
              <th>Prénom</th>
              <th v-if="isAdmin">Email</th>
              <th v-if="isAdmin">Admin</th>
              <th v-if="isAdmin">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="user in filtered" :key="user.id_utilisateur">
              <td>{{ user.nom }}</td>
              <td>{{ user.prenom }}</td>
              <td v-if="isAdmin">{{ user.email }}</td>
              <td v-if="isAdmin">
                <span :class="user.administrateur === 'true' ? 'badge-admin' : 'badge-user'">
                  {{ user.administrateur === 'true' ? 'Admin' : 'User' }}
                </span>
              </td>
              <td v-if="isAdmin">
                <button @click="editUser(user)" class="btn-sm">✏️</button>
                <button @click="deleteUser(user.id_utilisateur)" class="btn-sm">🗑️</button>
              </td>
            </tr>
          </tbody>
        </table>
        <p v-if="filtered.length === 0">Aucun utilisateur</p>
      </div>

      <div v-if="showModal" class="modal" @click.self="showModal = false">
        <div class="modal-content">
          <h3>{{ editing ? 'Modifier' : 'Créer' }} Utilisateur</h3>
          <form @submit.prevent="saveUser">
            <input v-model="form.nom" placeholder="Nom" required>
            <input v-model="form.prenom" placeholder="Prénom" required>
            <input v-model="form.email" type="email" placeholder="Email" required>
            <input v-if="!editing" v-model="form.password" type="password" placeholder="Mot de passe" required>
            <input v-model="form.telephone" placeholder="Téléphone">
            <label><input v-model="form.administrateur" type="checkbox"> Admin</label>
            <label><input v-model="form.premium" type="checkbox"> Premium</label>
            <button type="submit" class="btn-primary">{{ editing ? 'Modifier' : 'Créer' }}</button>
            <button type="button" @click="showModal = false" class="btn">Annuler</button>
            <p v-if="error" class="error">{{ error }}</p>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Users',
  data() {
    return {
      users: [],
      filtered: [],
      search: '',
      loading: true,
      isAdmin: false,
      showModal: false,
      editing: false,
      error: '',
      form: {
        nom: '',
        prenom: '',
        email: '',
        telephone: '',
        password: '',
        administrateur: false,
        premium: false
      },
      editId: null
    }
  },
  mounted() {
    const user = JSON.parse(localStorage.getItem('user') || '{}')
    this.isAdmin = (user.administrateur === 'true')
    this.loadUsers()
  },
  methods: {
    async loadUsers() {
      this.loading = true
      const token = localStorage.getItem('token')
      const endpoint = this.isAdmin ? 'http://localhost:6090/auth/ausers' : 'http://localhost:6090/auth/users'
      
      try {
        const response = await fetch(endpoint, {
          headers: { 'Authorization': `Bearer ${token}` }
        })
        if (response.ok) {
          this.users = await response.json()
          this.filtered = this.users
        }
      } catch (err) {
        console.error(err)
      } finally {
        this.loading = false
      }
    },

    filterUsers() {
      const q = this.search.toLowerCase()
      this.filtered = this.users.filter(u => 
        u.nom.toLowerCase().includes(q) || 
        u.prenom.toLowerCase().includes(q) ||
        (u.email && u.email.toLowerCase().includes(q))
      )
    },

    editUser(user) {
      this.editing = true
      this.editId = user.id_utilisateur
      this.form = {
        nom: user.nom,
        prenom: user.prenom,
        email: user.email,
        telephone: user.telephone || '',
        administrateur: user.administrateur === 'true',
        premium: user.premium === 'true'
      }
      this.showModal = true
    },

    async saveUser() {
      this.error = ''
      const token = localStorage.getItem('token')
      
      try {
        let url = 'http://localhost:6090/auth/users'
        let method = 'POST'
        let body = { ...this.form }

        if (this.editing) {
          url = `${url}/${this.editId}`
          method = 'PUT'
          delete body.password
        }

        const response = await fetch(url, {
          method,
          headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`
          },
          body: JSON.stringify(body)
        })

        if (response.ok) {
          this.showModal = false
          this.loadUsers()
        } else {
          const data = await response.json()
          this.error = data.message || 'Erreur'
        }
      } catch (err) {
        this.error = 'Erreur de connexion'
      }
    },

    async deleteUser(id) {
      if (!confirm('Supprimer cet utilisateur ?')) return

      const token = localStorage.getItem('token')
      
      try {
        const response = await fetch(`http://localhost:6090/auth/users/${id}`, {
          method: 'DELETE',
          headers: { 'Authorization': `Bearer ${token}` }
        })

        if (response.ok) this.loadUsers()
      } catch (err) {
        console.error(err)
      }
    }
  }
}
</script>

<style scoped>
.page {
  min-height: 100vh;
  background: #f5f7fa;
  padding: 2rem 1rem;
}

.container {
  max-width: 1100px;
  margin: 0 auto;
}

.header {
  background: white;
  padding: 1.5rem;
  border-radius: 12px 12px 0 0;
  box-shadow: 0 2px 8px rgba(0,0,0,0.08);
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.header h2 {
  color: #333;
  margin: 0;
}

.btn {
  padding: 0.5rem 1rem;
  background: #f5f7fa;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  text-decoration: none;
  color: #333;
  margin-left: 0.5rem;
}

.btn:hover {
  background: #e0e0e0;
}

.btn-primary {
  padding: 0.5rem 1rem;
  background: #2196F3;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  color: white;
  margin-left: 0.5rem;
}

.btn-primary:hover {
  background: #1976D2;
}

input[type="text"], input[type="email"], input[type="password"] {
  width: 100%;
  padding: 0.75rem;
  border: 2px solid #e0e0e0;
  border-radius: 8px;
  margin: 0.5rem 0;
}

.table {
  background: white;
  padding: 1.5rem;
  border-radius: 0 0 12px 12px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.08);
  overflow-x: auto;
}

table {
  width: 100%;
  border-collapse: collapse;
}

th {
  text-align: left;
  padding: 0.75rem;
  border-bottom: 2px solid #e0e0e0;
  color: #666;
  font-weight: 600;
}

td {
  padding: 0.75rem;
  border-bottom: 1px solid #f0f0f0;
}

.badge-admin {
  background: #FF6B35;
  color: white;
  padding: 0.25rem 0.75rem;
  border-radius: 12px;
  font-size: 0.85rem;
}

.badge-user {
  background: #e0e0e0;
  color: #666;
  padding: 0.25rem 0.75rem;
  border-radius: 12px;
  font-size: 0.85rem;
}

.btn-sm {
  background: none;
  border: none;
  cursor: pointer;
  font-size: 1.2rem;
  margin: 0 0.25rem;
}

.modal {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0,0,0,0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal-content {
  background: white;
  padding: 2rem;
  border-radius: 12px;
  width: 90%;
  max-width: 500px;
}

.modal-content h3 {
  margin-bottom: 1rem;
}

.modal-content form {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.modal-content label {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.9rem;
}

.error {
  padding: 0.75rem;
  background: #ffebee;
  color: #c62828;
  border-radius: 8px;
  font-size: 0.9rem;
}
</style>
