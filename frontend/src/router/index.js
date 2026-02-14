import { createRouter, createWebHistory } from 'vue-router'
import Home from '../views/Home.vue'
import Chat from '../views/Chat.vue'
import Login from '../views/Login.vue'
import Register from '../views/Register.vue'
import Profile from '../views/Profile.vue'
import Users from '../views/Users.vue'
import Groups from '../views/Groups.vue'
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
  },
  {
    path: '/groups',
    name: 'Groups',
    component: Groups,
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
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router
