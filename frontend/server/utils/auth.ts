import { H3Event } from 'h3'
import crypto from 'crypto'
import { findOne, query, PREFIX } from './db'

export interface AuthUser {
  id: number
  nome: string
  cognome: string
  email: string
  piano: string
  trial_ends_at: string | null
  stripe_customer_id: string | null
  stripe_subscription_id: string | null
}

export async function getAuthUser(event: H3Event): Promise<AuthUser | null> {
  const authHeader = getHeader(event, 'Authorization') ||
                     getHeader(event, 'HTTP_AUTHORIZATION') ||
                     getHeader(event, 'REDIRECT_HTTP_AUTHORIZATION')

  if (!authHeader || !authHeader.startsWith('Bearer ')) {
    return null
  }

  const token = authHeader.substring(7)
  const hashedToken = crypto.createHash('sha256').update(token).digest('hex')

  const tokenRecord = await findOne<{ user_id: number, expires_at: string }>('tokens', { token: hashedToken })

  if (!tokenRecord) {
    return null
  }

  // Check expiration
  if (tokenRecord.expires_at && new Date(tokenRecord.expires_at) < new Date()) {
    return null
  }

  const user = await findOne<AuthUser>('users', { id: tokenRecord.user_id })
  return user
}

export function generateToken(): string {
  return crypto.randomBytes(32).toString('hex')
}

export function hashToken(token: string): string {
  return crypto.createHash('sha256').update(token).digest('hex')
}

export async function createToken(userId: number, name: string = 'auth'): Promise<string> {
  const token = generateToken()
  const hashedToken = hashToken(token)
  const expiresAt = new Date()
  expiresAt.setDate(expiresAt.getDate() + 30) // 30 days

  await query(
    `INSERT INTO ${PREFIX}tokens (user_id, token, name, expires_at, created_at) VALUES (?, ?, ?, ?, NOW())`,
    [userId, hashedToken, name, expiresAt.toISOString().slice(0, 19).replace('T', ' ')]
  )

  return token
}

export async function deleteToken(token: string): Promise<void> {
  const hashedToken = hashToken(token)
  await query(`DELETE FROM ${PREFIX}tokens WHERE token = ?`, [hashedToken])
}

export function getUserPlan(user: AuthUser): string {
  // Se in trial e trial attivo, ritorna 'pro'
  if (user.piano === 'trial' && user.trial_ends_at) {
    const trialEnd = new Date(user.trial_ends_at)
    if (trialEnd > new Date()) {
      return 'pro'
    }
  }
  return user.piano || 'free'
}

export function isTrialActive(user: AuthUser): boolean {
  if (user.piano !== 'trial' || !user.trial_ends_at) {
    return false
  }
  return new Date(user.trial_ends_at) > new Date()
}

export function getTrialDaysLeft(user: AuthUser): number {
  if (!isTrialActive(user) || !user.trial_ends_at) {
    return 0
  }
  const now = new Date()
  const trialEnd = new Date(user.trial_ends_at)
  const diff = trialEnd.getTime() - now.getTime()
  return Math.ceil(diff / (1000 * 60 * 60 * 24))
}
