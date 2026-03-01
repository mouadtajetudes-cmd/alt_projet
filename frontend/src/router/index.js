import { createRouter, createWebHistory } from 'vue-router'
import authRoutes from './routes/auth'
import chatRoutes from './routes/chat'
import { requireAuth } from '../modules/auth/middleware/auth'

import Home from '../views/Home.vue'
import Profile from '../views/Profile.vue'
import Users from '../views/Users.vue'
import Marketplace from '../views/Marketplace.vue'
import ProductDetail from '../views/ProductDetail.vue'
import Social from '../views/Social.vue'
import Avatar from '../views/Avatar.vue'
import AvatarDetail from '../views/AvatarDetail.vue'
import CreateAvatar from '../views/CreateAvatar.vue'
import EditAvatar from '../views/EditAvatar.vue'
import Levels from '../views/Levels.vue'
import UserAvatar from '../views/UserAvatar.vue'

const routes = [
  {
    path: '/',
    name: 'Home',
    component: Home
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
  },
  {
    path: '/social',
    name: 'Social',
    component: Social,
    beforeEnter: requireAuth
  },
  {
    path: '/avatar',
    name: 'Avatar',
    component: Avatar
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
  },
  ...authRoutes,
  ...chatRoutes
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router
