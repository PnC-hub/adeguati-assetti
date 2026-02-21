<template>
  <div class="space-y-6">
    <!-- Header -->
    <div>
      <h1 class="text-2xl font-bold text-gray-900">Crediti & Guadagni</h1>
      <p class="text-gray-500 mt-1">Il 20% di ogni cliente pagante ti viene riconosciuto mensilmente</p>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="flex items-center justify-center py-12">
      <Icon name="heroicons:arrow-path" class="w-8 h-8 text-purple-600 animate-spin" />
    </div>

    <template v-else>
      <!-- Summary Cards -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
          <div class="flex items-center gap-3">
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
              <Icon name="heroicons:banknotes" class="w-6 h-6 text-green-600" />
            </div>
            <div>
              <div class="text-2xl font-bold text-green-600">&euro;{{ riepilogo.totale_guadagnato }}</div>
              <div class="text-sm text-gray-500">Totale Guadagnato</div>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
          <div class="flex items-center gap-3">
            <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
              <Icon name="heroicons:clock" class="w-6 h-6 text-yellow-600" />
            </div>
            <div>
              <div class="text-2xl font-bold text-yellow-600">&euro;{{ riepilogo.totale_pending }}</div>
              <div class="text-sm text-gray-500">In Attesa</div>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
          <div class="flex items-center gap-3">
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
              <Icon name="heroicons:check-badge" class="w-6 h-6 text-blue-600" />
            </div>
            <div>
              <div class="text-2xl font-bold text-blue-600">&euro;{{ riepilogo.totale_pagato }}</div>
              <div class="text-sm text-gray-500">Pagato</div>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
          <div class="flex items-center gap-3">
            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
              <Icon name="heroicons:users" class="w-6 h-6 text-purple-600" />
            </div>
            <div>
              <div class="text-2xl font-bold text-purple-600">{{ riepilogo.clienti_attivi }}</div>
              <div class="text-sm text-gray-500">Clienti Attivi</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Monthly Breakdown -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="p-6 border-b border-gray-100">
          <h2 class="text-lg font-semibold text-gray-900">Dettaglio Mensile</h2>
        </div>

        <div v-if="riepilogo.mesi.length === 0" class="p-12 text-center">
          <Icon name="heroicons:currency-euro" class="w-16 h-16 text-gray-300 mx-auto mb-4" />
          <h3 class="text-lg font-medium text-gray-900 mb-2">Nessun credito ancora</h3>
          <p class="text-gray-500">I crediti verranno calcolati automaticamente ogni mese per ogni cliente con abbonamento attivo.</p>
        </div>

        <div v-else class="divide-y divide-gray-100">
          <div
            v-for="mese in riepilogo.mesi"
            :key="`${mese.anno}-${mese.mese}`"
            class="p-4 hover:bg-gray-50 cursor-pointer"
            @click="toggleMese(mese)"
          >
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-4">
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                  <Icon name="heroicons:calendar" class="w-5 h-5 text-purple-600" />
                </div>
                <div>
                  <div class="font-medium text-gray-900">{{ getMeseLabel(mese.mese) }} {{ mese.anno }}</div>
                  <div class="text-sm text-gray-500">{{ mese.numero_clienti }} clienti</div>
                </div>
              </div>
              <div class="flex items-center gap-4">
                <div class="text-right">
                  <div class="font-bold text-gray-900">&euro;{{ mese.totale }}</div>
                  <span
                    class="text-xs px-2 py-1 rounded-full"
                    :class="getStatoClass(mese.stato_prevalente)"
                  >
                    {{ getStatoLabel(mese.stato_prevalente) }}
                  </span>
                </div>
                <Icon
                  name="heroicons:chevron-down"
                  class="w-5 h-5 text-gray-400 transition-transform"
                  :class="{ 'rotate-180': expandedMese === `${mese.anno}-${mese.mese}` }"
                />
              </div>
            </div>

            <!-- Expanded Detail -->
            <div v-if="expandedMese === `${mese.anno}-${mese.mese}`" class="mt-4 pl-14 space-y-2">
              <div
                v-for="credito in meseDetails"
                :key="credito.id"
                class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0"
              >
                <div class="flex items-center gap-3">
                  <div
                    class="w-2 h-2 rounded-full"
                    :class="credito.credito_valido ? 'bg-green-500' : 'bg-gray-300'"
                  ></div>
                  <div>
                    <div class="text-sm font-medium text-gray-900">{{ credito.azienda_nome }}</div>
                    <div class="text-xs text-gray-500">{{ credito.client_nome }} {{ credito.client_cognome }}</div>
                  </div>
                </div>
                <div class="text-sm font-medium" :class="credito.credito_valido ? 'text-green-600' : 'text-gray-400'">
                  {{ credito.credito_valido ? `€${credito.importo_credito}` : 'Non valido' }}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Info Box -->
      <div class="bg-purple-50 border border-purple-200 rounded-xl p-6">
        <div class="flex gap-3">
          <Icon name="heroicons:information-circle" class="w-6 h-6 text-purple-600 flex-shrink-0" />
          <div>
            <h3 class="font-semibold text-purple-900 mb-2">Come funzionano i crediti</h3>
            <ul class="text-sm text-purple-700 space-y-1">
              <li>Ricevi &euro;9,80 (20% di &euro;49) per ogni cliente con abbonamento attivo</li>
              <li>Il credito matura solo sui mesi completi (dal 1 all'ultimo giorno del mese)</li>
              <li>I crediti vengono calcolati automaticamente il 2 di ogni mese</li>
              <li>Il pagamento avviene tramite bonifico bancario</li>
            </ul>
          </div>
        </div>
      </div>
    </template>
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  layout: 'consulente'
})

const config = useRuntimeConfig()

const loading = ref(true)
const expandedMese = ref('')
const meseDetails = ref<any[]>([])

const riepilogo = ref({
  totale_guadagnato: '0.00',
  totale_pending: '0.00',
  totale_pagato: '0.00',
  clienti_attivi: 0,
  mesi: [] as any[]
})

const mesiLabels = ['', 'Gennaio', 'Febbraio', 'Marzo', 'Aprile', 'Maggio', 'Giugno', 'Luglio', 'Agosto', 'Settembre', 'Ottobre', 'Novembre', 'Dicembre']

const getMeseLabel = (mese: number) => mesiLabels[mese] || ''

const getStatoClass = (stato: string) => {
  switch (stato) {
    case 'pagato': return 'bg-green-100 text-green-700'
    case 'confermato': return 'bg-blue-100 text-blue-700'
    case 'calcolato': return 'bg-yellow-100 text-yellow-700'
    default: return 'bg-gray-100 text-gray-700'
  }
}

const getStatoLabel = (stato: string) => {
  switch (stato) {
    case 'pagato': return 'Pagato'
    case 'confermato': return 'Confermato'
    case 'calcolato': return 'Da pagare'
    default: return stato
  }
}

const getHeaders = () => {
  const token = localStorage.getItem('aa_token')
  return {
    'Authorization': `Bearer ${token}`,
    'X-API-Key': config.public.apiKey,
    'Accept': 'application/json'
  }
}

const loadData = async () => {
  try {
    const res = await $fetch<{ success: boolean; data: any }>(`${config.public.apiBase}/commercialista/crediti/riepilogo`, {
      headers: getHeaders()
    })

    if (res.success) {
      riepilogo.value = {
        totale_guadagnato: res.data.totale_guadagnato || '0.00',
        totale_pending: res.data.totale_pending || '0.00',
        totale_pagato: res.data.totale_pagato || '0.00',
        clienti_attivi: res.data.clienti_attivi || 0,
        mesi: res.data.mesi || []
      }
    }
  } catch (e) {
    console.error('Error loading credits:', e)
  } finally {
    loading.value = false
  }
}

const toggleMese = async (mese: any) => {
  const key = `${mese.anno}-${mese.mese}`
  if (expandedMese.value === key) {
    expandedMese.value = ''
    return
  }

  try {
    const res = await $fetch<{ success: boolean; data: any[] }>(`${config.public.apiBase}/commercialista/crediti/${mese.anno}/${mese.mese}`, {
      headers: getHeaders()
    })

    if (res.success) {
      meseDetails.value = res.data
      expandedMese.value = key
    }
  } catch (e) {
    console.error('Error loading month details:', e)
  }
}

onMounted(loadData)
</script>
