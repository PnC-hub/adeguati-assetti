import { getAuthUser } from '../../utils/auth'
import { findAll } from '../../utils/db'

export default defineEventHandler(async (event) => {
  const user = await getAuthUser(event)

  if (!user) {
    throw createError({
      statusCode: 401,
      message: 'Token non valido o mancante'
    })
  }

  // Get user's aziende
  const aziende = await findAll<any>('aziende', { user_id: user.id })

  return {
    success: true,
    data: {
      user: {
        id: user.id,
        nome: user.nome,
        cognome: user.cognome,
        email: user.email,
        piano: user.piano,
        trial_ends_at: user.trial_ends_at
      },
      aziende
    }
  }
})
