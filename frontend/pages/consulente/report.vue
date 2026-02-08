<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Genera Report</h1>
        <p class="text-gray-600 mt-1">Esporta report PDF o Excel per i tuoi clienti</p>
      </div>
    </div>

    <!-- Report Types -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <!-- Report KPI -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center gap-4 mb-4">
          <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
            <Icon name="heroicons:chart-bar" class="w-6 h-6 text-purple-600" />
          </div>
          <div>
            <h3 class="font-semibold text-gray-900">Report KPI</h3>
            <p class="text-sm text-gray-500">Indicatori di performance</p>
          </div>
        </div>
        <p class="text-sm text-gray-600 mb-4">
          Report completo con tutti i 7 KPI obbligatori, soglie ATECO e analisi trend.
        </p>
        <button
          @click="openReportModal('kpi')"
          class="w-full bg-purple-600 text-white py-2 px-4 rounded-lg text-sm font-medium hover:bg-purple-700 transition"
        >
          Genera Report
        </button>
      </div>

      <!-- Report Compliance -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center gap-4 mb-4">
          <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
            <Icon name="heroicons:clipboard-document-check" class="w-6 h-6 text-green-600" />
          </div>
          <div>
            <h3 class="font-semibold text-gray-900">Report Compliance</h3>
            <p class="text-sm text-gray-500">Checklist Art. 2086</p>
          </div>
        </div>
        <p class="text-sm text-gray-600 mb-4">
          Stato conformità alla normativa con checklist dettagliata e raccomandazioni.
        </p>
        <button
          @click="openReportModal('compliance')"
          class="w-full bg-green-600 text-white py-2 px-4 rounded-lg text-sm font-medium hover:bg-green-700 transition"
        >
          Genera Report
        </button>
      </div>

      <!-- Report Riepilogo -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center gap-4 mb-4">
          <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
            <Icon name="heroicons:document-text" class="w-6 h-6 text-blue-600" />
          </div>
          <div>
            <h3 class="font-semibold text-gray-900">Riepilogo Mensile</h3>
            <p class="text-sm text-gray-500">Sintesi per il cliente</p>
          </div>
        </div>
        <p class="text-sm text-gray-600 mb-4">
          Report sintetico mensile con KPI principali e variazioni rispetto al mese precedente.
        </p>
        <button
          @click="openReportModal('riepilogo')"
          class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg text-sm font-medium hover:bg-blue-700 transition"
        >
          Genera Report
        </button>
      </div>

      <!-- Export Excel -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center gap-4 mb-4">
          <div class="w-12 h-12 bg-emerald-100 rounded-lg flex items-center justify-center">
            <Icon name="heroicons:table-cells" class="w-6 h-6 text-emerald-600" />
          </div>
          <div>
            <h3 class="font-semibold text-gray-900">Export Excel</h3>
            <p class="text-sm text-gray-500">Dati grezzi</p>
          </div>
        </div>
        <p class="text-sm text-gray-600 mb-4">
          Esporta tutti i dati economici e KPI in formato Excel per analisi personalizzate.
        </p>
        <button
          @click="openReportModal('excel')"
          class="w-full bg-emerald-600 text-white py-2 px-4 rounded-lg text-sm font-medium hover:bg-emerald-700 transition"
        >
          Export Excel
        </button>
      </div>

      <!-- Report Storico -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center gap-4 mb-4">
          <div class="w-12 h-12 bg-amber-100 rounded-lg flex items-center justify-center">
            <Icon name="heroicons:clock" class="w-6 h-6 text-amber-600" />
          </div>
          <div>
            <h3 class="font-semibold text-gray-900">Analisi Storica</h3>
            <p class="text-sm text-gray-500">Trend 12 mesi</p>
          </div>
        </div>
        <p class="text-sm text-gray-600 mb-4">
          Analisi dell'andamento storico con grafici e previsioni basate sui trend.
        </p>
        <button
          @click="openReportModal('storico')"
          class="w-full bg-amber-600 text-white py-2 px-4 rounded-lg text-sm font-medium hover:bg-amber-700 transition"
        >
          Genera Report
        </button>
      </div>

      <!-- Report Multiplo -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center gap-4 mb-4">
          <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
            <Icon name="heroicons:squares-2x2" class="w-6 h-6 text-indigo-600" />
          </div>
          <div>
            <h3 class="font-semibold text-gray-900">Report Multiplo</h3>
            <p class="text-sm text-gray-500">Tutte le aziende</p>
          </div>
        </div>
        <p class="text-sm text-gray-600 mb-4">
          Report aggregato con confronto tra tutte le aziende clienti del tuo studio.
        </p>
        <button
          @click="openReportModal('multiplo')"
          class="w-full bg-indigo-600 text-white py-2 px-4 rounded-lg text-sm font-medium hover:bg-indigo-700 transition"
        >
          Genera Report
        </button>
      </div>
    </div>

    <!-- Report History -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
      <h2 class="text-lg font-semibold text-gray-900 mb-4">Report Generati Recentemente</h2>

      <div v-if="reportHistory.length === 0" class="text-center py-8">
        <Icon name="heroicons:document" class="w-12 h-12 text-gray-300 mx-auto mb-3" />
        <p class="text-gray-500">Nessun report generato</p>
      </div>

      <div v-else class="overflow-x-auto">
        <table class="min-w-full">
          <thead>
            <tr class="border-b border-gray-100">
              <th class="text-left py-3 px-4 text-sm font-medium text-gray-500">Tipo</th>
              <th class="text-left py-3 px-4 text-sm font-medium text-gray-500">Azienda</th>
              <th class="text-left py-3 px-4 text-sm font-medium text-gray-500">Periodo</th>
              <th class="text-left py-3 px-4 text-sm font-medium text-gray-500">Formato</th>
              <th class="text-left py-3 px-4 text-sm font-medium text-gray-500">Data</th>
              <th class="text-right py-3 px-4 text-sm font-medium text-gray-500">Azioni</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="report in reportHistory" :key="report.id" class="border-b border-gray-50 hover:bg-gray-50">
              <td class="py-3 px-4">
                <span class="text-sm font-medium text-gray-900">{{ report.tipo }}</span>
              </td>
              <td class="py-3 px-4">
                <span class="text-sm text-gray-600">{{ report.azienda }}</span>
              </td>
              <td class="py-3 px-4">
                <span class="text-sm text-gray-600">{{ report.periodo }}</span>
              </td>
              <td class="py-3 px-4">
                <span
                  class="text-xs px-2 py-1 rounded-full"
                  :class="report.formato === 'PDF' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700'"
                >
                  {{ report.formato }}
                </span>
              </td>
              <td class="py-3 px-4">
                <span class="text-sm text-gray-600">{{ formatDate(report.created_at) }}</span>
              </td>
              <td class="py-3 px-4 text-right">
                <button
                  @click="downloadReport(report)"
                  class="text-purple-600 hover:text-purple-700 text-sm font-medium"
                >
                  Scarica
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Report Modal -->
    <div v-if="showReportModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
      <div class="bg-white rounded-xl shadow-xl max-w-md w-full mx-4 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">
          {{ reportTypeLabels[selectedReportType] }}
        </h3>

        <div class="space-y-4">
          <!-- Azienda Selection -->
          <div v-if="selectedReportType !== 'multiplo'">
            <label class="block text-sm font-medium text-gray-700 mb-1">Azienda</label>
            <select
              v-model="reportForm.azienda_id"
              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
            >
              <option value="">Seleziona azienda</option>
              <option v-for="azienda in aziende" :key="azienda.id" :value="azienda.id">
                {{ azienda.nome }}
              </option>
            </select>
          </div>

          <!-- Periodo -->
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Da</label>
              <input
                v-model="reportForm.data_inizio"
                type="date"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">A</label>
              <input
                v-model="reportForm.data_fine"
                type="date"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
              />
            </div>
          </div>

          <!-- Format -->
          <div v-if="selectedReportType !== 'excel'">
            <label class="block text-sm font-medium text-gray-700 mb-1">Formato</label>
            <div class="flex gap-4">
              <label class="flex items-center gap-2 cursor-pointer">
                <input
                  v-model="reportForm.formato"
                  type="radio"
                  value="PDF"
                  class="text-purple-600 focus:ring-purple-500"
                />
                <span class="text-sm text-gray-700">PDF</span>
              </label>
              <label class="flex items-center gap-2 cursor-pointer">
                <input
                  v-model="reportForm.formato"
                  type="radio"
                  value="Excel"
                  class="text-purple-600 focus:ring-purple-500"
                />
                <span class="text-sm text-gray-700">Excel</span>
              </label>
            </div>
          </div>

          <!-- Include Logo -->
          <label class="flex items-center gap-2 cursor-pointer">
            <input
              v-model="reportForm.include_logo"
              type="checkbox"
              class="text-purple-600 focus:ring-purple-500 rounded"
            />
            <span class="text-sm text-gray-700">Includi logo studio</span>
          </label>
        </div>

        <div class="flex gap-3 mt-6">
          <button
            @click="showReportModal = false"
            class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition"
          >
            Annulla
          </button>
          <button
            @click="generateReport"
            :disabled="generating"
            class="flex-1 bg-purple-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-purple-700 disabled:opacity-50 transition"
          >
            <span v-if="generating">Generando...</span>
            <span v-else>Genera</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Toast -->
    <div
      v-if="toast.show"
      class="fixed bottom-4 right-4 px-4 py-3 rounded-lg shadow-lg z-50"
      :class="toast.type === 'success' ? 'bg-green-600 text-white' : 'bg-red-600 text-white'"
    >
      {{ toast.message }}
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  layout: 'consulente'
})

const config = useRuntimeConfig()

const showReportModal = ref(false)
const selectedReportType = ref('')
const generating = ref(false)
const aziende = ref<any[]>([])
const reportHistory = ref<any[]>([])

const reportTypeLabels: Record<string, string> = {
  kpi: 'Report KPI',
  compliance: 'Report Compliance',
  riepilogo: 'Riepilogo Mensile',
  excel: 'Export Excel',
  storico: 'Analisi Storica',
  multiplo: 'Report Multiplo'
}

const reportForm = ref({
  azienda_id: '',
  data_inizio: new Date(new Date().getFullYear(), new Date().getMonth(), 1).toISOString().split('T')[0],
  data_fine: new Date().toISOString().split('T')[0],
  formato: 'PDF',
  include_logo: true
})

const toast = ref({
  show: false,
  message: '',
  type: 'success'
})

const showToast = (message: string, type: 'success' | 'error' = 'success') => {
  toast.value = { show: true, message, type }
  setTimeout(() => { toast.value.show = false }, 3000)
}

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('it-IT', { day: '2-digit', month: 'short', year: 'numeric' })
}

const openReportModal = (type: string) => {
  selectedReportType.value = type
  if (type === 'excel') {
    reportForm.value.formato = 'Excel'
  } else {
    reportForm.value.formato = 'PDF'
  }
  showReportModal.value = true
}

const generateReport = async () => {
  if (selectedReportType.value !== 'multiplo' && !reportForm.value.azienda_id) {
    showToast('Seleziona un\'azienda', 'error')
    return
  }

  generating.value = true
  const token = localStorage.getItem('aa_token')

  try {
    const response = await $fetch<{ success: boolean; data: any }>(`${config.public.apiBase}/report/genera`, {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${token}`,
        'X-API-Key': config.public.apiKey,
        'Content-Type': 'application/json'
      },
      body: {
        tipo: selectedReportType.value,
        ...reportForm.value
      }
    })

    if (response.success) {
      showToast('Report generato con successo!')
      showReportModal.value = false
      // Reload history
      loadReportHistory()
      // Download file
      if (response.data.download_url) {
        window.open(response.data.download_url, '_blank')
      }
    }
  } catch (e: any) {
    showToast(e.data?.message || 'Errore nella generazione', 'error')
  } finally {
    generating.value = false
  }
}

const downloadReport = (report: any) => {
  if (report.download_url) {
    window.open(report.download_url, '_blank')
  } else {
    showToast('File non disponibile', 'error')
  }
}

const loadAziende = async () => {
  const token = localStorage.getItem('aa_token')
  if (!token) return

  try {
    const response = await $fetch<{ success: boolean; data: any[] }>(`${config.public.apiBase}/aziende-cliente`, {
      headers: {
        'Authorization': `Bearer ${token}`,
        'X-API-Key': config.public.apiKey
      }
    })

    if (response.success) {
      aziende.value = response.data
    }
  } catch (e) {
    console.error('Error loading aziende:', e)
  }
}

const loadReportHistory = async () => {
  const token = localStorage.getItem('aa_token')
  if (!token) return

  try {
    const response = await $fetch<{ success: boolean; data: any[] }>(`${config.public.apiBase}/report/history`, {
      headers: {
        'Authorization': `Bearer ${token}`,
        'X-API-Key': config.public.apiKey
      }
    })

    if (response.success) {
      reportHistory.value = response.data
    }
  } catch (e) {
    console.error('Error loading report history:', e)
  }
}

onMounted(() => {
  loadAziende()
  loadReportHistory()
})
</script>
