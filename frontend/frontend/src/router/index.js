import { createRouter, createWebHistory } from 'vue-router'
import Home from '../views/Home.vue'
import Chat from '../views/Chat.vue'
import Login from '../views/Login.vue'
import Marketplace from '../views/Marketplace.vue'
import ProductDetail from '../views/ProductDetail.vue'
import Social from '../views/Social.vue'
import Avatar from '../views/Avatar.vue'

const routes = [
  {
    path: '/',
    name: 'Home',
    component: Home
  },
  {
    path: '/chat',
    name: 'Chat',
    component: Chat
  },
  {
    path: '/login',
    name: 'Login',
    component: Login
  },
  {
    path: '/marketplace',
    name: 'Marketplace',
    component: Marketplace
  },
  {
    path: '/products/:id',
    name: 'ProductDetail',
    component: ProductDetail
  },
  {
    path: '/social',
    name: 'Social',
    component: Social
  },
  {
    path: '/avatar',
    name: 'Avatar',
    component: Avatar
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router
