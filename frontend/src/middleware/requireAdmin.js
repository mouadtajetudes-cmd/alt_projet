import { useAuth } from "../composables/useAuth";

export function requireAdmin(to, from, next) {
  const { isAuthenticated, isAdmin, initAuth, user } = useAuth();

  initAuth();
  
  console.log('[MIDDLEWARE] User:', user.value)
  console.log('[MIDDLEWARE] isAuthenticated:', isAuthenticated.value)
  console.log('[MIDDLEWARE] isAdmin:', isAdmin.value)

  if (!isAuthenticated.value) {
    console.log("[MIDDLEWARE] Accès refusé : utilisateur non connecté");
    next({
      path: "/login",
      query: {
        redirect: to.fullPath,
        error: "login-required",
      },
    });
    return;
  }

  if (!isAdmin.value) {
    console.log("[MIDDLEWARE] Accès refusé : droits administrateur requis");
    next({
      path: "/avatar",
      query: {
        error: "admin-required",
      },
    });
    return;
  }

  console.log("[MIDDLEWARE] Accès autorisé : admin vérifié");
  next();
}
