<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">KPI Aziendali</h1>
        <p class="text-gray-600 mt-1">Indicatori di performance secondo Art. 2086 c.c.</p>
      </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
      <div class="flex items-center gap-4">
        <span class="text-sm font-medium text-gray-700">Filtra per stato:</span>
        <div class="flex gap-2">
          <button
            @click="filter = 'tutti'"
            class="px-3 py-1 rounded-full text-sm font-medium transition"
            :class="filter === 'tutti' ? 'bg-slate-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
          >
            Tutti
          </button>
          <button
            @click="filter = 'verde'"
            class="px-3 py-1 rounded-full text-sm font-medium transition"
            :class="filter === 'verde' ? 'bg-green-600 text-white' : 'bg-green-100 text-green-600 hover:bg-green-200'"
          >
            In Regola
          </button>
          <button
            @click="filter = 'giallo'"
            class="px-3 py-1 rounded-full text-sm font-medium transition"
            :class="filter === 'giallo' ? 'bg-yellow-500 text-white' : 'bg-yellow-100 text-yellow-600 hover:bg-yellow-200'"
          >
            Attenzione
          </button>
          <button
            @click="filter = 'rosso'"
            class="px-3 py-1 rounded-full text-sm font-medium transition"
            :class="filter === 'rosso' ? 'bg-red-600 text-white' : 'bg-red-100 text-red-600 hover:bg-red-200'"
          >
            Critici
          </button>
        </div>
      </div>
    </div>

    <!-- KPI Grid -->
    <div v-if="filteredKpi.length === 0" class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
      <Icon name="heroicons:chart-bar" class="w-16 h-16 text-gray-300 mx-auto mb-4" />
      <p class="text-gray-500 text-lg">Nessun KPI trovato</p>
      <p class="text-sm text-gray-400 mt-1">
        {{ filter !== 'tutti' ? 'Prova a cambiare il filtro' : 'I dati non sono ancora disponibili' }}
      </p>
    </div>

    <div v-else class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <div
        v-for="kpi in filteredKpi"
        :key="kpi.codice"
        class="bg-white rounded-xl shadow-sm border border-gray-100 p-6"
      >
        <div class="flex items-start justify-between mb-4">
          <div class="flex items-center gap-3">
            <div
              class="w-12 h-12 rounded-lg flex items-center justify-center"
              :class="{
                'bg-green-100': kpi.stato === 'verde',
                'bg-yellow-100': kpi.stato === 'giallo',
                'bg-red-100': kpi.stato === 'rosso'
              }"
            >
              <Icon
                :name="kpi.stato === 'verde' ? 'heroicons:check-circle' : kpi.stato === 'giallo' ? 'heroicons:exclamation-triangle' : 'heroicons:x-circle'"
                class="w-6 h-6"
                :class="{
                  'text-green-600': kpi.stato === 'verde',
                  'text-yellow-600': kpi.stato === 'giallo',
                  'text-red-600': kpi.stato === 'rosso'
                }"
              />
            </div>
            <div>
              <h3 class="font-semibold text-gray-900">{{ kpi.nome }}</h3>
              <p class="text-sm text-gray-500">{{ kpi.codice }}</p>
            </div>
          </div>
          <span
            class="text-xs px-2 py-1 rounded-full"
            :class="{
              'bg-green-100 text-green-700': kpi.stato === 'verde',
              'bg-yellow-100 text-yellow-700': kpi.stato === 'giallo',
              'bg-red-100 text-red-700': kpi.stato === 'rosso'
            }"
          >
            {{ kpi.stato === 'verde' ? 'In Regola' : kpi.stato === 'giallo' ? 'Attenzione' : 'Critico' }}
          </span>
        </div>

        <p class="text-sm text-gray-600 mb-4">{{ kpi.descrizione }}</p>

        <div class="flex items-end justify-between">
          <div>
            <div class="text-sm text-gray-500 mb-1">Valore Attuale</div>
            <div
              class="text-3xl font-bold"
              :class="{
                'text-green-600': kpi.stato === 'verde',
                'text-yellow-600': kpi.stato === 'giallo',
                'text-red-600': kpi.stato === 'rosso'
              }"
            >
              {{ kpi.valore }}
            </div>
          </div>
          <div class="text-right">
            <div class="text-sm text-gray-500 mb-1">Soglia</div>
            <div class="text-lg text-gray-700">{{ kpi.soglia }}</div>
          </div>
        </div>

        <!-- Progress Bar -->
        <div class="mt-4">
          <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
            <div
              class="h-full rounded-full transition-all duration-500"
              :class="{
                'bg-green-500': kpi.stato === 'verde',
                'bg-yellow-500': kpi.stato === 'giallo',
                'bg-red-500': kpi.stato === 'rosso'
              }"
              :style="{ width: `${Math.min(100, kpi.percentuale || 0)}%` }"
            ></div>
          </div>
        </div>

        <!-- Trend -->
        <div v-if="kpi.trend" class="mt-4 flex items-center gap-2">
          <Icon
            :name="kpi.trend > 0 ? 'heroicons:arrow-trending-up' : kpi.trend < 0 ? 'heroicons:arrow-trending-down' : 'heroicons:minus'"
            class="w-4 h-4"
            :class="{
              'text-green-600': kpi.trend > 0,
              'text-red-600': kpi.trend < 0,
              'text-gray-400': kpi.trend === 0
            }"
          />
          <span class="text-sm" :class="{
            'text-green-600': kpi.trend > 0,
            'text-red-600': kpi.trend < 0,
            'text-gray-500': kpi.trend === 0
          }">
            {{ kpi.trend > 0 ? '+' : '' }}{{ kpi.trend }}% rispetto al mese precedente
          </span>
        </div>
      </div>
    </div>

    <!-- Legend -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
      <h3 class="font-semibold text-gray-900 mb-4">Legenda KPI CNDCEC</h3>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 text-sm">
        <div class="flex items-start gap-2">
          <div class="w-6 h-6 bg-green-100 rounded flex items-center justify-center flex-shrink-0">
            <Icon name="heroicons:check" class="w-4 h-4 text-green-600" />
          </div>
          <div>
            <div class="font-medium text-gray-900">In Regola</div>
            <div class="text-gray-500">Il valore rispetta i parametri normativi</div>
          </div>
        </div>
        <div class="flex items-start gap-2">
          <div class="w-6 h-6 bg-yellow-100 rounded flex items-center justify-center flex-shrink-0">
            <Icon name="heroicons:exclamation-triangle" class="w-4 h-4 text-yellow-600" />
          </div>
          <div>
            <div class="font-medium text-gray-900">Attenzione</div>
            <div class="text-gray-500">Valore vicino alla soglia critica</div>
          </div>
        </div>
        <div class="flex items-start gap-2">
          <div class="w-6 h-6 bg-red-100 rounded flex items-center justify-center flex-shrink-0">
            <Icon name="heroicons:x-mark" class="w-4 h-4 text-red-600" />
          </div>
          <div>
            <div class="font-medium text-gray-900">Critico</div>
            <div class="text-gray-500">Richiede intervento immediato</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Disclaimer -->
    <div class="bg-slate-50 rounded-lg p-4">
      <div class="flex items-start gap-3">
        <Icon name="heroicons:information-circle" class="w-5 h-5 text-slate-500 mt-0.5" />
        <p class="text-sm text-slate-600">
          Gli indicatori mostrati sono calcolati secondo le metodologie CNDCEC per l'adeguatezza degli assetti.
          Per qualsiasi chiarimento, contatta il tuo consulente.
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

const filter = ref('tutti')
const kpiList = ref<any[]>([])

const filteredKpi = computed(() => {
  if (filter.value === 'tutti') return kpiList.value
  return kpiList.value.filter(k => k.stato === filter.value)
})

const loadKpi = async () => {
  const token = localStorage.getItem('aa_token')
  if (!token) return

  try {
    const response = await $fetch<{ success: boolean; data: any[] }>(`${config.public.apiBase}/cliente/kpi`, {
      headers: {
        'Authorization': `Bearer ${token}`,
        'X-API-Key': config.public.apiKey
      }
    })

    if (response.success) {
      kpiList.value = response.data
    }
  } catch (e) {
    console.error('Error loading KPI:', e)
  }
}

onMounted(loadKpi)
</script>
