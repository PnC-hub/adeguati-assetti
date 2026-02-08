import bcrypt from 'bcryptjs'
import { findOne, insert, query, PREFIX } from '../../utils/db'
import { createToken } from '../../utils/auth'

export default defineEventHandler(async (event) => {
  const body = await readBody(event)
  const { nome, cognome, azienda, email, password, password_confirmation } = body

  // Validation
  if (!nome || !cognome || !azienda || !email || !password) {
    throw createError({
      statusCode: 400,
      message: 'Tutti i campi sono obbligatori'
    })
  }

  if (password.length < 8) {
    throw createError({
      statusCode: 400,
      message: 'La password deve essere di almeno 8 caratteri'
    })
  }

  if (password !== password_confirmation) {
    throw createError({
      statusCode: 400,
      message: 'Le password non coincidono'
    })
  }

  // Check if email exists
  const existingUser = await findOne<any>('users', { email })
  if (existingUser) {
    throw createError({
      statusCode: 400,
      message: 'Email già registrata'
    })
  }

  try {
    // Hash password
    const hashedPassword = await bcrypt.hash(password, 10)

    // Trial ends in 14 days
    const trialEnds = new Date()
    trialEnds.setDate(trialEnds.getDate() + 14)
    const trialEndsStr = trialEnds.toISOString().slice(0, 19).replace('T', ' ')

    // Create user
    const userId = await insert('users', {
      nome,
      cognome,
      email,
      password: hashedPassword,
      piano: 'trial',
      trial_ends_at: trialEndsStr,
      created_at: new Date().toISOString().slice(0, 19).replace('T', ' '),
      updated_at: new Date().toISOString().slice(0, 19).replace('T', ' ')
    })

    // Create default company
    await insert('aziende', {
      user_id: userId,
      nome: azienda,
      dimensione: 'micro',
      attiva: true,
      created_at: new Date().toISOString().slice(0, 19).replace('T', ' '),
      updated_at: new Date().toISOString().slice(0, 19).replace('T', ' ')
    })

    // Generate token
    const token = await createToken(userId)

    // Get user
    const user = await findOne<any>('users', { id: userId })

    return {
      success: true,
      data: {
        token,
        user: {
          id: user.id,
          nome: user.nome,
          cognome: user.cognome,
          email: user.email,
          piano: user.piano
        }
      }
    }
  } catch (error: any) {
    console.error('Registration error:', error)
    throw createError({
      statusCode: 500,
      message: 'Errore durante la registrazione: ' + error.message
    })
  }
})
