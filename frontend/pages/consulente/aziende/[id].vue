<template>
  <div class="space-y-6">
    <!-- Loading -->
    <div v-if="loading" class="text-center py-12">
      <Icon name="heroicons:arrow-path" class="w-8 h-8 text-purple-600 animate-spin mx-auto" />
      <p class="text-gray-500 mt-2">Caricamento...</p>
    </div>

    <template v-else-if="azienda">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
          <NuxtLink
            to="/consulente/aziende"
            class="p-2 hover:bg-gray-100 rounded-lg transition"
          >
            <Icon name="heroicons:arrow-left" class="w-5 h-5 text-gray-600" />
          </NuxtLink>
          <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ azienda.nome }}</h1>
            <p class="text-gray-500">{{ azienda.p_iva || 'P.IVA non inserita' }}</p>
          </div>
        </div>
        <div class="flex gap-2">
          <button
            @click="showEditModal = true"
            class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 flex items-center gap-2"
          >
            <Icon name="heroicons:pencil" class="w-4 h-4" />
            Modifica
          </button>
          <button
            @click="showInviteModal = true"
            class="px-4 py-2 bg-purple-600 text-white rounded-lg font-medium hover:bg-purple-700 flex items-center gap-2"
          >
            <Icon name="heroicons:envelope" class="w-4 h-4" />
            Invita Cliente
          </button>
        </div>
      </div>

      <!-- Status Card -->
      <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
          <div>
            <div class="text-sm text-gray-500 mb-1">Stato Complessivo</div>
            <div class="flex items-center gap-2">
              <div
                class="w-10 h-10 rounded-lg flex items-center justify-center"
                :class="getStatoClass(azienda.stato_kpi)"
              >
                <Icon :name="getStatoIcon(azienda.stato_kpi)" class="w-5 h-5" />
              </div>
              <span class="font-semibold text-gray-900 capitalize">{{ azienda.stato_kpi || 'Non calcolato' }}</span>
            </div>
          </div>
          <div>
            <div class="text-sm text-gray-500 mb-1">Dimensione</div>
            <span
              class="inline-block px-3 py-1 rounded-full text-sm font-medium capitalize"
              :class="getDimensioneClass(azienda.dimensione)"
            >
              {{ azienda.dimensione }}
            </span>
          </div>
          <div>
            <div class="text-sm text-gray-500 mb-1">Codice ATECO</div>
            <div class="font-semibold text-gray-900">{{ azienda.codice_ateco || '-' }}</div>
          </div>
          <div>
            <div class="text-sm text-gray-500 mb-1">Ultimo Aggiornamento</div>
            <div class="font-semibold text-gray-900">
              {{ azienda.ultimo_aggiornamento ? formatDate(azienda.ultimo_aggiornamento) : 'Mai' }}
            </div>
          </div>
        </div>
      </div>

      <!-- Tabs -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="border-b border-gray-100">
          <nav class="flex gap-1 p-2">
            <button
              v-for="tab in tabs"
              :key="tab.id"
              @click="activeTab = tab.id"
              class="px-4 py-2 rounded-lg text-sm font-medium transition"
              :class="activeTab === tab.id ? 'bg-purple-100 text-purple-700' : 'text-gray-600 hover:bg-gray-100'"
            >
              {{ tab.label }}
            </button>
          </nav>
        </div>

        <div class="p-6">
          <!-- KPI Tab -->
          <div v-if="activeTab === 'kpi'" class="space-y-6">
            <div v-if="!kpiData || kpiData.length === 0" class="text-center py-8">
              <Icon name="heroicons:chart-bar" class="w-12 h-12 text-gray-300 mx-auto mb-3" />
              <p class="text-gray-600 mb-4">Nessun dato KPI disponibile</p>
              <button
                @click="showInsertDataModal = true"
                class="bg-purple-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-purple-700"
              >
                Inserisci Dati Economici
              </button>
            </div>

            <div v-else class="space-y-4">
              <div
                v-for="kpi in kpiData"
                :key="kpi.codice"
                class="flex items-center justify-between p-4 rounded-lg border"
                :class="getKpiBorderClass(kpi.stato)"
              >
                <div class="flex items-center gap-4">
                  <div
                    class="w-10 h-10 rounded-lg flex items-center justify-center"
                    :class="getStatoClass(kpi.stato)"
                  >
                    <Icon :name="getStatoIcon(kpi.stato)" class="w-5 h-5" />
                  </div>
                  <div>
                    <div class="font-medium text-gray-900">{{ kpi.nome }}</div>
                    <div class="text-sm text-gray-500">{{ kpi.descrizione }}</div>
                  </div>
                </div>
                <div class="text-right">
                  <div class="text-xl font-bold text-gray-900">{{ formatKpiValue(kpi.valore, kpi.tipo) }}</div>
                  <div class="text-sm text-gray-500">
                    Soglia: {{ formatKpiValue(kpi.soglia_min, kpi.tipo) }} - {{ formatKpiValue(kpi.soglia_max, kpi.tipo) }}
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Dati Tab -->
          <div v-if="activeTab === 'dati'" class="space-y-6">
            <div class="flex justify-between items-center">
              <h3 class="font-semibold text-gray-900">Dati Economici</h3>
              <button
                @click="showInsertDataModal = true"
                class="bg-purple-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-purple-700"
              >
                Inserisci Dati
              </button>
            </div>

            <div v-if="datiEconomici.length === 0" class="text-center py-8">
              <Icon name="heroicons:document-text" class="w-12 h-12 text-gray-300 mx-auto mb-3" />
              <p class="text-gray-600">Nessun dato economico inserito</p>
            </div>

            <div v-else class="overflow-x-auto">
              <table class="w-full">
                <thead>
                  <tr class="text-left text-sm text-gray-500 border-b">
                    <th class="pb-3 font-medium">Periodo</th>
                    <th class="pb-3 font-medium">Ricavi</th>
                    <th class="pb-3 font-medium">Costi</th>
                    <th class="pb-3 font-medium">Utile</th>
                    <th class="pb-3 font-medium">Azioni</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="dato in datiEconomici" :key="`${dato.anno}-${dato.mese}`" class="border-b last:border-0">
                    <td class="py-3 font-medium text-gray-900">{{ getMeseName(dato.mese) }} {{ dato.anno }}</td>
                    <td class="py-3 text-gray-700">€ {{ formatNumber(dato.ricavi) }}</td>
                    <td class="py-3 text-gray-700">€ {{ formatNumber(dato.costi) }}</td>
                    <td class="py-3" :class="dato.ricavi - dato.costi >= 0 ? 'text-green-600' : 'text-red-600'">
                      € {{ formatNumber(dato.ricavi - dato.costi) }}
                    </td>
                    <td class="py-3">
                      <button class="text-purple-600 hover:text-purple-700 text-sm font-medium">
                        Modifica
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Storico Tab -->
          <div v-if="activeTab === 'storico'" class="space-y-4">
            <div v-if="storico.length === 0" class="text-center py-8">
              <Icon name="heroicons:clock" class="w-12 h-12 text-gray-300 mx-auto mb-3" />
              <p class="text-gray-600">Nessuno storico disponibile</p>
            </div>

            <div v-else class="space-y-3">
              <div
                v-for="evento in storico"
                :key="evento.id"
                class="flex items-start gap-4 p-4 rounded-lg bg-gray-50"
              >
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                  <Icon :name="evento.icon" class="w-5 h-5 text-purple-600" />
                </div>
                <div class="flex-1">
                  <div class="font-medium text-gray-900">{{ evento.titolo }}</div>
                  <div class="text-sm text-gray-500">{{ evento.descrizione }}</div>
                  <div class="text-xs text-gray-400 mt-1">{{ formatDateTime(evento.created_at) }}</div>
                </div>
              </div>
            </div>
          </div>

          <!-- Accessi Tab -->
          <div v-if="activeTab === 'accessi'" class="space-y-4">
            <div class="flex justify-between items-center">
              <h3 class="font-semibold text-gray-900">Accessi Cliente</h3>
              <button
                @click="showInviteModal = true"
                class="bg-purple-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-purple-700"
              >
                Invita Cliente
              </button>
            </div>

            <div v-if="accessi.length === 0" class="text-center py-8">
              <Icon name="heroicons:users" class="w-12 h-12 text-gray-300 mx-auto mb-3" />
              <p class="text-gray-600">Nessun accesso cliente configurato</p>
            </div>

            <div v-else class="space-y-3">
              <div
                v-for="accesso in accessi"
                :key="accesso.id"
                class="flex items-center justify-between p-4 rounded-lg border border-gray-200"
              >
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                    <Icon name="heroicons:user" class="w-5 h-5 text-blue-600" />
                  </div>
                  <div>
                    <div class="font-medium text-gray-900">{{ accesso.email }}</div>
                    <div class="text-sm text-gray-500">Accesso dal {{ formatDate(accesso.created_at) }}</div>
                  </div>
                </div>
                <button class="text-red-600 hover:text-red-700 text-sm font-medium">
                  Revoca
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </template>

    <!-- Not Found -->
    <div v-else class="bg-white rounded-xl shadow-sm p-12 border border-gray-100 text-center">
      <Icon name="heroicons:exclamation-circle" class="w-16 h-16 text-gray-300 mx-auto mb-4" />
      <h3 class="text-lg font-medium text-gray-900 mb-2">Azienda non trovata</h3>
      <NuxtLink
        to="/consulente/aziende"
        class="text-purple-600 hover:text-purple-700 font-medium"
      >
        Torna alla lista aziende
      </NuxtLink>
    </div>

    <!-- Invite Modal -->
    <div v-if="showInviteModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6">
        <div class="flex items-center justify-between mb-6">
          <h2 class="text-xl font-bold text-gray-900">Invita Cliente</h2>
          <button @click="showInviteModal = false" class="text-gray-400 hover:text-gray-600">
            <Icon name="heroicons:x-mark" class="w-6 h-6" />
          </button>
        </div>

        <form @submit.prevent="handleInvite" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email Cliente</label>
            <input
              v-model="inviteEmail"
              type="email"
              required
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
              placeholder="cliente@azienda.it"
            />
          </div>

          <p class="text-sm text-gray-500">
            Il cliente riceverà un invito via email per accedere in sola lettura alla dashboard della propria azienda.
          </p>

          <div v-if="inviteError" class="bg-red-50 text-red-600 text-sm p-3 rounded-lg">
            {{ inviteError }}
          </div>

          <div v-if="inviteSuccess" class="bg-green-50 text-green-600 text-sm p-3 rounded-lg">
            Invito inviato con successo!
          </div>

          <div class="flex gap-3">
            <button
              type="button"
              @click="showInviteModal = false"
              class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50"
            >
              Annulla
            </button>
            <button
              type="submit"
              :disabled="inviteLoading"
              class="flex-1 px-4 py-2 bg-purple-600 text-white rounded-lg font-medium hover:bg-purple-700 disabled:opacity-50"
            >
              {{ inviteLoading ? 'Invio...' : 'Invia Invito' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  layout: 'consulente'
})

const route = useRoute()
const config = useRuntimeConfig()

const aziendaId = computed(() => route.params.id as string)

const azienda = ref<any>(null)
const loading = ref(true)
const activeTab = ref('kpi')

const kpiData = ref<any[]>([])
const datiEconomici = ref<any[]>([])
const storico = ref<any[]>([])
const accessi = ref<any[]>([])

const showEditModal = ref(false)
const showInsertDataModal = ref(false)
const showInviteModal = ref(false)

const inviteEmail = ref('')
const inviteLoading = ref(false)
const inviteError = ref('')
const inviteSuccess = ref(false)

const tabs = [
  { id: 'kpi', label: 'KPI' },
  { id: 'dati', label: 'Dati Economici' },
  { id: 'storico', label: 'Storico' },
  { id: 'accessi', label: 'Accessi Cliente' }
]

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('it-IT', { day: '2-digit', month: 'short', year: 'numeric' })
}

const formatDateTime = (date: string) => {
  return new Date(date).toLocaleDateString('it-IT', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' })
}

const formatNumber = (num: number) => {
  return new Intl.NumberFormat('it-IT').format(num)
}

const formatKpiValue = (value: number, tipo: string) => {
  if (tipo === 'percentuale') return `${(value * 100).toFixed(1)}%`
  if (tipo === 'giorni') return `${value} gg`
  return value.toFixed(2)
}

const getMeseName = (mese: number) => {
  const mesi = ['Gen', 'Feb', 'Mar', 'Apr', 'Mag', 'Giu', 'Lug', 'Ago', 'Set', 'Ott', 'Nov', 'Dic']
  return mesi[mese - 1]
}

const getStatoClass = (stato: string) => {
  switch (stato) {
    case 'verde': return 'bg-green-100 text-green-600'
    case 'giallo': return 'bg-yellow-100 text-yellow-600'
    case 'rosso': return 'bg-red-100 text-red-600'
    default: return 'bg-gray-100 text-gray-600'
  }
}

const getStatoIcon = (stato: string) => {
  switch (stato) {
    case 'verde': return 'heroicons:check-circle'
    case 'giallo': return 'heroicons:exclamation-triangle'
    case 'rosso': return 'heroicons:x-circle'
    default: return 'heroicons:question-mark-circle'
  }
}

const getKpiBorderClass = (stato: string) => {
  switch (stato) {
    case 'verde': return 'border-green-200 bg-green-50/50'
    case 'giallo': return 'border-yellow-200 bg-yellow-50/50'
    case 'rosso': return 'border-red-200 bg-red-50/50'
    default: return 'border-gray-200'
  }
}

const getDimensioneClass = (dimensione: string) => {
  switch (dimensione) {
    case 'micro': return 'bg-blue-100 text-blue-700'
    case 'piccola': return 'bg-green-100 text-green-700'
    case 'media': return 'bg-yellow-100 text-yellow-700'
    case 'grande': return 'bg-purple-100 text-purple-700'
    default: return 'bg-gray-100 text-gray-700'
  }
}

const handleInvite = async () => {
  inviteLoading.value = true
  inviteError.value = ''
  inviteSuccess.value = false

  const token = localStorage.getItem('aa_token')
  if (!token) return

  try {
    const response = await $fetch<{ success: boolean; message?: string }>(`${config.public.apiBase}/inviti`, {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${token}`,
        'X-API-Key': config.public.apiKey,
        'Content-Type': 'application/json'
      },
      body: {
        azienda_cliente_id: aziendaId.value,
        email: inviteEmail.value
      }
    })

    if (response.success) {
      inviteSuccess.value = true
      inviteEmail.value = ''
      setTimeout(() => {
        showInviteModal.value = false
        inviteSuccess.value = false
      }, 2000)
    } else {
      inviteError.value = response.message || 'Errore durante l\'invio'
    }
  } catch (e: any) {
    inviteError.value = e.data?.message || 'Errore durante l\'invio'
  } finally {
    inviteLoading.value = false
  }
}

const loadAzienda = async () => {
  const token = localStorage.getItem('aa_token')
  if (!token) return

  try {
    const response = await $fetch<{ success: boolean; data: any }>(`${config.public.apiBase}/aziende-cliente/${aziendaId.value}`, {
      headers: {
        'Authorization': `Bearer ${token}`,
        'X-API-Key': config.public.apiKey
      }
    })

    if (response.success) {
      azienda.value = response.data
      kpiData.value = response.data.kpi || []
      datiEconomici.value = response.data.dati_economici || []
      storico.value = response.data.storico || []
      accessi.value = response.data.accessi || []
    }
  } catch (e) {
    console.error('Error loading azienda:', e)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  loadAzienda()

  // Check for tab query param
  if (route.query.tab) {
    activeTab.value = route.query.tab as string
  }
})
</script>
