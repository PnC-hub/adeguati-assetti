<template>
  <div class="min-h-screen bg-gray-50 flex">
    <!-- Sidebar -->
    <aside class="w-64 bg-purple-900 min-h-screen flex flex-col">
      <!-- Logo -->
      <div class="p-4 border-b border-purple-800">
        <NuxtLink to="/consulente" class="flex items-center gap-2">
          <div class="w-8 h-8 bg-purple-600 rounded-lg flex items-center justify-center">
            <Icon name="heroicons:user-group" class="w-5 h-5 text-white" />
          </div>
          <span class="font-bold text-white">Area Consulente</span>
        </NuxtLink>
      </div>

      <!-- Studio Info -->
      <div class="p-4 border-b border-purple-800">
        <div class="text-xs text-purple-300 mb-1">Studio</div>
        <div class="text-white font-medium truncate">{{ studio?.nome || 'Il mio studio' }}</div>
        <div class="mt-2">
          <div class="text-xs text-green-300">Accesso gratuito</div>
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
              ? 'bg-purple-600/30 text-purple-200'
              : 'text-purple-300 hover:bg-purple-800 hover:text-white'
          ]"
        >
          <Icon :name="item.icon" class="w-5 h-5" />
          {{ item.label }}
          <span v-if="item.badge" class="ml-auto bg-purple-600 text-white text-xs px-2 py-0.5 rounded-full">
            {{ item.badge }}
          </span>
        </NuxtLink>
      </nav>

      <!-- Stats -->
      <div class="p-4 border-t border-purple-800">
        <div class="text-xs text-purple-300 mb-2">Riepilogo Clienti</div>
        <div class="grid grid-cols-3 gap-2 text-xs">
          <div class="bg-purple-800/50 rounded p-2 text-center">
            <div class="text-green-400 font-bold">{{ stats.verdi }}</div>
            <div class="text-purple-400">OK</div>
          </div>
          <div class="bg-purple-800/50 rounded p-2 text-center">
            <div class="text-yellow-400 font-bold">{{ stats.gialli }}</div>
            <div class="text-purple-400">Att.</div>
          </div>
          <div class="bg-purple-800/50 rounded p-2 text-center">
            <div class="text-red-400 font-bold">{{ stats.rossi }}</div>
            <div class="text-purple-400">Crit.</div>
          </div>
        </div>
      </div>

      <!-- Logout -->
      <div class="p-4 border-t border-purple-800">
        <button
          @click="logout"
          class="flex items-center gap-2 w-full px-3 py-2 text-purple-300 hover:text-white hover:bg-purple-800 rounded-lg transition text-sm"
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
          <span class="text-sm text-gray-500">{{ user?.nome }} {{ user?.cognome }}</span>
          <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
            <span class="text-purple-600 font-medium text-sm">{{ userInitials }}</span>
          </div>
        </div>
      </header>

      <!-- Page Content -->
      <main class="flex-1 p-6 overflow-auto">
        <slot />
      </main>
    </div>

    <!-- Floating Ticket Button -->
    <TicketButton />
  </div>
</template>

<script setup lang="ts">
const router = useRouter()
const route = useRoute()
const config = useRuntimeConfig()

const user = ref<any>(null)
const studio = ref<any>(null)
const stats = ref({ verdi: 0, gialli: 0, rossi: 0 })
const trialDaysLeft = ref<number | null>(null)

const navigation = computed(() => [
  { label: 'Dashboard', icon: 'heroicons:home', to: '/consulente' },
  { label: 'Aziende Clienti', icon: 'heroicons:building-office-2', to: '/consulente/aziende', badge: stats.value.verdi + stats.value.gialli + stats.value.rossi || null },
  { label: 'Invita Clienti', icon: 'heroicons:envelope', to: '/consulente/inviti' },
  { label: 'Crediti & Guadagni', icon: 'heroicons:banknotes', to: '/consulente/crediti' },
  { label: 'Report', icon: 'heroicons:document-chart-bar', to: '/consulente/report' },
  { label: 'Il Mio Studio', icon: 'heroicons:cog-6-tooth', to: '/consulente/studio' },
  { label: 'Account', icon: 'heroicons:user-circle', to: '/consulente/account' },
])

const pageTitle = computed(() => {
  const titles: Record<string, string> = {
    '/consulente': 'Dashboard Consulente',
    '/consulente/aziende': 'Aziende Clienti',
    '/consulente/inviti': 'Invita Clienti',
    '/consulente/crediti': 'Crediti & Guadagni',
    '/consulente/report': 'Report',
    '/consulente/studio': 'Impostazioni Studio',
    '/consulente/account': 'Il Mio Account',
  }
  return titles[route.path] || 'Area Consulente'
})

const userInitials = computed(() => {
  if (!user.value) return '?'
  return `${user.value.nome?.[0] || ''}${user.value.cognome?.[0] || ''}`.toUpperCase()
})

const isActiveRoute = (path: string) => {
  if (path === '/consulente') {
    return route.path === '/consulente'
  }
  return route.path.startsWith(path)
}

const logout = () => {
  localStorage.removeItem('aa_token')
  localStorage.removeItem('aa_user')
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
    // Load studio info and stats
    const [studioRes, statsRes] = await Promise.all([
      $fetch<{ success: boolean; data: any }>(`${config.public.apiBase}/studio`, {
        headers: {
          'Authorization': `Bearer ${token}`,
          'X-API-Key': config.public.apiKey
        }
      }).catch(() => null),
      $fetch<{ success: boolean; data: any }>(`${config.public.apiBase}/studio/stats`, {
        headers: {
          'Authorization': `Bearer ${token}`,
          'X-API-Key': config.public.apiKey
        }
      }).catch(() => null)
    ])

    if (studioRes?.success) {
      studio.value = studioRes.data
    }

    if (statsRes?.success) {
      stats.value = {
        verdi: statsRes.data.kpi_verdi || 0,
        gialli: statsRes.data.kpi_gialli || 0,
        rossi: statsRes.data.kpi_rossi || 0
      }
    }

    // Calculate trial days left
    if (user.value?.trial_ends_at) {
      const trialEnd = new Date(user.value.trial_ends_at)
      const now = new Date()
      const diffTime = trialEnd.getTime() - now.getTime()
      trialDaysLeft.value = Math.ceil(diffTime / (1000 * 60 * 60 * 24))
    }
  } catch (e) {
    console.error('Error loading consulente data:', e)
  }
}

onMounted(loadData)
</script>
