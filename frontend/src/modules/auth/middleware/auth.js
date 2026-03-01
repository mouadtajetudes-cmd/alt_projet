export function checkAuth() {
  const token = localStorage.getItem('token')
  return token !== null && token !== undefined && token !== ''
}

export async function validateToken() {
  const token = localStorage.getItem('token')
  
  if (!token) {
    return false
  }
  
  try {
    const response = await fetch('http://localhost:6090/auth/auth/tokens/validate', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${token}`
      }
    })
    
    return response.ok
  } catch (error) {
    console.error('[AUTH] Token validation error:', error)
    return false
  }
}

export function requireAuth(to, from, next) {
  if (checkAuth()) {
    next()
  } else {
    next('/login')
  }
}
