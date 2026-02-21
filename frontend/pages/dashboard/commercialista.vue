<template>
  <div class="space-y-6">
    <!-- Header -->
    <div>
      <h1 class="text-2xl font-bold text-gray-900">Il Mio Commercialista</h1>
      <p class="text-gray-500 mt-1">Collega il tuo commercialista per dargli accesso ai tuoi dati</p>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="flex items-center justify-center py-12">
      <Icon name="heroicons:arrow-path" class="w-8 h-8 text-blue-600 animate-spin" />
    </div>

    <template v-else>
      <!-- Linked Commercialista -->
      <div v-if="commercialista" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-purple-100 rounded-xl flex items-center justify-center">
              <Icon name="heroicons:user-circle" class="w-8 h-8 text-purple-600" />
            </div>
            <div>
              <h3 class="text-lg font-semibold text-gray-900">{{ commercialista.nome }} {{ commercialista.cognome }}</h3>
              <p class="text-sm text-gray-500">{{ commercialista.email }}</p>
              <p v-if="commercialista.studio_nome" class="text-sm text-purple-600">{{ commercialista.studio_nome }}</p>
            </div>
          </div>
          <div class="flex items-center gap-3">
            <span class="bg-green-100 text-green-700 text-xs px-3 py-1 rounded-full font-medium">Collegato</span>
            <button
              @click="showUnlinkModal = true"
              class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg"
              title="Scollega"
            >
              <Icon name="heroicons:x-circle" class="w-5 h-5" />
            </button>
          </div>
        </div>

        <div class="mt-4 bg-blue-50 border border-blue-200 rounded-lg p-4">
          <div class="flex gap-3">
            <Icon name="heroicons:information-circle" class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" />
            <div class="text-sm text-blue-700">
              <p class="font-medium mb-1">Cosa vede il tuo commercialista?</p>
              <ul class="list-disc list-inside space-y-1 text-blue-600">
                <li>Dashboard con i KPI della tua azienda</li>
                <li>Dati economici inseriti</li>
                <li>Indicatori di salute aziendale</li>
              </ul>
            </div>
          </div>
        </div>
      </div>

      <!-- No Commercialista - Invite Form -->
      <div v-else class="space-y-6">
        <!-- How it works -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
          <h2 class="text-lg font-semibold text-gray-900 mb-4">Come funziona</h2>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="text-center">
              <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
                <span class="text-xl font-bold text-blue-600">1</span>
              </div>
              <h3 class="font-medium text-gray-900">Invita</h3>
              <p class="text-sm text-gray-500 mt-1">Inserisci l'email del tuo commercialista</p>
            </div>
            <div class="text-center">
              <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
                <span class="text-xl font-bold text-blue-600">2</span>
              </div>
              <h3 class="font-medium text-gray-900">Accetta</h3>
              <p class="text-sm text-gray-500 mt-1">Il commercialista riceve e accetta l'invito</p>
            </div>
            <div class="text-center">
              <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
                <span class="text-xl font-bold text-blue-600">3</span>
              </div>
              <h3 class="font-medium text-gray-900">Collabora</h3>
              <p class="text-sm text-gray-500 mt-1">Vede i tuoi dati e ti supporta</p>
            </div>
          </div>
        </div>

        <!-- Invite Form -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
          <h2 class="text-lg font-semibold text-gray-900 mb-4">Invita il tuo Commercialista</h2>

          <form @submit.prevent="sendInvite" class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Email del Commercialista</label>
              <input
                v-model="inviteEmail"
                type="email"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                placeholder="commercialista@studio.it"
              />
            </div>

            <div v-if="inviteError" class="bg-red-50 text-red-600 text-sm p-3 rounded-lg">
              {{ inviteError }}
            </div>

            <button
              type="submit"
              :disabled="inviteLoading"
              class="bg-blue-600 text-white px-6 py-2 rounded-lg font-medium hover:bg-blue-700 disabled:opacity-50 flex items-center gap-2"
            >
              <Icon name="heroicons:paper-airplane" class="w-5 h-5" />
              {{ inviteLoading ? 'Invio...' : 'Invia Invito' }}
            </button>
          </form>
        </div>

        <!-- Pending Invites -->
        <div v-if="pendingInvites.length > 0" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
          <h2 class="text-lg font-semibold text-gray-900 mb-4">Inviti in Attesa</h2>
          <div class="space-y-3">
            <div
              v-for="invite in pendingInvites"
              :key="invite.id"
              class="flex items-center justify-between p-4 rounded-lg bg-yellow-50 border border-yellow-100"
            >
              <div class="flex items-center gap-3">
                <Icon name="heroicons:clock" class="w-5 h-5 text-yellow-600" />
                <div>
                  <div class="font-medium text-gray-900">{{ invite.recipient_email }}</div>
                  <div class="text-sm text-gray-500">Inviato il {{ formatDate(invite.created_at) }}</div>
                </div>
              </div>
              <div class="flex items-center gap-2">
                <button
                  @click="copyInviteLink(invite)"
                  class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg"
                  title="Copia link"
                >
                  <Icon name="heroicons:link" class="w-4 h-4" />
                </button>
                <button
                  @click="revokeInvite(invite)"
                  class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg"
                  title="Revoca"
                >
                  <Icon name="heroicons:trash" class="w-4 h-4" />
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </template>

    <!-- Unlink Modal -->
    <div v-if="showUnlinkModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6">
        <div class="text-center mb-6">
          <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <Icon name="heroicons:x-circle" class="w-8 h-8 text-red-600" />
          </div>
          <h2 class="text-xl font-bold text-gray-900 mb-2">Scollega Commercialista</h2>
          <p class="text-gray-600">
            Sei sicuro? Il commercialista non potrà più accedere ai tuoi dati.
          </p>
        </div>
        <div class="flex gap-3">
          <button
            @click="showUnlinkModal = false"
            class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50"
          >
            Annulla
          </button>
          <button
            @click="unlinkCommercialista"
            :disabled="unlinkLoading"
            class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg font-medium hover:bg-red-700 disabled:opacity-50"
          >
            {{ unlinkLoading ? 'Scollegamento...' : 'Scollega' }}
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
  layout: 'dashboard'
})

const config = useRuntimeConfig()

const loading = ref(true)
const commercialista = ref<any>(null)
const commercialistaLinkId = ref<number | null>(null)
const pendingInvites = ref<any[]>([])

const inviteEmail = ref('')
const inviteLoading = ref(false)
const inviteError = ref('')

const showUnlinkModal = ref(false)
const unlinkLoading = ref(false)

const toast = reactive({
  show: false,
  message: '',
  type: 'success' as 'success' | 'error'
})

const showToast = (message: string, type: 'success' | 'error') => {
  toast.message = message
  toast.type = type
  toast.show = true
  setTimeout(() => { toast.show = false }, 3000)
}

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('it-IT', { day: '2-digit', month: 'short', year: 'numeric' })
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
    // Load linked commercialisti
    const res = await $fetch<{ success: boolean; data: any[] }>(`${config.public.apiBase}/miei-commercialisti`, {
      headers: getHeaders()
    })

    if (res.success && res.data.length > 0) {
      const link = res.data[0]
      commercialista.value = {
        nome: link.nome,
        cognome: link.cognome,
        email: link.email,
        studio_nome: link.studio_nome
      }
      commercialistaLinkId.value = link.link_id
    }

    // Load pending invites
    const invRes = await $fetch<{ success: boolean; data: any[] }>(`${config.public.apiBase}/inviti-v2`, {
      headers: getHeaders()
    })

    if (invRes.success) {
      pendingInvites.value = invRes.data.filter((i: any) => i.stato === 'pending' && i.tipo === 'client_to_commercialista')
    }
  } catch (e) {
    console.error('Error loading data:', e)
  } finally {
    loading.value = false
  }
}

const sendInvite = async () => {
  inviteLoading.value = true
  inviteError.value = ''

  try {
    const user = JSON.parse(localStorage.getItem('aa_user') || '{}')
    const res = await $fetch<{ success: boolean; data: any; message?: string }>(`${config.public.apiBase}/inviti-v2`, {
      method: 'POST',
      headers: {
        ...getHeaders(),
        'Content-Type': 'application/json'
      },
      body: {
        email: inviteEmail.value,
        azienda_id: user.azienda_id
      }
    })

    if (res.success) {
      pendingInvites.value.unshift(res.data)
      inviteEmail.value = ''
      showToast('Invito inviato con successo', 'success')
    } else {
      inviteError.value = res.message || 'Errore durante l\'invio'
    }
  } catch (e: any) {
    inviteError.value = e.data?.message || 'Errore durante l\'invio'
  } finally {
    inviteLoading.value = false
  }
}

const copyInviteLink = async (invite: any) => {
  const link = `${window.location.origin}/invite-v2/${invite.token}`
  try {
    await navigator.clipboard.writeText(link)
    showToast('Link copiato negli appunti', 'success')
  } catch (e) {
    showToast('Errore nella copia', 'error')
  }
}

const revokeInvite = async (invite: any) => {
  try {
    await $fetch(`${config.public.apiBase}/inviti-v2/${invite.id}`, {
      method: 'DELETE',
      headers: getHeaders()
    })
    pendingInvites.value = pendingInvites.value.filter(i => i.id !== invite.id)
    showToast('Invito revocato', 'success')
  } catch (e) {
    showToast('Errore nella revoca', 'error')
  }
}

const unlinkCommercialista = async () => {
  if (!commercialistaLinkId.value) return
  unlinkLoading.value = true

  try {
    await $fetch(`${config.public.apiBase}/link/${commercialistaLinkId.value}`, {
      method: 'DELETE',
      headers: getHeaders()
    })
    commercialista.value = null
    commercialistaLinkId.value = null
    showUnlinkModal.value = false
    showToast('Commercialista scollegato', 'success')
  } catch (e) {
    showToast('Errore nello scollegamento', 'error')
  } finally {
    unlinkLoading.value = false
  }
}

onMounted(loadData)
</script>
