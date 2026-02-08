<template>
  <div class="space-y-6">
    <!-- Welcome -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
      <div class="flex items-center gap-4">
        <div class="w-14 h-14 bg-slate-100 rounded-lg flex items-center justify-center">
          <Icon name="heroicons:building-office-2" class="w-7 h-7 text-slate-600" />
        </div>
        <div>
          <h1 class="text-xl font-bold text-gray-900">{{ azienda?.nome || 'La tua azienda' }}</h1>
          <p class="text-gray-500">Dashboard di monitoraggio assetti organizzativi</p>
        </div>
      </div>
    </div>

    <!-- Health Score -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
      <h2 class="text-lg font-semibold text-gray-900 mb-4">Stato di Salute Aziendale</h2>

      <div class="flex items-center gap-8">
        <div class="relative w-32 h-32">
          <svg class="w-full h-full transform -rotate-90" viewBox="0 0 100 100">
            <circle
              cx="50"
              cy="50"
              r="40"
              stroke="#e5e7eb"
              stroke-width="12"
              fill="none"
            />
            <circle
              cx="50"
              cy="50"
              r="40"
              :stroke="healthScoreColor"
              stroke-width="12"
              fill="none"
              :stroke-dasharray="`${healthScore * 2.51} 251`"
              stroke-linecap="round"
            />
          </svg>
          <div class="absolute inset-0 flex items-center justify-center">
            <span class="text-3xl font-bold" :class="healthScoreTextColor">{{ healthScore }}</span>
          </div>
        </div>

        <div class="flex-1">
          <div class="text-2xl font-bold" :class="healthScoreTextColor">{{ healthScoreLabel }}</div>
          <p class="text-gray-600 mt-1">
            {{ healthScoreDescription }}
          </p>
        </div>
      </div>
    </div>

    <!-- KPI Summary -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center gap-3">
          <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
            <Icon name="heroicons:check-circle" class="w-6 h-6 text-green-600" />
          </div>
          <div>
            <div class="text-2xl font-bold text-green-600">{{ kpiStats.verdi }}</div>
            <div class="text-sm text-gray-500">KPI in Regola</div>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center gap-3">
          <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
            <Icon name="heroicons:exclamation-triangle" class="w-6 h-6 text-yellow-600" />
          </div>
          <div>
            <div class="text-2xl font-bold text-yellow-600">{{ kpiStats.gialli }}</div>
            <div class="text-sm text-gray-500">KPI Attenzione</div>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center gap-3">
          <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
            <Icon name="heroicons:x-circle" class="w-6 h-6 text-red-600" />
          </div>
          <div>
            <div class="text-2xl font-bold text-red-600">{{ kpiStats.rossi }}</div>
            <div class="text-sm text-gray-500">KPI Critici</div>
          </div>
        </div>
      </div>
    </div>

    <!-- KPI List -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
      <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-semibold text-gray-900">Indicatori KPI Obbligatori</h2>
        <NuxtLink to="/cliente/kpi" class="text-slate-600 text-sm font-medium hover:text-slate-700">
          Vedi dettagli →
        </NuxtLink>
      </div>

      <div v-if="kpiList.length === 0" class="text-center py-8">
        <Icon name="heroicons:chart-bar" class="w-12 h-12 text-gray-300 mx-auto mb-3" />
        <p class="text-gray-500">Nessun KPI disponibile</p>
        <p class="text-sm text-gray-400 mt-1">Il tuo consulente sta elaborando i dati</p>
      </div>

      <div v-else class="space-y-3">
        <div
          v-for="kpi in kpiList"
          :key="kpi.codice"
          class="flex items-center justify-between p-4 rounded-lg border"
          :class="{
            'border-green-200 bg-green-50': kpi.stato === 'verde',
            'border-yellow-200 bg-yellow-50': kpi.stato === 'giallo',
            'border-red-200 bg-red-50': kpi.stato === 'rosso'
          }"
        >
          <div class="flex items-center gap-3">
            <div
              class="w-10 h-10 rounded-lg flex items-center justify-center"
              :class="{
                'bg-green-100': kpi.stato === 'verde',
                'bg-yellow-100': kpi.stato === 'giallo',
                'bg-red-100': kpi.stato === 'rosso'
              }"
            >
              <Icon
                :name="kpi.stato === 'verde' ? 'heroicons:check' : kpi.stato === 'giallo' ? 'heroicons:exclamation-triangle' : 'heroicons:x-mark'"
                class="w-5 h-5"
                :class="{
                  'text-green-600': kpi.stato === 'verde',
                  'text-yellow-600': kpi.stato === 'giallo',
                  'text-red-600': kpi.stato === 'rosso'
                }"
              />
            </div>
            <div>
              <div class="font-medium text-gray-900">{{ kpi.nome }}</div>
              <div class="text-sm text-gray-500">{{ kpi.descrizione }}</div>
            </div>
          </div>
          <div class="text-right">
            <div
              class="text-xl font-bold"
              :class="{
                'text-green-600': kpi.stato === 'verde',
                'text-yellow-600': kpi.stato === 'giallo',
                'text-red-600': kpi.stato === 'rosso'
              }"
            >
              {{ kpi.valore }}
            </div>
            <div class="text-xs text-gray-500">
              Soglia: {{ kpi.soglia }}
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Last Update -->
    <div class="bg-slate-50 rounded-lg p-4 flex items-center gap-3">
      <Icon name="heroicons:clock" class="w-5 h-5 text-slate-400" />
      <span class="text-sm text-slate-600">
        Ultimo aggiornamento: {{ lastUpdate ? formatDate(lastUpdate) : 'Non disponibile' }}
      </span>
    </div>

    <!-- Contact Consulente -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
          <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
            <Icon name="heroicons:user-circle" class="w-6 h-6 text-purple-600" />
          </div>
          <div>
            <div class="font-medium text-gray-900">{{ studio?.nome || 'Il tuo consulente' }}</div>
            <div class="text-sm text-gray-500">{{ studio?.email }}</div>
          </div>
        </div>
        <a
          v-if="studio?.email"
          :href="`mailto:${studio.email}`"
          class="bg-purple-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-purple-700 transition"
        >
          Contatta
        </a>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  layout: 'cliente'
})

const config = useRuntimeConfig()

const azienda = ref<any>(null)
const studio = ref<any>(null)
const kpiList = ref<any[]>([])
const lastUpdate = ref('')

const kpiStats = computed(() => ({
  verdi: kpiList.value.filter(k => k.stato === 'verde').length,
  gialli: kpiList.value.filter(k => k.stato === 'giallo').length,
  rossi: kpiList.value.filter(k => k.stato === 'rosso').length
}))

const healthScore = computed(() => {
  const total = kpiList.value.length
  if (total === 0) return 0
  const verdi = kpiStats.value.verdi
  const gialli = kpiStats.value.gialli
  return Math.round((verdi * 100 + gialli * 50) / total)
})

const healthScoreColor = computed(() => {
  if (healthScore.value >= 80) return '#22c55e'
  if (healthScore.value >= 50) return '#eab308'
  return '#ef4444'
})

const healthScoreTextColor = computed(() => {
  if (healthScore.value >= 80) return 'text-green-600'
  if (healthScore.value >= 50) return 'text-yellow-600'
  return 'text-red-600'
})

const healthScoreLabel = computed(() => {
  if (healthScore.value >= 80) return 'Buono'
  if (healthScore.value >= 50) return 'Attenzione'
  return 'Critico'
})

const healthScoreDescription = computed(() => {
  if (healthScore.value >= 80) return 'La tua azienda ha assetti adeguati secondo i parametri Art. 2086 c.c.'
  if (healthScore.value >= 50) return 'Alcuni indicatori richiedono attenzione. Contatta il tuo consulente.'
  return 'Situazione critica. È necessario un intervento urgente con il tuo consulente.'
})

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('it-IT', {
    day: '2-digit',
    month: 'long',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const loadData = async () => {
  const token = localStorage.getItem('aa_token')
  if (!token) return

  try {
    const response = await $fetch<{ success: boolean; data: any }>(`${config.public.apiBase}/cliente/dashboard`, {
      headers: {
        'Authorization': `Bearer ${token}`,
        'X-API-Key': config.public.apiKey
      }
    })

    if (response.success) {
      azienda.value = response.data.azienda
      studio.value = response.data.studio
      kpiList.value = response.data.kpi || []
      lastUpdate.value = response.data.last_update
    }
  } catch (e) {
    console.error('Error loading dashboard:', e)
  }
}

onMounted(loadData)
</script>
