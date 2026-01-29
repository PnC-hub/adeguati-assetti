<template>
  <div>
    <!-- Header -->
    <div class="mb-6">
      <div class="flex items-center gap-3">
        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-red-500 to-red-700 flex items-center justify-center">
          <Icon name="heroicons:clipboard-document-check" class="w-6 h-6 text-white" />
        </div>
        <div>
          <h1 class="text-2xl font-bold text-gray-800">Checklist Compliance Art. 2086 c.c.</h1>
          <p class="text-gray-500">Verifica la conformita degli assetti organizzativi, amministrativi e contabili</p>
        </div>
      </div>
    </div>

    <!-- Info Box -->
    <div class="bg-red-50 border border-red-200 rounded-xl p-5 mb-6">
      <div class="flex items-start gap-3">
        <Icon name="heroicons:exclamation-triangle" class="w-6 h-6 text-red-600 flex-shrink-0 mt-0.5" />
        <div>
          <h3 class="font-semibold text-red-800">Obbligo di legge per tutte le imprese</h3>
          <p class="text-red-700 text-sm mt-1">
            L'Art. 2086 c.c. (come modificato dal D.Lgs. 14/2019) impone a ogni imprenditore che operi in forma societaria o collettiva di istituire
            assetti organizzativi, amministrativi e contabili adeguati alla natura e alle dimensioni dell'impresa, anche in funzione della rilevazione
            tempestiva della crisi e della perdita della continuita aziendale.
          </p>
        </div>
      </div>
    </div>

    <!-- Score di conformita -->
    <div class="bg-white rounded-xl shadow p-6 mb-6">
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-lg font-semibold text-gray-800">Livello di Conformita</h2>
          <p class="text-gray-500 text-sm mt-1">Basato sugli elementi verificati dai Tribunali italiani</p>
        </div>
        <div class="text-center">
          <div class="text-4xl font-bold" :class="scoreColor">{{ completedCount }}/{{ checklistItems.length }}</div>
          <div class="text-sm" :class="scoreColor">{{ scoreLabel }}</div>
        </div>
      </div>
      <!-- Progress bar -->
      <div class="mt-4 w-full bg-gray-200 rounded-full h-3">
        <div class="h-3 rounded-full transition-all duration-500" :class="progressBarColor" :style="{ width: progressPercent + '%' }"></div>
      </div>
    </div>

    <!-- Checklist Items -->
    <div class="space-y-4">
      <!-- Assetto Organizzativo -->
      <div class="bg-white rounded-xl shadow p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
          <Icon name="heroicons:building-office" class="w-5 h-5 text-blue-600" />
          Assetto Organizzativo
        </h3>
        <div class="space-y-3">
          <label v-for="item in organizzativoItems" :key="item.id" class="flex items-start gap-3 p-3 rounded-lg hover:bg-gray-50 cursor-pointer transition">
            <input type="checkbox" v-model="item.checked" @change="saveChecklist" class="w-5 h-5 mt-0.5 rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
            <div>
              <span class="font-medium text-gray-800">{{ item.titolo }}</span>
              <p class="text-gray-500 text-sm mt-0.5">{{ item.descrizione }}</p>
            </div>
          </label>
        </div>
      </div>

      <!-- Assetto Amministrativo -->
      <div class="bg-white rounded-xl shadow p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
          <Icon name="heroicons:document-text" class="w-5 h-5 text-purple-600" />
          Assetto Amministrativo
        </h3>
        <div class="space-y-3">
          <label v-for="item in amministrativoItems" :key="item.id" class="flex items-start gap-3 p-3 rounded-lg hover:bg-gray-50 cursor-pointer transition">
            <input type="checkbox" v-model="item.checked" @change="saveChecklist" class="w-5 h-5 mt-0.5 rounded border-gray-300 text-purple-600 focus:ring-purple-500" />
            <div>
              <span class="font-medium text-gray-800">{{ item.titolo }}</span>
              <p class="text-gray-500 text-sm mt-0.5">{{ item.descrizione }}</p>
            </div>
          </label>
        </div>
      </div>

      <!-- Assetto Contabile -->
      <div class="bg-white rounded-xl shadow p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
          <Icon name="heroicons:calculator" class="w-5 h-5 text-green-600" />
          Assetto Contabile
        </h3>
        <div class="space-y-3">
          <label v-for="item in contabileItems" :key="item.id" class="flex items-start gap-3 p-3 rounded-lg hover:bg-gray-50 cursor-pointer transition">
            <input type="checkbox" v-model="item.checked" @change="saveChecklist" class="w-5 h-5 mt-0.5 rounded border-gray-300 text-green-600 focus:ring-green-500" />
            <div>
              <span class="font-medium text-gray-800">{{ item.titolo }}</span>
              <p class="text-gray-500 text-sm mt-0.5">{{ item.descrizione }}</p>
            </div>
          </label>
        </div>
      </div>
    </div>

    <!-- Riferimenti normativi -->
    <div class="mt-6 bg-gray-50 rounded-xl p-6">
      <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
        <Icon name="heroicons:book-open" class="w-5 h-5 text-gray-600" />
        Riferimenti Normativi
      </h3>
      <ul class="space-y-2 text-sm text-gray-600">
        <li><strong>Art. 2086 c.c.</strong> - Gestione dell'impresa: obbligo di assetti adeguati</li>
        <li><strong>Art. 2409 c.c.</strong> - Denunzia al tribunale per gravi irregolarita</li>
        <li><strong>D.Lgs. 14/2019</strong> - Codice della crisi d'impresa e dell'insolvenza</li>
        <li><strong>D.Lgs. 83/2022</strong> - Attuazione direttiva UE 2019/1023 su ristrutturazione preventiva</li>
        <li><strong>Indicatori CNDCEC</strong> - 7 KPI obbligatori per la rilevazione tempestiva della crisi</li>
      </ul>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  layout: 'dashboard',
  middleware: 'auth'
})

interface ChecklistItem {
  id: string
  categoria: 'organizzativo' | 'amministrativo' | 'contabile'
  titolo: string
  descrizione: string
  checked: boolean
}

const checklistItems = reactive<ChecklistItem[]>([
  // Organizzativo
  { id: 'org_organigramma', categoria: 'organizzativo', titolo: 'Organigramma aziendale aggiornato', descrizione: 'Struttura organizzativa con ruoli, responsabilita e linee di riporto chiaramente definite', checked: false },
  { id: 'org_mansionario', categoria: 'organizzativo', titolo: 'Mansionario documentato', descrizione: 'Descrizione dettagliata delle mansioni, competenze richieste e responsabilita per ogni ruolo', checked: false },
  { id: 'org_deleghe', categoria: 'organizzativo', titolo: 'Sistema di deleghe e procure', descrizione: 'Deleghe operative e procure formalizzate con limiti di spesa e ambiti di competenza', checked: false },
  { id: 'org_rischi', categoria: 'organizzativo', titolo: 'Sistema di gestione rischi aziendali', descrizione: 'Identificazione, valutazione e monitoraggio periodico dei principali rischi operativi e finanziari', checked: false },
  { id: 'org_continuita', categoria: 'organizzativo', titolo: 'Piano di continuita aziendale', descrizione: 'Procedure per garantire la continuita operativa in caso di eventi avversi o crisi', checked: false },

  // Amministrativo
  { id: 'amm_budget', categoria: 'amministrativo', titolo: 'Budget annuale e previsionale', descrizione: 'Budget economico e finanziario con proiezioni a 6-12 mesi e analisi degli scostamenti', checked: false },
  { id: 'amm_piano', categoria: 'amministrativo', titolo: 'Piano industriale / Business plan', descrizione: 'Documento strategico con obiettivi, strategie, proiezioni economiche e piano di investimenti', checked: false },
  { id: 'amm_reporting', categoria: 'amministrativo', titolo: 'Reporting periodico alla direzione', descrizione: 'Report mensili o trimestrali con KPI, analisi degli scostamenti e indicatori di allerta', checked: false },
  { id: 'amm_tesoreria', categoria: 'amministrativo', titolo: 'Gestione tesoreria e flussi di cassa', descrizione: 'Monitoraggio sistematico della liquidita, previsione dei flussi e gestione dei rapporti bancari', checked: false },
  { id: 'amm_crediti', categoria: 'amministrativo', titolo: 'Procedura gestione crediti commerciali', descrizione: 'Processo formalizzato di sollecito, recupero crediti e monitoraggio dei crediti scaduti', checked: false },

  // Contabile
  { id: 'cont_contabilita', categoria: 'contabile', titolo: 'Contabilita aggiornata e tempestiva', descrizione: 'Registrazioni contabili aggiornate che consentano la formazione tempestiva del bilancio', checked: false },
  { id: 'cont_bilancio', categoria: 'contabile', titolo: 'Analisi di bilancio periodica', descrizione: 'Analisi per indici e flussi con calcolo degli indicatori obbligatori CNDCEC', checked: false },
  { id: 'cont_nota', categoria: 'contabile', titolo: 'Nota integrativa completa e trasparente', descrizione: 'Nota integrativa con informazioni su parti correlate, impegni, rischi e operazioni rilevanti', checked: false },
  { id: 'cont_kpi', categoria: 'contabile', titolo: 'Monitoraggio KPI obbligatori CNDCEC', descrizione: 'Calcolo periodico dei 7 indicatori della crisi definiti dal CNDCEC (PN, DSCR, CR, OF/Ric, ecc.)', checked: false },
  { id: 'cont_fiscale', categoria: 'contabile', titolo: 'Regolarita fiscale e contributiva', descrizione: 'Adempimenti fiscali e contributivi in regola, assenza di debiti tributari significativi', checked: false },
])

const organizzativoItems = computed(() => checklistItems.filter(i => i.categoria === 'organizzativo'))
const amministrativoItems = computed(() => checklistItems.filter(i => i.categoria === 'amministrativo'))
const contabileItems = computed(() => checklistItems.filter(i => i.categoria === 'contabile'))

const completedCount = computed(() => checklistItems.filter(i => i.checked).length)
const progressPercent = computed(() => Math.round((completedCount.value / checklistItems.length) * 100))

const scoreColor = computed(() => {
  const pct = progressPercent.value
  if (pct >= 80) return 'text-green-600'
  if (pct >= 50) return 'text-yellow-600'
  return 'text-red-600'
})

const scoreLabel = computed(() => {
  const pct = progressPercent.value
  if (pct >= 80) return 'Conforme'
  if (pct >= 50) return 'Parziale'
  if (pct > 0) return 'Insufficiente'
  return 'Non conforme'
})

const progressBarColor = computed(() => {
  const pct = progressPercent.value
  if (pct >= 80) return 'bg-green-500'
  if (pct >= 50) return 'bg-yellow-500'
  return 'bg-red-500'
})

const saveChecklist = () => {
  const data = checklistItems.map(i => ({ id: i.id, checked: i.checked }))
  localStorage.setItem('aa_checklist', JSON.stringify(data))
}

const loadChecklist = () => {
  try {
    const stored = localStorage.getItem('aa_checklist')
    if (stored) {
      const data = JSON.parse(stored) as { id: string; checked: boolean }[]
      data.forEach(d => {
        const item = checklistItems.find(i => i.id === d.id)
        if (item) item.checked = d.checked
      })
    }
  } catch (e) {
    // ignore
  }
}

onMounted(() => {
  loadChecklist()
})
</script>
