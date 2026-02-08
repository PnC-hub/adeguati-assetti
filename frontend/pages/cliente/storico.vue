<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Storico KPI</h1>
        <p class="text-gray-600 mt-1">Andamento degli indicatori nel tempo</p>
      </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
      <div class="flex flex-wrap items-center gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">KPI</label>
          <select
            v-model="selectedKpi"
            class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-slate-500 focus:border-slate-500 min-w-48"
          >
            <option value="">Tutti i KPI</option>
            <option v-for="kpi in kpiOptions" :key="kpi.codice" :value="kpi.codice">
              {{ kpi.nome }}
            </option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Periodo</label>
          <select
            v-model="selectedPeriod"
            class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-slate-500 focus:border-slate-500"
          >
            <option value="3">Ultimi 3 mesi</option>
            <option value="6">Ultimi 6 mesi</option>
            <option value="12">Ultimo anno</option>
            <option value="24">Ultimi 2 anni</option>
          </select>
        </div>
      </div>
    </div>

    <!-- No Data -->
    <div v-if="storicoData.length === 0" class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
      <Icon name="heroicons:clock" class="w-16 h-16 text-gray-300 mx-auto mb-4" />
      <p class="text-gray-500 text-lg">Nessun dato storico disponibile</p>
      <p class="text-sm text-gray-400 mt-1">I dati storici appariranno qui man mano che vengono inseriti</p>
    </div>

    <!-- Storico Grid -->
    <div v-else class="space-y-6">
      <!-- Summary Chart Placeholder -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Andamento nel Tempo</h2>
        <div class="h-64 bg-gray-50 rounded-lg flex items-center justify-center">
          <div class="text-center">
            <Icon name="heroicons:chart-bar" class="w-12 h-12 text-gray-300 mx-auto mb-2" />
            <p class="text-gray-500 text-sm">Grafico andamento KPI</p>
            <p class="text-xs text-gray-400 mt-1">{{ storicoData.length }} rilevazioni trovate</p>
          </div>
        </div>
      </div>

      <!-- Storico Table -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
          <h2 class="text-lg font-semibold text-gray-900">Dettaglio Rilevazioni</h2>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">KPI</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valore</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Soglia</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stato</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Variazione</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              <tr v-for="(item, index) in paginatedData" :key="index" class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ formatDate(item.data) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm font-medium text-gray-900">{{ item.kpi_nome }}</div>
                  <div class="text-xs text-gray-500">{{ item.kpi_codice }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                  {{ item.valore }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ item.soglia }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                    :class="{
                      'bg-green-100 text-green-800': item.stato === 'verde',
                      'bg-yellow-100 text-yellow-800': item.stato === 'giallo',
                      'bg-red-100 text-red-800': item.stato === 'rosso'
                    }"
                  >
                    <Icon
                      :name="item.stato === 'verde' ? 'heroicons:check-circle' : item.stato === 'giallo' ? 'heroicons:exclamation-triangle' : 'heroicons:x-circle'"
                      class="w-3 h-3 mr-1"
                    />
                    {{ item.stato === 'verde' ? 'In Regola' : item.stato === 'giallo' ? 'Attenzione' : 'Critico' }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div v-if="item.variazione !== null" class="flex items-center text-sm">
                    <Icon
                      :name="item.variazione > 0 ? 'heroicons:arrow-trending-up' : item.variazione < 0 ? 'heroicons:arrow-trending-down' : 'heroicons:minus'"
                      class="w-4 h-4 mr-1"
                      :class="{
                        'text-green-600': item.variazione > 0,
                        'text-red-600': item.variazione < 0,
                        'text-gray-400': item.variazione === 0
                      }"
                    />
                    <span
                      :class="{
                        'text-green-600': item.variazione > 0,
                        'text-red-600': item.variazione < 0,
                        'text-gray-500': item.variazione === 0
                      }"
                    >
                      {{ item.variazione > 0 ? '+' : '' }}{{ item.variazione }}%
                    </span>
                  </div>
                  <span v-else class="text-gray-400 text-sm">-</span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="totalPages > 1" class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
          <div class="text-sm text-gray-500">
            Mostrando {{ (currentPage - 1) * pageSize + 1 }}-{{ Math.min(currentPage * pageSize, storicoData.length) }} di {{ storicoData.length }} rilevazioni
          </div>
          <div class="flex gap-2">
            <button
              @click="currentPage--"
              :disabled="currentPage === 1"
              class="px-3 py-1 border border-gray-300 rounded-lg text-sm disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50"
            >
              Precedente
            </button>
            <button
              @click="currentPage++"
              :disabled="currentPage === totalPages"
              class="px-3 py-1 border border-gray-300 rounded-lg text-sm disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50"
            >
              Successivo
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Export Note -->
    <div class="bg-slate-50 rounded-lg p-4">
      <div class="flex items-start gap-3">
        <Icon name="heroicons:information-circle" class="w-5 h-5 text-slate-500 mt-0.5" />
        <p class="text-sm text-slate-600">
          Per richiedere un export completo dello storico o analisi personalizzate, contatta il tuo consulente.
        </p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  layout: 'cliente'
})

const config = useRuntimeConfig()

const selectedKpi = ref('')
const selectedPeriod = ref('12')
const storicoData = ref<any[]>([])
const kpiOptions = ref<any[]>([])
const currentPage = ref(1)
const pageSize = 10

const totalPages = computed(() => Math.ceil(storicoData.value.length / pageSize))

const paginatedData = computed(() => {
  const start = (currentPage.value - 1) * pageSize
  return storicoData.value.slice(start, start + pageSize)
})

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('it-IT', {
    day: '2-digit',
    month: 'short',
    year: 'numeric'
  })
}

const loadStorico = async () => {
  const token = localStorage.getItem('aa_token')
  if (!token) return

  try {
    const response = await $fetch<{ success: boolean; data: any }>(`${config.public.apiBase}/cliente/storico`, {
      params: {
        kpi: selectedKpi.value || undefined,
        mesi: selectedPeriod.value
      },
      headers: {
        'Authorization': `Bearer ${token}`,
        'X-API-Key': config.public.apiKey
      }
    })

    if (response.success) {
      storicoData.value = response.data.storico || []
      kpiOptions.value = response.data.kpi_options || []
    }
  } catch (e) {
    console.error('Error loading storico:', e)
  }
}

watch([selectedKpi, selectedPeriod], () => {
  currentPage.value = 1
  loadStorico()
})

onMounted(loadStorico)
</script>
