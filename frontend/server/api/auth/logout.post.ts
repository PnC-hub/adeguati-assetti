import { deleteToken } from '../../utils/auth'

export default defineEventHandler(async (event) => {
  const authHeader = getHeader(event, 'Authorization')

  if (authHeader && authHeader.startsWith('Bearer ')) {
    const token = authHeader.substring(7)
    await deleteToken(token)
  }

  return {
    success: true,
    message: 'Logout effettuato'
  }
})
