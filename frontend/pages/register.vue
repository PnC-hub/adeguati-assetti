<template>
  <div class="bg-white rounded-2xl shadow-xl p-8 w-full max-w-lg">
    <!-- Referral Banner -->
    <div v-if="referralCode" class="bg-green-50 border border-green-200 rounded-lg p-3 mb-6">
      <div class="flex items-center gap-2 text-green-700">
        <Icon name="heroicons:gift" class="w-5 h-5" />
        <span class="text-sm font-medium">Sei stato invitato da un collega!</span>
      </div>
    </div>

    <!-- Invite Token Banner -->
    <div v-if="inviteToken" class="bg-purple-50 border border-purple-200 rounded-lg p-3 mb-6">
      <div class="flex items-center gap-2 text-purple-700">
        <Icon name="heroicons:link" class="w-5 h-5" />
        <span class="text-sm font-medium">Registrati per collegarti al tuo commercialista/cliente</span>
      </div>
    </div>

    <div class="text-center mb-8">
      <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center mx-auto mb-4">
        <Icon name="heroicons:chart-bar" class="w-7 h-7 text-white" />
      </div>
      <h1 class="text-2xl font-bold text-gray-900">Registrati Gratis</h1>
      <p class="text-gray-600 mt-1">Nessuna carta richiesta</p>
    </div>

    <!-- Step 1: User Type Selection -->
    <div v-if="step === 1" class="space-y-4">
      <p class="text-center text-gray-700 font-medium mb-4">Chi sei?</p>

      <button
        @click="selectUserType('imprenditore')"
        class="w-full p-4 border-2 rounded-xl text-left hover:border-blue-500 hover:bg-blue-50 transition-all"
        :class="{ 'border-blue-500 bg-blue-50': form.tipo_utente === 'imprenditore', 'border-gray-200': form.tipo_utente !== 'imprenditore' }"
      >
        <div class="flex items-center gap-4">
          <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
            <Icon name="heroicons:building-office-2" class="w-6 h-6 text-blue-600" />
          </div>
          <div>
            <h3 class="font-semibold text-gray-900">Imprenditore</h3>
            <p class="text-sm text-gray-600">Gestisco la mia azienda</p>
          </div>
        </div>
        <div class="mt-3 flex items-center gap-2 text-sm text-blue-600">
          <Icon name="heroicons:check-circle" class="w-4 h-4" />
          <span>Registrati gratis, poi &euro;49/mese</span>
        </div>
      </button>

      <button
        @click="selectUserType('consulente')"
        class="w-full p-4 border-2 rounded-xl text-left hover:border-purple-500 hover:bg-purple-50 transition-all"
        :class="{ 'border-purple-500 bg-purple-50': form.tipo_utente === 'consulente', 'border-gray-200': form.tipo_utente !== 'consulente' }"
      >
        <div class="flex items-center gap-4">
          <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
            <Icon name="heroicons:user-group" class="w-6 h-6 text-purple-600" />
          </div>
          <div>
            <h3 class="font-semibold text-gray-900">Commercialista / Consulente</h3>
            <p class="text-sm text-gray-600">Gestisco i clienti e guadagno il 20%</p>
          </div>
        </div>
        <div class="mt-3 flex items-center gap-2 text-sm text-purple-600">
          <Icon name="heroicons:currency-euro" class="w-4 h-4" />
          <span>Sempre gratuito - Guadagna il 20% sui tuoi clienti</span>
        </div>
      </button>

      <button
        @click="goToStep2"
        :disabled="!form.tipo_utente"
        class="w-full mt-4 bg-blue-600 text-white py-3 rounded-lg font-medium hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
      >
        Continua
      </button>
    </div>

    <!-- Step 2: Registration Form -->
    <form v-else @submit.prevent="handleRegister" class="space-y-4">
      <button type="button" @click="step = 1" class="flex items-center gap-1 text-gray-600 hover:text-gray-900 text-sm mb-4">
        <Icon name="heroicons:arrow-left" class="w-4 h-4" />
        Torna indietro
      </button>

      <!-- User type badge -->
      <div class="flex justify-center mb-4">
        <span
          class="px-4 py-2 rounded-full text-sm font-medium"
          :class="form.tipo_utente === 'consulente' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700'"
        >
          {{ form.tipo_utente === 'consulente' ? '👔 Consulente' : '🏢 Imprenditore' }}
        </span>
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Nome</label>
          <input
            v-model="form.nome"
            type="text"
            required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            placeholder="Mario"
          />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Cognome</label>
          <input
            v-model="form.cognome"
            type="text"
            required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            placeholder="Rossi"
          />
        </div>
      </div>

      <!-- Imprenditore: Azienda -->
      <div v-if="form.tipo_utente === 'imprenditore'">
        <label class="block text-sm font-medium text-gray-700 mb-1">Nome Azienda</label>
        <input
          v-model="form.azienda"
          type="text"
          required
          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
          placeholder="Rossi S.r.l."
        />
      </div>

      <!-- Consulente: Studio -->
      <div v-else class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Nome Studio</label>
          <input
            v-model="form.studio_nome"
            type="text"
            required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
            placeholder="Studio Rossi & Associati"
          />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">P.IVA Studio (opzionale)</label>
          <input
            v-model="form.studio_p_iva"
            type="text"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
            placeholder="12345678901"
          />
        </div>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
        <input
          v-model="form.email"
          type="email"
          required
          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
          placeholder="mario@studio.it"
        />
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
        <input
          v-model="form.password"
          type="password"
          required
          minlength="8"
          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
          placeholder="Minimo 8 caratteri"
        />
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Conferma Password</label>
        <input
          v-model="form.password_confirmation"
          type="password"
          required
          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
          placeholder="Ripeti la password"
        />
      </div>

      <label class="flex items-start gap-2">
        <input type="checkbox" v-model="form.terms" required class="rounded text-blue-600 mt-1" />
        <span class="text-sm text-gray-600">
          Accetto i <NuxtLink to="/termini-servizio" target="_blank" class="text-blue-600 hover:underline">Termini di Servizio</NuxtLink> e la <NuxtLink to="/privacy-policy" target="_blank" class="text-blue-600 hover:underline">Privacy Policy</NuxtLink>
        </span>
      </label>

      <div v-if="error" class="bg-red-50 text-red-600 text-sm p-3 rounded-lg">
        {{ error }}
      </div>

      <button
        type="submit"
        :disabled="loading"
        class="w-full py-3 rounded-lg font-medium disabled:opacity-50 disabled:cursor-not-allowed"
        :class="form.tipo_utente === 'consulente' ? 'bg-purple-600 text-white hover:bg-purple-700' : 'bg-blue-600 text-white hover:bg-blue-700'"
      >
        <span v-if="loading">Registrazione in corso...</span>
        <span v-else>{{ form.tipo_utente === 'consulente' ? 'Registrati Gratis' : 'Registrati Gratis' }}</span>
      </button>

      <!-- Info per tipo utente -->
      <p v-if="form.tipo_utente === 'consulente'" class="text-center text-sm text-gray-500">
        Sempre gratuito. Guadagna il 20% su ogni cliente pagante.
      </p>
      <p v-else-if="form.tipo_utente === 'imprenditore'" class="text-center text-sm text-gray-500">
        Registrazione gratuita, poi €49/mese per tutte le funzionalità.
      </p>
    </form>

    <p class="text-center text-gray-600 mt-6">
      Hai già un account?
      <NuxtLink to="/login" class="text-blue-600 hover:text-blue-700 font-medium">
        Accedi
      </NuxtLink>
    </p>
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  layout: 'auth'
})

const router = useRouter()
const route = useRoute()
const config = useRuntimeConfig()

const step = ref(1)

const referralCode = computed(() => {
  return (route.query.ref as string) || ''
})

const inviteToken = computed(() => {
  return (route.query.invite_token as string) || ''
})

const form = reactive({
  tipo_utente: '' as 'imprenditore' | 'consulente' | '',
  nome: '',
  cognome: '',
  azienda: '',
  studio_nome: '',
  studio_p_iva: '',
  email: '',
  password: '',
  password_confirmation: '',
  terms: false
})

const loading = ref(false)
const error = ref('')

const selectUserType = (type: 'imprenditore' | 'consulente') => {
  form.tipo_utente = type
}

const goToStep2 = () => {
  if (form.tipo_utente) {
    step.value = 2
  }
}

const handleRegister = async () => {
  if (form.password !== form.password_confirmation) {
    error.value = 'Le password non corrispondono'
    return
  }

  loading.value = true
  error.value = ''

  try {
    const params: Record<string, string> = {
      tipo_utente: form.tipo_utente,
      nome: form.nome,
      cognome: form.cognome,
      email: form.email,
      password: form.password,
      password_confirmation: form.password_confirmation
    }

    if (form.tipo_utente === 'imprenditore') {
      params.azienda = form.azienda
    } else {
      params.studio_nome = form.studio_nome
      if (form.studio_p_iva) {
        params.studio_p_iva = form.studio_p_iva
      }
    }

    if (referralCode.value) {
      params.referral_code = referralCode.value
    }

    if (inviteToken.value) {
      params.invite_token = inviteToken.value
    }

    const response = await $fetch<{ success: boolean; data: { token: string; user: any; studio_id?: number }; message?: string }>(
      `${config.public.apiBase}/auth/register`,
      {
        method: 'POST',
        headers: {
          'X-API-Key': config.public.apiKey,
          'Content-Type': 'application/x-www-form-urlencoded',
          'Accept': 'application/json'
        },
        body: new URLSearchParams(params).toString()
      }
    )

    if (response.success) {
      localStorage.setItem('aa_token', response.data.token)
      localStorage.setItem('aa_user', JSON.stringify(response.data.user))

      // Redirect based on user type
      if (response.data.user.tipo_utente === 'consulente') {
        router.push('/consulente')
      } else {
        router.push('/dashboard')
      }
    } else {
      error.value = response.message || 'Errore durante la registrazione'
    }
  } catch (e: any) {
    error.value = e.data?.message || 'Errore durante la registrazione'
  } finally {
    loading.value = false
  }
}
</script>
