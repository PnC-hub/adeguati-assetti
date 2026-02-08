<template>
  <div class="relative">
    <!-- Current Tenant Button -->
    <button
      @click="open = !open"
      class="flex items-center gap-3 w-full p-3 hover:bg-red-900/30 rounded-lg transition border border-red-500/20"
    >
      <!-- Icon -->
      <div
        class="w-10 h-10 rounded-lg flex items-center justify-center text-white font-bold"
        :class="isAggregated ? 'bg-amber-600' : 'bg-red-600'"
      >
        <Icon v-if="isAggregated" name="heroicons:squares-2x2" class="w-5 h-5" />
        <span v-else>{{ currentTenant?.ragioneSociale?.charAt(0) || '?' }}</span>
      </div>

      <!-- Info -->
      <div class="flex-1 text-left min-w-0">
        <div class="font-medium text-white truncate">
          {{ isAggregated ? 'Vista Aggregata' : (currentTenant?.ragioneSociale || 'Seleziona azienda') }}
        </div>
        <div class="text-xs text-red-300 truncate">
          <template v-if="isAggregated">
            {{ tenants.length }} aziende monitorate
          </template>
          <template v-else-if="currentTenant">
            Score: {{ currentTenant.complianceScore || '—' }}% · {{ formatPiano(currentTenant.piano) }}
          </template>
        </div>
      </div>

      <!-- Chevron -->
      <Icon
        name="heroicons:chevron-down"
        class="w-5 h-5 text-red-300 transition-transform"
        :class="{ 'rotate-180': open }"
      />
    </button>

    <!-- Dropdown -->
    <Teleport to="body">
      <div v-if="open" class="fixed inset-0 z-40" @click="open = false" />
    </Teleport>

    <Transition name="dropdown">
      <div
        v-if="open"
        class="absolute top-full left-0 right-0 mt-1 bg-slate-900 border border-red-500/30 rounded-lg shadow-2xl z-50 overflow-hidden"
      >
        <!-- Vista Aggregata (solo se più di un tenant) -->
        <button
          v-if="tenants.length > 1"
          @click="handleSwitchAggregated"
          class="flex items-center gap-3 w-full p-3 hover:bg-red-900/30 border-b border-slate-700 transition"
          :class="{ 'bg-red-900/30': isAggregated }"
        >
          <div class="w-10 h-10 bg-amber-600 rounded-lg flex items-center justify-center">
            <Icon name="heroicons:squares-2x2" class="w-5 h-5 text-white" />
          </div>
          <div class="flex-1 text-left">
            <div class="font-medium text-white">Vista Aggregata</div>
            <div class="text-xs text-slate-400">Compliance tutte le aziende</div>
          </div>
          <Icon v-if="isAggregated" name="heroicons:check" class="w-5 h-5 text-green-400" />
        </button>

        <!-- Lista Tenants -->
        <div class="max-h-64 overflow-y-auto">
          <button
            v-for="tenant in tenants"
            :key="tenant.id"
            @click="handleSwitchTenant(tenant.id)"
            class="flex items-center gap-3 w-full p-3 hover:bg-red-900/30 transition"
            :class="{ 'bg-red-900/30': tenant.id === currentTenant?.id && !isAggregated }"
          >
            <!-- Avatar -->
            <div class="w-10 h-10 bg-slate-700 rounded-lg flex items-center justify-center text-white font-bold">
              {{ tenant.ragioneSociale?.charAt(0) }}
            </div>

            <!-- Info -->
            <div class="flex-1 text-left min-w-0">
              <div class="font-medium text-white truncate">{{ tenant.ragioneSociale }}</div>
              <div class="text-xs text-slate-400">
                P.IVA {{ tenant.partitaIva || 'N/D' }} · {{ formatRuolo(tenant.ruolo) }}
              </div>
            </div>

            <!-- Compliance Score Badge -->
            <div class="flex items-center gap-2">
              <span
                class="px-2 py-0.5 rounded text-xs font-medium"
                :class="{
                  'bg-green-600 text-white': (tenant.complianceScore || 0) >= 80,
                  'bg-yellow-600 text-white': (tenant.complianceScore || 0) >= 50 && (tenant.complianceScore || 0) < 80,
                  'bg-red-600 text-white': (tenant.complianceScore || 0) < 50
                }"
              >
                {{ tenant.complianceScore || '—' }}%
              </span>
              <Icon
                v-if="tenant.id === currentTenant?.id && !isAggregated"
                name="heroicons:check"
                class="w-5 h-5 text-green-400 flex-shrink-0"
              />
            </div>
          </button>
        </div>

        <!-- Aggiungi Azienda -->
        <NuxtLink
          to="/onboarding"
          class="flex items-center gap-3 w-full p-3 hover:bg-red-900/30 border-t border-slate-700 text-red-400 transition"
          @click="open = false"
        >
          <Icon name="heroicons:plus" class="w-5 h-5" />
          <span>Aggiungi azienda</span>
        </NuxtLink>
      </div>
    </Transition>
  </div>
</template>

<script setup lang="ts">
const {
  currentTenant,
  tenants,
  isAggregated,
  switchTenant,
  switchToAggregated,
  formatPiano,
  formatRuolo,
  init
} = useTenant()

const open = ref(false)

// Fetch on mount
onMounted(async () => {
  await init()
})

// Handlers
const handleSwitchTenant = async (tenantId: string) => {
  await switchTenant(tenantId)
  open.value = false
}

const handleSwitchAggregated = () => {
  switchToAggregated()
  open.value = false
}
</script>

<style scoped>
.dropdown-enter-active,
.dropdown-leave-active {
  transition: all 0.2s ease;
}

.dropdown-enter-from,
.dropdown-leave-to {
  opacity: 0;
  transform: translateY(-8px);
}
</style>
