<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Il Mio Account</h1>
        <p class="text-gray-600 mt-1">Gestisci il tuo profilo e abbonamento</p>
      </div>
    </div>

    <!-- Current Plan -->
    <div class="bg-gradient-to-r from-purple-600 to-purple-800 rounded-xl shadow-lg p-6 text-white">
      <div class="flex items-center justify-between">
        <div>
          <div class="text-purple-200 text-sm">Piano Attuale</div>
          <div class="text-2xl font-bold mt-1">{{ currentPlan.nome }}</div>
          <div class="text-purple-200 mt-2">
            <span v-if="currentPlan.is_trial">
              Trial scade il {{ formatDate(currentPlan.trial_ends_at) }}
            </span>
            <span v-else-if="currentPlan.prezzo > 0">
              €{{ currentPlan.prezzo }}/mese
            </span>
            <span v-else>Piano gratuito</span>
          </div>
        </div>
        <div class="text-right">
          <div class="text-purple-200 text-sm">Aziende</div>
          <div class="text-3xl font-bold">{{ currentPlan.aziende_usate }}/{{ currentPlan.max_aziende }}</div>
        </div>
      </div>

      <div v-if="currentPlan.is_trial" class="mt-4 pt-4 border-t border-purple-500">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-2">
            <Icon name="heroicons:clock" class="w-5 h-5" />
            <span>{{ trialDaysLeft }} giorni rimasti nel trial</span>
          </div>
          <button
            @click="showUpgradeModal = true"
            class="bg-white text-purple-600 px-4 py-2 rounded-lg text-sm font-medium hover:bg-purple-50 transition"
          >
            Effettua Upgrade
          </button>
        </div>
      </div>
    </div>

    <!-- Profile Info -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
      <h2 class="text-lg font-semibold text-gray-900 mb-4">Informazioni Personali</h2>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Nome</label>
          <input
            v-model="profileForm.nome"
            type="text"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Cognome</label>
          <input
            v-model="profileForm.cognome"
            type="text"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
          <input
            v-model="profileForm.email"
            type="email"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Telefono</label>
          <input
            v-model="profileForm.telefono"
            type="tel"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
          />
        </div>
      </div>

      <div class="mt-6 flex justify-end">
        <button
          @click="saveProfile"
          :disabled="saving"
          class="bg-purple-600 text-white px-6 py-2 rounded-lg text-sm font-medium hover:bg-purple-700 disabled:opacity-50 transition"
        >
          <span v-if="saving">Salvando...</span>
          <span v-else>Salva Modifiche</span>
        </button>
      </div>
    </div>

    <!-- Change Password -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
      <h2 class="text-lg font-semibold text-gray-900 mb-4">Cambia Password</h2>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Password Attuale</label>
          <input
            v-model="passwordForm.current_password"
            type="password"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Nuova Password</label>
          <input
            v-model="passwordForm.new_password"
            type="password"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Conferma Password</label>
          <input
            v-model="passwordForm.confirm_password"
            type="password"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
          />
        </div>
      </div>

      <div class="mt-6 flex justify-end">
        <button
          @click="changePassword"
          :disabled="changingPassword"
          class="bg-purple-600 text-white px-6 py-2 rounded-lg text-sm font-medium hover:bg-purple-700 disabled:opacity-50 transition"
        >
          <span v-if="changingPassword">Cambiando...</span>
          <span v-else>Cambia Password</span>
        </button>
      </div>
    </div>

    <!-- Billing -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
      <h2 class="text-lg font-semibold text-gray-900 mb-4">Fatturazione</h2>

      <div class="space-y-4">
        <!-- Payment Method -->
        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
          <div class="flex items-center gap-3">
            <Icon name="heroicons:credit-card" class="w-6 h-6 text-gray-600" />
            <div>
              <div class="font-medium text-gray-900">Metodo di Pagamento</div>
              <div class="text-sm text-gray-500">
                <span v-if="paymentMethod">{{ paymentMethod.brand }} •••• {{ paymentMethod.last4 }}</span>
                <span v-else>Nessun metodo configurato</span>
              </div>
            </div>
          </div>
          <button
            @click="managePaymentMethod"
            class="text-purple-600 text-sm font-medium hover:text-purple-700"
          >
            {{ paymentMethod ? 'Modifica' : 'Aggiungi' }}
          </button>
        </div>

        <!-- Billing Address -->
        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
          <div class="flex items-center gap-3">
            <Icon name="heroicons:map-pin" class="w-6 h-6 text-gray-600" />
            <div>
              <div class="font-medium text-gray-900">Indirizzo di Fatturazione</div>
              <div class="text-sm text-gray-500">
                <span v-if="billingAddress">{{ billingAddress }}</span>
                <span v-else>Non configurato</span>
              </div>
            </div>
          </div>
          <button
            @click="showBillingModal = true"
            class="text-purple-600 text-sm font-medium hover:text-purple-700"
          >
            {{ billingAddress ? 'Modifica' : 'Aggiungi' }}
          </button>
        </div>

        <!-- Invoices -->
        <div>
          <h3 class="font-medium text-gray-900 mb-3">Fatture Recenti</h3>
          <div v-if="invoices.length === 0" class="text-sm text-gray-500 p-4 bg-gray-50 rounded-lg">
            Nessuna fattura disponibile
          </div>
          <div v-else class="space-y-2">
            <div
              v-for="invoice in invoices"
              :key="invoice.id"
              class="flex items-center justify-between p-3 bg-gray-50 rounded-lg"
            >
              <div class="flex items-center gap-3">
                <Icon name="heroicons:document-text" class="w-5 h-5 text-gray-400" />
                <div>
                  <div class="text-sm font-medium text-gray-900">{{ invoice.numero }}</div>
                  <div class="text-xs text-gray-500">{{ formatDate(invoice.data) }}</div>
                </div>
              </div>
              <div class="flex items-center gap-4">
                <span class="text-sm font-medium text-gray-900">€{{ invoice.totale }}</span>
                <button
                  @click="downloadInvoice(invoice)"
                  class="text-purple-600 hover:text-purple-700"
                >
                  <Icon name="heroicons:arrow-down-tray" class="w-5 h-5" />
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Plans Comparison -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
      <h2 class="text-lg font-semibold text-gray-900 mb-4">Piani Disponibili</h2>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Business Plan -->
        <div
          class="border-2 rounded-xl p-6"
          :class="currentPlan.nome === 'Business' ? 'border-purple-500 bg-purple-50' : 'border-gray-200'"
        >
          <div class="text-center">
            <h3 class="text-lg font-semibold text-gray-900">Business</h3>
            <div class="mt-2">
              <span class="text-3xl font-bold text-gray-900">€79</span>
              <span class="text-gray-500">/mese</span>
            </div>
            <ul class="mt-4 space-y-2 text-sm text-gray-600 text-left">
              <li class="flex items-center gap-2">
                <Icon name="heroicons:check" class="w-4 h-4 text-green-600" />
                Fino a 10 aziende
              </li>
              <li class="flex items-center gap-2">
                <Icon name="heroicons:check" class="w-4 h-4 text-green-600" />
                KPI obbligatori + settoriali
              </li>
              <li class="flex items-center gap-2">
                <Icon name="heroicons:check" class="w-4 h-4 text-green-600" />
                Report PDF personalizzati
              </li>
              <li class="flex items-center gap-2">
                <Icon name="heroicons:check" class="w-4 h-4 text-green-600" />
                Inviti clienti readonly
              </li>
            </ul>
            <button
              v-if="currentPlan.nome !== 'Business'"
              @click="selectPlan('business')"
              class="mt-4 w-full bg-purple-600 text-white py-2 rounded-lg text-sm font-medium hover:bg-purple-700 transition"
            >
              Passa a Business
            </button>
            <div v-else class="mt-4 text-center text-sm text-purple-600 font-medium">
              Piano Attuale
            </div>
          </div>
        </div>

        <!-- Enterprise Plan -->
        <div
          class="border-2 rounded-xl p-6 relative"
          :class="currentPlan.nome === 'Enterprise' ? 'border-purple-500 bg-purple-50' : 'border-gray-200'"
        >
          <div class="absolute -top-3 left-1/2 transform -translate-x-1/2">
            <span class="bg-purple-600 text-white text-xs px-3 py-1 rounded-full">Consigliato</span>
          </div>
          <div class="text-center">
            <h3 class="text-lg font-semibold text-gray-900">Enterprise</h3>
            <div class="mt-2">
              <span class="text-3xl font-bold text-gray-900">€199</span>
              <span class="text-gray-500">/mese</span>
            </div>
            <ul class="mt-4 space-y-2 text-sm text-gray-600 text-left">
              <li class="flex items-center gap-2">
                <Icon name="heroicons:check" class="w-4 h-4 text-green-600" />
                Aziende illimitate
              </li>
              <li class="flex items-center gap-2">
                <Icon name="heroicons:check" class="w-4 h-4 text-green-600" />
                Tutto di Business +
              </li>
              <li class="flex items-center gap-2">
                <Icon name="heroicons:check" class="w-4 h-4 text-green-600" />
                Branding personalizzato
              </li>
              <li class="flex items-center gap-2">
                <Icon name="heroicons:check" class="w-4 h-4 text-green-600" />
                Supporto prioritario
              </li>
              <li class="flex items-center gap-2">
                <Icon name="heroicons:check" class="w-4 h-4 text-green-600" />
                API access
              </li>
            </ul>
            <button
              v-if="currentPlan.nome !== 'Enterprise'"
              @click="selectPlan('enterprise')"
              class="mt-4 w-full bg-purple-600 text-white py-2 rounded-lg text-sm font-medium hover:bg-purple-700 transition"
            >
              Passa a Enterprise
            </button>
            <div v-else class="mt-4 text-center text-sm text-purple-600 font-medium">
              Piano Attuale
            </div>
          </div>
        </div>

        <!-- Custom Plan -->
        <div class="border-2 border-gray-200 rounded-xl p-6">
          <div class="text-center">
            <h3 class="text-lg font-semibold text-gray-900">Custom</h3>
            <div class="mt-2">
              <span class="text-3xl font-bold text-gray-900">Su misura</span>
            </div>
            <ul class="mt-4 space-y-2 text-sm text-gray-600 text-left">
              <li class="flex items-center gap-2">
                <Icon name="heroicons:check" class="w-4 h-4 text-green-600" />
                Tutto di Enterprise +
              </li>
              <li class="flex items-center gap-2">
                <Icon name="heroicons:check" class="w-4 h-4 text-green-600" />
                SLA dedicato
              </li>
              <li class="flex items-center gap-2">
                <Icon name="heroicons:check" class="w-4 h-4 text-green-600" />
                Formazione team
              </li>
              <li class="flex items-center gap-2">
                <Icon name="heroicons:check" class="w-4 h-4 text-green-600" />
                Integrazioni custom
              </li>
            </ul>
            <button
              @click="contactSales"
              class="mt-4 w-full border-2 border-purple-600 text-purple-600 py-2 rounded-lg text-sm font-medium hover:bg-purple-50 transition"
            >
              Contattaci
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Danger Zone -->
    <div class="bg-white rounded-xl shadow-sm border border-red-200 p-6">
      <h2 class="text-lg font-semibold text-red-600 mb-4">Zona Pericolosa</h2>

      <div class="flex items-center justify-between p-4 bg-red-50 rounded-lg">
        <div>
          <div class="font-medium text-gray-900">Elimina Account</div>
          <div class="text-sm text-gray-500">Cancella permanentemente il tuo account e tutti i dati</div>
        </div>
        <button
          @click="showDeleteAccountModal = true"
          class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700 transition"
        >
          Elimina Account
        </button>
      </div>
    </div>

    <!-- Upgrade Modal -->
    <div v-if="showUpgradeModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
      <div class="bg-white rounded-xl shadow-xl max-w-md w-full mx-4 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Effettua Upgrade</h3>
        <p class="text-sm text-gray-600 mb-4">
          Scegli il piano più adatto alle tue esigenze. Il trial verrà convertito immediatamente.
        </p>
        <div class="space-y-3">
          <button
            @click="upgradeToPlan('business')"
            class="w-full p-4 border-2 border-gray-200 rounded-lg text-left hover:border-purple-500 transition"
          >
            <div class="flex items-center justify-between">
              <div>
                <div class="font-medium text-gray-900">Business</div>
                <div class="text-sm text-gray-500">Fino a 10 aziende</div>
              </div>
              <div class="text-lg font-bold text-gray-900">€79/mese</div>
            </div>
          </button>
          <button
            @click="upgradeToPlan('enterprise')"
            class="w-full p-4 border-2 border-purple-500 bg-purple-50 rounded-lg text-left"
          >
            <div class="flex items-center justify-between">
              <div>
                <div class="font-medium text-gray-900">Enterprise</div>
                <div class="text-sm text-gray-500">Aziende illimitate</div>
              </div>
              <div class="text-lg font-bold text-gray-900">€199/mese</div>
            </div>
          </button>
        </div>
        <button
          @click="showUpgradeModal = false"
          class="w-full mt-4 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition"
        >
          Annulla
        </button>
      </div>
    </div>

    <!-- Delete Account Modal -->
    <div v-if="showDeleteAccountModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
      <div class="bg-white rounded-xl shadow-xl max-w-md w-full mx-4 p-6">
        <div class="flex items-center gap-3 mb-4">
          <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
            <Icon name="heroicons:exclamation-triangle" class="w-6 h-6 text-red-600" />
          </div>
          <div>
            <h3 class="text-lg font-semibold text-gray-900">Elimina Account</h3>
            <p class="text-sm text-gray-500">Questa azione è irreversibile</p>
          </div>
        </div>

        <p class="text-sm text-gray-600 mb-4">
          Stai per eliminare permanentemente il tuo account, lo studio associato, tutte le aziende clienti e i dati.
          Questa azione non può essere annullata.
        </p>

        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-1">
            Digita la tua email per confermare
          </label>
          <input
            v-model="deleteAccountConfirmation"
            type="email"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500"
            :placeholder="profileForm.email"
          />
        </div>

        <div class="flex gap-3">
          <button
            @click="showDeleteAccountModal = false; deleteAccountConfirmation = ''"
            class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition"
          >
            Annulla
          </button>
          <button
            @click="deleteAccount"
            :disabled="deleteAccountConfirmation !== profileForm.email"
            class="flex-1 bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-red-700 disabled:opacity-50 transition"
          >
            Elimina Account
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
const router = useRouter()

const saving = ref(false)
const changingPassword = ref(false)
const showUpgradeModal = ref(false)
const showBillingModal = ref(false)
const showDeleteAccountModal = ref(false)
const deleteAccountConfirmation = ref('')

const currentPlan = ref({
  nome: 'Business',
  prezzo: 79,
  max_aziende: 10,
  aziende_usate: 0,
  is_trial: true,
  trial_ends_at: ''
})

const trialDaysLeft = computed(() => {
  if (!currentPlan.value.trial_ends_at) return 0
  const end = new Date(currentPlan.value.trial_ends_at)
  const now = new Date()
  return Math.max(0, Math.ceil((end.getTime() - now.getTime()) / (1000 * 60 * 60 * 24)))
})

const profileForm = ref({
  nome: '',
  cognome: '',
  email: '',
  telefono: ''
})

const passwordForm = ref({
  current_password: '',
  new_password: '',
  confirm_password: ''
})

const paymentMethod = ref<{ brand: string; last4: string } | null>(null)
const billingAddress = ref('')
const invoices = ref<any[]>([])

const toast = ref({
  show: false,
  message: '',
  type: 'success'
})

const showToast = (message: string, type: 'success' | 'error' = 'success') => {
  toast.value = { show: true, message, type }
  setTimeout(() => { toast.value.show = false }, 3000)
}

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('it-IT', { day: '2-digit', month: 'short', year: 'numeric' })
}

const loadAccount = async () => {
  const token = localStorage.getItem('aa_token')
  if (!token) return

  try {
    const response = await $fetch<{ success: boolean; data: any }>(`${config.public.apiBase}/account`, {
      headers: {
        'Authorization': `Bearer ${token}`,
        'X-API-Key': config.public.apiKey
      }
    })

    if (response.success) {
      const data = response.data
      profileForm.value = {
        nome: data.nome || '',
        cognome: data.cognome || '',
        email: data.email || '',
        telefono: data.telefono || ''
      }
      currentPlan.value = {
        nome: data.piano || 'Business',
        prezzo: data.prezzo || 79,
        max_aziende: data.max_aziende || 10,
        aziende_usate: data.aziende_usate || 0,
        is_trial: data.is_trial || false,
        trial_ends_at: data.trial_ends_at || ''
      }
      paymentMethod.value = data.payment_method || null
      billingAddress.value = data.billing_address || ''
      invoices.value = data.invoices || []
    }
  } catch (e) {
    console.error('Error loading account:', e)
  }
}

const saveProfile = async () => {
  saving.value = true
  const token = localStorage.getItem('aa_token')

  try {
    const response = await $fetch<{ success: boolean }>(`${config.public.apiBase}/account`, {
      method: 'PUT',
      headers: {
        'Authorization': `Bearer ${token}`,
        'X-API-Key': config.public.apiKey,
        'Content-Type': 'application/json'
      },
      body: profileForm.value
    })

    if (response.success) {
      showToast('Profilo aggiornato con successo!')
    }
  } catch (e: any) {
    showToast(e.data?.message || 'Errore nel salvataggio', 'error')
  } finally {
    saving.value = false
  }
}

const changePassword = async () => {
  if (passwordForm.value.new_password !== passwordForm.value.confirm_password) {
    showToast('Le password non coincidono', 'error')
    return
  }

  changingPassword.value = true
  const token = localStorage.getItem('aa_token')

  try {
    const response = await $fetch<{ success: boolean }>(`${config.public.apiBase}/account/change-password`, {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${token}`,
        'X-API-Key': config.public.apiKey,
        'Content-Type': 'application/json'
      },
      body: passwordForm.value
    })

    if (response.success) {
      showToast('Password cambiata con successo!')
      passwordForm.value = { current_password: '', new_password: '', confirm_password: '' }
    }
  } catch (e: any) {
    showToast(e.data?.message || 'Errore nel cambio password', 'error')
  } finally {
    changingPassword.value = false
  }
}

const managePaymentMethod = async () => {
  const token = localStorage.getItem('aa_token')

  try {
    const response = await $fetch<{ success: boolean; data: { url: string } }>(`${config.public.apiBase}/billing/portal`, {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${token}`,
        'X-API-Key': config.public.apiKey
      }
    })

    if (response.success && response.data.url) {
      window.location.href = response.data.url
    }
  } catch (e: any) {
    showToast(e.data?.message || 'Errore nell\'apertura del portale', 'error')
  }
}

const downloadInvoice = (invoice: any) => {
  if (invoice.download_url) {
    window.open(invoice.download_url, '_blank')
  }
}

const selectPlan = (plan: string) => {
  showUpgradeModal.value = true
}

const upgradeToPlan = async (plan: string) => {
  const token = localStorage.getItem('aa_token')

  try {
    const response = await $fetch<{ success: boolean; data: { url: string } }>(`${config.public.apiBase}/upgrade`, {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${token}`,
        'X-API-Key': config.public.apiKey,
        'Content-Type': 'application/json'
      },
      body: { piano: plan }
    })

    if (response.success && response.data.url) {
      window.location.href = response.data.url
    }
  } catch (e: any) {
    showToast(e.data?.message || 'Errore nell\'upgrade', 'error')
  }
}

const contactSales = () => {
  window.location.href = 'mailto:info@adeguatiassettiimpresa.it?subject=Richiesta Piano Custom'
}

const deleteAccount = async () => {
  if (deleteAccountConfirmation.value !== profileForm.value.email) return

  const token = localStorage.getItem('aa_token')

  try {
    const response = await $fetch<{ success: boolean }>(`${config.public.apiBase}/account`, {
      method: 'DELETE',
      headers: {
        'Authorization': `Bearer ${token}`,
        'X-API-Key': config.public.apiKey
      }
    })

    if (response.success) {
      localStorage.removeItem('aa_token')
      localStorage.removeItem('aa_user')
      router.push('/login')
    }
  } catch (e: any) {
    showToast(e.data?.message || 'Errore nell\'eliminazione', 'error')
  }
}

onMounted(loadAccount)
</script>
