import { update, query, PREFIX } from '../../utils/db'

export default defineEventHandler(async (event) => {
  const body = await readRawBody(event)
  const sig = getHeader(event, 'stripe-signature')

  const webhookSecret = process.env.STRIPE_WEBHOOK_SECRET

  let stripeEvent: any

  try {
    const Stripe = (await import('stripe')).default
    const stripe = new Stripe(process.env.STRIPE_SECRET_KEY || '')

    if (webhookSecret && sig) {
      stripeEvent = stripe.webhooks.constructEvent(body || '', sig, webhookSecret)
    } else {
      // Development mode - parse directly
      stripeEvent = JSON.parse(body || '{}')
    }
  } catch (err: any) {
    console.error('Webhook signature verification failed:', err.message)
    throw createError({
      statusCode: 400,
      message: 'Webhook Error: ' + err.message
    })
  }

  const now = new Date().toISOString().slice(0, 19).replace('T', ' ')

  switch (stripeEvent.type) {
    case 'checkout.session.completed': {
      const session = stripeEvent.data.object
      const userId = session.metadata?.user_id
      const piano = session.metadata?.piano

      if (userId && piano) {
        await update('users', {
          piano,
          stripe_subscription_id: session.subscription,
          piano_attivo_dal: now,
          updated_at: now
        }, { id: parseInt(userId) })

        console.log(`User ${userId} upgraded to ${piano}`)
      }
      break
    }

    case 'customer.subscription.deleted': {
      const subscription = stripeEvent.data.object

      // Find user by subscription ID
      const [users] = await query<any[]>(
        `SELECT * FROM ${PREFIX}users WHERE stripe_subscription_id = ? LIMIT 1`,
        [subscription.id]
      )

      if (users && users.length > 0) {
        const user = users[0]
        await update('users', {
          piano: 'free',
          stripe_subscription_id: null,
          piano_scade_il: now,
          updated_at: now
        }, { id: user.id })

        console.log(`User ${user.id} subscription cancelled`)
      }
      break
    }

    case 'invoice.payment_failed': {
      const invoice = stripeEvent.data.object
      console.log(`Payment failed for invoice ${invoice.id}`)
      // TODO: Send email notification
      break
    }

    case 'customer.subscription.updated': {
      const subscription = stripeEvent.data.object

      // Find user by subscription ID
      const [users] = await query<any[]>(
        `SELECT * FROM ${PREFIX}users WHERE stripe_subscription_id = ? LIMIT 1`,
        [subscription.id]
      )

      if (users && users.length > 0) {
        const user = users[0]
        const newPiano = subscription.metadata?.piano

        if (newPiano && newPiano !== user.piano) {
          await update('users', {
            piano: newPiano,
            updated_at: now
          }, { id: user.id })

          console.log(`User ${user.id} plan changed to ${newPiano}`)
        }
      }
      break
    }

    default:
      console.log(`Unhandled event type: ${stripeEvent.type}`)
  }

  return { received: true }
})
