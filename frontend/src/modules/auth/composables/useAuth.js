import { ref, computed } from "vue";
import { useRouter } from "vue-router";

const user = ref(null);
const token = ref(null);

export function useAuth() {
  const router = useRouter();

  const initAuth = () => {
    const storedUser = localStorage.getItem("user");
    const storedToken = localStorage.getItem("token");

    if (storedUser && storedToken) {
      try {
        user.value = JSON.parse(storedUser);
        token.value = storedToken;
      } catch (e) {
        console.error("[AUTH] Erreur parsing user:", e);
        logout();
      }
    }
  };

  const isAuthenticated = computed(() => {
    return !!token.value && !!user.value;
  });

  const isAdmin = computed(() => {
    if (!user.value) return false;
    console.log(
      "[AUTH] Vérification isAdmin - user.value.administrateur:",
      user.value.administrateur,
      "type:",
      typeof user.value.administrateur,
    );
    const result =
      user.value.administrateur === true ||
      user.value.administrateur === "true" ||
      user.value.administrateur === "1" ||
      user.value.administrateur === 1 ||
      user.value.role === "admin" ||
      user.value.isAdmin === true ||
      user.value.is_admin === true;
    console.log("[AUTH] isAdmin result:", result);
    return result;
  });

  const login = (userData, authToken) => {
    user.value = userData;
    token.value = authToken;
    localStorage.setItem("user", JSON.stringify(userData));
    localStorage.setItem("token", authToken);
  };

  const logout = () => {
    user.value = null;
    token.value = null;
    localStorage.removeItem("user");
    localStorage.removeItem("token");
    router.push("/login");
  };

  const getUserId = () => {
    return user.value?.id || user.value?.id_utilisateur || null;
  };

  const getAuthHeader = () => {
    return token.value ? { Authorization: `Bearer ${token.value}` } : {};
  };

  return {
    user: computed(() => user.value),
    token: computed(() => token.value),
    isAuthenticated,
    isAdmin,
    login,
    logout,
    initAuth,
    getUserId,
    getAuthHeader,
  };
}
