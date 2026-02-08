<template>
  <div>
    <!-- Header -->
    <div class="mb-6">
      <div class="flex items-center gap-3 mb-4">
        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-700 flex items-center justify-center">
          <Icon name="heroicons:heart" class="w-6 h-6 text-white" />
        </div>
        <div class="flex-1">
          <h1 class="text-2xl font-bold text-gray-800">Continuità Aziendale</h1>
          <p class="text-gray-500">Monitoraggio Going Concern ex Art. 2086 c.c.</p>
        </div>
        <PageInfoButton
          title="Continuità Aziendale"
          description="Monitoraggio degli indicatori di continuità aziendale secondo ISA 570 e CCII"
          :features="['DSCR e sostenibilità debito', 'Checklist ISA 570 per going concern', 'Cash flow prospettico 6 mesi']"
        />
      </div>

      <!-- Selettore Azienda -->
      <div v-if="aziende.length > 1" class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">Seleziona Azienda</label>
        <select
          v-model="selectedAziendaId"
          class="w-full md:w-auto min-w-[300px] px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 bg-white shadow-sm"
          @change="loadData"
        >
          <option v-for="az in aziende" :key="az.id" :value="az.id">
            {{ az.nome }} <span v-if="az.settore">- {{ az.settore }}</span>
          </option>
        </select>
      </div>

      <div class="flex justify-end gap-3">
        <button @click="exportReport" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 flex items-center gap-2">
          <Icon name="heroicons:document-arrow-down" class="w-4 h-4" />
          Genera Report Continuità PDF
        </button>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="flex justify-center items-center py-20">
      <Icon name="heroicons:arrow-path" class="w-12 h-12 text-emerald-600 animate-spin" />
    </div>

    <!-- Content -->
    <template v-else>
      <!-- KPI Continuità Aziendale -->
      <div class="bg-white rounded-xl shadow p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
          <Icon name="heroicons:chart-bar-square" class="w-5 h-5 text-emerald-600" />
          Indicatori Continuità Aziendale
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
          <!-- DSCR -->
          <div
            class="bg-gray-50 rounded-lg p-4 border-l-4 cursor-pointer hover:shadow-md transition-shadow"
            :class="getDSCRBorderColor()"
          >
            <div class="flex items-start justify-between mb-2">
              <span class="text-sm font-medium text-gray-600">DSCR (Debt Service Coverage)</span>
              <div class="w-3 h-3 rounded-full" :class="getDSCRStatusColor()"></div>
            </div>
            <div class="text-2xl font-bold text-gray-900 mb-1">{{ continuita.dscr.toFixed(2) }}</div>
            <div class="text-xs text-gray-500">EBITDA / (Capitale + Interessi)</div>
            <div class="mt-2 text-xs text-gray-600">
              <span :class="getDSCRStatusColor().replace('bg-', 'text-').replace('-500', '-700')">
                {{ getDSCRLabel() }}
              </span>
            </div>
          </div>

          <!-- Sostenibilità Debito -->
          <div
            class="bg-gray-50 rounded-lg p-4 border-l-4 cursor-pointer hover:shadow-md transition-shadow"
            :class="getDebtBorderColor()"
          >
            <div class="flex items-start justify-between mb-2">
              <span class="text-sm font-medium text-gray-600">Sostenibilità Debito</span>
              <div class="w-3 h-3 rounded-full" :class="getDebtStatusColor()"></div>
            </div>
            <div class="text-2xl font-bold text-gray-900 mb-1">{{ continuita.debtSustainability.toFixed(1) }}</div>
            <div class="text-xs text-gray-500">PFN / EBITDA</div>
            <div class="mt-2 text-xs text-gray-600">
              <span :class="getDebtStatusColor().replace('bg-', 'text-').replace('-500', '-700')">
                {{ getDebtLabel() }}
              </span>
            </div>
          </div>

          <!-- Cash Runway -->
          <div
            class="bg-gray-50 rounded-lg p-4 border-l-4 cursor-pointer hover:shadow-md transition-shadow"
            :class="getRunwayBorderColor()"
          >
            <div class="flex items-start justify-between mb-2">
              <span class="text-sm font-medium text-gray-600">Cash Runway</span>
              <div class="w-3 h-3 rounded-full" :class="getRunwayStatusColor()"></div>
            </div>
            <div class="text-2xl font-bold text-gray-900 mb-1">{{ continuita.cashRunway }} mesi</div>
            <div class="text-xs text-gray-500">Liquidità / Burn rate mensile</div>
            <div class="mt-2 text-xs text-gray-600">
              <span :class="getRunwayStatusColor().replace('bg-', 'text-').replace('-500', '-700')">
                {{ getRunwayLabel() }}
              </span>
            </div>
          </div>

          <!-- Score Continuità -->
          <div
            class="bg-gray-50 rounded-lg p-4 border-l-4 cursor-pointer hover:shadow-md transition-shadow"
            :class="getScoreBorderColor()"
          >
            <div class="flex items-start justify-between mb-2">
              <span class="text-sm font-medium text-gray-600">Score Continuità</span>
              <div class="w-3 h-3 rounded-full" :class="getScoreStatusColor()"></div>
            </div>
            <div class="text-2xl font-bold text-gray-900 mb-1">{{ continuita.score }}/100</div>
            <div class="text-xs text-gray-500">Valutazione complessiva</div>
            <div class="mt-2 text-xs text-gray-600">
              <span :class="getScoreStatusColor().replace('bg-', 'text-').replace('-500', '-700')">
                {{ getScoreLabel() }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Checklist ISA 570 - Going Concern -->
      <div class="bg-white rounded-xl shadow p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
          <Icon name="heroicons:clipboard-document-check" class="w-5 h-5 text-emerald-600" />
          Checklist ISA 570 - Going Concern
        </h2>
        <div class="space-y-3">
          <div
            v-for="(item, index) in checklist"
            :key="index"
            class="flex items-start gap-3 p-3 rounded-lg border"
            :class="item.ok ? 'bg-green-50 border-green-200' : 'bg-yellow-50 border-yellow-200'"
          >
            <Icon
              :name="item.ok ? 'heroicons:check-circle' : 'heroicons:exclamation-triangle'"
              class="w-5 h-5 mt-0.5"
              :class="item.ok ? 'text-green-600' : 'text-yellow-600'"
            />
            <div class="flex-1">
              <div class="font-medium text-gray-900">{{ item.label }}</div>
              <div v-if="item.note" class="text-sm text-gray-600 mt-1">{{ item.note }}</div>
            </div>
          </div>
        </div>
        <div class="mt-4 pt-4 border-t border-gray-200">
          <div class="flex items-center justify-between">
            <span class="text-sm font-medium text-gray-700">Conformità ISA 570:</span>
            <span class="text-xl font-bold" :class="getChecklistScoreColor()">
              {{ checklistScore }}/{{ checklist.length }}
            </span>
          </div>
        </div>
      </div>

      <!-- Indici Allerta CCII -->
      <div class="bg-white rounded-xl shadow p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
          <Icon name="heroicons:shield-exclamation" class="w-5 h-5 text-emerald-600" />
          Indici Allerta CCII (D.Lgs. 14/2019)
        </h2>
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-gray-800 text-white">
              <tr>
                <th class="px-6 py-3 text-left text-sm font-semibold">Indice</th>
                <th class="px-6 py-3 text-center text-sm font-semibold">Valore</th>
                <th class="px-6 py-3 text-center text-sm font-semibold">Soglia Critica</th>
                <th class="px-6 py-3 text-center text-sm font-semibold">Stato</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              <tr v-for="(idx, i) in indiciCCII" :key="i" class="hover:bg-gray-50">
                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ idx.nome }}</td>
                <td class="px-6 py-4 text-center text-sm text-gray-700">{{ idx.valore }}</td>
                <td class="px-6 py-4 text-center text-sm text-gray-600">{{ idx.soglia }}</td>
                <td class="px-6 py-4 text-center">
                  <span
                    class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-sm font-semibold"
                    :class="idx.ok ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                  >
                    <Icon
                      :name="idx.ok ? 'heroicons:check' : 'heroicons:x-mark'"
                      class="w-4 h-4"
                    />
                    {{ idx.ok ? 'OK' : 'ATTENZIONE' }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="mt-4 pt-4 border-t border-gray-200 bg-green-50 rounded-lg p-4">
          <div class="flex items-center gap-2 text-green-700">
            <Icon name="heroicons:check-badge" class="w-6 h-6" />
            <span class="font-semibold">Tutti gli indici CCII risultano conformi</span>
          </div>
        </div>
      </div>

      <!-- Cash Flow Prospettico -->
      <div class="bg-white rounded-xl shadow p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
          <Icon name="heroicons:banknotes" class="w-5 h-5 text-emerald-600" />
          Cash Flow Prospettico (6 mesi)
        </h2>
        <div class="space-y-4">
          <div v-for="(cf, index) in cashFlowProspettico" :key="index" class="border-b border-gray-200 pb-4 last:border-0 last:pb-0">
            <div class="flex items-center justify-between mb-2">
              <span class="font-medium text-gray-700">{{ cf.mese }}</span>
              <span class="font-bold text-lg" :class="cf.saldo >= 0 ? 'text-green-600' : 'text-red-600'">
                {{ formatCurrency(cf.saldo) }}
              </span>
            </div>
            <div class="grid grid-cols-3 gap-2 text-sm mb-2">
              <div class="text-gray-600">Entrate: <span class="font-semibold text-green-700">{{ formatCurrency(cf.entrate) }}</span></div>
              <div class="text-gray-600">Uscite: <span class="font-semibold text-red-700">{{ formatCurrency(cf.uscite) }}</span></div>
              <div class="text-gray-600">Saldo: <span class="font-semibold" :class="cf.saldo >= 0 ? 'text-green-700' : 'text-red-700'">{{ formatCurrency(cf.saldo) }}</span></div>
            </div>
            <!-- Bar Chart -->
            <div class="relative h-8 bg-gray-100 rounded-lg overflow-hidden">
              <div
                class="absolute h-full bg-gradient-to-r from-green-400 to-green-600 transition-all"
                :style="{ width: `${getBarWidth(cf.entrate, cf.uscite)}%` }"
              ></div>
              <div
                class="absolute h-full bg-gradient-to-r from-red-400 to-red-600 transition-all opacity-50"
                :style="{ width: `${getBarWidth(cf.uscite, cf.entrate)}%` }"
              ></div>
            </div>
          </div>
        </div>
        <div class="mt-6 pt-4 border-t border-gray-200 bg-green-50 rounded-lg p-4">
          <div class="flex items-center gap-2 text-green-700">
            <Icon name="heroicons:arrow-trending-up" class="w-6 h-6" />
            <span class="font-semibold">Flusso di cassa positivo per tutti i prossimi 6 mesi</span>
          </div>
        </div>
      </div>

      <!-- Info Box -->
      <div class="bg-blue-50 border border-blue-200 rounded-xl p-6">
        <div class="flex items-start gap-3">
          <Icon name="heroicons:information-circle" class="w-6 h-6 text-blue-600 mt-0.5" />
          <div class="flex-1">
            <h3 class="font-semibold text-blue-900 mb-2">Score complessivo per Cruscotto 2086</h3>
            <p class="text-sm text-blue-800 leading-relaxed">
              Il punteggio di continuità aziendale ({{ continuita.score }}/100) viene automaticamente integrato nel
              calcolo del <strong>Cruscotto Assetti 2086</strong> e contribuisce alla valutazione complessiva degli
              assetti organizzativi, amministrativi e contabili dell'azienda. Un punteggio superiore a 70 indica
              una solida prospettiva di continuità aziendale conforme ai principi ISA 570.
            </p>
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

const config = useRuntimeConfig()

interface Azienda {
  id: number
  nome: string
  settore?: string
}

interface ContinuitaData {
  dscr: number
  debtSustainability: number
  cashRunway: number
  score: number
}

interface ChecklistItem {
  label: string
  ok: boolean
  note?: string
}

interface IndiceCCII {
  nome: string
  valore: string
  soglia: string
  ok: boolean
}

interface CashFlowMonth {
  mese: string
  entrate: number
  uscite: number
  saldo: number
}

const loading = ref(false)
const aziende = ref<Azienda[]>([])
const selectedAziendaId = ref<number | null>(null)

// Mock data - in produzione questi dati verranno dall'API
const continuita = reactive<ContinuitaData>({
  dscr: 1.45,
  debtSustainability: 2.8,
  cashRunway: 8,
  score: 78
})

const checklist = ref<ChecklistItem[]>([
  { label: 'Patrimonio netto positivo', ok: true },
  { label: 'Posizione finanziaria netta sostenibile', ok: true },
  { label: 'Cash flow operativo positivo', ok: true },
  { label: 'Capacità di rimborso debiti a scadenza', ok: true },
  { label: 'Assenza di perdite operative significative', ok: true },
  { label: 'Piano industriale aggiornato', ok: false, note: 'Da aggiornare entro 30/06/2026' },
  { label: 'Clientela diversificata (no dipendenza >30% da singolo cliente)', ok: true },
  { label: 'Assenza di contenziosi rilevanti', ok: true },
  { label: 'Conformità normativa (licenze, autorizzazioni)', ok: true },
  { label: 'Copertura assicurativa adeguata', ok: true }
])

const indiciCCII = ref<IndiceCCII[]>([
  { nome: 'Patrimonio Netto Negativo', valore: 'No', soglia: 'Sì = Allerta', ok: true },
  { nome: 'DSCR a 6 mesi < 1', valore: '1.45', soglia: '< 1.0 = Allerta', ok: true },
  { nome: 'Ritardi pagamenti INPS > 90gg', valore: 'No', soglia: 'Sì = Allerta', ok: true },
  { nome: 'Ritardi pagamenti INAIL > 90gg', valore: 'No', soglia: 'Sì = Allerta', ok: true },
  { nome: 'Ritardi pagamenti Agenzia Entrate > 90gg', valore: 'No', soglia: 'Sì = Allerta', ok: true }
])

const cashFlowProspettico = ref<CashFlowMonth[]>([
  { mese: 'Febbraio 2026', entrate: 120000, uscite: 95000, saldo: 25000 },
  { mese: 'Marzo 2026', entrate: 115000, uscite: 98000, saldo: 17000 },
  { mese: 'Aprile 2026', entrate: 125000, uscite: 92000, saldo: 33000 },
  { mese: 'Maggio 2026', entrate: 118000, uscite: 100000, saldo: 18000 },
  { mese: 'Giugno 2026', entrate: 130000, uscite: 95000, saldo: 35000 },
  { mese: 'Luglio 2026', entrate: 110000, uscite: 90000, saldo: 20000 }
])

const checklistScore = computed(() => {
  return checklist.value.filter(item => item.ok).length
})

// DSCR helpers
const getDSCRStatusColor = () => {
  if (continuita.dscr >= 1.2) return 'bg-green-500'
  if (continuita.dscr >= 1.0) return 'bg-yellow-500'
  return 'bg-red-500'
}

const getDSCRBorderColor = () => {
  if (continuita.dscr >= 1.2) return 'border-green-500'
  if (continuita.dscr >= 1.0) return 'border-yellow-500'
  return 'border-red-500'
}

const getDSCRLabel = () => {
  if (continuita.dscr >= 1.2) return 'Ottimo - Copertura solida'
  if (continuita.dscr >= 1.0) return 'Sufficiente - Monitorare'
  return 'Critico - Azione richiesta'
}

// Debt Sustainability helpers
const getDebtStatusColor = () => {
  if (continuita.debtSustainability < 3) return 'bg-green-500'
  if (continuita.debtSustainability <= 5) return 'bg-yellow-500'
  return 'bg-red-500'
}

const getDebtBorderColor = () => {
  if (continuita.debtSustainability < 3) return 'border-green-500'
  if (continuita.debtSustainability <= 5) return 'border-yellow-500'
  return 'border-red-500'
}

const getDebtLabel = () => {
  if (continuita.debtSustainability < 3) return 'Debito sostenibile'
  if (continuita.debtSustainability <= 5) return 'Attenzione - Debito elevato'
  return 'Critico - Debito eccessivo'
}

// Cash Runway helpers
const getRunwayStatusColor = () => {
  if (continuita.cashRunway > 6) return 'bg-green-500'
  if (continuita.cashRunway >= 3) return 'bg-yellow-500'
  return 'bg-red-500'
}

const getRunwayBorderColor = () => {
  if (continuita.cashRunway > 6) return 'border-green-500'
  if (continuita.cashRunway >= 3) return 'border-yellow-500'
  return 'border-red-500'
}

const getRunwayLabel = () => {
  if (continuita.cashRunway > 6) return 'Liquidità abbondante'
  if (continuita.cashRunway >= 3) return 'Liquidità sufficiente'
  return 'Liquidità critica'
}

// Score helpers
const getScoreStatusColor = () => {
  if (continuita.score >= 70) return 'bg-green-500'
  if (continuita.score >= 50) return 'bg-yellow-500'
  return 'bg-red-500'
}

const getScoreBorderColor = () => {
  if (continuita.score >= 70) return 'border-green-500'
  if (continuita.score >= 50) return 'border-yellow-500'
  return 'border-red-500'
}

const getScoreLabel = () => {
  if (continuita.score >= 70) return 'Continuità solida'
  if (continuita.score >= 50) return 'Continuità a rischio'
  return 'Continuità critica'
}

const getChecklistScoreColor = () => {
  const ratio = checklistScore.value / checklist.value.length
  if (ratio >= 0.9) return 'text-green-600'
  if (ratio >= 0.7) return 'text-yellow-600'
  return 'text-red-600'
}

const formatCurrency = (value: number): string => {
  return new Intl.NumberFormat('it-IT', {
    style: 'currency',
    currency: 'EUR',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
  }).format(value)
}

const getBarWidth = (value: number, maxValue: number): number => {
  const max = Math.max(value, maxValue)
  return (value / max) * 100
}

const loadAziende = async () => {
  try {
    const token = localStorage.getItem('aa_token')
    const response = await $fetch<{ success: boolean; data: { aziende: Azienda[] } }>(
      `${config.public.apiBase}/auth/me`,
      {
        headers: {
          'X-API-Key': config.public.apiKey,
          'Authorization': `Bearer ${token}`,
          'Content-Type': 'application/json'
        }
      }
    )
    if (response.success && response.data.aziende) {
      aziende.value = response.data.aziende
      if (aziende.value.length > 0) {
        selectedAziendaId.value = aziende.value[0].id
      }
    }
  } catch (e) {
    console.error('Errore caricamento aziende:', e)
  }
}

const loadData = async () => {
  if (!selectedAziendaId.value) return

  loading.value = true

  // In produzione, qui faresti una chiamata API:
  // const response = await $fetch(`${config.public.apiBase}/continuita-score?azienda_id=${selectedAziendaId.value}`)
  // Object.assign(continuita, response.data)

  // Simulazione delay per mostrare loading
  await new Promise(resolve => setTimeout(resolve, 500))

  loading.value = false
}

const exportReport = () => {
  alert('Funzionalità in arrivo: esportazione report continuità in formato PDF')
}

onMounted(async () => {
  await loadAziende()
  await loadData()
})
</script>
