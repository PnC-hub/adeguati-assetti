import { getAuthUser } from '../../utils/auth'
import { findOne, update } from '../../utils/db'
import { mapAtecoToSettore } from '../../utils/kpi'

const SETTORE_LABELS: Record<string, string> = {
  '01': 'Agricoltura', '02': 'Silvicoltura', '03': 'Pesca',
  '10': 'Alimentare', '25': 'Metalmeccanica', '41': 'Edilizia',
  '42': 'Ingegneria civile', '43': 'Costruzioni specializzate',
  '45': 'Commercio autoveicoli', '46': 'Commercio ingrosso', '47': 'Commercio dettaglio',
  '49': 'Trasporto terrestre', '55': 'Alloggio', '56': 'Ristorazione',
  '62': 'Software e ICT', '69': 'Studi professionali',
  '86': 'Servizi sanitari', '96': 'Servizi alla persona',
}

function getSettoreLabel(codiceAteco: string | null): string | null {
  if (!codiceAteco) return null
  const prefisso = codiceAteco.substring(0, 2)
  return SETTORE_LABELS[prefisso] || null
}

export default defineEventHandler(async (event) => {
  const user = await getAuthUser(event)

  if (!user) {
    throw createError({
      statusCode: 401,
      message: 'Non autorizzato'
    })
  }

  const id = parseInt(getRouterParam(event, 'id') || '0')
  const body = await readBody(event)

  // Verify ownership
  const azienda = await findOne<any>('aziende', { id, user_id: user.id })

  if (!azienda) {
    throw createError({
      statusCode: 404,
      message: 'Azienda non trovata'
    })
  }

  const updates: Record<string, any> = {}

  if (body.codice_ateco !== undefined) {
    updates.codice_ateco = body.codice_ateco
    // Auto-set settore from codice ATECO
    const settoreLabel = getSettoreLabel(body.codice_ateco)
    if (settoreLabel) {
      updates.settore = settoreLabel
    }
  }

  if (body.nome !== undefined) updates.nome = body.nome
  if (body.partita_iva !== undefined) updates.partita_iva = body.partita_iva
  if (body.dimensione !== undefined) updates.dimensione = body.dimensione

  if (Object.keys(updates).length > 0) {
    updates.updated_at = new Date().toISOString().slice(0, 19).replace('T', ' ')
    await update('aziende', updates, { id })
  }

  const updatedAzienda = await findOne<any>('aziende', { id })

  return {
    success: true,
    data: updatedAzienda
  }
})
