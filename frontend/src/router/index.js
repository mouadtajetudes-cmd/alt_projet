import { createRouter, createWebHistory } from 'vue-router'
import Home from '../views/Home.vue'
import Chat from '../views/Chat.vue'
import Login from '../views/Login.vue'
import Marketplace from '../views/Marketplace.vue'
import ProductDetail from '../views/ProductDetail.vue'
import CreateProduct from '../views/CreateProduct.vue'
import EditProduct from '../views/EditProduct.vue'
import Categories from '../views/Categories.vue'
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
    path: '/products/create',
    name: 'CreateProduct',
    component: CreateProduct
  },
  {
    path: '/products/:id/edit',
    name: 'EditProduct',
    component: EditProduct
  },
  {
    path: '/products/:id',
    name: 'ProductDetail',
    component: ProductDetail
  },
  {
    path: '/categories',
    name: 'Categories',
    component: Categories
  },
  {
    path: '/cart',
    name: 'Cart',  
    component: () => import('../views/Cart.vue')
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
