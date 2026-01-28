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
      <!-- Settore ATECO -->
      <div v-if="aziendaNome" class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-3 flex items-center justify-between">
        <div class="flex items-center gap-2">
          <Icon name="heroicons:building-office" class="w-5 h-5 text-blue-600" />
          <span class="font-medium text-blue-800">{{ aziendaNome }}</span>
          <span v-if="aziendaAteco" class="text-sm text-blue-600">(ATECO: {{ aziendaAteco }})</span>
        </div>
        <select v-model="selectedAteco" @change="salvaAteco" class="px-3 py-1.5 text-sm border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white">
          <option value="">-- Seleziona settore ATECO --</option>
          <option v-for="s in settoriAteco" :key="s.codice" :value="s.codice">{{ s.codice }} - {{ s.label }}</option>
        </select>
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
      <!-- SEZIONE 1: STATO PATRIMONIALE (KPI Obbligatori CNDCEC) -->
      <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-2 flex items-center gap-2">
          <Icon name="heroicons:scale" class="w-5 h-5 text-blue-600" />
          Stato Patrimoniale
        </h2>
        <p class="text-xs text-gray-500 mb-4">Dati obbligatori per calcolo KPI CNDCEC</p>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Patrimonio Netto *</label>
            <div class="relative">
              <span class="absolute left-3 top-2.5 text-gray-500">€</span>
              <input v-model.number="form.patrimonio_netto" type="number" step="0.01" class="w-full px-3 py-2 pl-8 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required />
            </div>
            <p class="text-xs text-gray-400 mt-1">Voce A Passivo SP</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Totale Attivo *</label>
            <div class="relative">
              <span class="absolute left-3 top-2.5 text-gray-500">€</span>
              <input v-model.number="form.totale_attivo" type="number" step="0.01" class="w-full px-3 py-2 pl-8 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required />
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Attivo Circolante *</label>
            <div class="relative">
              <span class="absolute left-3 top-2.5 text-gray-500">€</span>
              <input v-model.number="form.attivo_circolante" type="number" step="0.01" class="w-full px-3 py-2 pl-8 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required />
            </div>
            <p class="text-xs text-gray-400 mt-1">Per Current Ratio</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Debiti Breve Termine *</label>
            <div class="relative">
              <span class="absolute left-3 top-2.5 text-gray-500">€</span>
              <input v-model.number="form.debiti_breve_termine" type="number" step="0.01" class="w-full px-3 py-2 pl-8 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required />
            </div>
            <p class="text-xs text-gray-400 mt-1">Scadenza entro 12 mesi</p>
          </div>
        </div>
      </div>

      <!-- SEZIONE 2: DEBITI E ONERI FINANZIARI -->
      <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-2 flex items-center gap-2">
          <Icon name="heroicons:document-text" class="w-5 h-5 text-blue-600" />
          Debiti e Oneri Finanziari
        </h2>
        <p class="text-xs text-gray-500 mb-4">Per calcolo DSCR, PN/Debiti, Deb.Trib/Attivo</p>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Debiti Totali *</label>
            <div class="relative">
              <span class="absolute left-3 top-2.5 text-gray-500">€</span>
              <input v-model.number="form.debiti_totali" type="number" step="0.01" class="w-full px-3 py-2 pl-8 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required />
            </div>
            <p class="text-xs text-gray-400 mt-1">Somma tutti i debiti</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Debiti Tributari *</label>
            <div class="relative">
              <span class="absolute left-3 top-2.5 text-gray-500">€</span>
              <input v-model.number="form.debiti_tributari" type="number" step="0.01" class="w-full px-3 py-2 pl-8 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required />
            </div>
            <p class="text-xs text-gray-400 mt-1">IVA, IRPEF, IRES, INPS</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Oneri Finanziari *</label>
            <div class="relative">
              <span class="absolute left-3 top-2.5 text-gray-500">€</span>
              <input v-model.number="form.oneri_finanziari" type="number" step="0.01" class="w-full px-3 py-2 pl-8 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required />
            </div>
            <p class="text-xs text-gray-400 mt-1">Interessi passivi annui</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Debiti vs Fornitori</label>
            <div class="relative">
              <span class="absolute left-3 top-2.5 text-gray-500">€</span>
              <input v-model.number="form.debiti_fornitori" type="number" step="0.01" class="w-full px-3 py-2 pl-8 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" />
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Debiti vs Banche Breve</label>
            <div class="relative">
              <span class="absolute left-3 top-2.5 text-gray-500">€</span>
              <input v-model.number="form.debiti_banche_breve" type="number" step="0.01" class="w-full px-3 py-2 pl-8 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" />
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Debiti vs Banche M/L</label>
            <div class="relative">
              <span class="absolute left-3 top-2.5 text-gray-500">€</span>
              <input v-model.number="form.debiti_banche_lungo" type="number" step="0.01" class="w-full px-3 py-2 pl-8 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" />
            </div>
          </div>
        </div>
      </div>

      <!-- SEZIONE 3: CONTO ECONOMICO -->
      <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-2 flex items-center gap-2">
          <Icon name="heroicons:banknotes" class="w-5 h-5 text-blue-600" />
          Conto Economico (Mensile)
        </h2>
        <p class="text-xs text-gray-500 mb-4">Per calcolo Cash Flow, Margine Lordo, DSCR</p>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Ricavi Totali *</label>
            <div class="relative">
              <span class="absolute left-3 top-2.5 text-gray-500">€</span>
              <input v-model.number="form.totale_ricavi" type="number" step="0.01" class="w-full px-3 py-2 pl-8 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required />
            </div>
            <p class="text-xs text-gray-400 mt-1">Fatturato del mese</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Costi Materie Prime</label>
            <div class="relative">
              <span class="absolute left-3 top-2.5 text-gray-500">€</span>
              <input v-model.number="form.costi_materie_prime" type="number" step="0.01" class="w-full px-3 py-2 pl-8 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" />
            </div>
            <p class="text-xs text-gray-400 mt-1">Materiali, consumabili</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Costi Servizi</label>
            <div class="relative">
              <span class="absolute left-3 top-2.5 text-gray-500">€</span>
              <input v-model.number="form.costi_servizi" type="number" step="0.01" class="w-full px-3 py-2 pl-8 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" />
            </div>
            <p class="text-xs text-gray-400 mt-1">Affitti, utenze, consulenze</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Costo Personale *</label>
            <div class="relative">
              <span class="absolute left-3 top-2.5 text-gray-500">€</span>
              <input v-model.number="form.costi_personale" type="number" step="0.01" class="w-full px-3 py-2 pl-8 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required />
            </div>
            <p class="text-xs text-gray-400 mt-1">Stipendi + contributi</p>
          </div>
        </div>
      </div>

      <!-- SEZIONE 4: CREDITI -->
      <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-2 flex items-center gap-2">
          <Icon name="heroicons:clipboard-document-list" class="w-5 h-5 text-blue-600" />
          Crediti
        </h2>
        <p class="text-xs text-gray-500 mb-4">Per calcolo DSO e Crediti Scaduti</p>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Crediti vs Clienti *</label>
            <div class="relative">
              <span class="absolute left-3 top-2.5 text-gray-500">€</span>
              <input v-model.number="form.crediti_vs_clienti" type="number" step="0.01" class="w-full px-3 py-2 pl-8 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required />
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Crediti Scaduti > 90gg</label>
            <div class="relative">
              <span class="absolute left-3 top-2.5 text-gray-500">€</span>
              <input v-model.number="form.crediti_scaduti_90gg" type="number" step="0.01" class="w-full px-3 py-2 pl-8 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" />
            </div>
            <p class="text-xs text-gray-400 mt-1">Per % crediti scaduti</p>
          </div>
        </div>
      </div>

      <!-- SEZIONE 5: KPI SETTORIALI STUDIO DENTISTICO (solo se ATECO sanitario) -->
      <div v-if="isSanitario" class="bg-white rounded-xl shadow p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-2 flex items-center gap-2">
          <Icon name="heroicons:building-office-2" class="w-5 h-5 text-purple-600" />
          Dati Operativi Studio
        </h2>
        <p class="text-xs text-gray-500 mb-4">Per KPI settoriali studio dentistico/sanitario</p>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Numero Poltrone</label>
            <input v-model.number="form.numero_poltrone" type="number" min="1" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" />
            <p class="text-xs text-gray-400 mt-1">Poltrone operative</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Ore Agenda Disponibili</label>
            <input v-model.number="form.ore_disponibili" type="number" step="0.5" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" />
            <p class="text-xs text-gray-400 mt-1">Ore totali mese</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Ore Appuntamenti</label>
            <input v-model.number="form.ore_appuntamenti" type="number" step="0.5" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" />
            <p class="text-xs text-gray-400 mt-1">Ore effettive occupate</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Pazienti Attivi</label>
            <input v-model.number="form.pazienti_attivi" type="number" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" />
            <p class="text-xs text-gray-400 mt-1">Con trattamento in corso</p>
          </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mt-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Preventivi Presentati</label>
            <input v-model.number="form.preventivi_presentati" type="number" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Preventivi Accettati</label>
            <input v-model.number="form.preventivi_accettati" type="number" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nuovi Pazienti</label>
            <input v-model.number="form.pazienti_nuovi" type="number" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" />
            <p class="text-xs text-gray-400 mt-1">Prima visita questo mese</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Appuntamenti No-Show</label>
            <input v-model.number="form.appuntamenti_no_show" type="number" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" />
            <p class="text-xs text-gray-400 mt-1">Non presentati</p>
          </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mt-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Appuntamenti Totali</label>
            <input v-model.number="form.appuntamenti_totali" type="number" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Pazienti Recall</label>
            <input v-model.number="form.pazienti_recall" type="number" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" />
            <p class="text-xs text-gray-400 mt-1">Tornati per igiene/controllo</p>
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
const route = useRoute()
const config = useRuntimeConfig()

const selectedAnno = ref(new Date().getFullYear())
const selectedMese = ref(new Date().getMonth() + 1)
const loadingData = ref(false)
const saving = ref(false)
const successMessage = ref('')
const errorMessage = ref('')
const existingDataId = ref<number | null>(null)
const lastUpdate = ref('')
const aziendaNome = ref('')
const aziendaAteco = ref('')
const selectedAteco = ref('')
const currentAziendaId = ref<number | null>(null)

const isSanitario = computed(() => {
  const ateco = selectedAteco.value || aziendaAteco.value
  return ateco.startsWith('86') || ateco.startsWith('87') || ateco.startsWith('88')
})

const settoriAteco = [
  { codice: '01.11', label: 'Agricoltura - Cereali' },
  { codice: '10.71', label: 'Alimentare - Panificazione' },
  { codice: '25.11', label: 'Metalmeccanica' },
  { codice: '41.20', label: 'Edilizia - Costruzione edifici' },
  { codice: '43.21', label: 'Costruzioni - Impianti elettrici' },
  { codice: '45.11', label: 'Commercio autoveicoli' },
  { codice: '46.49', label: 'Commercio ingrosso' },
  { codice: '47.11', label: 'Commercio dettaglio alimentari' },
  { codice: '47.73', label: 'Commercio dettaglio farmaci' },
  { codice: '49.41', label: 'Trasporto merci' },
  { codice: '55.10', label: 'Alberghi' },
  { codice: '56.10', label: 'Ristorazione' },
  { codice: '62.01', label: 'Software e consulenza informatica' },
  { codice: '69.20', label: 'Studi commercialisti e consulenza' },
  { codice: '71.12', label: 'Studi ingegneria' },
  { codice: '86.10', label: 'Servizi ospedalieri' },
  { codice: '86.21', label: 'Servizi medici generici' },
  { codice: '86.23', label: 'Studi odontoiatrici' },
  { codice: '86.90', label: 'Altri servizi sanitari' },
  { codice: '96.02', label: 'Parrucchieri e trattamenti estetici' },
]

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
  // Stato Patrimoniale (obbligatori CNDCEC)
  patrimonio_netto: null as number | null,
  totale_attivo: null as number | null,
  attivo_circolante: null as number | null,
  debiti_breve_termine: null as number | null,
  // Debiti
  debiti_totali: null as number | null,
  debiti_tributari: null as number | null,
  oneri_finanziari: null as number | null,
  debiti_fornitori: null as number | null,
  debiti_banche_breve: null as number | null,
  debiti_banche_lungo: null as number | null,
  // Conto Economico
  totale_ricavi: null as number | null,
  costi_materie_prime: null as number | null,
  costi_servizi: null as number | null,
  costi_personale: null as number | null,
  // Crediti
  crediti_vs_clienti: null as number | null,
  crediti_scaduti_90gg: null as number | null,
  // KPI Settoriali Studio
  numero_poltrone: null as number | null,
  ore_disponibili: null as number | null,
  ore_appuntamenti: null as number | null,
  pazienti_attivi: null as number | null,
  preventivi_presentati: null as number | null,
  preventivi_accettati: null as number | null,
  pazienti_nuovi: null as number | null,
  appuntamenti_no_show: null as number | null,
  appuntamenti_totali: null as number | null,
  pazienti_recall: null as number | null,
  // Note
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
      `${config.public.apiBase}/dati-economici/${selectedAnno.value}/${selectedMese.value}`,
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
    // Get azienda_id from user data or route query
    const user = JSON.parse(localStorage.getItem('aa_user') || '{}')
    const aziendaId = route.query.azienda ? Number(route.query.azienda) : (user.azienda_id || 1)

    const response = await $fetch<{ success: boolean; message: string }>(
      `${config.public.apiBase}/dati-economici`,
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
      await $fetch(`${config.public.apiBase}/calcola`, {
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

const salvaAteco = async () => {
  if (!currentAziendaId.value || !selectedAteco.value) return
  try {
    await $fetch(`${config.public.apiBase}/aziende/${currentAziendaId.value}`, {
      method: 'PUT',
      headers: getAuthHeaders(),
      body: { codice_ateco: selectedAteco.value }
    })
    aziendaAteco.value = selectedAteco.value
  } catch (e) {
    console.error('Errore salvataggio ATECO:', e)
  }
}

const loadAziendaInfo = async () => {
  try {
    const response = await $fetch<{ success: boolean; data: any[] }>(
      `${config.public.apiBase}/aziende`,
      { headers: getAuthHeaders() }
    )
    if (response.success && response.data.length > 0) {
      const aziendaIdParam = route.query.azienda ? Number(route.query.azienda) : null
      const az = aziendaIdParam
        ? response.data.find(a => a.id === aziendaIdParam) || response.data[0]
        : response.data[0]
      aziendaNome.value = az.nome
      aziendaAteco.value = az.codice_ateco || ''
      selectedAteco.value = az.codice_ateco || ''
      currentAziendaId.value = az.id
    }
  } catch (e) {
    console.error('Errore caricamento azienda:', e)
  }
}

onMounted(() => {
  // Check for query params
  if (route.query.anno) selectedAnno.value = Number(route.query.anno)
  if (route.query.mese) selectedMese.value = Number(route.query.mese)
  loadAziendaInfo()
  loadExistingData()
})
</script>
