import { createRouter, createWebHistory } from 'vue-router'
import Home from '../views/Home.vue'
import Chat from '../views/Chat.vue'
import Login from '../views/Login.vue'
import Register from '../views/Register.vue'
import Profile from '../views/Profile.vue'
import Users from '../views/Users.vue'
import { requireAuth } from '../middleware/auth'

const routes = [
  {
    path: '/',
    name: 'Home',
    component: Home
  },
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
    path: '/profile',
    name: 'Profile',
    component: Profile,
    beforeEnter: requireAuth
  },
  {
    path: '/users',
    name: 'Users',
    component: Users,
    beforeEnter: requireAuth
  },
  {
    path: '/chat',
    name: 'Chat',
    component: Chat,
    beforeEnter: requireAuth
  }
  // ,
  // {
  //   path: '/marketplace',
  //   name: 'Marketplace',
  //   component: Marketplace
  // },
  // {
  //   path: '/social',
  //   name: 'Social',
  //   component: Social
  // },
  // {
  //   path: '/avatar',
  //   name: 'Avatar',
  //   component: Avatar
  // }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router
