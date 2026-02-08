<template>
  <button @click="showModal = true" class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Info pagina">
    <Icon name="heroicons:information-circle" class="w-6 h-6" />
  </button>
  <Teleport to="body">
    <div v-if="showModal" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" @click.self="showModal = false">
      <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-bold text-gray-900">{{ title }}</h3>
          <button @click="showModal = false" class="text-gray-400 hover:text-gray-600"><Icon name="heroicons:x-mark" class="w-5 h-5" /></button>
        </div>
        <p class="text-gray-600 text-sm mb-4">{{ description }}</p>
        <ul v-if="features?.length" class="space-y-2">
          <li v-for="f in features" :key="f" class="flex items-start gap-2 text-sm text-gray-500">
            <Icon name="heroicons:check-circle" class="w-4 h-4 text-blue-500 mt-0.5 flex-shrink-0" />
            <span>{{ f }}</span>
          </li>
        </ul>
      </div>
    </div>
  </Teleport>
</template>
<script setup lang="ts">
defineProps<{ title: string; description: string; features?: string[] }>()
const showModal = ref(false)
</script>
