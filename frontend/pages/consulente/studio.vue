<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Impostazioni Studio</h1>
        <p class="text-gray-600 mt-1">Configura i dati del tuo studio</p>
      </div>
    </div>

    <!-- Studio Info -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
      <h2 class="text-lg font-semibold text-gray-900 mb-4">Informazioni Studio</h2>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Nome Studio</label>
          <input
            v-model="studioForm.nome"
            type="text"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
            placeholder="Studio Commercialista Rossi"
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Partita IVA</label>
          <input
            v-model="studioForm.partita_iva"
            type="text"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
            placeholder="IT12345678901"
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Codice Fiscale</label>
          <input
            v-model="studioForm.codice_fiscale"
            type="text"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
            placeholder="RSSMRA80A01H501Z"
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Telefono</label>
          <input
            v-model="studioForm.telefono"
            type="tel"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
            placeholder="+39 06 1234567"
          />
        </div>

        <div class="md:col-span-2">
          <label class="block text-sm font-medium text-gray-700 mb-1">Indirizzo</label>
          <input
            v-model="studioForm.indirizzo"
            type="text"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
            placeholder="Via Roma 123, 00100 Roma (RM)"
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Email Studio</label>
          <input
            v-model="studioForm.email"
            type="email"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
            placeholder="info@studiosrossi.it"
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Sito Web</label>
          <input
            v-model="studioForm.sito_web"
            type="url"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
            placeholder="https://www.studiorossi.it"
          />
        </div>
      </div>

      <div class="mt-6 flex justify-end">
        <button
          @click="saveStudio"
          :disabled="saving"
          class="bg-purple-600 text-white px-6 py-2 rounded-lg text-sm font-medium hover:bg-purple-700 disabled:opacity-50 transition"
        >
          <span v-if="saving">Salvando...</span>
          <span v-else>Salva Modifiche</span>
        </button>
      </div>
    </div>

    <!-- Branding -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
      <h2 class="text-lg font-semibold text-gray-900 mb-4">Branding</h2>
      <p class="text-sm text-gray-600 mb-4">
        Personalizza l'aspetto dei report con il tuo logo e colori.
      </p>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Logo -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Logo Studio</label>
          <div class="flex items-center gap-4">
            <div class="w-24 h-24 bg-gray-100 rounded-lg flex items-center justify-center border-2 border-dashed border-gray-300">
              <img
                v-if="studioForm.logo_url"
                :src="studioForm.logo_url"
                alt="Logo"
                class="max-w-full max-h-full object-contain"
              />
              <Icon v-else name="heroicons:photo" class="w-8 h-8 text-gray-400" />
            </div>
            <div>
              <input
                ref="logoInput"
                type="file"
                accept="image/*"
                class="hidden"
                @change="handleLogoUpload"
              />
              <button
                @click="$refs.logoInput?.click()"
                class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition"
              >
                Carica Logo
              </button>
              <p class="text-xs text-gray-500 mt-1">PNG o JPG, max 1MB</p>
            </div>
          </div>
        </div>

        <!-- Colors -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Colore Primario</label>
          <div class="flex items-center gap-4">
            <input
              v-model="studioForm.colore_primario"
              type="color"
              class="w-12 h-12 rounded-lg border border-gray-300 cursor-pointer"
            />
            <input
              v-model="studioForm.colore_primario"
              type="text"
              class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
              placeholder="#7C3AED"
            />
          </div>
        </div>
      </div>
    </div>

    <!-- Notifiche -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
      <h2 class="text-lg font-semibold text-gray-900 mb-4">Notifiche</h2>

      <div class="space-y-4">
        <label class="flex items-center justify-between p-4 bg-gray-50 rounded-lg cursor-pointer">
          <div>
            <div class="font-medium text-gray-900">KPI Critici</div>
            <div class="text-sm text-gray-500">Ricevi un'email quando un KPI diventa rosso</div>
          </div>
          <input
            v-model="studioForm.notifica_kpi_critico"
            type="checkbox"
            class="w-5 h-5 text-purple-600 focus:ring-purple-500 rounded"
          />
        </label>

        <label class="flex items-center justify-between p-4 bg-gray-50 rounded-lg cursor-pointer">
          <div>
            <div class="font-medium text-gray-900">Report Settimanale</div>
            <div class="text-sm text-gray-500">Ricevi un riepilogo settimanale di tutte le aziende</div>
          </div>
          <input
            v-model="studioForm.notifica_report_settimanale"
            type="checkbox"
            class="w-5 h-5 text-purple-600 focus:ring-purple-500 rounded"
          />
        </label>

        <label class="flex items-center justify-between p-4 bg-gray-50 rounded-lg cursor-pointer">
          <div>
            <div class="font-medium text-gray-900">Inviti Accettati</div>
            <div class="text-sm text-gray-500">Ricevi notifica quando un cliente accetta l'invito</div>
          </div>
          <input
            v-model="studioForm.notifica_invito_accettato"
            type="checkbox"
            class="w-5 h-5 text-purple-600 focus:ring-purple-500 rounded"
          />
        </label>

        <label class="flex items-center justify-between p-4 bg-gray-50 rounded-lg cursor-pointer">
          <div>
            <div class="font-medium text-gray-900">Scadenze Compliance</div>
            <div class="text-sm text-gray-500">Promemoria per scadenze normative</div>
          </div>
          <input
            v-model="studioForm.notifica_scadenze"
            type="checkbox"
            class="w-5 h-5 text-purple-600 focus:ring-purple-500 rounded"
          />
        </label>
      </div>
    </div>

    <!-- API Keys -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
      <h2 class="text-lg font-semibold text-gray-900 mb-4">Integrazioni API</h2>
      <p class="text-sm text-gray-600 mb-4">
        Chiavi API per integrare Adeguati Assetti con i tuoi sistemi.
      </p>

      <div class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">API Key</label>
          <div class="flex gap-2">
            <input
              :value="apiKey"
              type="text"
              readonly
              class="flex-1 bg-gray-50 border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono"
            />
            <button
              @click="copyApiKey"
              class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition"
            >
              <Icon name="heroicons:clipboard" class="w-4 h-4" />
            </button>
            <button
              @click="regenerateApiKey"
              class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition"
            >
              <Icon name="heroicons:arrow-path" class="w-4 h-4" />
            </button>
          </div>
          <p class="text-xs text-gray-500 mt-1">
            Usa questa chiave per autenticare le richieste API.
          </p>
        </div>

        <div class="p-4 bg-amber-50 border border-amber-200 rounded-lg">
          <div class="flex items-start gap-3">
            <Icon name="heroicons:exclamation-triangle" class="w-5 h-5 text-amber-600 mt-0.5" />
            <div>
              <div class="font-medium text-amber-800">Attenzione</div>
              <div class="text-sm text-amber-700">
                Non condividere mai la tua API Key. Rigenerala se pensi che sia stata compromessa.
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Danger Zone -->
    <div class="bg-white rounded-xl shadow-sm border border-red-200 p-6">
      <h2 class="text-lg font-semibold text-red-600 mb-4">Zona Pericolosa</h2>

      <div class="space-y-4">
        <div class="flex items-center justify-between p-4 bg-red-50 rounded-lg">
          <div>
            <div class="font-medium text-gray-900">Elimina Tutti i Dati</div>
            <div class="text-sm text-gray-500">Cancella permanentemente tutte le aziende e i dati</div>
          </div>
          <button
            @click="showDeleteModal = true"
            class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700 transition"
          >
            Elimina Tutto
          </button>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div v-if="showDeleteModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
      <div class="bg-white rounded-xl shadow-xl max-w-md w-full mx-4 p-6">
        <div class="flex items-center gap-3 mb-4">
          <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
            <Icon name="heroicons:exclamation-triangle" class="w-6 h-6 text-red-600" />
          </div>
          <div>
            <h3 class="text-lg font-semibold text-gray-900">Conferma Eliminazione</h3>
            <p class="text-sm text-gray-500">Questa azione è irreversibile</p>
          </div>
        </div>

        <p class="text-sm text-gray-600 mb-4">
          Stai per eliminare permanentemente tutte le aziende clienti, i dati economici, gli inviti e i report.
          Questa azione non può essere annullata.
        </p>

        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-1">
            Digita "ELIMINA TUTTO" per confermare
          </label>
          <input
            v-model="deleteConfirmation"
            type="text"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500"
            placeholder="ELIMINA TUTTO"
          />
        </div>

        <div class="flex gap-3">
          <button
            @click="showDeleteModal = false; deleteConfirmation = ''"
            class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition"
          >
            Annulla
          </button>
          <button
            @click="deleteAllData"
            :disabled="deleteConfirmation !== 'ELIMINA TUTTO'"
            class="flex-1 bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-red-700 disabled:opacity-50 transition"
          >
            Elimina Tutto
          </button>
        </div>
      </div>
    </div>

    <!-- Toast -->
    <div
      v-if="toast.show"
      class="fixed bottom-4 right-4 px-4 py-3 rounded-lg shadow-lg z-50"
      :class="toast.type === 'success' ? 'bg-green-600 text-white' : 'bg-red-600 text-white'"
    >
      {{ toast.message }}
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  layout: 'consulente'
})

const config = useRuntimeConfig()

const saving = ref(false)
const showDeleteModal = ref(false)
const deleteConfirmation = ref('')
const apiKey = ref('aa_sk_xxxxxxxxxxxxxxxxxxxxxxxxxxxx')

const studioForm = ref({
  nome: '',
  partita_iva: '',
  codice_fiscale: '',
  telefono: '',
  indirizzo: '',
  email: '',
  sito_web: '',
  logo_url: '',
  colore_primario: '#7C3AED',
  notifica_kpi_critico: true,
  notifica_report_settimanale: true,
  notifica_invito_accettato: true,
  notifica_scadenze: true
})

const toast = ref({
  show: false,
  message: '',
  type: 'success'
})

const showToast = (message: string, type: 'success' | 'error' = 'success') => {
  toast.value = { show: true, message, type }
  setTimeout(() => { toast.value.show = false }, 3000)
}

const loadStudio = async () => {
  const token = localStorage.getItem('aa_token')
  if (!token) return

  try {
    const response = await $fetch<{ success: boolean; data: any }>(`${config.public.apiBase}/studio`, {
      headers: {
        'Authorization': `Bearer ${token}`,
        'X-API-Key': config.public.apiKey
      }
    })

    if (response.success && response.data) {
      Object.assign(studioForm.value, response.data)
      if (response.data.api_key) {
        apiKey.value = response.data.api_key
      }
    }
  } catch (e) {
    console.error('Error loading studio:', e)
  }
}

const saveStudio = async () => {
  saving.value = true
  const token = localStorage.getItem('aa_token')

  try {
    const response = await $fetch<{ success: boolean }>(`${config.public.apiBase}/studio`, {
      method: 'PUT',
      headers: {
        'Authorization': `Bearer ${token}`,
        'X-API-Key': config.public.apiKey,
        'Content-Type': 'application/json'
      },
      body: studioForm.value
    })

    if (response.success) {
      showToast('Impostazioni salvate con successo!')
    }
  } catch (e: any) {
    showToast(e.data?.message || 'Errore nel salvataggio', 'error')
  } finally {
    saving.value = false
  }
}

const handleLogoUpload = async (event: Event) => {
  const input = event.target as HTMLInputElement
  const file = input.files?.[0]
  if (!file) return

  if (file.size > 1024 * 1024) {
    showToast('Il file è troppo grande (max 1MB)', 'error')
    return
  }

  const formData = new FormData()
  formData.append('logo', file)

  const token = localStorage.getItem('aa_token')

  try {
    const response = await $fetch<{ success: boolean; data: { url: string } }>(`${config.public.apiBase}/studio/logo`, {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${token}`,
        'X-API-Key': config.public.apiKey
      },
      body: formData
    })

    if (response.success) {
      studioForm.value.logo_url = response.data.url
      showToast('Logo caricato con successo!')
    }
  } catch (e: any) {
    showToast(e.data?.message || 'Errore nel caricamento', 'error')
  }
}

const copyApiKey = async () => {
  try {
    await navigator.clipboard.writeText(apiKey.value)
    showToast('API Key copiata negli appunti!')
  } catch (e) {
    showToast('Errore nella copia', 'error')
  }
}

const regenerateApiKey = async () => {
  const token = localStorage.getItem('aa_token')

  try {
    const response = await $fetch<{ success: boolean; data: { api_key: string } }>(`${config.public.apiBase}/studio/regenerate-api-key`, {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${token}`,
        'X-API-Key': config.public.apiKey
      }
    })

    if (response.success) {
      apiKey.value = response.data.api_key
      showToast('Nuova API Key generata!')
    }
  } catch (e: any) {
    showToast(e.data?.message || 'Errore nella rigenerazione', 'error')
  }
}

const deleteAllData = async () => {
  if (deleteConfirmation.value !== 'ELIMINA TUTTO') return

  const token = localStorage.getItem('aa_token')

  try {
    const response = await $fetch<{ success: boolean }>(`${config.public.apiBase}/studio/delete-all-data`, {
      method: 'DELETE',
      headers: {
        'Authorization': `Bearer ${token}`,
        'X-API-Key': config.public.apiKey
      }
    })

    if (response.success) {
      showToast('Tutti i dati sono stati eliminati')
      showDeleteModal.value = false
      deleteConfirmation.value = ''
    }
  } catch (e: any) {
    showToast(e.data?.message || 'Errore nell\'eliminazione', 'error')
  }
}

onMounted(loadStudio)
</script>
