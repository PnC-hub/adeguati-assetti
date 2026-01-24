<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Navbar -->
    <nav class="bg-white shadow-sm border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex items-center">
            <NuxtLink to="/dashboard" class="flex items-center gap-2">
              <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                <Icon name="heroicons:chart-bar" class="w-5 h-5 text-white" />
              </div>
              <span class="font-bold text-gray-900">Adeguati Assetti</span>
            </NuxtLink>
          </div>

          <div class="flex items-center gap-4">
            <NuxtLink to="/dashboard" class="text-gray-600 hover:text-gray-900 px-3 py-2 text-sm font-medium">
              Dashboard
            </NuxtLink>
            <NuxtLink to="/dashboard/inserimento" class="text-gray-600 hover:text-gray-900 px-3 py-2 text-sm font-medium">
              Inserimento Dati
            </NuxtLink>
            <button @click="logout" class="text-gray-600 hover:text-gray-900 px-3 py-2 text-sm font-medium">
              Esci
            </button>
          </div>
        </div>
      </div>
    </nav>

    <!-- Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <slot />
    </main>
  </div>
</template>

<script setup lang="ts">
const router = useRouter()

const logout = () => {
  localStorage.removeItem('aa_token')
  localStorage.removeItem('aa_user')
  router.push('/login')
}
</script>
