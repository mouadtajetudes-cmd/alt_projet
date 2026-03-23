import Chat from '../../modules/chat/views/Chat.vue'
import Groups from '../../modules/chat/views/Groups.vue'
import GroupChat from '../../modules/chat/views/GroupChat.vue'
import Friends from '../../modules/chat/views/Friends.vue'
import { requireAuth } from '../../modules/auth/middleware/auth'

export default [
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
  }
]
