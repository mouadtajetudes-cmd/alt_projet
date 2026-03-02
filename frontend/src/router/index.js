import { createRouter, createWebHistory } from 'vue-router'
import Home from '../views/Home.vue'
import Chat from '../views/Chat.vue'
import Login from '../views/Login.vue'
import Marketplace from '../views/Marketplace.vue'
import Social from '../views/Social.vue'
import Avatar from '../views/Avatar.vue'
import AjoutPost from '../views/social/AjoutPost.vue'
import Reaction from '../views/social/Reaction.vue'
import Comment from '../views/social/Comment.vue'
import AjoutComment from '../views/social/AjoutComment.vue'
import Like from '../views/social/Like.vue'

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
  }
  ,
  {
    path: '/social',
    name: 'Social',
    component: Social
  },
  {
    path: '/post/new',
    name: 'NewPost',
    component:AjoutPost
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
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router
