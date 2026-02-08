import { getAuthUser, getUserPlan, isTrialActive, getTrialDaysLeft } from '../utils/auth'
import { findOne, findAll } from '../utils/db'

export default defineEventHandler(async (event) => {
  const user = await getAuthUser(event)

  if (!user) {
    throw createError({
      statusCode: 401,
      message: 'Non autorizzato'
    })
  }

  const pianoEffettivo = getUserPlan(user)
  const pianoData = await findOne<any>('piani', { codice: pianoEffettivo })
  const aziende = await findAll<any>('aziende', { user_id: user.id, attiva: true })

  const trialActive = isTrialActive(user)
  const trialDaysLeft = getTrialDaysLeft(user)

  let features: string[] = []
  if (pianoData && pianoData.features) {
    try {
      features = typeof pianoData.features === 'string' ? JSON.parse(pianoData.features) : pianoData.features
    } catch (e) {
      features = []
    }
  }

  return {
    success: true,
    data: {
      user: {
        id: user.id,
        nome: user.nome,
        cognome: user.cognome,
        email: user.email
      },
      piano: {
        codice: user.piano || 'free',
        piano_effettivo: pianoEffettivo,
        nome: pianoData?.nome || 'Free',
        prezzo_mensile: pianoData?.prezzo_mensile || 0,
        max_aziende: pianoData?.max_aziende || 1,
        features
      },
      trial: {
        active: trialActive,
        days_left: trialDaysLeft,
        ends_at: user.trial_ends_at
      },
      usage: {
        aziende_count: aziende.length,
        aziende_limit: pianoData?.max_aziende || 1
      },
      stripe: {
        has_subscription: !!user.stripe_subscription_id,
        customer_id: user.stripe_customer_id
      }
    }
  }
})
