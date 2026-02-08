<template>
  <Teleport to="body">
    <div v-if="show" class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4" @click.self="close">
      <div class="bg-white rounded-2xl max-w-lg w-full overflow-hidden shadow-2xl relative">
        <button @click="close" class="absolute top-4 right-4 text-white/80 hover:text-white z-10">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
        <div class="bg-gradient-to-r from-red-600 to-red-700 p-6 text-white">
          <h2 class="text-2xl font-bold mb-2">Aspetta\!</h2>
          <p class="text-red-100">Prima di andare via, scarica la checklist gratuita</p>
        </div>
        <div class="p-6">
          <div class="flex items-start gap-4 mb-6">
            <div class="w-16 h-16 bg-red-100 rounded-lg flex items-center justify-center flex-shrink-0">
              <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            <div>
              <h3 class="font-bold text-gray-900 text-lg mb-1">Checklist Gratuita Art. 2086 c.c.</h3>
              <p class="text-gray-600 text-sm">PDF con i 13 elementi che i Tribunali verificano.</p>
            </div>
          </div>
          <form @submit.prevent="handleSubmit" class="space-y-4">
            <input v-model="email" type="email" placeholder="La tua email aziendale" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500" />
            <button type="submit" :disabled="loading" class="w-full bg-red-600 text-white py-3 rounded-lg font-bold hover:bg-red-700 disabled:opacity-50">
              {{ loading ? "Invio..." : "Scarica la Checklist" }}
            </button>
          </form>
          <p class="text-xs text-gray-500 text-center mt-4">Niente spam.</p>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup lang="ts">
defineProps<{ show: boolean }>()
const emit = defineEmits<{ (e: "close"): void; (e: "submit", email: string): void }>()
const email = ref("")
const loading = ref(false)
const close = () => { emit("close"); document.cookie = "exitPopupShown=true; max-age=604800; path=/"; }
const handleSubmit = async () => {
  if (\!email.value) return
  loading.value = true
  try { emit("submit", email.value); alert("Grazie\!"); close() }
  finally { loading.value = false }
}
</script>
