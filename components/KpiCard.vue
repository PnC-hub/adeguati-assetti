<template>
  <div
    class="bg-gray-50 rounded-lg p-4 border-l-4 cursor-pointer hover:shadow-md transition-shadow"
    :class="borderColor"
    @click="$emit('click')"
  >
    <div class="flex items-start justify-between mb-2">
      <span class="text-sm font-medium text-gray-600">{{ kpi.nome }}</span>
      <div
        class="w-3 h-3 rounded-full"
        :class="statusColor"
      ></div>
    </div>
    <div class="flex items-end justify-between">
      <div class="text-2xl font-bold text-gray-900">
        {{ formattedValue }}
      </div>
      <div v-if="kpi.delta_precedente !== null && kpi.delta_precedente !== undefined" class="flex items-center gap-1 text-sm" :class="deltaColor">
        <Icon
          :name="kpi.delta_precedente >= 0 ? 'heroicons:arrow-up' : 'heroicons:arrow-down'"
          class="w-4 h-4"
        />
        <span>{{ Math.abs(kpi.delta_precedente).toFixed(1) }}</span>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
interface KpiData {
  codice: string
  nome: string
  valore: number | null
  stato: 'verde' | 'giallo' | 'rosso' | 'nd'
  unita_misura: string
  delta_precedente?: number | null
}

const props = defineProps<{
  kpi: KpiData
}>()

defineEmits(['click'])

const statusColor = computed(() => {
  switch (props.kpi.stato) {
    case 'verde': return 'bg-green-500'
    case 'giallo': return 'bg-yellow-500'
    case 'rosso': return 'bg-red-500'
    default: return 'bg-gray-400'
  }
})

const borderColor = computed(() => {
  switch (props.kpi.stato) {
    case 'verde': return 'border-green-500'
    case 'giallo': return 'border-yellow-500'
    case 'rosso': return 'border-red-500'
    default: return 'border-gray-400'
  }
})

const deltaColor = computed(() => {
  if (!props.kpi.delta_precedente) return 'text-gray-500'
  return props.kpi.delta_precedente >= 0 ? 'text-green-600' : 'text-red-600'
})

const formattedValue = computed(() => {
  if (props.kpi.valore === null || props.kpi.valore === undefined) return 'N/D'

  const val = props.kpi.valore

  if (props.kpi.unita_misura === '€') {
    return new Intl.NumberFormat('it-IT', { style: 'currency', currency: 'EUR', maximumFractionDigits: 0 }).format(val)
  }

  if (props.kpi.unita_misura === '%') {
    return (val * 100).toFixed(1) + '%'
  }

  if (props.kpi.unita_misura === 'ratio') {
    return val.toFixed(2)
  }

  if (props.kpi.unita_misura === 'giorni') {
    return val.toFixed(0) + ' gg'
  }

  return val.toString()
})
</script>
