<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Invita Clienti</h1>
        <p class="text-gray-500 mt-1">Invia accessi readonly ai tuoi clienti</p>
      </div>
      <button
        @click="showInviteModal = true"
        class="bg-purple-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-purple-700 flex items-center gap-2"
      >
        <Icon name="heroicons:paper-airplane" class="w-5 h-5" />
        Nuovo Invito
      </button>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <div class="flex items-center gap-3">
          <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
            <Icon name="heroicons:envelope" class="w-6 h-6 text-blue-600" />
          </div>
          <div>
            <div class="text-2xl font-bold text-gray-900">{{ stats.inviati }}</div>
            <div class="text-sm text-gray-500">Inviti Inviati</div>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <div class="flex items-center gap-3">
          <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
            <Icon name="heroicons:clock" class="w-6 h-6 text-yellow-600" />
          </div>
          <div>
            <div class="text-2xl font-bold text-gray-900">{{ stats.pendenti }}</div>
            <div class="text-sm text-gray-500">In Attesa</div>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <div class="flex items-center gap-3">
          <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
            <Icon name="heroicons:check-circle" class="w-6 h-6 text-green-600" />
          </div>
          <div>
            <div class="text-2xl font-bold text-gray-900">{{ stats.accettati }}</div>
            <div class="text-sm text-gray-500">Accettati</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Inviti List -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
      <div class="p-4 border-b border-gray-100">
        <div class="flex flex-wrap gap-4 items-center justify-between">
          <div class="flex gap-2">
            <button
              v-for="filter in filters"
              :key="filter.value"
              @click="activeFilter = filter.value"
              class="px-4 py-2 rounded-lg text-sm font-medium transition"
              :class="activeFilter === filter.value ? 'bg-purple-100 text-purple-700' : 'text-gray-600 hover:bg-gray-100'"
            >
              {{ filter.label }}
            </button>
          </div>
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Cerca per email..."
            class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 w-64"
          />
        </div>
      </div>

      <div v-if="loading" class="p-12 text-center">
        <Icon name="heroicons:arrow-path" class="w-8 h-8 text-purple-600 animate-spin mx-auto" />
      </div>

      <div v-else-if="filteredInviti.length === 0" class="p-12 text-center">
        <Icon name="heroicons:inbox" class="w-16 h-16 text-gray-300 mx-auto mb-4" />
        <h3 class="text-lg font-medium text-gray-900 mb-2">Nessun invito trovato</h3>
        <p class="text-gray-500 mb-4">
          {{ searchQuery ? 'Prova a modificare la ricerca' : 'Inizia invitando i tuoi clienti' }}
        </p>
        <button
          v-if="!searchQuery"
          @click="showInviteModal = true"
          class="bg-purple-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-purple-700"
        >
          Nuovo Invito
        </button>
      </div>

      <div v-else class="divide-y divide-gray-100">
        <div
          v-for="invito in filteredInviti"
          :key="invito.id"
          class="p-4 hover:bg-gray-50"
        >
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
              <div
                class="w-10 h-10 rounded-full flex items-center justify-center"
                :class="getStatoClass(invito.stato)"
              >
                <Icon :name="getStatoIcon(invito.stato)" class="w-5 h-5" />
              </div>
              <div>
                <div class="font-medium text-gray-900">{{ invito.email }}</div>
                <div class="text-sm text-gray-500">
                  {{ invito.azienda_nome }} • Inviato il {{ formatDate(invito.created_at) }}
                </div>
              </div>
            </div>
            <div class="flex items-center gap-3">
              <span
                class="text-xs px-2 py-1 rounded-full"
                :class="getStatoBadgeClass(invito.stato)"
              >
                {{ getStatoLabel(invito.stato) }}
              </span>
              <div class="flex gap-1">
                <button
                  v-if="invito.stato === 'pending'"
                  @click="resendInvite(invito)"
                  class="p-2 text-gray-400 hover:text-purple-600 hover:bg-purple-50 rounded-lg"
                  title="Reinvia"
                >
                  <Icon name="heroicons:arrow-path" class="w-4 h-4" />
                </button>
                <button
                  v-if="invito.stato === 'pending'"
                  @click="copyInviteLink(invito)"
                  class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg"
                  title="Copia link"
                >
                  <Icon name="heroicons:link" class="w-4 h-4" />
                </button>
                <button
                  v-if="invito.stato !== 'accepted'"
                  @click="confirmDelete(invito)"
                  class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg"
                  title="Elimina"
                >
                  <Icon name="heroicons:trash" class="w-4 h-4" />
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- New Invite Modal -->
    <div v-if="showInviteModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-2xl shadow-xl max-w-lg w-full">
        <div class="p-6 border-b border-gray-100">
          <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-900">Nuovo Invito</h2>
            <button @click="showInviteModal = false" class="text-gray-400 hover:text-gray-600">
              <Icon name="heroicons:x-mark" class="w-6 h-6" />
            </button>
          </div>
        </div>

        <form @submit.prevent="handleInvite" class="p-6 space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Azienda Cliente *</label>
            <select
              v-model="newInvite.azienda_cliente_id"
              required
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"
            >
              <option value="">Seleziona azienda</option>
              <option v-for="azienda in aziende" :key="azienda.id" :value="azienda.id">
                {{ azienda.nome }}
              </option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email Cliente *</label>
            <input
              v-model="newInvite.email"
              type="email"
              required
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"
              placeholder="cliente@azienda.it"
            />
          </div>

          <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex gap-3">
              <Icon name="heroicons:information-circle" class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" />
              <div class="text-sm text-blue-700">
                <p class="font-medium mb-1">Cosa può fare il cliente?</p>
                <ul class="list-disc list-inside space-y-1 text-blue-600">
                  <li>Visualizzare la dashboard della propria azienda</li>
                  <li>Vedere i KPI e gli indicatori di salute</li>
                  <li>Scaricare report in PDF</li>
                </ul>
              </div>
            </div>
          </div>

          <div v-if="inviteError" class="bg-red-50 text-red-600 text-sm p-3 rounded-lg">
            {{ inviteError }}
          </div>

          <div class="flex gap-3 pt-4">
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

    <!-- Delete Confirmation Modal -->
    <div v-if="showDeleteModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6">
        <div class="text-center mb-6">
          <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <Icon name="heroicons:trash" class="w-8 h-8 text-red-600" />
          </div>
          <h2 class="text-xl font-bold text-gray-900 mb-2">Elimina Invito</h2>
          <p class="text-gray-600">
            Sei sicuro di voler eliminare l'invito per <strong>{{ deleteTarget?.email }}</strong>?
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

    <!-- Toast -->
    <div
      v-if="toast.show"
      class="fixed bottom-4 right-4 px-4 py-3 rounded-lg shadow-lg flex items-center gap-2"
      :class="toast.type === 'success' ? 'bg-green-600 text-white' : 'bg-red-600 text-white'"
    >
      <Icon :name="toast.type === 'success' ? 'heroicons:check-circle' : 'heroicons:x-circle'" class="w-5 h-5" />
      {{ toast.message }}
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  layout: 'consulente'
})

const config = useRuntimeConfig()

const inviti = ref<any[]>([])
const aziende = ref<any[]>([])
const loading = ref(true)
const searchQuery = ref('')
const activeFilter = ref('all')

const showInviteModal = ref(false)
const inviteLoading = ref(false)
const inviteError = ref('')
const newInvite = reactive({
  azienda_cliente_id: '',
  email: ''
})

const showDeleteModal = ref(false)
const deleteLoading = ref(false)
const deleteTarget = ref<any>(null)

const toast = reactive({
  show: false,
  message: '',
  type: 'success' as 'success' | 'error'
})

const filters = [
  { label: 'Tutti', value: 'all' },
  { label: 'In Attesa', value: 'pending' },
  { label: 'Accettati', value: 'accepted' },
  { label: 'Scaduti', value: 'expired' }
]

const stats = computed(() => ({
  inviati: inviti.value.length,
  pendenti: inviti.value.filter(i => i.stato === 'pending').length,
  accettati: inviti.value.filter(i => i.stato === 'accepted').length
}))

const filteredInviti = computed(() => {
  return inviti.value.filter(i => {
    const matchSearch = !searchQuery.value ||
      i.email.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      i.azienda_nome?.toLowerCase().includes(searchQuery.value.toLowerCase())

    const matchFilter = activeFilter.value === 'all' || i.stato === activeFilter.value

    return matchSearch && matchFilter
  })
})

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('it-IT', { day: '2-digit', month: 'short', year: 'numeric' })
}

const getStatoClass = (stato: string) => {
  switch (stato) {
    case 'pending': return 'bg-yellow-100 text-yellow-600'
    case 'accepted': return 'bg-green-100 text-green-600'
    case 'expired': return 'bg-gray-100 text-gray-600'
    default: return 'bg-gray-100 text-gray-600'
  }
}

const getStatoIcon = (stato: string) => {
  switch (stato) {
    case 'pending': return 'heroicons:clock'
    case 'accepted': return 'heroicons:check-circle'
    case 'expired': return 'heroicons:x-circle'
    default: return 'heroicons:question-mark-circle'
  }
}

const getStatoBadgeClass = (stato: string) => {
  switch (stato) {
    case 'pending': return 'bg-yellow-100 text-yellow-700'
    case 'accepted': return 'bg-green-100 text-green-700'
    case 'expired': return 'bg-gray-100 text-gray-700'
    default: return 'bg-gray-100 text-gray-700'
  }
}

const getStatoLabel = (stato: string) => {
  switch (stato) {
    case 'pending': return 'In attesa'
    case 'accepted': return 'Accettato'
    case 'expired': return 'Scaduto'
    default: return stato
  }
}

const showToast = (message: string, type: 'success' | 'error') => {
  toast.message = message
  toast.type = type
  toast.show = true
  setTimeout(() => { toast.show = false }, 3000)
}

const copyInviteLink = async (invito: any) => {
  const link = `${window.location.origin}/invite/${invito.token}`
  try {
    await navigator.clipboard.writeText(link)
    showToast('Link copiato negli appunti', 'success')
  } catch (e) {
    showToast('Errore nella copia', 'error')
  }
}

const resendInvite = async (invito: any) => {
  // TODO: API call to resend
  showToast('Invito reinviato', 'success')
}

const confirmDelete = (invito: any) => {
  deleteTarget.value = invito
  showDeleteModal.value = true
}

const handleInvite = async () => {
  inviteLoading.value = true
  inviteError.value = ''

  const token = localStorage.getItem('aa_token')
  if (!token) return

  try {
    const response = await $fetch<{ success: boolean; data: any; message?: string }>(`${config.public.apiBase}/inviti`, {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${token}`,
        'X-API-Key': config.public.apiKey,
        'Content-Type': 'application/json'
      },
      body: newInvite
    })

    if (response.success) {
      inviti.value.unshift(response.data)
      showInviteModal.value = false
      Object.assign(newInvite, { azienda_cliente_id: '', email: '' })
      showToast('Invito inviato con successo', 'success')
    } else {
      inviteError.value = response.message || 'Errore durante l\'invio'
    }
  } catch (e: any) {
    inviteError.value = e.data?.message || 'Errore durante l\'invio'
  } finally {
    inviteLoading.value = false
  }
}

const handleDelete = async () => {
  if (!deleteTarget.value) return

  deleteLoading.value = true
  const token = localStorage.getItem('aa_token')
  if (!token) return

  try {
    await $fetch(`${config.public.apiBase}/inviti/${deleteTarget.value.id}`, {
      method: 'DELETE',
      headers: {
        'Authorization': `Bearer ${token}`,
        'X-API-Key': config.public.apiKey
      }
    })

    inviti.value = inviti.value.filter(i => i.id !== deleteTarget.value.id)
    showDeleteModal.value = false
    deleteTarget.value = null
    showToast('Invito eliminato', 'success')
  } catch (e) {
    showToast('Errore durante l\'eliminazione', 'error')
  } finally {
    deleteLoading.value = false
  }
}

const loadData = async () => {
  const token = localStorage.getItem('aa_token')
  if (!token) return

  try {
    const [invitiRes, aziendeRes] = await Promise.all([
      $fetch<{ success: boolean; data: any[] }>(`${config.public.apiBase}/inviti`, {
        headers: {
          'Authorization': `Bearer ${token}`,
          'X-API-Key': config.public.apiKey
        }
      }),
      $fetch<{ success: boolean; data: any[] }>(`${config.public.apiBase}/aziende-cliente`, {
        headers: {
          'Authorization': `Bearer ${token}`,
          'X-API-Key': config.public.apiKey
        }
      })
    ])

    if (invitiRes.success) inviti.value = invitiRes.data
    if (aziendeRes.success) aziende.value = aziendeRes.data
  } catch (e) {
    console.error('Error loading data:', e)
  } finally {
    loading.value = false
  }
}

onMounted(loadData)
</script>
