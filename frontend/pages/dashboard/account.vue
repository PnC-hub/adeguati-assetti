<template>
  <div>
    <!-- Header -->
    <div class="mb-6">
      <div class="flex items-center gap-3">
        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-purple-500 to-purple-700 flex items-center justify-center">
          <Icon name="heroicons:user-circle" class="w-6 h-6 text-white" />
        </div>
        <div class="flex-1">
          <h1 class="text-2xl font-bold text-gray-800">Il mio Account</h1>
          <p class="text-gray-500">Gestisci profilo e abbonamento</p>
        </div>
        <PageInfoButton
          title="Account"
          description="Gestione del profilo utente, piano attuale e opzioni di upgrade"
          :features="['Profilo con dati personali', 'Piano attuale con features incluse', 'Upgrade a Pro o Studio con trial 14 giorni']"
        />
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="flex justify-center py-20">
      <Icon name="heroicons:arrow-path" class="w-12 h-12 text-purple-600 animate-spin" />
    </div>

    <template v-else>
      <!-- Trial Banner -->
      <div v-if="accountData.trial.active" class="bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-xl p-6 mb-6">
        <div class="flex items-center justify-between">
          <div>
            <h3 class="text-lg font-bold">Periodo di prova attivo</h3>
            <p class="text-blue-100 mt-1">
              Ti restano <strong>{{ accountData.trial.days_left }} giorni</strong> di accesso completo alle funzionalita Pro.
            </p>
          </div>
          <button @click="scrollToPiani" class="bg-white text-purple-700 px-6 py-2 rounded-lg font-semibold hover:bg-purple-50 transition">
            Scegli un Piano
          </button>
        </div>
      </div>

      <!-- Profilo -->
      <div class="bg-white rounded-xl shadow p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
          <Icon name="heroicons:identification" class="w-5 h-5 text-purple-600" />
          Profilo
        </h2>
        <div class="grid md:grid-cols-2 gap-4">
          <div>
            <label class="text-sm text-gray-500">Nome</label>
            <p class="font-medium text-gray-900">{{ accountData.user.nome }} {{ accountData.user.cognome }}</p>
          </div>
          <div>
            <label class="text-sm text-gray-500">Email</label>
            <p class="font-medium text-gray-900">{{ accountData.user.email }}</p>
          </div>
        </div>
      </div>

      <!-- Piano Attuale -->
      <div class="bg-white rounded-xl shadow p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
          <Icon name="heroicons:credit-card" class="w-5 h-5 text-purple-600" />
          Piano Attuale
        </h2>
        <div class="flex items-center justify-between p-4 bg-purple-50 rounded-lg">
          <div>
            <div class="flex items-center gap-2">
              <span class="text-xl font-bold text-purple-700">{{ accountData.piano.nome }}</span>
              <span v-if="accountData.piano.codice === 'trial'" class="bg-blue-100 text-blue-700 text-xs font-bold px-2 py-1 rounded-full">TRIAL</span>
              <span v-if="accountData.piano.codice === 'free'" class="bg-gray-100 text-gray-600 text-xs font-bold px-2 py-1 rounded-full">GRATUITO</span>
            </div>
            <p class="text-gray-600 mt-1">
              <span v-if="accountData.piano.prezzo_mensile > 0">&euro;{{ accountData.piano.prezzo_mensile }}/mese</span>
              <span v-else>Gratuito</span>
            </p>
          </div>
          <div class="text-right">
            <p class="text-sm text-gray-500">Aziende</p>
            <p class="font-bold text-gray-800">{{ accountData.usage.aziende_count }} / {{ accountData.usage.aziende_limit }}</p>
          </div>
        </div>

        <!-- Features del piano -->
        <div class="mt-4 grid grid-cols-2 md:grid-cols-3 gap-3">
          <div v-for="(enabled, feature) in accountData.piano.features" :key="feature"
               class="flex items-center gap-2 text-sm"
               :class="enabled ? 'text-green-700' : 'text-gray-400'">
            <Icon :name="enabled ? 'heroicons:check-circle' : 'heroicons:x-circle'" class="w-5 h-5 flex-shrink-0" />
            {{ featureLabels[feature] || feature }}
          </div>
        </div>

        <!-- Gestisci abbonamento -->
        <div v-if="accountData.stripe.has_subscription" class="mt-4 pt-4 border-t">
          <button @click="openBillingPortal" class="text-purple-600 hover:text-purple-800 font-medium text-sm flex items-center gap-1">
            <Icon name="heroicons:arrow-top-right-on-square" class="w-4 h-4" />
            Gestisci Abbonamento su Stripe
          </button>
        </div>
      </div>

      <!-- Piani Disponibili -->
      <div ref="pianiSection" class="mb-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
          <Icon name="heroicons:sparkles" class="w-5 h-5 text-purple-600" />
          Cambia Piano
        </h2>
        <div class="grid md:grid-cols-3 gap-6">
          <!-- Free -->
          <div class="bg-white rounded-xl shadow p-6" :class="accountData.piano.piano_effettivo === 'free' ? 'ring-2 ring-purple-500' : ''">
            <div v-if="accountData.piano.piano_effettivo === 'free'" class="text-purple-600 text-xs font-bold mb-2">PIANO ATTUALE</div>
            <h3 class="text-xl font-bold text-gray-900">Free</h3>
            <div class="mt-2 mb-4">
              <span class="text-3xl font-bold">&euro;0</span><span class="text-gray-500">/mese</span>
            </div>
            <ul class="space-y-2 text-sm text-gray-600 mb-6">
              <li class="flex items-center gap-2"><Icon name="heroicons:check" class="w-4 h-4 text-green-500" /> 1 azienda</li>
              <li class="flex items-center gap-2"><Icon name="heroicons:check" class="w-4 h-4 text-green-500" /> 7 KPI obbligatori CNDCEC</li>
              <li class="flex items-center gap-2"><Icon name="heroicons:check" class="w-4 h-4 text-green-500" /> Score salute aziendale</li>
              <li class="flex items-center gap-2 text-gray-400"><Icon name="heroicons:x-mark" class="w-4 h-4" /> KPI settoriali</li>
              <li class="flex items-center gap-2 text-gray-400"><Icon name="heroicons:x-mark" class="w-4 h-4" /> Alert automatici</li>
              <li class="flex items-center gap-2 text-gray-400"><Icon name="heroicons:x-mark" class="w-4 h-4" /> Export PDF</li>
            </ul>
          </div>

          <!-- Pro -->
          <div class="bg-white rounded-xl shadow p-6 border-2 border-purple-500 relative">
            <div class="absolute -top-3 left-1/2 -translate-x-1/2 bg-purple-600 text-white text-xs font-bold px-3 py-1 rounded-full">CONSIGLIATO</div>
            <div v-if="accountData.piano.piano_effettivo === 'pro'" class="text-purple-600 text-xs font-bold mb-2">PIANO ATTUALE</div>
            <h3 class="text-xl font-bold text-gray-900">Pro</h3>
            <div class="mt-2 mb-4">
              <span class="text-3xl font-bold">&euro;49</span><span class="text-gray-500">/mese</span>
            </div>
            <ul class="space-y-2 text-sm text-gray-600 mb-6">
              <li class="flex items-center gap-2"><Icon name="heroicons:check" class="w-4 h-4 text-green-500" /> 1 azienda</li>
              <li class="flex items-center gap-2"><Icon name="heroicons:check" class="w-4 h-4 text-green-500" /> Tutti i KPI + settoriali</li>
              <li class="flex items-center gap-2"><Icon name="heroicons:check" class="w-4 h-4 text-green-500" /> Soglie CNDCEC per ATECO</li>
              <li class="flex items-center gap-2"><Icon name="heroicons:check" class="w-4 h-4 text-green-500" /> Alert automatici</li>
              <li class="flex items-center gap-2"><Icon name="heroicons:check" class="w-4 h-4 text-green-500" /> Export Report PDF</li>
              <li class="flex items-center gap-2"><Icon name="heroicons:check" class="w-4 h-4 text-green-500" /> Storico illimitato</li>
            </ul>
            <button v-if="accountData.piano.piano_effettivo !== 'pro'"
                    @click="handleUpgrade('pro')"
                    class="w-full bg-purple-600 text-white py-3 rounded-lg font-semibold hover:bg-purple-700 transition">
              Upgrade a Pro
            </button>
          </div>

          <!-- Studio -->
          <div class="bg-white rounded-xl shadow p-6" :class="accountData.piano.piano_effettivo === 'studio' ? 'ring-2 ring-purple-500' : ''">
            <div v-if="accountData.piano.piano_effettivo === 'studio'" class="text-purple-600 text-xs font-bold mb-2">PIANO ATTUALE</div>
            <h3 class="text-xl font-bold text-gray-900">Studio</h3>
            <div class="mt-2 mb-4">
              <span class="text-3xl font-bold">&euro;149</span><span class="text-gray-500">/mese</span>
            </div>
            <ul class="space-y-2 text-sm text-gray-600 mb-6">
              <li class="flex items-center gap-2"><Icon name="heroicons:check" class="w-4 h-4 text-green-500" /> Fino a 20 aziende</li>
              <li class="flex items-center gap-2"><Icon name="heroicons:check" class="w-4 h-4 text-green-500" /> Tutto il piano Pro</li>
              <li class="flex items-center gap-2"><Icon name="heroicons:check" class="w-4 h-4 text-green-500" /> Gestione multi-azienda</li>
              <li class="flex items-center gap-2"><Icon name="heroicons:check" class="w-4 h-4 text-green-500" /> Report con branding</li>
              <li class="flex items-center gap-2"><Icon name="heroicons:check" class="w-4 h-4 text-green-500" /> Supporto prioritario</li>
              <li class="flex items-center gap-2"><Icon name="heroicons:check" class="w-4 h-4 text-green-500" /> Dashboard comparativa</li>
            </ul>
            <button v-if="accountData.piano.piano_effettivo !== 'studio'"
                    @click="handleUpgrade('studio')"
                    class="w-full border-2 border-purple-600 text-purple-600 py-3 rounded-lg font-semibold hover:bg-purple-50 transition">
              Upgrade a Studio
            </button>
          </div>
        </div>
      </div>

      <!-- Upgrade Message -->
      <div v-if="upgradeMessage" class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
        <p class="text-blue-800">{{ upgradeMessage }}</p>
      </div>
    </template>
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  layout: 'dashboard',
  middleware: 'auth'
})

const config = useRuntimeConfig()
const loading = ref(true)
const upgradeMessage = ref('')
const pianiSection = ref<HTMLElement | null>(null)

const featureLabels: Record<string, string> = {
  kpi_obbligatori: 'KPI Obbligatori',
  kpi_settoriali: 'KPI Settoriali',
  alert: 'Alert Automatici',
  export_pdf: 'Export PDF',
  branding: 'Branding Personalizzato',
}

interface AccountData {
  user: { id: number; nome: string; cognome: string; email: string }
  piano: { codice: string; piano_effettivo: string; nome: string; prezzo_mensile: number; max_aziende: number; features: Record<string, boolean> }
  trial: { active: boolean; days_left: number; ends_at: string | null }
  usage: { aziende_count: number; aziende_limit: number }
  stripe: { has_subscription: boolean; customer_id: string | null }
}

const accountData = reactive<AccountData>({
  user: { id: 0, nome: '', cognome: '', email: '' },
  piano: { codice: 'free', piano_effettivo: 'free', nome: 'Free', prezzo_mensile: 0, max_aziende: 1, features: {} },
  trial: { active: false, days_left: 0, ends_at: null },
  usage: { aziende_count: 0, aziende_limit: 1 },
  stripe: { has_subscription: false, customer_id: null },
})

const getAuthHeaders = () => {
  const token = localStorage.getItem('aa_token')
  return {
    'X-API-Key': config.public.apiKey,
    'Authorization': `Bearer ${token}`,
    'Content-Type': 'application/json'
  }
}

const loadAccount = async () => {
  loading.value = true
  try {
    const response = await $fetch<{ success: boolean; data: AccountData }>(
      `${config.public.apiBase}/account`,
      { headers: getAuthHeaders() }
    )
    if (response.success && response.data) {
      Object.assign(accountData, response.data)
      // Store piano in localStorage for UI gating
      localStorage.setItem('aa_piano', JSON.stringify({
        codice: response.data.piano.piano_effettivo,
        features: response.data.piano.features,
      }))
    }
  } catch (e) {
    console.error('Errore caricamento account:', e)
  } finally {
    loading.value = false
  }
}

const handleUpgrade = async (piano: string, billing: string = 'monthly') => {
  try {
    upgradeMessage.value = ''
    const response = await $fetch<{ success: boolean; data: any }>(
      `${config.public.apiBase}/upgrade`,
      {
        method: 'POST',
        headers: getAuthHeaders(),
        body: { piano, billing }
      }
    )
    if (response.success && response.data) {
      if (response.data.checkout_url) {
        window.location.href = response.data.checkout_url
      } else {
        upgradeMessage.value = response.data.message
      }
    }
  } catch (e: any) {
    upgradeMessage.value = e?.data?.message || 'Errore durante l\'upgrade'
  }
}

const openBillingPortal = async () => {
  try {
    const response = await $fetch<{ success: boolean; data: any }>(
      `${config.public.apiBase}/billing-portal`,
      {
        method: 'POST',
        headers: getAuthHeaders(),
      }
    )
    if (response.success && response.data?.url) {
      window.location.href = response.data.url
    } else {
      upgradeMessage.value = response.data?.message || 'Billing portal non disponibile'
    }
  } catch (e) {
    console.error('Errore billing portal:', e)
  }
}

const scrollToPiani = () => {
  pianiSection.value?.scrollIntoView({ behavior: 'smooth' })
}

onMounted(() => {
  loadAccount()
})
</script>
