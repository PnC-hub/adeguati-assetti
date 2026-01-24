export default defineNuxtRouteMiddleware((to, from) => {
  // Skip on server side
  if (import.meta.server) return

  const token = localStorage.getItem('aa_token')

  if (!token) {
    return navigateTo('/login')
  }
})
