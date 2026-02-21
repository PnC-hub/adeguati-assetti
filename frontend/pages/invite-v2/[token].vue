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
        class="inline-block bg-blue-600 text-white px-6 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition"
      >
        Vai al Login
      </NuxtLink>
    </div>

    <!-- Valid Token - Accept Invite -->
    <div v-else-if="invite && !accepted" class="max-w-md w-full bg-white rounded-xl shadow-lg p-8">
      <div class="text-center mb-6">
        <div
          class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4"
          :class="isCommercialistaInvite ? 'bg-blue-100' : 'bg-purple-100'"
        >
          <Icon
            :name="isCommercialistaInvite ? 'heroicons:building-office-2' : 'heroicons:user-group'"
            class="w-8 h-8"
            :class="isCommercialistaInvite ? 'text-blue-600' : 'text-purple-600'"
          />
        </div>

        <!-- Commercialista invites Client -->
        <template v-if="isCommercialistaInvite">
          <h1 class="text-xl font-bold text-gray-900">Il tuo commercialista ti invita!</h1>
          <p class="text-gray-600 mt-2">
            <strong>{{ invite.sender_nome }} {{ invite.sender_cognome }}</strong>
            <span v-if="invite.studio_nome"> ({{ invite.studio_nome }})</span>
            ti invita a registrarti su Adeguati Assetti.
          </p>
        </template>

        <!-- Client invites Commercialista -->
        <template v-else>
          <h1 class="text-xl font-bold text-gray-900">Un cliente ti invita!</h1>
          <p class="text-gray-600 mt-2">
            <strong>{{ invite.sender_nome }} {{ invite.sender_cognome }}</strong>
            ti invita a diventare il commercialista della sua azienda
            <strong v-if="invite.azienda_nome">{{ invite.azienda_nome }}</strong>.
          </p>
        </template>
      </div>

      <!-- Benefits -->
      <div class="space-y-3 mb-6">
        <template v-if="isCommercialistaInvite">
          <div class="text-sm text-gray-600">
            <Icon name="heroicons:check-circle" class="w-4 h-4 text-green-600 inline mr-2" />
            Monitora i KPI della tua azienda
          </div>
          <div class="text-sm text-gray-600">
            <Icon name="heroicons:check-circle" class="w-4 h-4 text-green-600 inline mr-2" />
            Registrazione gratuita, poi &euro;49/mese
          </div>
          <div class="text-sm text-gray-600">
            <Icon name="heroicons:check-circle" class="w-4 h-4 text-green-600 inline mr-2" />
            Il tuo commercialista potrà supportarti
          </div>
        </template>
        <template v-else>
          <div class="text-sm text-gray-600">
            <Icon name="heroicons:check-circle" class="w-4 h-4 text-green-600 inline mr-2" />
            Accesso gratuito per sempre
          </div>
          <div class="text-sm text-gray-600">
            <Icon name="heroicons:check-circle" class="w-4 h-4 text-green-600 inline mr-2" />
            Vedi i dati aziendali del cliente
          </div>
          <div class="text-sm text-gray-600">
            <Icon name="heroicons:check-circle" class="w-4 h-4 text-green-600 inline mr-2" />
            Guadagna il 20% (&euro;9,80/mese) per cliente
          </div>
        </template>
      </div>

      <!-- Has Account -->
      <div v-if="invite.has_account" class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
        <div class="flex items-start gap-3">
          <Icon name="heroicons:information-circle" class="w-5 h-5 text-blue-600 mt-0.5" />
          <div>
            <div class="text-sm font-medium text-blue-800">Hai già un account</div>
            <div class="text-sm text-blue-700">
              L'invito verrà collegato al tuo account esistente ({{ invite.recipient_email }})
            </div>
          </div>
        </div>
      </div>

      <!-- New User: Redirect to Register -->
      <div v-else class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
        <div class="flex items-start gap-3">
          <Icon name="heroicons:user-plus" class="w-5 h-5 text-green-600 mt-0.5" />
          <div>
            <div class="text-sm font-medium text-green-800">Crea un account</div>
            <div class="text-sm text-green-700">
              Non hai ancora un account. Cliccando "Accetta" verrai portato alla pagina di registrazione.
            </div>
          </div>
        </div>
      </div>

      <div v-if="acceptError" class="bg-red-50 text-red-600 text-sm p-3 rounded-lg mb-4">
        {{ acceptError }}
      </div>

      <button
        @click="handleAccept"
        :disabled="accepting"
        class="w-full py-3 rounded-lg font-medium transition disabled:opacity-50"
        :class="isCommercialistaInvite ? 'bg-blue-600 text-white hover:bg-blue-700' : 'bg-purple-600 text-white hover:bg-purple-700'"
      >
        <span v-if="accepting">Accettando...</span>
        <span v-else>{{ invite.has_account ? 'Accetta Invito' : 'Registrati e Accetta' }}</span>
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
        {{ isCommercialistaInvite
          ? 'Il collegamento con il tuo commercialista è stato creato.'
          : 'Sei stato collegato come commercialista del cliente.'
        }}
      </p>
      <NuxtLink
        :to="isCommercialistaInvite ? '/dashboard' : '/consulente'"
        class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-blue-700 transition"
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
const acceptError = ref('')

const isCommercialistaInvite = computed(() => {
  return invite.value?.tipo === 'commercialista_to_client'
})

const verifyToken = async () => {
  const token = route.params.token as string

  try {
    const res = await $fetch<{ success: boolean; data: any; message?: string }>(`${config.public.apiBase}/invite-v2/${token}`, {
      headers: {
        'X-API-Key': config.public.apiKey
      }
    })

    if (res.success) {
      invite.value = res.data
    } else {
      error.value = true
      errorMessage.value = res.message || 'Invito non valido o scaduto'
    }
  } catch (e: any) {
    error.value = true
    errorMessage.value = e.data?.message || 'Invito non valido o scaduto'
  } finally {
    loading.value = false
  }
}

const handleAccept = async () => {
  // If user doesn't have account, redirect to register with invite_token
  if (!invite.value.has_account) {
    const token = route.params.token as string
    router.push(`/register?invite_token=${token}`)
    return
  }

  // User has account - accept directly
  accepting.value = true
  acceptError.value = ''
  const token = route.params.token as string

  try {
    const res = await $fetch<{ success: boolean; data: any; message?: string }>(`${config.public.apiBase}/invite-v2/${token}/accept`, {
      method: 'POST',
      headers: {
        'X-API-Key': config.public.apiKey,
        'Content-Type': 'application/json'
      },
      body: {}
    })

    if (res.success) {
      if (res.data.token) {
        localStorage.setItem('aa_token', res.data.token)
        localStorage.setItem('aa_user', JSON.stringify(res.data.user))
      }
      accepted.value = true
    } else {
      acceptError.value = res.message || 'Errore nell\'accettazione'
    }
  } catch (e: any) {
    acceptError.value = e.data?.message || 'Errore nell\'accettazione dell\'invito'
  } finally {
    accepting.value = false
  }
}

onMounted(verifyToken)
</script>
