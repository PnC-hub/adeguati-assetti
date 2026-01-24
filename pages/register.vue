<template>
  <div class="bg-white rounded-2xl shadow-xl p-8 w-full max-w-md">
    <div class="text-center mb-8">
      <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center mx-auto mb-4">
        <Icon name="heroicons:chart-bar" class="w-7 h-7 text-white" />
      </div>
      <h1 class="text-2xl font-bold text-gray-900">Crea il tuo account</h1>
      <p class="text-gray-600 mt-1">14 giorni di prova gratuita</p>
    </div>

    <form @submit.prevent="handleRegister" class="space-y-4">
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

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Nome Azienda</label>
        <input
          v-model="form.azienda"
          type="text"
          required
          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
          placeholder="Rossi S.r.l."
        />
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
        <input
          v-model="form.email"
          type="email"
          required
          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
          placeholder="mario@rossi-srl.it"
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
          Accetto i <a href="#" class="text-blue-600">Termini di Servizio</a> e la <a href="#" class="text-blue-600">Privacy Policy</a>
        </span>
      </label>

      <div v-if="error" class="bg-red-50 text-red-600 text-sm p-3 rounded-lg">
        {{ error }}
      </div>

      <button
        type="submit"
        :disabled="loading"
        class="w-full bg-blue-600 text-white py-2 rounded-lg font-medium hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
      >
        <span v-if="loading">Registrazione in corso...</span>
        <span v-else>Crea Account Gratuito</span>
      </button>
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
const config = useRuntimeConfig()

const form = reactive({
  nome: '',
  cognome: '',
  azienda: '',
  email: '',
  password: '',
  password_confirmation: '',
  terms: false
})

const loading = ref(false)
const error = ref('')

const handleRegister = async () => {
  if (form.password !== form.password_confirmation) {
    error.value = 'Le password non corrispondono'
    return
  }

  loading.value = true
  error.value = ''

  try {
    const response = await $fetch<{ success: boolean; data: { token: string; user: any }; message?: string }>(
      `${config.public.apiBase}/adeguati-assetti/auth/register`,
      {
        method: 'POST',
        headers: {
          'X-API-Key': config.public.apiKey,
          'Content-Type': 'application/json'
        },
        body: {
          nome: form.nome,
          cognome: form.cognome,
          azienda: form.azienda,
          email: form.email,
          password: form.password,
          password_confirmation: form.password_confirmation
        }
      }
    )

    if (response.success) {
      localStorage.setItem('aa_token', response.data.token)
      localStorage.setItem('aa_user', JSON.stringify(response.data.user))
      router.push('/dashboard')
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
