import { createRouter, createWebHistory } from 'vue-router'
import Home from '../views/Home.vue'
import Chat from '../views/Chat.vue'
import Login from '../views/Login.vue'
import Register from '../views/Register.vue'
import Profile from '../views/Profile.vue'
import Users from '../views/Users.vue'
import Groups from '../views/Groups.vue'
import Marketplace from '../views/Marketplace.vue'
import Social from '../views/Social.vue'
import Avatar from '../views/Avatar.vue'
import AvatarDetail from '../views/AvatarDetail.vue'
import CreateAvatar from '../views/CreateAvatar.vue'
import EditAvatar from '../views/EditAvatar.vue'
import Levels from '../views/Levels.vue'
import UserAvatar from '../views/UserAvatar.vue'
import { requireAuth } from '../middleware/auth'
import { requireAdmin } from '../middleware/requireAdmin'

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
  {
    path: '/avatar',
    name: 'Avatar',
    component: Avatar,
    beforeEnter: requireAuth
  },
  {
    path: '/avatar/create',
    name: 'CreateAvatar',
    component: CreateAvatar,
    beforeEnter: requireAdmin
  },
  {
    path: '/avatar/:id/edit',
    name: 'EditAvatar',
    component: EditAvatar,
    beforeEnter: requireAuth
  },
  {
    path: '/avatar/:id',
    name: 'AvatarDetail',
    component: AvatarDetail,
    beforeEnter: requireAuth
  },
  {
    path: '/levels',
    name: 'Levels',
    component: Levels,
    beforeEnter: requireAuth
  },
  {
    path: '/my-avatars',
    name: 'UserAvatar',
    component: UserAvatar,
    beforeEnter: requireAuth
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router
