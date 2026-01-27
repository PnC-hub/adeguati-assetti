<template>
  <div class="bg-white rounded-2xl shadow-xl p-8 w-full max-w-md">
    <!-- Demo Banner -->
    <div v-if="isDemo" class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
      <div class="flex items-center gap-2 text-blue-800">
        <Icon name="heroicons:information-circle" class="w-5 h-5" />
        <span class="font-medium">Modalita Demo</span>
      </div>
      <p class="text-sm text-blue-600 mt-1">
        Credenziali pre-compilate. Clicca "Accedi" per esplorare.
      </p>
    </div>

    <div class="text-center mb-8">
      <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center mx-auto mb-4">
        <Icon name="heroicons:chart-bar" class="w-7 h-7 text-white" />
      </div>
      <h1 class="text-2xl font-bold text-gray-900">Accedi</h1>
      <p class="text-gray-600 mt-1">Bentornato in Adeguati Assetti</p>
    </div>

    <form @submit.prevent="handleLogin" class="space-y-4">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
        <input
          v-model="form.email"
          type="email"
          required
          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
          placeholder="nome@azienda.it"
        />
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
        <div class="relative">
          <input
            v-model="form.password"
            :type="showPassword ? 'text' : 'password'"
            required
            class="w-full px-4 py-2 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            placeholder="••••••••"
          />
          <button
            type="button"
            @click="showPassword = !showPassword"
            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700"
          >
            <Icon :name="showPassword ? 'heroicons:eye-slash' : 'heroicons:eye'" class="w-5 h-5" />
          </button>
        </div>
      </div>

      <div class="flex items-center justify-between">
        <label class="flex items-center gap-2">
          <input type="checkbox" v-model="form.remember" class="rounded text-blue-600" />
          <span class="text-sm text-gray-600">Ricordami</span>
        </label>
        <NuxtLink to="/forgot-password" class="text-sm text-blue-600 hover:text-blue-700">
          Password dimenticata?
        </NuxtLink>
      </div>

      <div v-if="error" class="bg-red-50 text-red-600 text-sm p-3 rounded-lg">
        {{ error }}
      </div>

      <button
        type="submit"
        :disabled="loading"
        class="w-full bg-blue-600 text-white py-2 rounded-lg font-medium hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
      >
        <span v-if="loading">Accesso in corso...</span>
        <span v-else>Accedi</span>
      </button>
    </form>

    <p class="text-center text-gray-600 mt-6">
      Non hai un account?
      <NuxtLink to="/register" class="text-blue-600 hover:text-blue-700 font-medium">
        Registrati gratis
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

// Demo credentials mapping
const demoCredentials: Record<string, { email: string; password: string }> = {
  sana: { email: 'demo.sana@adeguatiassetti.it', password: 'Demo2025!' },
  critica: { email: 'demo.critica@adeguatiassetti.it', password: 'Demo2025!' },
  studio: { email: 'demo.studio@adeguatiassetti.it', password: 'Demo2025!' }
}

const form = reactive({
  email: '',
  password: '',
  remember: false
})

const isDemo = ref(false)

// Check for demo parameter on client-side mount and auto-login
onMounted(async () => {
  const demoType = route.query.demo as string
  if (demoType && demoCredentials[demoType]) {
    form.email = demoCredentials[demoType].email
    form.password = demoCredentials[demoType].password
    isDemo.value = true

    // Auto-login for demo accounts
    await nextTick()
    handleLogin()
  }
})

const loading = ref(false)
const error = ref('')
const showPassword = ref(false)

const handleLogin = async () => {
  loading.value = true
  error.value = ''

  try {
    const response = await $fetch<{ success: boolean; data: { token: string; user: any }; message?: string }>(
      `${config.public.apiBase}/adeguati-assetti/auth/login`,
      {
        method: 'POST',
        headers: {
          'X-API-Key': config.public.apiKey,
          'Content-Type': 'application/x-www-form-urlencoded',
          'Accept': 'application/json'
        },
        body: new URLSearchParams({
          email: form.email,
          password: form.password
        }).toString()
      }
    )

    if (response.success) {
      localStorage.setItem('aa_token', response.data.token)
      localStorage.setItem('aa_user', JSON.stringify(response.data.user))
      router.push('/dashboard')
    } else {
      error.value = response.message || 'Credenziali non valide'
    }
  } catch (e: any) {
    error.value = e.data?.message || 'Errore durante il login'
  } finally {
    loading.value = false
  }
}
</script>
