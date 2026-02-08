import { getAuthUser } from '../../utils/auth'
import { findAll } from '../../utils/db'

export default defineEventHandler(async (event) => {
  const user = await getAuthUser(event)

  if (!user) {
    throw createError({
      statusCode: 401,
      message: 'Non autorizzato'
    })
  }

  const aziende = await findAll<any>('aziende', { user_id: user.id, attiva: true })

  return {
    success: true,
    data: aziende.map(a => ({
      id: a.id,
      nome: a.nome,
      settore: a.settore,
      codice_ateco: a.codice_ateco,
      partita_iva: a.partita_iva,
      dimensione: a.dimensione
    }))
  }
})
