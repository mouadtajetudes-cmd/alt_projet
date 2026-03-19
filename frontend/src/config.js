export const API_GATEWAY = import.meta.env.VITE_API_GATEWAY || 'http://localhost:6090'
export const WS_URL = import.meta.env.VITE_WS_URL || 'ws://localhost:3001'

export const API_ENDPOINTS = {
  auth: `${API_GATEWAY}/auth`,
  social: `${API_GATEWAY}/social`,
  marketplace: `${API_GATEWAY}/marketplace`,
  avatar: `${API_GATEWAY}/avatar`,
  chat: `${API_GATEWAY}/chat`,
}

export const MEDIA_BASE = {
  social: `${API_GATEWAY}/social/uploads/images/`,
  marketplace: `${API_GATEWAY}/marketplace/uploads/`,
}
