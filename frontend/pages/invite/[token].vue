<template>
  <div class="min-h-screen bg-gray-50 flex items-center justify-center p-4">
    <!-- Loading -->
    <div v-if="loading" class="text-center">
      <div class="w-12 h-12 border-4 border-purple-600 border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
      <p class="text-gray-600">Verifica invito in corso...</p>
    </div>

    <!-- Invalid/Expired Token -->
    <div v-else-if="error" class="max-w-md w-full bg-white rounded-xl shadow-lg p-8 text-center">
      <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
        <Icon name="heroicons:x-circle" class="w-8 h-8 text-red-600" />
      </div>
      <h1 class="text-xl font-bold text-gray-900 mb-2">Invito Non Valido</h1>
      <p class="text-gray-600 mb-6">{{ errorMessage }}</p>
      <NuxtLink
        to="/login"
        class="inline-block bg-purple-600 text-white px-6 py-2 rounded-lg text-sm font-medium hover:bg-purple-700 transition"
      >
        Vai al Login
      </NuxtLink>
    </div>

    <!-- Valid Token - Accept Invite -->
    <div v-else-if="invite && !accepted" class="max-w-md w-full bg-white rounded-xl shadow-lg p-8">
      <div class="text-center mb-6">
        <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
          <Icon name="heroicons:envelope-open" class="w-8 h-8 text-purple-600" />
        </div>
        <h1 class="text-xl font-bold text-gray-900">Sei stato invitato!</h1>
        <p class="text-gray-600 mt-2">
          <strong>{{ invite.studio_nome }}</strong> ti ha invitato a visualizzare i dati della tua azienda.
        </p>
      </div>

      <div class="bg-gray-50 rounded-lg p-4 mb-6">
        <div class="text-sm text-gray-500 mb-1">Azienda</div>
        <div class="font-semibold text-gray-900">{{ invite.azienda_nome }}</div>
      </div>

      <div class="space-y-4 mb-6">
        <div class="text-sm text-gray-600">
          <Icon name="heroicons:check-circle" class="w-4 h-4 text-green-600 inline mr-2" />
          Visualizza i KPI della tua azienda
        </div>
        <div class="text-sm text-gray-600">
          <Icon name="heroicons:check-circle" class="w-4 h-4 text-green-600 inline mr-2" />
          Monitora lo stato di salute aziendale
        </div>
        <div class="text-sm text-gray-600">
          <Icon name="heroicons:check-circle" class="w-4 h-4 text-green-600 inline mr-2" />
          Accedi ai dati in sola lettura
        </div>
      </div>

      <!-- New User Form -->
      <div v-if="!invite.has_account" class="space-y-4 mb-6">
        <div class="text-sm font-medium text-gray-900 mb-2">Crea il tuo account</div>

        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nome</label>
            <input
              v-model="form.nome"
              type="text"
              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
              required
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Cognome</label>
            <input
              v-model="form.cognome"
              type="text"
              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
              required
            />
          </div>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
          <input
            :value="invite.email"
            type="email"
            class="w-full bg-gray-100 border border-gray-300 rounded-lg px-3 py-2 text-sm"
            disabled
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
          <input
            v-model="form.password"
            type="password"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
            placeholder="Minimo 8 caratteri"
            required
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Conferma Password</label>
          <input
            v-model="form.password_confirmation"
            type="password"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
            required
          />
        </div>
      </div>

      <!-- Existing User -->
      <div v-else class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
        <div class="flex items-start gap-3">
          <Icon name="heroicons:information-circle" class="w-5 h-5 text-blue-600 mt-0.5" />
          <div>
            <div class="text-sm font-medium text-blue-800">Hai già un account</div>
            <div class="text-sm text-blue-700">
              L'invito verrà collegato al tuo account esistente con email {{ invite.email }}
            </div>
          </div>
        </div>
      </div>

      <button
        @click="acceptInvite"
        :disabled="accepting"
        class="w-full bg-purple-600 text-white py-3 rounded-lg font-medium hover:bg-purple-700 disabled:opacity-50 transition"
      >
        <span v-if="accepting">Accettando...</span>
        <span v-else>Accetta Invito</span>
      </button>

      <p class="text-xs text-gray-500 text-center mt-4">
        Accettando l'invito accetti i <NuxtLink to="/termini-servizio" class="text-purple-600 hover:underline">Termini di Servizio</NuxtLink>
        e la <NuxtLink to="/privacy-policy" class="text-purple-600 hover:underline">Privacy Policy</NuxtLink>.
      </p>
    </div>

    <!-- Accepted -->
    <div v-else-if="accepted" class="max-w-md w-full bg-white rounded-xl shadow-lg p-8 text-center">
      <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
        <Icon name="heroicons:check-circle" class="w-8 h-8 text-green-600" />
      </div>
      <h1 class="text-xl font-bold text-gray-900 mb-2">Invito Accettato!</h1>
      <p class="text-gray-600 mb-6">
        Il tuo account è stato configurato. Ora puoi accedere alla dashboard.
      </p>
      <NuxtLink
        to="/cliente"
        class="inline-block bg-purple-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-purple-700 transition"
      >
        Vai alla Dashboard
      </NuxtLink>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  layout: false
})

const route = useRoute()
const router = useRouter()
const config = useRuntimeConfig()

const loading = ref(true)
const error = ref(false)
const errorMessage = ref('')
const invite = ref<any>(null)
const accepting = ref(false)
const accepted = ref(false)

const form = ref({
  nome: '',
  cognome: '',
  password: '',
  password_confirmation: ''
})

const verifyToken = async () => {
  const token = route.params.token as string

  try {
    const response = await $fetch<{ success: boolean; data: any; message?: string }>(`${config.public.apiBase}/inviti/verify/${token}`, {
      headers: {
        'X-API-Key': config.public.apiKey
      }
    })

    if (response.success) {
      invite.value = response.data
    } else {
      error.value = true
      errorMessage.value = response.message || 'Invito non valido o scaduto'
    }
  } catch (e: any) {
    error.value = true
    errorMessage.value = e.data?.message || 'Invito non valido o scaduto'
  } finally {
    loading.value = false
  }
}

const acceptInvite = async () => {
  if (!invite.value.has_account) {
    if (!form.value.nome || !form.value.cognome || !form.value.password) {
      return
    }
    if (form.value.password !== form.value.password_confirmation) {
      return
    }
  }

  accepting.value = true
  const token = route.params.token as string

  try {
    const response = await $fetch<{ success: boolean; data: any }>(`${config.public.apiBase}/inviti/accept/${token}`, {
      method: 'POST',
      headers: {
        'X-API-Key': config.public.apiKey,
        'Content-Type': 'application/json'
      },
      body: invite.value.has_account ? {} : form.value
    })

    if (response.success) {
      // Store token and user
      localStorage.setItem('aa_token', response.data.token)
      localStorage.setItem('aa_user', JSON.stringify(response.data.user))
      localStorage.setItem('aa_piano', response.data.user.piano || 'cliente')

      accepted.value = true
    }
  } catch (e: any) {
    error.value = true
    errorMessage.value = e.data?.message || 'Errore nell\'accettazione dell\'invito'
  } finally {
    accepting.value = false
  }
}

onMounted(verifyToken)
</script>
