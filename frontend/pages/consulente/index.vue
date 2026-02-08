<template>
  <div class="space-y-6">
    <!-- Welcome & Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
      <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <div class="flex items-center gap-3">
          <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
            <Icon name="heroicons:building-office-2" class="w-6 h-6 text-purple-600" />
          </div>
          <div>
            <div class="text-2xl font-bold text-gray-900">{{ stats.totale_aziende }}</div>
            <div class="text-sm text-gray-500">Aziende Clienti</div>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <div class="flex items-center gap-3">
          <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
            <Icon name="heroicons:check-circle" class="w-6 h-6 text-green-600" />
          </div>
          <div>
            <div class="text-2xl font-bold text-green-600">{{ stats.kpi_verdi }}</div>
            <div class="text-sm text-gray-500">KPI in Regola</div>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <div class="flex items-center gap-3">
          <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
            <Icon name="heroicons:exclamation-triangle" class="w-6 h-6 text-yellow-600" />
          </div>
          <div>
            <div class="text-2xl font-bold text-yellow-600">{{ stats.kpi_gialli }}</div>
            <div class="text-sm text-gray-500">KPI Attenzione</div>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <div class="flex items-center gap-3">
          <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
            <Icon name="heroicons:x-circle" class="w-6 h-6 text-red-600" />
          </div>
          <div>
            <div class="text-2xl font-bold text-red-600">{{ stats.kpi_rossi }}</div>
            <div class="text-sm text-gray-500">KPI Critici</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
      <h2 class="text-lg font-semibold text-gray-900 mb-4">Azioni Rapide</h2>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <NuxtLink
          to="/consulente/aziende?action=new"
          class="flex items-center gap-3 p-4 rounded-lg border-2 border-dashed border-gray-200 hover:border-purple-500 hover:bg-purple-50 transition-all"
        >
          <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
            <Icon name="heroicons:plus" class="w-5 h-5 text-purple-600" />
          </div>
          <div>
            <div class="font-medium text-gray-900">Aggiungi Azienda</div>
            <div class="text-sm text-gray-500">Inserisci un nuovo cliente</div>
          </div>
        </NuxtLink>

        <NuxtLink
          to="/consulente/inviti"
          class="flex items-center gap-3 p-4 rounded-lg border-2 border-dashed border-gray-200 hover:border-purple-500 hover:bg-purple-50 transition-all"
        >
          <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
            <Icon name="heroicons:envelope" class="w-5 h-5 text-blue-600" />
          </div>
          <div>
            <div class="font-medium text-gray-900">Invita Cliente</div>
            <div class="text-sm text-gray-500">Invia accesso readonly</div>
          </div>
        </NuxtLink>

        <NuxtLink
          to="/consulente/report"
          class="flex items-center gap-3 p-4 rounded-lg border-2 border-dashed border-gray-200 hover:border-purple-500 hover:bg-purple-50 transition-all"
        >
          <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
            <Icon name="heroicons:document-arrow-down" class="w-5 h-5 text-green-600" />
          </div>
          <div>
            <div class="font-medium text-gray-900">Genera Report</div>
            <div class="text-sm text-gray-500">Export PDF/Excel</div>
          </div>
        </NuxtLink>
      </div>
    </div>

    <!-- Aziende con problemi -->
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
      <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-semibold text-gray-900">Aziende che Richiedono Attenzione</h2>
        <NuxtLink to="/consulente/aziende" class="text-purple-600 text-sm font-medium hover:text-purple-700">
          Vedi tutte →
        </NuxtLink>
      </div>

      <div v-if="aziendeProblematiche.length === 0" class="text-center py-8">
        <Icon name="heroicons:check-badge" class="w-12 h-12 text-green-500 mx-auto mb-3" />
        <p class="text-gray-600">Nessuna azienda con KPI critici</p>
      </div>

      <div v-else class="space-y-3">
        <div
          v-for="azienda in aziendeProblematiche"
          :key="azienda.id"
          class="flex items-center justify-between p-4 rounded-lg border border-gray-100 hover:bg-gray-50"
        >
          <div class="flex items-center gap-3">
            <div
              class="w-10 h-10 rounded-lg flex items-center justify-center"
              :class="azienda.stato === 'rosso' ? 'bg-red-100' : 'bg-yellow-100'"
            >
              <Icon
                :name="azienda.stato === 'rosso' ? 'heroicons:exclamation-circle' : 'heroicons:exclamation-triangle'"
                class="w-5 h-5"
                :class="azienda.stato === 'rosso' ? 'text-red-600' : 'text-yellow-600'"
              />
            </div>
            <div>
              <div class="font-medium text-gray-900">{{ azienda.nome }}</div>
              <div class="text-sm text-gray-500">{{ azienda.kpi_problema }}</div>
            </div>
          </div>
          <NuxtLink
            :to="`/consulente/aziende/${azienda.id}`"
            class="text-purple-600 text-sm font-medium hover:text-purple-700"
          >
            Dettagli →
          </NuxtLink>
        </div>
      </div>
    </div>

    <!-- Inviti pendenti -->
    <div v-if="invitiPendenti.length > 0" class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
      <h2 class="text-lg font-semibold text-gray-900 mb-4">Inviti in Attesa</h2>
      <div class="space-y-3">
        <div
          v-for="invito in invitiPendenti"
          :key="invito.id"
          class="flex items-center justify-between p-4 rounded-lg bg-blue-50 border border-blue-100"
        >
          <div class="flex items-center gap-3">
            <Icon name="heroicons:envelope" class="w-5 h-5 text-blue-600" />
            <div>
              <div class="font-medium text-gray-900">{{ invito.email }}</div>
              <div class="text-sm text-gray-500">{{ invito.azienda_nome }} - Inviato il {{ formatDate(invito.created_at) }}</div>
            </div>
          </div>
          <span class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded-full">In attesa</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  layout: 'consulente'
})

const config = useRuntimeConfig()

const stats = ref({
  totale_aziende: 0,
  kpi_verdi: 0,
  kpi_gialli: 0,
  kpi_rossi: 0,
  inviti_inviati: 0
})

const aziendeProblematiche = ref<any[]>([])
const invitiPendenti = ref<any[]>([])

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('it-IT', { day: '2-digit', month: 'short' })
}

const loadData = async () => {
  const token = localStorage.getItem('aa_token')
  if (!token) return

  try {
    // Load stats
    const statsRes = await $fetch<{ success: boolean; data: any }>(`${config.public.apiBase}/studio/stats`, {
      headers: {
        'Authorization': `Bearer ${token}`,
        'X-API-Key': config.public.apiKey
      }
    })

    if (statsRes.success) {
      stats.value = statsRes.data
    }

    // Load aziende with problems
    const aziendeRes = await $fetch<{ success: boolean; data: any[] }>(`${config.public.apiBase}/aziende-cliente`, {
      headers: {
        'Authorization': `Bearer ${token}`,
        'X-API-Key': config.public.apiKey
      }
    })

    if (aziendeRes.success) {
      // Filter aziende with red or yellow KPIs
      aziendeProblematiche.value = aziendeRes.data
        .filter((a: any) => a.kpi?.some((k: any) => k.stato === 'rosso' || k.stato === 'giallo'))
        .slice(0, 5)
        .map((a: any) => {
          const problemaKpi = a.kpi?.find((k: any) => k.stato === 'rosso') || a.kpi?.find((k: any) => k.stato === 'giallo')
          return {
            ...a,
            stato: problemaKpi?.stato || 'giallo',
            kpi_problema: problemaKpi ? `${problemaKpi.nome}: ${problemaKpi.valore}` : 'KPI da verificare'
          }
        })
    }

    // Load pending invites
    const invitiRes = await $fetch<{ success: boolean; data: any[] }>(`${config.public.apiBase}/inviti`, {
      headers: {
        'Authorization': `Bearer ${token}`,
        'X-API-Key': config.public.apiKey
      }
    })

    if (invitiRes.success) {
      invitiPendenti.value = invitiRes.data.filter((i: any) => i.stato === 'pending').slice(0, 3)
    }
  } catch (e) {
    console.error('Error loading dashboard data:', e)
  }
}

onMounted(loadData)
</script>
