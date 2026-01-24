<template>
  <div class="bg-white rounded-2xl shadow-xl p-8 w-full max-w-md">
    <div class="text-center mb-8">
      <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center mx-auto mb-4">
        <Icon name="heroicons:key" class="w-7 h-7 text-white" />
      </div>
      <h1 class="text-2xl font-bold text-gray-900">Password dimenticata?</h1>
      <p class="text-gray-600 mt-1">Inserisci la tua email per recuperarla</p>
    </div>

    <form v-if="!sent" @submit.prevent="handleReset" class="space-y-4">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
        <input
          v-model="email"
          type="email"
          required
          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
          placeholder="nome@azienda.it"
        />
      </div>

      <div v-if="error" class="bg-red-50 text-red-600 text-sm p-3 rounded-lg">
        {{ error }}
      </div>

      <button
        type="submit"
        :disabled="loading"
        class="w-full bg-blue-600 text-white py-2 rounded-lg font-medium hover:bg-blue-700 disabled:opacity-50"
      >
        <span v-if="loading">Invio in corso...</span>
        <span v-else>Invia link di reset</span>
      </button>
    </form>

    <div v-else class="text-center">
      <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
        <Icon name="heroicons:check" class="w-8 h-8 text-green-600" />
      </div>
      <p class="text-gray-600 mb-4">
        Se l'email esiste nel sistema, riceverai un link per reimpostare la password.
      </p>
      <NuxtLink to="/login" class="text-blue-600 hover:text-blue-700 font-medium">
        Torna al login
      </NuxtLink>
    </div>

    <p v-if="!sent" class="text-center text-gray-600 mt-6">
      Ricordi la password?
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

const config = useRuntimeConfig()

const email = ref('')
const loading = ref(false)
const error = ref('')
const sent = ref(false)

const handleReset = async () => {
  loading.value = true
  error.value = ''

  try {
    await $fetch(`${config.public.apiBase}/adeguati-assetti/auth/forgot-password`, {
      method: 'POST',
      headers: {
        'X-API-Key': config.public.apiKey,
        'Content-Type': 'application/json'
      },
      body: { email: email.value }
    })
    sent.value = true
  } catch (e: any) {
    // Don't show error to prevent email enumeration
    sent.value = true
  } finally {
    loading.value = false
  }
}
</script>
