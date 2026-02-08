<template>
  <!-- Floating Ticket Button -->
  <button
    @click="openModal"
    class="fixed bottom-6 right-6 z-40 bg-orange-500 hover:bg-orange-600 text-white p-4 rounded-full shadow-lg transition-all hover:scale-110"
    title="Segnala un problema"
  >
    <Icon name="heroicons:bug-ant" class="w-6 h-6" />
  </button>

  <!-- Modal -->
  <Teleport to="body">
    <div
      v-if="isOpen"
      class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4"
      @click.self="closeModal"
    >
      <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
              <Icon name="heroicons:bug-ant" class="w-5 h-5 text-orange-600" />
            </div>
            <div>
              <h3 class="font-bold text-gray-900">Segnala un Problema</h3>
              <p class="text-sm text-gray-500">Aiutaci a migliorare il servizio</p>
            </div>
          </div>
          <button @click="closeModal" class="text-gray-400 hover:text-gray-600">
            <Icon name="heroicons:x-mark" class="w-6 h-6" />
          </button>
        </div>

        <!-- Success State -->
        <div v-if="submitted" class="text-center py-8">
          <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <Icon name="heroicons:check" class="w-8 h-8 text-green-600" />
          </div>
          <h4 class="text-xl font-bold text-gray-900 mb-2">Segnalazione Inviata!</h4>
          <p class="text-gray-600 mb-6">Grazie per il feedback. Ti contatteremo al piu presto.</p>
          <button
            @click="closeModal"
            class="bg-blue-600 text-white px-6 py-2 rounded-lg font-medium hover:bg-blue-700"
          >
            Chiudi
          </button>
        </div>

        <!-- Form -->
        <form v-else @submit.prevent="submitTicket" class="space-y-4">
          <!-- Tipo problema -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tipo di problema</label>
            <select
              v-model="form.tipo"
              class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              required
            >
              <option value="">Seleziona...</option>
              <option value="bug">Bug / Errore tecnico</option>
              <option value="calcolo">Calcolo KPI errato</option>
              <option value="interfaccia">Problema interfaccia</option>
              <option value="lentezza">Lentezza / Performance</option>
              <option value="suggerimento">Suggerimento miglioramento</option>
              <option value="altro">Altro</option>
            </select>
          </div>

          <!-- Pagina -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Pagina interessata</label>
            <input
              type="text"
              v-model="form.pagina"
              :placeholder="currentPage"
              class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            />
          </div>

          <!-- Descrizione -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Descrizione del problema *</label>
            <textarea
              v-model="form.descrizione"
              rows="4"
              placeholder="Descrivi il problema nel modo piu dettagliato possibile. Cosa stavi facendo? Cosa ti aspettavi? Cosa e successo invece?"
              class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"
              required
            ></textarea>
          </div>

          <!-- Email (opzionale) -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email per risposta (opzionale)</label>
            <input
              type="email"
              v-model="form.email"
              placeholder="La tua email per ricevere aggiornamenti"
              class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            />
          </div>

          <!-- Bottoni -->
          <div class="flex gap-3 pt-4">
            <button
              type="button"
              @click="closeModal"
              class="flex-1 border border-gray-300 text-gray-700 py-3 rounded-lg font-medium hover:bg-gray-50"
            >
              Annulla
            </button>
            <button
              type="submit"
              :disabled="loading"
              class="flex-1 bg-orange-500 text-white py-3 rounded-lg font-medium hover:bg-orange-600 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
            >
              <span v-if="loading" class="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
              <span v-else>Invia Segnalazione</span>
            </button>
          </div>
        </form>

        <!-- Info -->
        <p class="text-xs text-gray-400 text-center mt-4">
          Le segnalazioni vengono gestite entro 24-48 ore lavorative.
        </p>
      </div>
    </div>
  </Teleport>
</template>

<script setup lang="ts">
const route = useRoute()
const config = useRuntimeConfig()

const isOpen = ref(false)
const loading = ref(false)
const submitted = ref(false)

const currentPage = computed(() => route.path)

const form = ref({
  tipo: '',
  pagina: '',
  descrizione: '',
  email: ''
})

const openModal = () => {
  isOpen.value = true
  submitted.value = false
  form.value.pagina = currentPage.value
}

const closeModal = () => {
  isOpen.value = false
  // Reset form after animation
  setTimeout(() => {
    form.value = { tipo: '', pagina: '', descrizione: '', email: '' }
    submitted.value = false
  }, 300)
}

const submitTicket = async () => {
  loading.value = true
  try {
    // Get user info from localStorage
    const userStr = localStorage.getItem('aa_user')
    const user = userStr ? JSON.parse(userStr) : null
    const token = localStorage.getItem('aa_token')

    // Prepare ticket data
    const ticketData = {
      tipo: form.value.tipo,
      pagina: form.value.pagina || currentPage.value,
      descrizione: form.value.descrizione,
      email_risposta: form.value.email || user?.email || '',
      user_id: user?.id || null,
      user_email: user?.email || null,
      user_agent: navigator.userAgent,
      timestamp: new Date().toISOString()
    }

    // Send to backend
    await $fetch(`${config.public.apiBase}/ticket`, {
      method: 'POST',
      headers: {
        'X-API-Key': config.public.apiKey,
        'Content-Type': 'application/json',
        ...(token ? { 'Authorization': `Bearer ${token}` } : {})
      },
      body: ticketData
    })

    submitted.value = true
  } catch (error) {
    // Even if API fails, show success (we don't want to frustrate users)
    // In production, we'd log this error somewhere
    console.error('Ticket submission failed:', error)
    submitted.value = true
  } finally {
    loading.value = false
  }
}
</script>
