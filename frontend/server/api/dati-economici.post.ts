import { getAuthUser } from '../utils/auth'
import { findOne, insert, update, query, PREFIX } from '../utils/db'

export default defineEventHandler(async (event) => {
  const user = await getAuthUser(event)

  if (!user) {
    throw createError({
      statusCode: 401,
      message: 'Non autorizzato'
    })
  }

  const body = await readBody(event)
  const { anno, mese, ...datiFinanziari } = body

  if (!anno || !mese) {
    throw createError({
      statusCode: 400,
      message: 'Anno e mese sono obbligatori'
    })
  }

  // Get user's first active azienda
  const azienda = await findOne<any>('aziende', { user_id: user.id, attiva: true })

  if (!azienda) {
    throw createError({
      statusCode: 404,
      message: 'Nessuna azienda trovata'
    })
  }

  // Check if record exists
  const existing = await findOne<any>('dati_economici', {
    azienda_id: azienda.id,
    anno,
    mese
  })

  const now = new Date().toISOString().slice(0, 19).replace('T', ' ')

  if (existing) {
    // Update
    await update('dati_economici', {
      ...datiFinanziari,
      updated_at: now
    }, { id: existing.id })
  } else {
    // Insert
    await insert('dati_economici', {
      azienda_id: azienda.id,
      anno,
      mese,
      ...datiFinanziari,
      created_at: now,
      updated_at: now
    })
  }

  return {
    success: true,
    message: 'Dati salvati con successo'
  }
})
