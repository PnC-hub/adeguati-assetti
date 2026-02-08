<template>
  <div class="min-h-screen bg-gray-50 flex">
    <!-- Sidebar -->
    <aside class="w-64 bg-slate-800 min-h-screen flex flex-col">
      <!-- Logo -->
      <div class="p-4 border-b border-slate-700">
        <NuxtLink to="/cliente" class="flex items-center gap-2">
          <div class="w-8 h-8 bg-slate-600 rounded-lg flex items-center justify-center">
            <Icon name="heroicons:eye" class="w-5 h-5 text-white" />
          </div>
          <span class="font-bold text-white">Area Cliente</span>
        </NuxtLink>
      </div>

      <!-- Azienda Info -->
      <div class="p-4 border-b border-slate-700">
        <div class="text-xs text-slate-400 mb-1">La Tua Azienda</div>
        <div class="text-white font-medium truncate">{{ azienda?.nome || 'La mia azienda' }}</div>
        <div v-if="studio" class="text-xs text-slate-400 mt-1">
          Gestito da: {{ studio.nome }}
        </div>
      </div>

      <!-- Navigation -->
      <nav class="flex-1 p-4 space-y-1">
        <NuxtLink
          v-for="item in navigation"
          :key="item.to"
          :to="item.to"
          class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors"
          :class="[
            isActiveRoute(item.to)
              ? 'bg-slate-700 text-white'
              : 'text-slate-300 hover:bg-slate-700 hover:text-white'
          ]"
        >
          <Icon :name="item.icon" class="w-5 h-5" />
          {{ item.label }}
        </NuxtLink>
      </nav>

      <!-- KPI Summary -->
      <div class="p-4 border-t border-slate-700">
        <div class="text-xs text-slate-400 mb-2">Stato KPI</div>
        <div class="grid grid-cols-3 gap-2 text-xs">
          <div class="bg-slate-700 rounded p-2 text-center">
            <div class="text-green-400 font-bold">{{ kpiSummary.verdi }}</div>
            <div class="text-slate-400">OK</div>
          </div>
          <div class="bg-slate-700 rounded p-2 text-center">
            <div class="text-yellow-400 font-bold">{{ kpiSummary.gialli }}</div>
            <div class="text-slate-400">Att.</div>
          </div>
          <div class="bg-slate-700 rounded p-2 text-center">
            <div class="text-red-400 font-bold">{{ kpiSummary.rossi }}</div>
            <div class="text-slate-400">Crit.</div>
          </div>
        </div>
      </div>

      <!-- Logout -->
      <div class="p-4 border-t border-slate-700">
        <button
          @click="logout"
          class="flex items-center gap-2 w-full px-3 py-2 text-slate-300 hover:text-white hover:bg-slate-700 rounded-lg transition text-sm"
        >
          <Icon name="heroicons:arrow-right-on-rectangle" class="w-5 h-5" />
          Esci
        </button>
      </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col">
      <!-- Top Bar -->
      <header class="bg-white shadow-sm border-b border-gray-200 h-16 flex items-center px-6">
        <div class="flex-1">
          <h1 class="text-lg font-semibold text-gray-900">{{ pageTitle }}</h1>
        </div>
        <div class="flex items-center gap-4">
          <span class="text-xs bg-slate-100 text-slate-600 px-2 py-1 rounded">Accesso in sola lettura</span>
          <span class="text-sm text-gray-500">{{ user?.nome }} {{ user?.cognome }}</span>
          <div class="w-8 h-8 bg-slate-100 rounded-full flex items-center justify-center">
            <span class="text-slate-600 font-medium text-sm">{{ userInitials }}</span>
          </div>
        </div>
      </header>

      <!-- Page Content -->
      <main class="flex-1 p-6 overflow-auto">
        <slot />
      </main>
    </div>
  </div>
</template>

<script setup lang="ts">
const router = useRouter()
const route = useRoute()
const config = useRuntimeConfig()

const user = ref<any>(null)
const azienda = ref<any>(null)
const studio = ref<any>(null)
const kpiSummary = ref({ verdi: 0, gialli: 0, rossi: 0 })

const navigation = [
  { label: 'Dashboard', icon: 'heroicons:home', to: '/cliente' },
  { label: 'KPI Aziendali', icon: 'heroicons:chart-bar', to: '/cliente/kpi' },
  { label: 'Dati Economici', icon: 'heroicons:document-chart-bar', to: '/cliente/dati' },
  { label: 'Storico', icon: 'heroicons:clock', to: '/cliente/storico' },
]

const pageTitle = computed(() => {
  const titles: Record<string, string> = {
    '/cliente': 'Dashboard',
    '/cliente/kpi': 'KPI Aziendali',
    '/cliente/dati': 'Dati Economici',
    '/cliente/storico': 'Storico',
  }
  return titles[route.path] || 'Area Cliente'
})

const userInitials = computed(() => {
  if (!user.value) return '?'
  return `${user.value.nome?.[0] || ''}${user.value.cognome?.[0] || ''}`.toUpperCase()
})

const isActiveRoute = (path: string) => {
  if (path === '/cliente') {
    return route.path === '/cliente'
  }
  return route.path.startsWith(path)
}

const logout = () => {
  localStorage.removeItem('aa_token')
  localStorage.removeItem('aa_user')
  localStorage.removeItem('aa_piano')
  router.push('/login')
}

const loadData = async () => {
  const storedUser = localStorage.getItem('aa_user')
  if (storedUser) {
    user.value = JSON.parse(storedUser)
  }

  const token = localStorage.getItem('aa_token')
  if (!token) {
    router.push('/login')
    return
  }

  try {
    const response = await $fetch<{ success: boolean; data: any }>(`${config.public.apiBase}/cliente/dashboard`, {
      headers: {
        'Authorization': `Bearer ${token}`,
        'X-API-Key': config.public.apiKey
      }
    })

    if (response.success) {
      azienda.value = response.data.azienda
      studio.value = response.data.studio
      kpiSummary.value = {
        verdi: response.data.kpi?.filter((k: any) => k.stato === 'verde').length || 0,
        gialli: response.data.kpi?.filter((k: any) => k.stato === 'giallo').length || 0,
        rossi: response.data.kpi?.filter((k: any) => k.stato === 'rosso').length || 0
      }
    }
  } catch (e) {
    console.error('Error loading cliente data:', e)
  }
}

onMounted(loadData)
</script>
