// composables/useTenant.ts
// Multi-tenant composable per Adeguati Assetti Impresa

export interface Tenant {
  id: string
  ragioneSociale: string
  partitaIva: string | null
  codiceFiscale: string
  codiceAteco: string | null
  formaGiuridica: 'SRL' | 'SPA' | 'SRLS' | 'SAS' | 'SNC' | 'DITTA_INDIVIDUALE'
  piano: 'FREE' | 'PRO' | 'STUDIO'
  complianceScore: number | null
  isObbligata: boolean
  attivoPatrimoniale: number | null
  ricavi: number | null
  dipendentiMedi: number | null
  lastUpdate: string | null
}

export interface TenantWithRole extends Tenant {
  ruolo: 'OWNER' | 'ADMIN' | 'COMMERCIALISTA' | 'VIEWER'
  kpiCritici?: number
}

// Mock data per sviluppo - 4 aziende realistiche
const MOCK_TENANTS: TenantWithRole[] = [
  {
    id: 'tenant-smiledoc',
    ragioneSociale: 'Smiledoc S.r.l.',
    partitaIva: '15131801001',
    codiceFiscale: '15131801001',
    codiceAteco: '86.23.00',
    formaGiuridica: 'SRL',
    piano: 'PRO',
    complianceScore: 78,
    isObbligata: true,
    attivoPatrimoniale: 1200000,
    ricavi: 850000,
    dipendentiMedi: 8,
    lastUpdate: '2026-02-01',
    ruolo: 'OWNER',
    kpiCritici: 2
  },
  {
    id: 'tenant-horus',
    ragioneSociale: 'Horus Holding S.r.l.',
    partitaIva: '15128261003',
    codiceFiscale: '15128261003',
    codiceAteco: '70.10.00',
    formaGiuridica: 'SRL',
    piano: 'PRO',
    complianceScore: 65,
    isObbligata: false,
    attivoPatrimoniale: 500000,
    ricavi: 180000,
    dipendentiMedi: 0,
    lastUpdate: '2026-01-28',
    ruolo: 'ADMIN',
    kpiCritici: 3
  },
  {
    id: 'tenant-civero',
    ragioneSociale: 'Studio Dott. Civero',
    partitaIva: '12345678901',
    codiceFiscale: 'CVRPNT74R12H501X',
    codiceAteco: '86.23.00',
    formaGiuridica: 'DITTA_INDIVIDUALE',
    piano: 'FREE',
    complianceScore: 45,
    isObbligata: false,
    attivoPatrimoniale: 150000,
    ricavi: 120000,
    dipendentiMedi: 2,
    lastUpdate: '2026-01-15',
    ruolo: 'OWNER',
    kpiCritici: 5
  },
  {
    id: 'tenant-annita',
    ragioneSociale: 'Studio Dott.ssa Di Vozzo',
    partitaIva: '98765432109',
    codiceFiscale: 'DVZNNT80A41H501X',
    codiceAteco: '86.23.00',
    formaGiuridica: 'DITTA_INDIVIDUALE',
    piano: 'FREE',
    complianceScore: 82,
    isObbligata: false,
    attivoPatrimoniale: 80000,
    ricavi: 95000,
    dipendentiMedi: 1,
    lastUpdate: '2026-02-02',
    ruolo: 'VIEWER',
    kpiCritici: 1
  }
]

export const useTenant = () => {
  // State
  const currentTenant = useState<TenantWithRole | null>('aa-tenant', () => null)
  const tenants = useState<TenantWithRole[]>('aa-tenants', () => [])
  const isAggregated = useState('aa-aggregated', () => false)
  const userRole = useState<string>('aa-role', () => 'VIEWER')
  const loading = useState('aa-loading', () => false)

  // Inizializza al mount
  const init = async () => {
    if (tenants.value.length === 0) {
      await fetchTenants()
    }

    // Ripristina tenant salvato
    if (import.meta.client) {
      const savedTenantId = localStorage.getItem('aa_tenant_id')
      if (savedTenantId && !currentTenant.value) {
        const tenant = tenants.value.find(t => t.id === savedTenantId)
        if (tenant) {
          currentTenant.value = tenant
          userRole.value = tenant.ruolo
        }
      }
    }
  }

  // Fetch tenants (mock)
  const fetchTenants = async () => {
    loading.value = true
    try {
      // TODO: Sostituire con API reale
      // const data = await $fetch('/api/adeguati-assetti/tenants')
      await new Promise(resolve => setTimeout(resolve, 300))
      tenants.value = MOCK_TENANTS

      // Auto-select se solo uno
      if (MOCK_TENANTS.length === 1 && !currentTenant.value) {
        await switchTenant(MOCK_TENANTS[0].id)
      }
    } catch (error) {
      console.error('Errore fetch tenants:', error)
    } finally {
      loading.value = false
    }
  }

  // Switch tenant
  const switchTenant = async (tenantId: string) => {
    // TODO: Chiamata API per switch
    // await $fetch(`/api/adeguati-assetti/tenants/${tenantId}/switch`, { method: 'POST' })

    const tenant = tenants.value.find(t => t.id === tenantId)
    if (tenant) {
      currentTenant.value = tenant
      userRole.value = tenant.ruolo
      isAggregated.value = false

      if (import.meta.client) {
        localStorage.setItem('aa_tenant_id', tenantId)
      }
    }
  }

  // Vista aggregata (per commercialisti)
  const switchToAggregated = () => {
    isAggregated.value = true
    currentTenant.value = null
    if (import.meta.client) {
      localStorage.removeItem('aa_tenant_id')
    }
  }

  // Computed stats aggregati
  const aggregatedStats = computed(() => {
    const total = tenants.value.length
    const compliant = tenants.value.filter(t => (t.complianceScore || 0) >= 80).length
    const warning = tenants.value.filter(t => (t.complianceScore || 0) >= 50 && (t.complianceScore || 0) < 80).length
    const critical = tenants.value.filter(t => (t.complianceScore || 0) < 50).length
    const avgScore = total > 0
      ? Math.round(tenants.value.reduce((sum, t) => sum + (t.complianceScore || 0), 0) / total)
      : 0

    return {
      totale: total,
      inRegola: compliant,
      attenzione: warning,
      critiche: critical,
      scoreMedia: avgScore
    }
  })

  // Permessi
  const canEdit = computed(() =>
    !isAggregated.value && ['OWNER', 'ADMIN'].includes(userRole.value)
  )

  const canExport = computed(() => {
    if (isAggregated.value) return false
    return currentTenant.value?.piano !== 'FREE' && ['OWNER', 'ADMIN', 'COMMERCIALISTA'].includes(userRole.value)
  })

  const canInvite = computed(() => userRole.value === 'OWNER')

  const canManageTeam = computed(() => ['OWNER', 'ADMIN'].includes(userRole.value))

  const isCommercialista = computed(() =>
    tenants.value.some(t => t.ruolo === 'COMMERCIALISTA')
  )

  // Format helpers
  const formatFormaGiuridica = (forma: string) => {
    const map: Record<string, string> = {
      'SRL': 'S.r.l.',
      'SPA': 'S.p.A.',
      'SRLS': 'S.r.l.s.',
      'SAS': 'S.a.s.',
      'SNC': 'S.n.c.',
      'DITTA_INDIVIDUALE': 'Ditta Individuale'
    }
    return map[forma] || forma
  }

  const formatPiano = (piano: string) => {
    const map: Record<string, string> = {
      'FREE': 'Gratuito',
      'PRO': 'Pro',
      'STUDIO': 'Studio'
    }
    return map[piano] || piano
  }

  const getRuoloBadgeClass = (ruolo: string) => {
    const classes: Record<string, string> = {
      'OWNER': 'bg-red-600 text-white',
      'ADMIN': 'bg-orange-600 text-white',
      'COMMERCIALISTA': 'bg-blue-600 text-white',
      'VIEWER': 'bg-gray-600 text-white'
    }
    return classes[ruolo] || 'bg-gray-600 text-white'
  }

  const formatRuolo = (ruolo: string) => {
    const map: Record<string, string> = {
      'OWNER': 'Titolare',
      'ADMIN': 'Admin',
      'COMMERCIALISTA': 'Commercialista',
      'VIEWER': 'Visualizzatore'
    }
    return map[ruolo] || ruolo
  }

  const getScoreColor = (score: number | null) => {
    if (score === null) return 'gray'
    if (score >= 80) return 'green'
    if (score >= 50) return 'yellow'
    return 'red'
  }

  return {
    // State
    currentTenant,
    tenants,
    isAggregated,
    userRole,
    loading,

    // Stats
    aggregatedStats,

    // Permissions
    canEdit,
    canExport,
    canInvite,
    canManageTeam,
    isCommercialista,

    // Actions
    init,
    fetchTenants,
    switchTenant,
    switchToAggregated,

    // Formatters
    formatFormaGiuridica,
    formatPiano,
    formatRuolo,
    getRuoloBadgeClass,
    getScoreColor
  }
}
