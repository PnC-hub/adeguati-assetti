<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Aziende Clienti</h1>
        <p class="text-gray-500 mt-1">Gestisci le aziende dei tuoi clienti</p>
      </div>
      <button
        @click="showAddModal = true"
        class="bg-purple-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-purple-700 flex items-center gap-2"
      >
        <Icon name="heroicons:plus" class="w-5 h-5" />
        Aggiungi Azienda
      </button>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
      <div class="flex flex-wrap gap-4">
        <div class="flex-1 min-w-[200px]">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Cerca per nome, P.IVA..."
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
          />
        </div>
        <select
          v-model="filterStato"
          class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"
        >
          <option value="">Tutti gli stati</option>
          <option value="verde">KPI OK</option>
          <option value="giallo">KPI Attenzione</option>
          <option value="rosso">KPI Critici</option>
        </select>
        <select
          v-model="filterDimensione"
          class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"
        >
          <option value="">Tutte le dimensioni</option>
          <option value="micro">Micro</option>
          <option value="piccola">Piccola</option>
          <option value="media">Media</option>
          <option value="grande">Grande</option>
        </select>
      </div>
    </div>

    <!-- Aziende Grid -->
    <div v-if="loading" class="text-center py-12">
      <Icon name="heroicons:arrow-path" class="w-8 h-8 text-purple-600 animate-spin mx-auto" />
      <p class="text-gray-500 mt-2">Caricamento aziende...</p>
    </div>

    <div v-else-if="filteredAziende.length === 0" class="bg-white rounded-xl shadow-sm p-12 border border-gray-100 text-center">
      <Icon name="heroicons:building-office-2" class="w-16 h-16 text-gray-300 mx-auto mb-4" />
      <h3 class="text-lg font-medium text-gray-900 mb-2">Nessuna azienda trovata</h3>
      <p class="text-gray-500 mb-4">
        {{ searchQuery || filterStato || filterDimensione ? 'Prova a modificare i filtri di ricerca' : 'Inizia aggiungendo la tua prima azienda cliente' }}
      </p>
      <button
        v-if="!searchQuery && !filterStato && !filterDimensione"
        @click="showAddModal = true"
        class="bg-purple-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-purple-700"
      >
        Aggiungi Azienda
      </button>
    </div>

    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <div
        v-for="azienda in filteredAziende"
        :key="azienda.id"
        class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow"
      >
        <div class="flex items-start justify-between mb-4">
          <div class="flex items-center gap-3">
            <div
              class="w-12 h-12 rounded-lg flex items-center justify-center"
              :class="getStatoClass(azienda.stato_kpi)"
            >
              <Icon :name="getStatoIcon(azienda.stato_kpi)" class="w-6 h-6" />
            </div>
            <div>
              <h3 class="font-semibold text-gray-900">{{ azienda.nome }}</h3>
              <p class="text-sm text-gray-500">{{ azienda.p_iva || 'P.IVA non inserita' }}</p>
            </div>
          </div>
          <span
            class="text-xs px-2 py-1 rounded-full"
            :class="getDimensioneClass(azienda.dimensione)"
          >
            {{ azienda.dimensione }}
          </span>
        </div>

        <!-- KPI Summary -->
        <div class="grid grid-cols-3 gap-2 mb-4">
          <div class="text-center p-2 bg-gray-50 rounded-lg">
            <div class="text-lg font-bold" :class="azienda.kpi_verdi > 0 ? 'text-green-600' : 'text-gray-400'">
              {{ azienda.kpi_verdi || 0 }}
            </div>
            <div class="text-xs text-gray-500">OK</div>
          </div>
          <div class="text-center p-2 bg-gray-50 rounded-lg">
            <div class="text-lg font-bold" :class="azienda.kpi_gialli > 0 ? 'text-yellow-600' : 'text-gray-400'">
              {{ azienda.kpi_gialli || 0 }}
            </div>
            <div class="text-xs text-gray-500">Att.</div>
          </div>
          <div class="text-center p-2 bg-gray-50 rounded-lg">
            <div class="text-lg font-bold" :class="azienda.kpi_rossi > 0 ? 'text-red-600' : 'text-gray-400'">
              {{ azienda.kpi_rossi || 0 }}
            </div>
            <div class="text-xs text-gray-500">Crit.</div>
          </div>
        </div>

        <!-- Last Update -->
        <div class="text-xs text-gray-400 mb-4">
          Ultimo aggiornamento: {{ azienda.ultimo_aggiornamento ? formatDate(azienda.ultimo_aggiornamento) : 'Mai' }}
        </div>

        <!-- Actions -->
        <div class="flex gap-2">
          <NuxtLink
            :to="`/consulente/aziende/${azienda.id}`"
            class="flex-1 text-center px-3 py-2 bg-purple-50 text-purple-600 rounded-lg text-sm font-medium hover:bg-purple-100"
          >
            Dettagli
          </NuxtLink>
          <button
            @click="openKpiModal(azienda)"
            class="px-3 py-2 bg-gray-50 text-gray-600 rounded-lg text-sm font-medium hover:bg-gray-100"
          >
            <Icon name="heroicons:chart-bar" class="w-4 h-4" />
          </button>
          <button
            @click="confirmDelete(azienda)"
            class="px-3 py-2 bg-gray-50 text-red-600 rounded-lg text-sm font-medium hover:bg-red-50"
          >
            <Icon name="heroicons:trash" class="w-4 h-4" />
          </button>
        </div>
      </div>
    </div>

    <!-- Add Modal -->
    <div v-if="showAddModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-2xl shadow-xl max-w-lg w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-100">
          <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-900">Aggiungi Azienda</h2>
            <button @click="showAddModal = false" class="text-gray-400 hover:text-gray-600">
              <Icon name="heroicons:x-mark" class="w-6 h-6" />
            </button>
          </div>
        </div>

        <form @submit.prevent="handleAddAzienda" class="p-6 space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nome Azienda *</label>
            <input
              v-model="newAzienda.nome"
              type="text"
              required
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
              placeholder="Rossi S.r.l."
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Partita IVA</label>
            <input
              v-model="newAzienda.p_iva"
              type="text"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
              placeholder="12345678901"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Codice ATECO</label>
            <input
              v-model="newAzienda.codice_ateco"
              type="text"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
              placeholder="62.01.00"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Dimensione *</label>
            <select
              v-model="newAzienda.dimensione"
              required
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
            >
              <option value="micro">Micro (< 10 dipendenti)</option>
              <option value="piccola">Piccola (10-49 dipendenti)</option>
              <option value="media">Media (50-249 dipendenti)</option>
              <option value="grande">Grande (250+ dipendenti)</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email Referente</label>
            <input
              v-model="newAzienda.email_referente"
              type="email"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
              placeholder="referente@azienda.it"
            />
          </div>

          <div v-if="addError" class="bg-red-50 text-red-600 text-sm p-3 rounded-lg">
            {{ addError }}
          </div>

          <div class="flex gap-3 pt-4">
            <button
              type="button"
              @click="showAddModal = false"
              class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50"
            >
              Annulla
            </button>
            <button
              type="submit"
              :disabled="addLoading"
              class="flex-1 px-4 py-2 bg-purple-600 text-white rounded-lg font-medium hover:bg-purple-700 disabled:opacity-50"
            >
              {{ addLoading ? 'Salvataggio...' : 'Aggiungi' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div v-if="showDeleteModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6">
        <div class="text-center mb-6">
          <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <Icon name="heroicons:exclamation-triangle" class="w-8 h-8 text-red-600" />
          </div>
          <h2 class="text-xl font-bold text-gray-900 mb-2">Conferma Eliminazione</h2>
          <p class="text-gray-600">
            Sei sicuro di voler eliminare <strong>{{ deleteTarget?.nome }}</strong>?
            Questa azione è irreversibile.
          </p>
        </div>

        <div class="flex gap-3">
          <button
            @click="showDeleteModal = false"
            class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50"
          >
            Annulla
          </button>
          <button
            @click="handleDelete"
            :disabled="deleteLoading"
            class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg font-medium hover:bg-red-700 disabled:opacity-50"
          >
            {{ deleteLoading ? 'Eliminazione...' : 'Elimina' }}
          </button>
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
const route = useRoute()

const aziende = ref<any[]>([])
const loading = ref(true)
const searchQuery = ref('')
const filterStato = ref('')
const filterDimensione = ref('')

const showAddModal = ref(false)
const addLoading = ref(false)
const addError = ref('')
const newAzienda = reactive({
  nome: '',
  p_iva: '',
  codice_ateco: '',
  dimensione: 'micro',
  email_referente: ''
})

const showDeleteModal = ref(false)
const deleteLoading = ref(false)
const deleteTarget = ref<any>(null)

const filteredAziende = computed(() => {
  return aziende.value.filter(a => {
    const matchSearch = !searchQuery.value ||
      a.nome.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      (a.p_iva && a.p_iva.includes(searchQuery.value))

    const matchStato = !filterStato.value || a.stato_kpi === filterStato.value
    const matchDimensione = !filterDimensione.value || a.dimensione === filterDimensione.value

    return matchSearch && matchStato && matchDimensione
  })
})

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('it-IT', { day: '2-digit', month: 'short', year: 'numeric' })
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

const getDimensioneClass = (dimensione: string) => {
  switch (dimensione) {
    case 'micro': return 'bg-blue-100 text-blue-700'
    case 'piccola': return 'bg-green-100 text-green-700'
    case 'media': return 'bg-yellow-100 text-yellow-700'
    case 'grande': return 'bg-purple-100 text-purple-700'
    default: return 'bg-gray-100 text-gray-700'
  }
}

const openKpiModal = (azienda: any) => {
  // Navigate to detail page with KPI tab
  navigateTo(`/consulente/aziende/${azienda.id}?tab=kpi`)
}

const confirmDelete = (azienda: any) => {
  deleteTarget.value = azienda
  showDeleteModal.value = true
}

const handleAddAzienda = async () => {
  addLoading.value = true
  addError.value = ''

  const token = localStorage.getItem('aa_token')
  if (!token) return

  try {
    const response = await $fetch<{ success: boolean; data: any; message?: string }>(
      `${config.public.apiBase}/aziende-cliente`,
      {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${token}`,
          'X-API-Key': config.public.apiKey,
          'Content-Type': 'application/json'
        },
        body: newAzienda
      }
    )

    if (response.success) {
      aziende.value.unshift(response.data)
      showAddModal.value = false
      Object.assign(newAzienda, {
        nome: '',
        p_iva: '',
        codice_ateco: '',
        dimensione: 'micro',
        email_referente: ''
      })
    } else {
      addError.value = response.message || 'Errore durante il salvataggio'
    }
  } catch (e: any) {
    addError.value = e.data?.message || 'Errore durante il salvataggio'
  } finally {
    addLoading.value = false
  }
}

const handleDelete = async () => {
  if (!deleteTarget.value) return

  deleteLoading.value = true
  const token = localStorage.getItem('aa_token')
  if (!token) return

  try {
    await $fetch(`${config.public.apiBase}/aziende-cliente/${deleteTarget.value.id}`, {
      method: 'DELETE',
      headers: {
        'Authorization': `Bearer ${token}`,
        'X-API-Key': config.public.apiKey
      }
    })

    aziende.value = aziende.value.filter(a => a.id !== deleteTarget.value.id)
    showDeleteModal.value = false
    deleteTarget.value = null
  } catch (e) {
    console.error('Error deleting azienda:', e)
  } finally {
    deleteLoading.value = false
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
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  loadAziende()

  // Check if we should open add modal
  if (route.query.action === 'new') {
    showAddModal.value = true
  }
})
</script>
