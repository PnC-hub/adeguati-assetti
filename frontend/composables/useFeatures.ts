// Feature gating composable for Adeguati Assetti SaaS
// Manages plan-based feature access

export interface PlanFeatures {
  maxAziende: number
  kpiSettoriali: boolean
  alertEmail: boolean
  exportPdf: boolean
  exportExcel: boolean
  reportPersonalizzati: boolean
  apiAccess: boolean
  whiteLabel: boolean
  supportoPrioritario: boolean
  multiUtente: boolean
}

const PLAN_FEATURES: Record<string, PlanFeatures> = {
  free: {
    maxAziende: 1,
    kpiSettoriali: false,
    alertEmail: false,
    exportPdf: false,
    exportExcel: false,
    reportPersonalizzati: false,
    apiAccess: false,
    whiteLabel: false,
    supportoPrioritario: false,
    multiUtente: false
  },
  pro: {
    maxAziende: 1,
    kpiSettoriali: true,
    alertEmail: true,
    exportPdf: true,
    exportExcel: false,
    reportPersonalizzati: false,
    apiAccess: false,
    whiteLabel: false,
    supportoPrioritario: false,
    multiUtente: false
  },
  business: {
    maxAziende: 10,
    kpiSettoriali: true,
    alertEmail: true,
    exportPdf: true,
    exportExcel: true,
    reportPersonalizzati: true,
    apiAccess: false,
    whiteLabel: false,
    supportoPrioritario: false,
    multiUtente: true
  },
  enterprise: {
    maxAziende: Infinity,
    kpiSettoriali: true,
    alertEmail: true,
    exportPdf: true,
    exportExcel: true,
    reportPersonalizzati: true,
    apiAccess: true,
    whiteLabel: true,
    supportoPrioritario: true,
    multiUtente: true
  },
  // Legacy plans mapping
  trial: {
    maxAziende: 10,
    kpiSettoriali: true,
    alertEmail: true,
    exportPdf: true,
    exportExcel: true,
    reportPersonalizzati: true,
    apiAccess: false,
    whiteLabel: false,
    supportoPrioritario: false,
    multiUtente: true
  },
  studio: {
    maxAziende: 20,
    kpiSettoriali: true,
    alertEmail: true,
    exportPdf: true,
    exportExcel: true,
    reportPersonalizzati: true,
    apiAccess: true,
    whiteLabel: true,
    supportoPrioritario: true,
    multiUtente: true
  }
}

export const useFeatures = () => {
  const currentPlan = ref<string>('free')
  const isTrialActive = ref(false)
  const trialDaysRemaining = ref(0)
  const isAccountFrozen = ref(false)

  // Initialize from localStorage
  const initFromStorage = () => {
    if (typeof window !== 'undefined') {
      const storedPlan = localStorage.getItem('aa_piano')
      if (storedPlan) {
        currentPlan.value = storedPlan.toLowerCase()
      }

      const storedUser = localStorage.getItem('aa_user')
      if (storedUser) {
        try {
          const user = JSON.parse(storedUser)
          if (user.trial_ends_at) {
            const trialEnd = new Date(user.trial_ends_at)
            const now = new Date()
            if (trialEnd > now) {
              isTrialActive.value = true
              trialDaysRemaining.value = Math.ceil((trialEnd.getTime() - now.getTime()) / (1000 * 60 * 60 * 24))
            }
          }
          if (user.account_frozen) {
            isAccountFrozen.value = true
          }
        } catch (e) {
          console.error('Error parsing user data:', e)
        }
      }
    }
  }

  // Get features for current plan
  const features = computed((): PlanFeatures => {
    // If account is frozen, return free features
    if (isAccountFrozen.value) {
      return PLAN_FEATURES.free
    }
    // If trial is active, use trial features (business-level)
    if (isTrialActive.value) {
      return PLAN_FEATURES.trial
    }
    return PLAN_FEATURES[currentPlan.value] || PLAN_FEATURES.free
  })

  // Check if a specific feature is available
  const hasFeature = (feature: keyof PlanFeatures): boolean => {
    const featureValue = features.value[feature]
    if (typeof featureValue === 'boolean') {
      return featureValue
    }
    if (typeof featureValue === 'number') {
      return featureValue > 0
    }
    return false
  }

  // Check if can add more aziende
  const canAddAzienda = (currentCount: number): boolean => {
    return currentCount < features.value.maxAziende
  }

  // Get remaining aziende slots
  const remainingAziendeSlots = (currentCount: number): number => {
    const max = features.value.maxAziende
    if (max === Infinity) return Infinity
    return Math.max(0, max - currentCount)
  }

  // Check if user should see upgrade prompt
  const shouldShowUpgrade = computed((): boolean => {
    return currentPlan.value === 'free' || (isTrialActive.value && trialDaysRemaining.value <= 7)
  })

  // Get plan display name
  const planDisplayName = computed((): string => {
    const names: Record<string, string> = {
      free: 'Free',
      pro: 'Pro',
      business: 'Business',
      enterprise: 'Enterprise',
      trial: 'Business (Trial)',
      studio: 'Studio'
    }
    if (isTrialActive.value) {
      return `${names[currentPlan.value] || currentPlan.value} (Trial)`
    }
    return names[currentPlan.value] || currentPlan.value
  })

  // Get upgrade path
  const getUpgradePath = (): string => {
    switch (currentPlan.value) {
      case 'free':
        return 'pro'
      case 'pro':
        return 'business'
      case 'business':
        return 'enterprise'
      default:
        return 'business'
    }
  }

  // Feature lock message
  const getFeatureLockMessage = (feature: keyof PlanFeatures): string => {
    const messages: Record<keyof PlanFeatures, string> = {
      maxAziende: 'Aggiorna il tuo piano per gestire più aziende',
      kpiSettoriali: 'KPI settoriali disponibili dal piano Pro',
      alertEmail: 'Alert email disponibili dal piano Pro',
      exportPdf: 'Export PDF disponibile dal piano Pro',
      exportExcel: 'Export Excel disponibile dal piano Business',
      reportPersonalizzati: 'Report personalizzati disponibili dal piano Business',
      apiAccess: 'Accesso API disponibile dal piano Enterprise',
      whiteLabel: 'White-label disponibile dal piano Enterprise',
      supportoPrioritario: 'Supporto prioritario disponibile dal piano Enterprise',
      multiUtente: 'Gestione multi-utente disponibile dal piano Business'
    }
    return messages[feature]
  }

  // Initialize on mount
  if (typeof window !== 'undefined') {
    initFromStorage()
  }

  return {
    currentPlan: readonly(currentPlan),
    isTrialActive: readonly(isTrialActive),
    trialDaysRemaining: readonly(trialDaysRemaining),
    isAccountFrozen: readonly(isAccountFrozen),
    features,
    hasFeature,
    canAddAzienda,
    remainingAziendeSlots,
    shouldShowUpgrade,
    planDisplayName,
    getUpgradePath,
    getFeatureLockMessage,
    initFromStorage
  }
}
