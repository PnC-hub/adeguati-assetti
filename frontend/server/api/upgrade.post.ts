import { getAuthUser } from '../utils/auth'
import { findOne, update, query, PREFIX } from '../utils/db'

export default defineEventHandler(async (event) => {
  const user = await getAuthUser(event)

  if (!user) {
    throw createError({
      statusCode: 401,
      message: 'Non autorizzato'
    })
  }

  const body = await readBody(event)
  const { piano, billing = 'monthly' } = body

  if (!piano || !['pro', 'studio'].includes(piano)) {
    throw createError({
      statusCode: 400,
      message: 'Piano non valido'
    })
  }

  const pianoData = await findOne<any>('piani', { codice: piano })

  if (!pianoData || !pianoData.stripe_price_id) {
    throw createError({
      statusCode: 400,
      message: 'Piano non disponibile per l\'acquisto'
    })
  }

  try {
    const Stripe = (await import('stripe')).default
    const stripe = new Stripe(process.env.STRIPE_SECRET_KEY || '')

    // Create or retrieve Stripe Customer
    let customerId = user.stripe_customer_id

    if (!customerId) {
      const customer = await stripe.customers.create({
        email: user.email,
        name: `${user.nome || ''} ${user.cognome || ''}`.trim(),
        metadata: {
          aa_user_id: String(user.id)
        }
      })
      customerId = customer.id

      await update('users', {
        stripe_customer_id: customerId,
        updated_at: new Date().toISOString().slice(0, 19).replace('T', ' ')
      }, { id: user.id })
    }

    // Create Checkout Session
    const appUrl = process.env.APP_URL || 'https://adeguatiassettiimpresa.it'
    const priceId = pianoData.stripe_price_id

    const session = await stripe.checkout.sessions.create({
      customer: customerId,
      mode: 'subscription',
      line_items: [{
        price: priceId,
        quantity: 1
      }],
      metadata: {
        piano,
        user_id: String(user.id)
      },
      subscription_data: {
        metadata: {
          piano,
          user_id: String(user.id)
        }
      },
      success_url: `${appUrl}/dashboard/account?upgrade=success`,
      cancel_url: `${appUrl}/dashboard/account?upgrade=cancelled`,
      allow_promotion_codes: true
    })

    return {
      success: true,
      data: {
        checkout_url: session.url,
        session_id: session.id
      }
    }
  } catch (error: any) {
    console.error('Stripe checkout error:', error)
    throw createError({
      statusCode: 500,
      message: 'Errore durante la creazione del checkout: ' + error.message
    })
  }
})
