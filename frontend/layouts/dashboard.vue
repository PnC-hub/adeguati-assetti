<template>
  <div class="min-h-screen bg-gray-50 flex">
    <!-- Sidebar -->
    <aside class="w-64 bg-slate-900 min-h-screen flex flex-col">
      <!-- Logo -->
      <div class="p-4 border-b border-slate-800">
        <NuxtLink to="/dashboard" class="flex items-center gap-2">
          <div class="w-8 h-8 bg-red-600 rounded-lg flex items-center justify-center">
            <Icon name="heroicons:scale" class="w-5 h-5 text-white" />
          </div>
          <span class="font-bold text-white">Adeguati Assetti</span>
        </NuxtLink>
      </div>

      <!-- Tenant Switcher -->
      <div class="p-4 border-b border-slate-800">
        <TenantSwitcher />
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
              ? 'bg-red-600/20 text-red-400'
              : 'text-slate-400 hover:bg-slate-800 hover:text-white'
          ]"
        >
          <Icon :name="item.icon" class="w-5 h-5" />
          {{ item.label }}
        </NuxtLink>
      </nav>

      <!-- Current Tenant Info -->
      <div v-if="currentTenant && !isAggregated" class="p-4 border-t border-slate-800">
        <div class="text-xs text-slate-500 mb-1">Score Compliance</div>
        <div class="flex items-center gap-2">
          <div class="flex-1 bg-slate-700 rounded-full h-2">
            <div
              class="h-2 rounded-full transition-all"
              :class="{
                'bg-green-500': (currentTenant.complianceScore || 0) >= 80,
                'bg-yellow-500': (currentTenant.complianceScore || 0) >= 50 && (currentTenant.complianceScore || 0) < 80,
                'bg-red-500': (currentTenant.complianceScore || 0) < 50
              }"
              :style="{ width: `${currentTenant.complianceScore || 0}%` }"
            ></div>
          </div>
          <span class="text-white font-bold text-sm">{{ currentTenant.complianceScore || '—' }}%</span>
        </div>
        <div class="text-xs text-slate-500 mt-2">
          {{ currentTenant.isObbligata ? 'Soggetta ad obbligo' : 'Non obbligata' }}
        </div>
      </div>

      <!-- Aggregated Stats -->
      <div v-else-if="isAggregated" class="p-4 border-t border-slate-800">
        <div class="text-xs text-slate-500 mb-2">Riepilogo Portafoglio</div>
        <div class="grid grid-cols-2 gap-2 text-xs">
          <div class="bg-slate-800 rounded p-2 text-center">
            <div class="text-green-400 font-bold">{{ aggregatedStats.inRegola }}</div>
            <div class="text-slate-500">In regola</div>
          </div>
          <div class="bg-slate-800 rounded p-2 text-center">
            <div class="text-red-400 font-bold">{{ aggregatedStats.critiche }}</div>
            <div class="text-slate-500">Critiche</div>
          </div>
        </div>
      </div>

      <!-- Logout -->
      <div class="p-4 border-t border-slate-800">
        <button
          @click="logout"
          class="flex items-center gap-2 w-full px-3 py-2 text-slate-400 hover:text-white hover:bg-slate-800 rounded-lg transition text-sm"
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
          <h1 v-if="currentTenant && !isAggregated" class="text-lg font-semibold text-gray-900">
            {{ currentTenant.ragioneSociale }}
          </h1>
          <h1 v-else-if="isAggregated" class="text-lg font-semibold text-gray-900">
            Vista Aggregata - {{ tenants.length }} Aziende
          </h1>
        </div>
        <div class="flex items-center gap-4">
          <span v-if="currentTenant && !isAggregated" class="text-sm text-gray-500">
            P.IVA {{ currentTenant.partitaIva }}
          </span>
        </div>
      </header>

      <!-- Page Content -->
      <main class="flex-1 p-6 overflow-auto">
        <!-- Aggregated View Banner -->
        <div v-if="isAggregated" class="mb-6 bg-amber-50 border border-amber-200 rounded-lg p-4 flex items-center gap-3">
          <Icon name="heroicons:information-circle" class="w-6 h-6 text-amber-600" />
          <div>
            <div class="font-medium text-amber-800">Vista Aggregata Attiva</div>
            <div class="text-sm text-amber-600">Stai visualizzando i dati di {{ tenants.length }} aziende. Seleziona un'azienda per modificare i dati.</div>
          </div>
        </div>

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

const {
  currentTenant,
  tenants,
  isAggregated,
  aggregatedStats
} = useTenant()

const navigation = [
  { label: 'Dashboard', icon: 'heroicons:home', to: '/dashboard' },
  { label: 'Inserimento Dati', icon: 'heroicons:pencil-square', to: '/dashboard/inserimento' },
  { label: 'Continuità', icon: 'heroicons:arrow-trending-up', to: '/dashboard/continuita' },
  { label: 'Checklist 2086', icon: 'heroicons:clipboard-document-check', to: '/dashboard/checklist' },
  { label: 'Invita Colleghi', icon: 'heroicons:gift', to: '/dashboard/referral' },
  { label: 'Account', icon: 'heroicons:user-circle', to: '/dashboard/account' },
]

const isActiveRoute = (path: string) => {
  if (path === '/dashboard') {
    return route.path === '/dashboard'
  }
  return route.path.startsWith(path)
}

const logout = () => {
  localStorage.removeItem('aa_token')
  localStorage.removeItem('aa_user')
  localStorage.removeItem('aa_tenant_id')
  router.push('/login')
}
</script>
