import crypto from 'crypto'
import { findOne, query, PREFIX } from '../../utils/db'
import { createToken } from '../../utils/auth'

export default defineEventHandler(async (event) => {
  const body = await readBody(event)
  const { email, password } = body

  if (!email || !password) {
    throw createError({
      statusCode: 400,
      message: 'Email e password sono obbligatori'
    })
  }

  // Find user
  const user = await findOne<any>('users', { email })

  if (!user) {
    throw createError({
      statusCode: 401,
      message: 'Credenziali non valide'
    })
  }

  // Verify password (bcrypt hash)
  const bcrypt = await import('bcryptjs')
  const validPassword = await bcrypt.compare(password, user.password)

  if (!validPassword) {
    throw createError({
      statusCode: 401,
      message: 'Credenziali non valide'
    })
  }

  // Generate token
  const token = await createToken(user.id)

  // Get user's first azienda
  const azienda = await findOne<any>('aziende', { user_id: user.id, attiva: true })

  return {
    success: true,
    data: {
      token,
      user: {
        id: user.id,
        nome: user.nome,
        cognome: user.cognome,
        email: user.email,
        piano: user.piano,
        azienda_id: azienda?.id || null,
        azienda_nome: azienda?.nome || null
      }
    }
  }
})
