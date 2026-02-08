import { findAll } from '../utils/db'

export default defineEventHandler(async (event) => {
  const piani = await findAll<any>('piani', { attivo: true })

  // Sort by prezzo_mensile
  piani.sort((a, b) => (a.prezzo_mensile || 0) - (b.prezzo_mensile || 0))

  return {
    success: true,
    data: piani
  }
})
