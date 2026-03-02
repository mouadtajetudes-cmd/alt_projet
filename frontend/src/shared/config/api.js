const API_BASE = 'http://localhost:6090'

export const API = {
  AUTH: `${API_BASE}/auth`,
  AVATAR: `${API_BASE}/avatar`,
  CHAT: `${API_BASE}/chat`,
  MARKETPLACE: `${API_BASE}/marketplace`,
  SOCIAL: `${API_BASE}/social`,
  WS: 'ws://localhost:3001'
}

export const ENDPOINTS = {
  AUTH: {
    LOGIN: `${API.AUTH}/auth/login`,
    REGISTER: `${API.AUTH}/auth/register`,
    VALIDATE_TOKEN: `${API.AUTH}/auth/tokens/validate`,
    REFRESH_TOKEN: `${API.AUTH}/auth/tokens/refresh`,
    GOOGLE_OAUTH: `${API.AUTH}/auth/google`,
    APPLE_OAUTH: `${API.AUTH}/auth/apple`,
    USERS: `${API.AUTH}/users`,
    GROUPS: `${API.AUTH}/groupes`
  },
  CHAT: {
    CONVERSATIONS: 'http://localhost:3001/conversations',
    MESSAGES: 'http://localhost:3001'
  },
  AVATAR: {
    LIST: `${API.AVATAR}/avatars`,
    CREATE: `${API.AVATAR}/avatars`,
    DETAIL: (id) => `${API.AVATAR}/avatars/${id}`,
    UPDATE: (id) => `${API.AVATAR}/avatars/${id}`,
    DELETE: (id) => `${API.AVATAR}/avatars/${id}`
  }
}

export default { API, ENDPOINTS }
