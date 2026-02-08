<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Dati Economici</h1>
        <p class="text-gray-600 mt-1">Visualizzazione dati inseriti dal tuo consulente</p>
      </div>
    </div>

    <!-- Period Selector -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
      <div class="flex items-center gap-4">
        <span class="text-sm font-medium text-gray-700">Periodo:</span>
        <select
          v-model="selectedYear"
          class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-slate-500 focus:border-slate-500"
        >
          <option v-for="year in availableYears" :key="year" :value="year">{{ year }}</option>
        </select>
        <select
          v-model="selectedMonth"
          class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-slate-500 focus:border-slate-500"
        >
          <option v-for="month in 12" :key="month" :value="month">{{ monthNames[month - 1] }}</option>
        </select>
      </div>
    </div>

    <!-- No Data -->
    <div v-if="!datiEconomici" class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
      <Icon name="heroicons:document-chart-bar" class="w-16 h-16 text-gray-300 mx-auto mb-4" />
      <p class="text-gray-500 text-lg">Nessun dato disponibile per il periodo selezionato</p>
      <p class="text-sm text-gray-400 mt-1">Il tuo consulente non ha ancora inserito i dati economici</p>
    </div>

    <!-- Data Display -->
    <div v-else class="space-y-6">
      <!-- Summary Cards -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
          <div class="flex items-center gap-3">
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
              <Icon name="heroicons:banknotes" class="w-6 h-6 text-blue-600" />
            </div>
            <div>
              <div class="text-2xl font-bold text-blue-600">{{ formatCurrency(datiEconomici.ricavi) }}</div>
              <div class="text-sm text-gray-500">Ricavi</div>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
          <div class="flex items-center gap-3">
            <div class="w-12 h-12 bg-amber-100 rounded-lg flex items-center justify-center">
              <Icon name="heroicons:credit-card" class="w-6 h-6 text-amber-600" />
            </div>
            <div>
              <div class="text-2xl font-bold text-amber-600">{{ formatCurrency(datiEconomici.debiti_breve) }}</div>
              <div class="text-sm text-gray-500">Debiti a Breve</div>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
          <div class="flex items-center gap-3">
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
              <Icon name="heroicons:calculator" class="w-6 h-6 text-green-600" />
            </div>
            <div>
              <div class="text-2xl font-bold text-green-600">{{ formatCurrency(datiEconomici.patrimonio_netto) }}</div>
              <div class="text-sm text-gray-500">Patrimonio Netto</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Detailed Data -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-6">Dati di Bilancio</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- Attivo -->
          <div>
            <h3 class="text-sm font-medium text-gray-700 mb-4 uppercase tracking-wide">Attivo</h3>
            <div class="space-y-3">
              <div class="flex justify-between items-center py-2 border-b border-gray-100">
                <span class="text-gray-600">Attivo Circolante</span>
                <span class="font-medium text-gray-900">{{ formatCurrency(datiEconomici.attivo_circolante) }}</span>
              </div>
              <div class="flex justify-between items-center py-2 border-b border-gray-100">
                <span class="text-gray-600">Disponibilità Liquide</span>
                <span class="font-medium text-gray-900">{{ formatCurrency(datiEconomici.disponibilita_liquide) }}</span>
              </div>
              <div class="flex justify-between items-center py-2 border-b border-gray-100">
                <span class="text-gray-600">Crediti Commerciali</span>
                <span class="font-medium text-gray-900">{{ formatCurrency(datiEconomici.crediti_commerciali) }}</span>
              </div>
              <div class="flex justify-between items-center py-2 border-b border-gray-100">
                <span class="text-gray-600">Rimanenze</span>
                <span class="font-medium text-gray-900">{{ formatCurrency(datiEconomici.rimanenze) }}</span>
              </div>
              <div class="flex justify-between items-center py-2 border-b border-gray-100">
                <span class="text-gray-600">Attivo Immobilizzato</span>
                <span class="font-medium text-gray-900">{{ formatCurrency(datiEconomici.attivo_immobilizzato) }}</span>
              </div>
            </div>
          </div>

          <!-- Passivo -->
          <div>
            <h3 class="text-sm font-medium text-gray-700 mb-4 uppercase tracking-wide">Passivo</h3>
            <div class="space-y-3">
              <div class="flex justify-between items-center py-2 border-b border-gray-100">
                <span class="text-gray-600">Debiti a Breve</span>
                <span class="font-medium text-gray-900">{{ formatCurrency(datiEconomici.debiti_breve) }}</span>
              </div>
              <div class="flex justify-between items-center py-2 border-b border-gray-100">
                <span class="text-gray-600">Debiti vs Fornitori</span>
                <span class="font-medium text-gray-900">{{ formatCurrency(datiEconomici.debiti_fornitori) }}</span>
              </div>
              <div class="flex justify-between items-center py-2 border-b border-gray-100">
                <span class="text-gray-600">Debiti vs Banche</span>
                <span class="font-medium text-gray-900">{{ formatCurrency(datiEconomici.debiti_banche) }}</span>
              </div>
              <div class="flex justify-between items-center py-2 border-b border-gray-100">
                <span class="text-gray-600">Debiti a Lungo</span>
                <span class="font-medium text-gray-900">{{ formatCurrency(datiEconomici.debiti_lungo) }}</span>
              </div>
              <div class="flex justify-between items-center py-2 border-b border-gray-100">
                <span class="text-gray-600">Patrimonio Netto</span>
                <span class="font-medium text-gray-900">{{ formatCurrency(datiEconomici.patrimonio_netto) }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Conto Economico -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-6">Conto Economico</h2>

        <div class="space-y-3">
          <div class="flex justify-between items-center py-2 border-b border-gray-100">
            <span class="text-gray-600">Ricavi</span>
            <span class="font-medium text-green-600">{{ formatCurrency(datiEconomici.ricavi) }}</span>
          </div>
          <div class="flex justify-between items-center py-2 border-b border-gray-100">
            <span class="text-gray-600">Costi Operativi</span>
            <span class="font-medium text-red-600">-{{ formatCurrency(datiEconomici.costi_operativi) }}</span>
          </div>
          <div class="flex justify-between items-center py-2 border-b border-gray-100">
            <span class="text-gray-600">EBITDA</span>
            <span class="font-medium" :class="datiEconomici.ebitda >= 0 ? 'text-green-600' : 'text-red-600'">
              {{ formatCurrency(datiEconomici.ebitda) }}
            </span>
          </div>
          <div class="flex justify-between items-center py-2 border-b border-gray-100">
            <span class="text-gray-600">Ammortamenti</span>
            <span class="font-medium text-red-600">-{{ formatCurrency(datiEconomici.ammortamenti) }}</span>
          </div>
          <div class="flex justify-between items-center py-2 border-b border-gray-100">
            <span class="text-gray-600">Oneri Finanziari</span>
            <span class="font-medium text-red-600">-{{ formatCurrency(datiEconomici.oneri_finanziari) }}</span>
          </div>
          <div class="flex justify-between items-center py-3 bg-gray-50 rounded-lg px-3 mt-4">
            <span class="font-semibold text-gray-900">Utile Netto</span>
            <span class="font-bold text-xl" :class="datiEconomici.utile_netto >= 0 ? 'text-green-600' : 'text-red-600'">
              {{ formatCurrency(datiEconomici.utile_netto) }}
            </span>
          </div>
        </div>
      </div>
    </div>

    <!-- Last Update -->
    <div class="bg-slate-50 rounded-lg p-4 flex items-center gap-3">
      <Icon name="heroicons:clock" class="w-5 h-5 text-slate-400" />
      <span class="text-sm text-slate-600">
        Ultimo aggiornamento: {{ datiEconomici?.updated_at ? formatDate(datiEconomici.updated_at) : 'Non disponibile' }}
      </span>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  layout: 'cliente'
})

const config = useRuntimeConfig()

const currentDate = new Date()
const selectedYear = ref(currentDate.getFullYear())
const selectedMonth = ref(currentDate.getMonth() + 1)
const datiEconomici = ref<any>(null)

const monthNames = [
  'Gennaio', 'Febbraio', 'Marzo', 'Aprile', 'Maggio', 'Giugno',
  'Luglio', 'Agosto', 'Settembre', 'Ottobre', 'Novembre', 'Dicembre'
]

const availableYears = computed(() => {
  const years = []
  for (let y = currentDate.getFullYear(); y >= 2020; y--) {
    years.push(y)
  }
  return years
})

const formatCurrency = (value: number | null | undefined) => {
  if (value === null || value === undefined) return '€ 0'
  return new Intl.NumberFormat('it-IT', {
    style: 'currency',
    currency: 'EUR',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
  }).format(value)
}

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('it-IT', {
    day: '2-digit',
    month: 'long',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const loadDati = async () => {
  const token = localStorage.getItem('aa_token')
  if (!token) return

  try {
    const response = await $fetch<{ success: boolean; data: any }>(`${config.public.apiBase}/cliente/dati`, {
      params: {
        anno: selectedYear.value,
        mese: selectedMonth.value
      },
      headers: {
        'Authorization': `Bearer ${token}`,
        'X-API-Key': config.public.apiKey
      }
    })

    if (response.success) {
      datiEconomici.value = response.data
    }
  } catch (e) {
    console.error('Error loading dati economici:', e)
    datiEconomici.value = null
  }
}

watch([selectedYear, selectedMonth], loadDati)
onMounted(loadDati)
</script>
