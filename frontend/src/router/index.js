import { createRouter, createWebHistory } from "vue-router";
import Home from "../views/Home.vue";
import Chat from "../views/Chat.vue";
import Login from "../views/Login.vue";
import Marketplace from "../views/Marketplace.vue";
import Social from "../views/Social.vue";
import Avatar from "../views/Avatar.vue";
import AvatarDetail from "../views/AvatarDetail.vue";
import CreateAvatar from "../views/CreateAvatar.vue";
import { useAuth } from "../composables/useAuth";

const routes = [
  {
    path: "/",
    name: "Home",
    component: Home,
  },
  {
    path: "/chat",
    name: "Chat",
    component: Chat,
  },
  // ,
  // {
  //   path: '/login',
  //   name: 'Login',
  //   component: Login
  // },
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
    path: "/avatar",
    name: "Avatar",
    component: Avatar,
  },
  {
    path: "/avatar/create",
    name: "CreateAvatar",
    component: CreateAvatar,
    meta: { requiresAdmin: true },
  },
  {
    path: "/avatar/:id",
    name: "AvatarDetail",
    component: AvatarDetail,
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach((to, from, next) => {
  const { isAdmin, isAuthenticated, initAuth } = useAuth();

  initAuth();

  if (to.meta.requiresAdmin) {
    if (!isAuthenticated.value) {
      next({ name: "Home", query: { error: "login-required" } });
    } else if (!isAdmin.value) {
      next({ name: "Avatar", query: { error: "admin-required" } });
    } else {
      next();
    }
  } else {
    next();
  }
});

export default router;
