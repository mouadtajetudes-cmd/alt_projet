import { createRouter, createWebHistory } from 'vue-router'
import Home from '../views/Home.vue'
import Chat from '../views/Chat.vue'
import Login from '../views/Login.vue'
<<<<<<< HEAD
import Marketplace from '../views/Marketplace.vue'
import ProductDetail from '../views/ProductDetail.vue'
import CreateProduct from '../views/CreateProduct.vue'
import EditProduct from '../views/EditProduct.vue'
import Categories from '../views/Categories.vue'
import Social from '../views/Social.vue'
import Avatar from '../views/Avatar.vue'
=======
import Register from '../views/Register.vue'
import Profile from '../views/Profile.vue'
import Users from '../views/Users.vue'
import Friends from '../views/Friends.vue'
import Groups from '../views/Groups.vue'
import GroupChat from '../views/GroupChat.vue'
import Marketplace from '../views/Marketplace.vue'
import ProductDetail from '../views/ProductDetail.vue'
import Social from '../views/Social.vue'
import Avatar from '../views/Avatar.vue'
import AvatarDetail from '../views/AvatarDetail.vue'
import CreateAvatar from '../views/CreateAvatar.vue'
import EditAvatar from '../views/EditAvatar.vue'
import Levels from '../views/Levels.vue'
import UserAvatar from '../views/UserAvatar.vue'
import { requireAuth } from '../middleware/auth'
>>>>>>> origin/groupe/develop

const routes = [
  {
    path: '/',
    name: 'Home',
    component: Home
  },
  {
<<<<<<< HEAD
    path: '/chat',
    name: 'Chat',
    component: Chat
  },
  {
=======
>>>>>>> origin/groupe/develop
    path: '/login',
    name: 'Login',
    component: Login
  },
  {
<<<<<<< HEAD
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
=======
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
  },
  {
    path: '/friends',
    name: 'Friends',
    component: Friends,
    beforeEnter: requireAuth
  },
  {
    path: '/groups',
    name: 'Groups',
    component: Groups,
    beforeEnter: requireAuth
  },
  {
    path: '/groups/:salleId/chat',
    name: 'GroupChat',
    component: GroupChat,
    beforeEnter: requireAuth
  },
  {
    path: '/marketplace',
    name: 'Marketplace',
    component: Marketplace,
    beforeEnter: requireAuth
  },
  {
    path: '/marketplace/:id',
    name: 'ProductDetail',
    component: ProductDetail,
    beforeEnter: requireAuth
>>>>>>> origin/groupe/develop
  },
  {
    path: '/social',
    name: 'Social',
<<<<<<< HEAD
    component: Social
=======
    component: Social,
    beforeEnter: requireAuth
>>>>>>> origin/groupe/develop
  },
  {
    path: '/avatar',
    name: 'Avatar',
    component: Avatar
<<<<<<< HEAD
=======
  },
  {
    path: '/avatar/create',
    name: 'CreateAvatar',
    component: CreateAvatar
  },
  {
    path: '/avatar/:id/edit',
    name: 'EditAvatar',
    component: EditAvatar
  },
  {
    path: '/avatar/:id',
    name: 'AvatarDetail',
    component: AvatarDetail
  },
  {
    path: '/levels',
    name: 'Levels',
    component: Levels
  },
  {
    path: '/user/:id/avatars',
    name: 'UserAvatar',
    component: UserAvatar
>>>>>>> origin/groupe/develop
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router
