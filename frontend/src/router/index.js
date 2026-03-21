import { createRouter, createWebHistory } from 'vue-router'

import Chat from '../views/Chat.vue'
import Login from '../views/Login.vue'
import AjoutPost from '../views/social/AjoutPost.vue'
import Reaction from '../views/social/Reaction.vue'
import Comment from '../views/social/Comment.vue'
import AjoutComment from '../views/social/AjoutComment.vue'
import Like from '../views/social/Like.vue'
import authRoutes from './routes/auth'
import chatRoutes from './routes/chat'
import { requireAuth } from '../modules/auth/middleware/auth'

import ProductDetail from '../views/ProductDetail.vue'
import Avatar from '../views/Avatar.vue'
import AvatarDetail from '../views/AvatarDetail.vue'
import CreateAvatar from '../views/CreateAvatar.vue'
import EditAvatar from '../views/EditAvatar.vue'
import Levels from '../views/Levels.vue'
import UserAvatar from '../views/UserAvatar.vue'
import Home from '../views/Home.vue'
import Social from '../views/Social.vue'
import Marketplace from '../views/Marketplace.vue'
import UserPosts from '../views/social/UserPosts.vue'
import ListFollowers from '../views/social/ListFollowers.vue'
import UpdatePost from '../views/social/UpdatePost.vue'

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
  }
  ,
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
    path:'/social',
    name:'social',
    component: Social
  }
  ,
  {
    path: '/post/new',
    name: 'NewPost',
    component:AjoutPost
  },
  {
    path: '/marketplace/:id',
    name: 'ProductDetail',
    component: ProductDetail,
    beforeEnter: requireAuth
  },
  {
    path: '/avatar',
    name: 'Avatar',
    component: Avatar
  },
  {
    path:'/Reaction',
    name:'Like',
    component:Reaction
  }
  ,
  {
    path:'/updatePost/:id',
    name:'UpdatePost',
    component: UpdatePost
  },
  {
    path:'/comment',
    name:'commantaire',
    component: Comment
  },
  {
    path:'/ajoutComment',
    name:'ajoutCommentaire',
    component: AjoutComment
  },
  {
    path:'/like',
    name:'like',
    component:Like
  },
  {
    path: '/posts/user/:id',
    name: 'UserPosts',
    component: UserPosts
  },
  {
    path: '/followers',
    name: 'Followers',
    component: ListFollowers
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
