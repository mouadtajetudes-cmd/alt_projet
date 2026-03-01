import Login from '../../modules/auth/views/Login.vue'
import Register from '../../modules/auth/views/Register.vue'
import GoogleCallback from '../../modules/auth/views/GoogleCallback.vue'

export default [
  {
    path: '/login',
    name: 'Login',
    component: Login
  },
  {
    path: '/register',
    name: 'Register',
    component: Register
  },
  {
    path: '/auth/google/callback',
    name: 'GoogleCallback',
    component: GoogleCallback
  }
]
