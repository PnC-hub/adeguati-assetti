<template>
  <div>
    <!-- Header -->
    <div class="mb-6">
      <div class="flex items-center gap-3 mb-4">
        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center">
          <Icon name="heroicons:chart-bar" class="w-6 h-6 text-white" />
        </div>
        <div class="flex-1">
          <h1 class="text-2xl font-bold text-gray-800">Dashboard</h1>
          <p class="text-gray-500">
            <span v-if="currentAziendaNome" class="font-medium text-gray-700">{{ currentAziendaNome }}</span>
            <span v-if="currentAziendaNome"> - </span>
            {{ periodoLabel }}
          </p>
        </div>
        <PageInfoButton
          title="Dashboard KPI"
          description="Monitoraggio dei 7 indicatori CNDCEC obbligatori e KPI settoriali per codice ATECO"
          :features="['7 KPI obbligatori: liquidità, solidità, redditività, DSCR, etc.', 'KPI settoriali basati su codice ATECO', 'Score conformità con semafori automatici']"
        />
      </div>

      <!-- Selettore Azienda (solo se più di una) -->
      <div v-if="aziende.length > 1" class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">Seleziona Azienda</label>
        <select
          v-model="selectedAziendaId"
          class="w-full md:w-auto min-w-[300px] px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white shadow-sm"
          @change="onAziendaChange"
        >
          <option :value="null">Tutte le aziende</option>
          <option v-for="az in aziende" :key="az.id" :value="az.id">
            {{ az.nome }} <span v-if="az.settore">- {{ az.settore }}</span>
          </option>
        </select>
      </div>

      <div class="flex justify-end gap-3">
        <select v-model="selectedMese" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" @change="onPeriodoChange">
          <option v-for="m in mesi" :key="m.value" :value="m.value">{{ m.label }}</option>
        </select>
        <select v-model="selectedAnno" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 w-24" @change="onPeriodoChange">
          <option v-for="a in anni" :key="a" :value="a">{{ a }}</option>
        </select>
        <NuxtLink v-if="selectedAziendaId !== null" :to="`/dashboard/inserimento?azienda=${selectedAziendaId}&anno=${selectedAnno}&mese=${selectedMese}`" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center gap-2">
          <Icon name="heroicons:arrow-path" class="w-4 h-4" />
          Ricalcola
        </NuxtLink>
        <button v-if="selectedAziendaId !== null && hasFeature('export_pdf')" @click="exportPdf" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 flex items-center gap-2" :disabled="exportingPdf">
          <Icon v-if="exportingPdf" name="heroicons:arrow-path" class="w-4 h-4 animate-spin" />
          <Icon v-else name="heroicons:document-arrow-down" class="w-4 h-4" />
          {{ exportingPdf ? 'Generazione...' : 'Report PDF' }}
        </button>
        <NuxtLink v-if="selectedAziendaId !== null && !hasFeature('export_pdf')" to="/dashboard/account" class="px-4 py-2 bg-gray-100 text-gray-400 rounded-lg flex items-center gap-2">
          <Icon name="heroicons:lock-closed" class="w-4 h-4" />
          Report PDF (Pro)
        </NuxtLink>
      </div>
    </div>

    <!-- Upgrade Banner per piano free -->
    <div v-if="userPlanCode === 'free'" class="bg-gradient-to-r from-purple-500 to-blue-600 text-white rounded-xl p-4 mb-6 flex items-center justify-between">
      <div>
        <p class="font-semibold">Stai usando il piano Free - funzionalita limitate</p>
        <p class="text-purple-100 text-sm">Sblocca KPI settoriali, alert automatici e export PDF</p>
      </div>
      <NuxtLink to="/dashboard/account" class="bg-white text-purple-700 px-5 py-2 rounded-lg font-semibold hover:bg-purple-50 transition text-sm">
        Upgrade
      </NuxtLink>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="flex justify-center items-center py-20">
      <Icon name="heroicons:arrow-path" class="w-12 h-12 text-blue-600 animate-spin" />
    </div>

    <!-- Error -->
    <div v-else-if="error" class="bg-red-50 border border-red-200 rounded-lg p-6 mb-6">
      <div class="flex items-center gap-3 text-red-700">
        <Icon name="heroicons:exclamation-circle" class="w-8 h-8" />
        <div>
          <div class="font-semibold">Errore nel caricamento</div>
          <div class="text-sm">{{ error }}</div>
        </div>
      </div>
      <button @click="loadData" class="mt-3 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
        Riprova
      </button>
    </div>

    <!-- Vista Panoramica (Tutte le aziende) -->
    <template v-else-if="selectedAziendaId === null && aziende.length > 1">
      <div class="bg-white rounded-xl shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
          <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
            <Icon name="heroicons:building-office-2" class="w-5 h-5 text-blue-600" />
            Panoramica Clienti - {{ periodoLabel }}
          </h2>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-gray-800 text-white">
              <tr>
                <th class="px-6 py-4 text-left font-semibold">Azienda</th>
                <th class="px-6 py-4 text-center font-semibold">Score</th>
                <th class="px-6 py-4 text-center font-semibold">Stato</th>
                <th class="px-6 py-4 text-center font-semibold">Alert</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              <tr
                v-for="az in aziendeRiepilogo"
                :key="az.id"
                class="hover:bg-blue-50 cursor-pointer transition-colors"
                @click="selectAzienda(az.id)"
              >
                <td class="px-6 py-4">
                  <div class="font-medium text-gray-900">{{ az.nome }}</div>
                  <div v-if="az.settore" class="text-sm text-gray-500">{{ az.settore }}</div>
                </td>
                <td class="px-6 py-4 text-center">
                  <span class="text-2xl font-bold" :class="getScoreColor(az.score)">{{ az.score }}</span>
                </td>
                <td class="px-6 py-4 text-center">
                  <span
                    class="px-3 py-1 rounded-full text-sm font-semibold"
                    :class="getStatoBadgeClass(az.stato)"
                  >
                    {{ getStatoLabel(az.stato) }}
                  </span>
                </td>
                <td class="px-6 py-4 text-center">
                  <span v-if="az.alert_count > 0" class="inline-flex items-center gap-1 text-red-600 font-semibold">
                    <Icon name="heroicons:exclamation-triangle" class="w-4 h-4" />
                    {{ az.alert_count }}
                  </span>
                  <span v-else class="text-gray-400">-</span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </template>

    <!-- Vista Singola Azienda -->
    <template v-else>
      <!-- Score Card -->
      <div class="bg-white rounded-xl shadow p-6 mb-6">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-4">
            <div class="text-6xl font-bold" :class="scoreColor">{{ dashboard.score }}</div>
            <div>
              <div class="text-lg font-semibold text-gray-700">STATO GENERALE</div>
              <div class="text-xl font-bold" :class="statoColor">
                {{ statoLabel }}
              </div>
            </div>
          </div>
          <div v-if="dashboard.alert_count > 0" class="flex items-center gap-2 text-red-600">
            <Icon name="heroicons:exclamation-triangle" class="w-8 h-8" />
            <span class="text-lg font-semibold">{{ dashboard.alert_count }} Alert attivi</span>
          </div>
        </div>
      </div>

      <!-- KPI Obbligatori -->
      <div class="bg-white rounded-xl shadow p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
          <Icon name="heroicons:scale" class="w-5 h-5 text-blue-600" />
          Indicatori Obbligatori (Codice della Crisi)
        </h2>
        <div v-if="dashboard.kpi_obbligatori.length === 0" class="text-gray-500 text-center py-8">
          <Icon name="heroicons:information-circle" class="w-8 h-8 mx-auto mb-2" />
          <p>Nessun dato disponibile. Inserisci i dati economici per calcolare i KPI.</p>
          <NuxtLink to="/dashboard/inserimento" class="inline-block mt-3 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            Inserisci Dati
          </NuxtLink>
        </div>
        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
          <KpiCard
            v-for="kpi in dashboard.kpi_obbligatori"
            :key="kpi.codice"
            :kpi="kpi"
            @click="goToDetail(kpi.codice)"
          />
        </div>
      </div>

      <!-- KPI Settoriali -->
      <div class="bg-white rounded-xl shadow p-6 mb-6 relative">
        <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
          <Icon name="heroicons:chart-pie" class="w-5 h-5 text-blue-600" />
          KPI Aziendali
        </h2>
        <!-- Upgrade overlay per piano free -->
        <div v-if="!hasFeature('kpi_settoriali')" class="absolute inset-0 bg-white/80 backdrop-blur-sm rounded-xl flex items-center justify-center z-10">
          <div class="text-center p-6">
            <Icon name="heroicons:lock-closed" class="w-12 h-12 text-gray-400 mx-auto mb-3" />
            <h3 class="text-lg font-bold text-gray-700">KPI Settoriali disponibili dal piano Pro</h3>
            <p class="text-gray-500 mt-1 mb-4">Sblocca analisi settoriale, alert e report PDF</p>
            <NuxtLink to="/dashboard/account" class="inline-block bg-purple-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-purple-700 transition">
              Upgrade a Pro - &euro;49/mese
            </NuxtLink>
          </div>
        </div>
        <div v-if="dashboard.kpi_settoriali.length === 0" class="text-gray-500 text-center py-8">
          <Icon name="heroicons:chart-bar" class="w-8 h-8 mx-auto mb-2" />
          <p>I KPI settoriali verranno calcolati automaticamente dai dati inseriti.</p>
        </div>
        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
          <KpiCard
            v-for="kpi in dashboard.kpi_settoriali"
            :key="kpi.codice"
            :kpi="kpi"
            @click="goToDetail(kpi.codice)"
          />
        </div>
      </div>

      <!-- Alert Section -->
      <div v-if="dashboard.alert && dashboard.alert.length > 0" class="bg-white rounded-xl shadow p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
          <Icon name="heroicons:bell-alert" class="w-5 h-5 text-red-500" />
          Alert Attivi
        </h2>
        <div class="space-y-3">
          <div
            v-for="alert in dashboard.alert"
            :key="alert.id"
            class="flex items-start gap-3 p-4 rounded-lg"
            :class="alert.livello === 'critical' ? 'bg-red-50 border border-red-200' : 'bg-yellow-50 border border-yellow-200'"
          >
            <Icon
              :name="alert.livello === 'critical' ? 'heroicons:x-circle' : 'heroicons:exclamation-triangle'"
              class="w-5 h-5 mt-0.5"
              :class="alert.livello === 'critical' ? 'text-red-500' : 'text-yellow-500'"
            />
            <div class="flex-1">
              <div class="font-semibold" :class="alert.livello === 'critical' ? 'text-red-700' : 'text-yellow-700'">
                {{ alert.messaggio }}
              </div>
              <div class="text-sm text-gray-600 mt-1">
                <strong>Azione suggerita:</strong> {{ alert.azione_suggerita }}
              </div>
            </div>
          </div>
        </div>
      </div>
    </template>
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  layout: 'dashboard',
  middleware: 'auth'
})

const router = useRouter()
const config = useRuntimeConfig()

const selectedAnno = ref(new Date().getFullYear())
const selectedMese = ref(new Date().getMonth() + 1)
const loading = ref(false)
const error = ref<string | null>(null)
const exportingPdf = ref(false)

// Piano utente per feature gating
const userPlanCode = ref('free')
const userFeatures = ref<Record<string, boolean>>({})

const hasFeature = (feature: string): boolean => {
  return userFeatures.value[feature] === true
}

const loadUserPlan = () => {
  try {
    const stored = localStorage.getItem('aa_piano')
    if (stored) {
      const parsed = JSON.parse(stored)
      userPlanCode.value = parsed.codice || 'free'
      userFeatures.value = parsed.features || {}
    }
  } catch (e) {
    // default free
  }
}

// Aziende management
interface Azienda {
  id: number
  nome: string
  settore?: string
}

interface AziendaRiepilogo extends Azienda {
  score: number
  stato: string
  alert_count: number
}

const aziende = ref<Azienda[]>([])
const aziendeRiepilogo = ref<AziendaRiepilogo[]>([])
const selectedAziendaId = ref<number | null>(null)

const anni = computed(() => {
  const current = new Date().getFullYear()
  return [current - 2, current - 1, current, current + 1]
})

const mesi = [
  { value: 1, label: 'Gennaio' },
  { value: 2, label: 'Febbraio' },
  { value: 3, label: 'Marzo' },
  { value: 4, label: 'Aprile' },
  { value: 5, label: 'Maggio' },
  { value: 6, label: 'Giugno' },
  { value: 7, label: 'Luglio' },
  { value: 8, label: 'Agosto' },
  { value: 9, label: 'Settembre' },
  { value: 10, label: 'Ottobre' },
  { value: 11, label: 'Novembre' },
  { value: 12, label: 'Dicembre' },
]

const periodoLabel = computed(() => {
  const m = mesi.find(x => x.value === selectedMese.value)
  return `${m?.label} ${selectedAnno.value}`
})

const currentAziendaNome = computed(() => {
  if (selectedAziendaId.value === null) return null
  const az = aziende.value.find(a => a.id === selectedAziendaId.value)
  return az?.nome || null
})

interface KpiData {
  codice: string
  nome: string
  valore: number | null
  stato: 'verde' | 'giallo' | 'rosso' | 'nd'
  unita_misura: string
  delta_precedente?: number | null
}

interface AlertData {
  id: number
  livello: 'critical' | 'warning'
  messaggio: string
  azione_suggerita: string
}

interface DashboardData {
  score: number
  stato_generale: string
  alert_count: number
  kpi_obbligatori: KpiData[]
  kpi_settoriali: KpiData[]
  alert: AlertData[]
}

const dashboard = reactive<DashboardData>({
  score: 0,
  stato_generale: 'nd',
  alert_count: 0,
  kpi_obbligatori: [],
  kpi_settoriali: [],
  alert: [],
})

const getAuthHeaders = () => {
  const token = localStorage.getItem('aa_token')
  return {
    'X-API-Key': config.public.apiKey,
    'Authorization': `Bearer ${token}`,
    'Content-Type': 'application/json'
  }
}

const onAziendaChange = () => {
  loadData()
}

const onPeriodoChange = () => {
  loadData()
}

const selectAzienda = (id: number) => {
  selectedAziendaId.value = id
  loadDashboard()
}

const loadAziende = async () => {
  try {
    const response = await $fetch<{ success: boolean; data: { aziende: Azienda[] } }>(
      `${config.public.apiBase}/auth/me`,
      { headers: getAuthHeaders() }
    )
    if (response.success && response.data.aziende) {
      aziende.value = response.data.aziende
      // Default to "Tutte" if more than one azienda
      if (aziende.value.length > 1) {
        selectedAziendaId.value = null
      } else if (aziende.value.length === 1) {
        selectedAziendaId.value = aziende.value[0].id
      }
    }
  } catch (e) {
    console.error('Errore caricamento aziende:', e)
  }
}

const loadRiepilogo = async () => {
  loading.value = true
  error.value = null
  aziendeRiepilogo.value = []

  try {
    // Load dashboard for each azienda
    const promises = aziende.value.map(async (az) => {
      try {
        const response = await $fetch<{ success: boolean; data: DashboardData }>(
          `${config.public.apiBase}/dashboard?azienda_id=${az.id}&anno=${selectedAnno.value}&mese=${selectedMese.value}`,
          { headers: getAuthHeaders() }
        )
        if (response.success && response.data) {
          return {
            ...az,
            score: response.data.score,
            stato: response.data.stato_generale,
            alert_count: response.data.alert_count
          }
        }
      } catch (e) {
        console.error(`Errore caricamento azienda ${az.id}:`, e)
      }
      return {
        ...az,
        score: 0,
        stato: 'nd',
        alert_count: 0
      }
    })

    aziendeRiepilogo.value = await Promise.all(promises)
  } catch (e) {
    console.error('Errore caricamento riepilogo:', e)
    error.value = 'Errore nel caricamento dei dati'
  } finally {
    loading.value = false
  }
}

const loadDashboard = async () => {
  if (selectedAziendaId.value === null) return

  loading.value = true
  error.value = null

  try {
    const response = await $fetch<{ success: boolean; data: DashboardData }>(
      `${config.public.apiBase}/dashboard?azienda_id=${selectedAziendaId.value}&anno=${selectedAnno.value}&mese=${selectedMese.value}`,
      { headers: getAuthHeaders() }
    )

    if (response.success && response.data) {
      Object.assign(dashboard, response.data)
      // Aggiorna piano dal backend
      if (response.data.piano) {
        userPlanCode.value = response.data.piano.codice || 'free'
        userFeatures.value = response.data.piano.features || {}
        localStorage.setItem('aa_piano', JSON.stringify({
          codice: response.data.piano.codice,
          features: response.data.piano.features,
        }))
      }
    }
  } catch (e: unknown) {
    console.error('Errore caricamento dashboard:', e)
    error.value = 'Errore nel caricamento della dashboard'
  } finally {
    loading.value = false
  }
}

const loadData = () => {
  if (selectedAziendaId.value === null && aziende.value.length > 1) {
    loadRiepilogo()
  } else {
    loadDashboard()
  }
}

const exportPdf = async () => {
  if (selectedAziendaId.value === null) return

  exportingPdf.value = true

  try {
    const response = await $fetch<{ success: boolean; data: any }>(
      `${config.public.apiBase}/export?azienda_id=${selectedAziendaId.value}&anno=${selectedAnno.value}`,
      { headers: getAuthHeaders() }
    )

    if (response.success) {
      // Create PDF content
      const aziendaNome = currentAziendaNome.value || 'Azienda'
      const periodo = periodoLabel.value

      // Generate simple text report and download
      const reportContent = generateReportText(aziendaNome, periodo, response.data)
      downloadTextFile(reportContent, `Report_${aziendaNome.replace(/\s+/g, '_')}_${selectedAnno.value}_${selectedMese.value}.txt`)
    }
  } catch (e) {
    console.error('Errore export:', e)
    alert('Errore nella generazione del report')
  } finally {
    exportingPdf.value = false
  }
}

const generateReportText = (azienda: string, periodo: string, data: any) => {
  let report = `REPORT ADEGUATI ASSETTI\n`
  report += `${'='.repeat(50)}\n\n`
  report += `Azienda: ${azienda}\n`
  report += `Periodo: ${periodo}\n`
  report += `Data generazione: ${new Date().toLocaleDateString('it-IT')}\n\n`
  report += `STATO GENERALE\n`
  report += `${'-'.repeat(30)}\n`
  report += `Score: ${dashboard.score}\n`
  report += `Stato: ${statoLabel.value}\n`
  report += `Alert attivi: ${dashboard.alert_count}\n\n`
  report += `INDICATORI OBBLIGATORI\n`
  report += `${'-'.repeat(30)}\n`
  dashboard.kpi_obbligatori.forEach(kpi => {
    report += `${kpi.nome}: ${kpi.valore !== null ? kpi.valore : 'N/D'} (${kpi.stato})\n`
  })
  report += `\nKPI AZIENDALI\n`
  report += `${'-'.repeat(30)}\n`
  dashboard.kpi_settoriali.forEach(kpi => {
    report += `${kpi.nome}: ${kpi.valore !== null ? kpi.valore : 'N/D'} (${kpi.stato})\n`
  })
  return report
}

const downloadTextFile = (content: string, filename: string) => {
  const blob = new Blob([content], { type: 'text/plain;charset=utf-8' })
  const url = URL.createObjectURL(blob)
  const a = document.createElement('a')
  a.href = url
  a.download = filename
  document.body.appendChild(a)
  a.click()
  document.body.removeChild(a)
  URL.revokeObjectURL(url)
}

const scoreColor = computed(() => {
  if (dashboard.score >= 70) return 'text-green-500'
  if (dashboard.score >= 50) return 'text-yellow-500'
  return 'text-red-500'
})

const statoColor = computed(() => {
  if (dashboard.stato_generale === 'buono') return 'text-green-600'
  if (dashboard.stato_generale === 'attenzione') return 'text-yellow-600'
  return 'text-red-600'
})

const statoLabel = computed(() => {
  if (dashboard.stato_generale === 'buono') return 'BUONO'
  if (dashboard.stato_generale === 'attenzione') return 'ATTENZIONE'
  if (dashboard.stato_generale === 'critico') return 'CRITICO'
  return 'N/D'
})

const getScoreColor = (score: number) => {
  if (score >= 70) return 'text-green-500'
  if (score >= 50) return 'text-yellow-500'
  return 'text-red-500'
}

const getStatoBadgeClass = (stato: string) => {
  if (stato === 'buono') return 'bg-green-100 text-green-800'
  if (stato === 'attenzione') return 'bg-yellow-100 text-yellow-800'
  if (stato === 'critico') return 'bg-red-100 text-red-800'
  return 'bg-gray-100 text-gray-800'
}

const getStatoLabel = (stato: string) => {
  if (stato === 'buono') return 'BUONO'
  if (stato === 'attenzione') return 'ATTENZIONE'
  if (stato === 'critico') return 'CRITICO'
  return 'N/D'
}

const goToDetail = (codice: string) => {
  router.push(`/dashboard/kpi/${codice}`)
}

onMounted(async () => {
  loadUserPlan()
  await loadAziende()
  loadData()
})
</script>
