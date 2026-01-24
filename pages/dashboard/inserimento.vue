<template>
  <div>
    <!-- Header -->
    <div class="mb-6">
      <div class="flex items-center gap-3 mb-4">
        <NuxtLink to="/dashboard" class="text-gray-400 hover:text-gray-600">
          <Icon name="heroicons:arrow-left" class="w-5 h-5" />
        </NuxtLink>
        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center">
          <Icon name="heroicons:pencil-square" class="w-6 h-6 text-white" />
        </div>
        <div class="flex-1">
          <h1 class="text-2xl font-bold text-gray-800">Inserimento Dati Economici</h1>
          <p class="text-gray-500">Dati per il calcolo degli indicatori di salute aziendale</p>
        </div>
      </div>
      <div class="flex justify-end gap-3">
        <select v-model="selectedMese" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 w-40" @change="loadExistingData">
          <option v-for="m in mesi" :key="m.value" :value="m.value">{{ m.label }}</option>
        </select>
        <select v-model="selectedAnno" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 w-24" @change="loadExistingData">
          <option v-for="a in anni" :key="a" :value="a">{{ a }}</option>
        </select>
      </div>
    </div>

    <!-- Loading existing data -->
    <div v-if="loadingData" class="flex justify-center items-center py-10">
      <Icon name="heroicons:arrow-path" class="w-6 h-6 text-blue-600 animate-spin mr-2" />
      <span class="text-gray-600">Caricamento dati esistenti...</span>
    </div>

    <form v-else @submit.prevent="salva" class="space-y-6">
      <!-- Stato Patrimoniale -->
      <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
          <Icon name="heroicons:scale" class="w-5 h-5 text-blue-600" />
          Stato Patrimoniale
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Patrimonio Netto</label>
            <div class="relative">
              <span class="absolute left-3 top-2.5 text-gray-500">€</span>
              <input v-model.number="form.patrimonio_netto" type="number" step="0.01" class="w-full px-3 py-2 pl-8 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" />
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Totale Attivo</label>
            <div class="relative">
              <span class="absolute left-3 top-2.5 text-gray-500">€</span>
              <input v-model.number="form.totale_attivo" type="number" step="0.01" class="w-full px-3 py-2 pl-8 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" />
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Attività Correnti</label>
            <div class="relative">
              <span class="absolute left-3 top-2.5 text-gray-500">€</span>
              <input v-model.number="form.attivita_correnti" type="number" step="0.01" class="w-full px-3 py-2 pl-8 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" />
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Passività Correnti</label>
            <div class="relative">
              <span class="absolute left-3 top-2.5 text-gray-500">€</span>
              <input v-model.number="form.passivita_correnti" type="number" step="0.01" class="w-full px-3 py-2 pl-8 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" />
            </div>
          </div>
        </div>
      </div>

      <!-- Debiti -->
      <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
          <Icon name="heroicons:document-text" class="w-5 h-5 text-blue-600" />
          Debiti
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Debiti vs Fornitori</label>
            <div class="relative">
              <span class="absolute left-3 top-2.5 text-gray-500">€</span>
              <input v-model.number="form.debiti_fornitori" type="number" step="0.01" class="w-full px-3 py-2 pl-8 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" />
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Debiti INPS</label>
            <div class="relative">
              <span class="absolute left-3 top-2.5 text-gray-500">€</span>
              <input v-model.number="form.debiti_inps" type="number" step="0.01" class="w-full px-3 py-2 pl-8 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" />
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Debiti Erario (IVA, IRPEF)</label>
            <div class="relative">
              <span class="absolute left-3 top-2.5 text-gray-500">€</span>
              <input v-model.number="form.debiti_erario" type="number" step="0.01" class="w-full px-3 py-2 pl-8 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" />
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Debiti Finanziari Breve</label>
            <div class="relative">
              <span class="absolute left-3 top-2.5 text-gray-500">€</span>
              <input v-model.number="form.debiti_finanziari_breve" type="number" step="0.01" class="w-full px-3 py-2 pl-8 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" />
            </div>
            <p class="text-xs text-gray-500 mt-1">Rate prossimi 12 mesi</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Debiti Finanziari Lungo</label>
            <div class="relative">
              <span class="absolute left-3 top-2.5 text-gray-500">€</span>
              <input v-model.number="form.debiti_finanziari_lungo" type="number" step="0.01" class="w-full px-3 py-2 pl-8 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" />
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Rate Mensili Finanziamenti</label>
            <div class="relative">
              <span class="absolute left-3 top-2.5 text-gray-500">€</span>
              <input v-model.number="form.rate_finanziamenti_mensili" type="number" step="0.01" class="w-full px-3 py-2 pl-8 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" />
            </div>
          </div>
        </div>
      </div>

      <!-- Costi Mensili -->
      <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
          <Icon name="heroicons:calculator" class="w-5 h-5 text-blue-600" />
          Costi Mensili
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Costi Fissi</label>
            <div class="relative">
              <span class="absolute left-3 top-2.5 text-gray-500">€</span>
              <input v-model.number="form.costi_fissi_mensili" type="number" step="0.01" class="w-full px-3 py-2 pl-8 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" />
            </div>
            <p class="text-xs text-gray-500 mt-1">Affitto, utenze, assicurazioni</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Costo Personale</label>
            <div class="relative">
              <span class="absolute left-3 top-2.5 text-gray-500">€</span>
              <input v-model.number="form.costo_personale_mensile" type="number" step="0.01" class="w-full px-3 py-2 pl-8 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" />
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Costi Variabili</label>
            <div class="relative">
              <span class="absolute left-3 top-2.5 text-gray-500">€</span>
              <input v-model.number="form.costi_variabili_mensili" type="number" step="0.01" class="w-full px-3 py-2 pl-8 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" />
            </div>
            <p class="text-xs text-gray-500 mt-1">Materiali, consumabili</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Interessi Passivi</label>
            <div class="relative">
              <span class="absolute left-3 top-2.5 text-gray-500">€</span>
              <input v-model.number="form.interessi_passivi_mensili" type="number" step="0.01" class="w-full px-3 py-2 pl-8 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" />
            </div>
          </div>
        </div>
      </div>

      <!-- Ricavi -->
      <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
          <Icon name="heroicons:banknotes" class="w-5 h-5 text-blue-600" />
          Ricavi
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Fatturato Mensile</label>
            <div class="relative">
              <span class="absolute left-3 top-2.5 text-gray-500">€</span>
              <input v-model.number="form.fatturato_mensile" type="number" step="0.01" class="w-full px-3 py-2 pl-8 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" />
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Crediti vs Clienti</label>
            <div class="relative">
              <span class="absolute left-3 top-2.5 text-gray-500">€</span>
              <input v-model.number="form.crediti_clienti" type="number" step="0.01" class="w-full px-3 py-2 pl-8 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" />
            </div>
          </div>
        </div>
      </div>

      <!-- Note -->
      <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
          <Icon name="heroicons:chat-bubble-left-ellipsis" class="w-5 h-5 text-blue-600" />
          Note
        </h2>
        <textarea v-model="form.note" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Note aggiuntive..."></textarea>
      </div>

      <!-- Actions -->
      <div class="flex justify-between items-center">
        <div v-if="existingDataId" class="text-sm text-gray-500">
          <Icon name="heroicons:information-circle" class="w-4 h-4 inline mr-1" />
          Dati esistenti per {{ periodoLabel }} - ultima modifica: {{ lastUpdate }}
        </div>
        <div v-else class="text-sm text-gray-500">
          <Icon name="heroicons:plus-circle" class="w-4 h-4 inline mr-1" />
          Nuovo inserimento per {{ periodoLabel }}
        </div>
        <div class="flex gap-3">
          <NuxtLink to="/dashboard" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
            Annulla
          </NuxtLink>
          <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center gap-2" :disabled="saving">
            <Icon v-if="saving" name="heroicons:arrow-path" class="w-4 h-4 animate-spin" />
            <Icon v-else name="heroicons:check" class="w-4 h-4" />
            {{ saving ? 'Salvataggio...' : 'Salva e Calcola KPI' }}
          </button>
        </div>
      </div>
    </form>

    <!-- Success Message -->
    <div v-if="successMessage" class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center gap-2">
      <Icon name="heroicons:check-circle" class="w-5 h-5" />
      {{ successMessage }}
    </div>

    <!-- Error Message -->
    <div v-if="errorMessage" class="fixed bottom-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center gap-2">
      <Icon name="heroicons:exclamation-circle" class="w-5 h-5" />
      {{ errorMessage }}
    </div>
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
const loadingData = ref(false)
const saving = ref(false)
const successMessage = ref('')
const errorMessage = ref('')
const existingDataId = ref<number | null>(null)
const lastUpdate = ref('')

const anni = computed(() => {
  const current = new Date().getFullYear()
  return [current - 1, current, current + 1]
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

const form = reactive({
  patrimonio_netto: null as number | null,
  totale_attivo: null as number | null,
  attivita_correnti: null as number | null,
  passivita_correnti: null as number | null,
  debiti_fornitori: null as number | null,
  debiti_inps: null as number | null,
  debiti_erario: null as number | null,
  debiti_finanziari_breve: null as number | null,
  debiti_finanziari_lungo: null as number | null,
  rate_finanziamenti_mensili: null as number | null,
  costi_fissi_mensili: null as number | null,
  costo_personale_mensile: null as number | null,
  costi_variabili_mensili: null as number | null,
  interessi_passivi_mensili: null as number | null,
  fatturato_mensile: null as number | null,
  crediti_clienti: null as number | null,
  note: '',
})

const getAuthHeaders = () => {
  const token = localStorage.getItem('aa_token')
  return {
    'X-API-Key': config.public.apiKey,
    'Authorization': `Bearer ${token}`,
    'Content-Type': 'application/json'
  }
}

const resetForm = () => {
  Object.keys(form).forEach(key => {
    if (key === 'note') {
      form.note = ''
    } else {
      (form as any)[key] = null
    }
  })
  existingDataId.value = null
  lastUpdate.value = ''
}

const loadExistingData = async () => {
  loadingData.value = true
  resetForm()

  try {
    const response = await $fetch<{ success: boolean; data: any }>(
      `${config.public.apiBase}/adeguati-assetti/api/dati-economici/${selectedAnno.value}/${selectedMese.value}`,
      { headers: getAuthHeaders() }
    )

    if (response.success && response.data) {
      Object.assign(form, response.data)
      existingDataId.value = response.data.id
      lastUpdate.value = new Date(response.data.updated_at).toLocaleString('it-IT')
    }
  } catch (e) {
    console.error('Errore caricamento dati:', e)
  } finally {
    loadingData.value = false
  }
}

const salva = async () => {
  saving.value = true
  successMessage.value = ''
  errorMessage.value = ''

  try {
    // Get azienda_id from user data
    const user = JSON.parse(localStorage.getItem('aa_user') || '{}')
    const aziendaId = user.azienda_id || 1

    const response = await $fetch<{ success: boolean; message: string }>(
      `${config.public.apiBase}/adeguati-assetti/api/dati-economici`,
      {
        method: 'POST',
        headers: getAuthHeaders(),
        body: {
          azienda_id: aziendaId,
          anno: selectedAnno.value,
          mese: selectedMese.value,
          ...form,
        }
      }
    )

    if (response.success) {
      successMessage.value = 'Dati salvati! KPI ricalcolati.'

      // Ricalcola KPI
      await $fetch(`${config.public.apiBase}/adeguati-assetti/api/calcola`, {
        method: 'POST',
        headers: getAuthHeaders(),
        body: { azienda_id: aziendaId, anno: selectedAnno.value, mese: selectedMese.value }
      })

      setTimeout(() => {
        successMessage.value = ''
        router.push('/dashboard')
      }, 2000)
    }
  } catch (e: unknown) {
    console.error('Errore salvataggio:', e)
    errorMessage.value = e instanceof Error ? e.message : 'Errore nel salvataggio'
    setTimeout(() => { errorMessage.value = '' }, 5000)
  } finally {
    saving.value = false
  }
}

onMounted(() => {
  loadExistingData()
})
</script>
