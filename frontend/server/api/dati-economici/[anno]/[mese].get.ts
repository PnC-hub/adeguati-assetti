import { getAuthUser } from '../../../utils/auth'
import { findOne } from '../../../utils/db'

export default defineEventHandler(async (event) => {
  const user = await getAuthUser(event)

  if (!user) {
    throw createError({
      statusCode: 401,
      message: 'Non autorizzato'
    })
  }

  const anno = parseInt(getRouterParam(event, 'anno') || '0')
  const mese = parseInt(getRouterParam(event, 'mese') || '0')

  if (!anno || !mese || mese < 1 || mese > 12) {
    throw createError({
      statusCode: 400,
      message: 'Anno e mese non validi'
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

  const dati = await findOne<any>('dati_economici', {
    azienda_id: azienda.id,
    anno,
    mese
  })

  return {
    success: true,
    data: dati || null
  }
})
