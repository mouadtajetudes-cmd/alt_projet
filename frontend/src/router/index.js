import { createRouter, createWebHistory } from 'vue-router'
import Home from '../views/Home.vue'
import Chat from '../views/Chat.vue'
import Login from '../views/Login.vue'
import Register from '../views/Register.vue'

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
    path: '/chat',
    name: 'Chat',
    component: Chat
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
