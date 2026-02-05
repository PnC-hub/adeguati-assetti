<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-2xl p-6 text-white">
      <h1 class="text-2xl font-bold">Invita i tuoi colleghi</h1>
      <p class="text-blue-100 mt-2">
        Ogni collega che si registra con il tuo link ti regala +3 mesi di storico dati
      </p>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex items-center justify-center py-12">
      <Icon name="heroicons:arrow-path" class="w-8 h-8 text-blue-600 animate-spin" />
    </div>

    <template v-else>
      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl shadow-sm border p-5">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
              <Icon name="heroicons:users" class="w-5 h-5 text-blue-600" />
            </div>
            <div>
              <p class="text-sm text-gray-500">Colleghi invitati</p>
              <p class="text-2xl font-bold text-gray-900">{{ referralCount }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border p-5">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
              <Icon name="heroicons:calendar" class="w-5 h-5 text-green-600" />
            </div>
            <div>
              <p class="text-sm text-gray-500">Mesi di storico</p>
              <p class="text-2xl font-bold text-gray-900">{{ storicoMesiTotali }} <span class="text-sm font-normal text-gray-500">/ 12 max</span></p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border p-5">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
              <Icon name="heroicons:gift" class="w-5 h-5 text-amber-600" />
            </div>
            <div>
              <p class="text-sm text-gray-500">Prossimo sblocco</p>
              <p class="text-lg font-bold text-gray-900">{{ nextUnlock }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Referral Link -->
      <div class="bg-white rounded-xl shadow-sm border p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Il tuo link di invito</h2>
        <div class="flex gap-3">
          <input
            type="text"
            :value="referralLink"
            readonly
            class="flex-1 px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg text-gray-700 text-sm"
          />
          <button
            @click="copyLink"
            class="px-6 py-3 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 flex items-center gap-2"
          >
            <Icon :name="copied ? 'heroicons:check' : 'heroicons:clipboard'" class="w-5 h-5" />
            {{ copied ? 'Copiato!' : 'Copia' }}
          </button>
        </div>
        <p class="text-sm text-gray-500 mt-3">
          Condividi questo link con i tuoi colleghi imprenditori e commercialisti
        </p>
      </div>

      <!-- Unlock Progress -->
      <div class="bg-white rounded-xl shadow-sm border p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Sblocca funzionalità</h2>

        <div class="space-y-4">
          <!-- Storico Progress -->
          <div>
            <div class="flex justify-between text-sm mb-2">
              <span class="text-gray-600">Storico dati</span>
              <span class="text-gray-900 font-medium">{{ storicoMesiTotali }} / 12 mesi</span>
            </div>
            <div class="h-3 bg-gray-100 rounded-full overflow-hidden">
              <div
                class="h-full bg-gradient-to-r from-blue-500 to-blue-600 rounded-full transition-all"
                :style="{ width: `${(storicoMesiTotali / 12) * 100}%` }"
              ></div>
            </div>
          </div>

          <!-- Unlock Items -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
            <!-- Export PDF -->
            <div :class="[
              'p-4 rounded-xl border-2 transition-all',
              sblocchi.exportPdf ? 'bg-green-50 border-green-200' : 'bg-gray-50 border-gray-200'
            ]">
              <div class="flex items-start gap-3">
                <div :class="[
                  'w-10 h-10 rounded-lg flex items-center justify-center',
                  sblocchi.exportPdf ? 'bg-green-500' : 'bg-gray-300'
                ]">
                  <Icon
                    :name="sblocchi.exportPdf ? 'heroicons:check' : 'heroicons:lock-closed'"
                    class="w-5 h-5 text-white"
                  />
                </div>
                <div>
                  <h3 :class="['font-semibold', sblocchi.exportPdf ? 'text-green-700' : 'text-gray-600']">
                    Export PDF
                  </h3>
                  <p class="text-sm text-gray-500 mt-1">
                    {{ sblocchi.exportPdf ? 'Sbloccato!' : '3 inviti necessari' }}
                  </p>
                </div>
              </div>
              <div v-if="!sblocchi.exportPdf" class="mt-3">
                <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                  <div
                    class="h-full bg-blue-500 rounded-full transition-all"
                    :style="{ width: `${Math.min(referralCount / 3, 1) * 100}%` }"
                  ></div>
                </div>
                <p class="text-xs text-gray-500 mt-1">{{ referralCount }} / 3 inviti</p>
              </div>
            </div>

            <!-- Alert Automatici -->
            <div :class="[
              'p-4 rounded-xl border-2 transition-all',
              sblocchi.alerts ? 'bg-green-50 border-green-200' : 'bg-gray-50 border-gray-200'
            ]">
              <div class="flex items-start gap-3">
                <div :class="[
                  'w-10 h-10 rounded-lg flex items-center justify-center',
                  sblocchi.alerts ? 'bg-green-500' : 'bg-gray-300'
                ]">
                  <Icon
                    :name="sblocchi.alerts ? 'heroicons:check' : 'heroicons:lock-closed'"
                    class="w-5 h-5 text-white"
                  />
                </div>
                <div>
                  <h3 :class="['font-semibold', sblocchi.alerts ? 'text-green-700' : 'text-gray-600']">
                    Alert Automatici
                  </h3>
                  <p class="text-sm text-gray-500 mt-1">
                    {{ sblocchi.alerts ? 'Sbloccato!' : '5 inviti necessari' }}
                  </p>
                </div>
              </div>
              <div v-if="!sblocchi.alerts" class="mt-3">
                <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                  <div
                    class="h-full bg-blue-500 rounded-full transition-all"
                    :style="{ width: `${Math.min(referralCount / 5, 1) * 100}%` }"
                  ></div>
                </div>
                <p class="text-xs text-gray-500 mt-1">{{ referralCount }} / 5 inviti</p>
              </div>
            </div>

            <!-- KPI Settoriali -->
            <div :class="[
              'p-4 rounded-xl border-2 transition-all',
              sblocchi.kpiSettoriali ? 'bg-green-50 border-green-200' : 'bg-gray-50 border-gray-200'
            ]">
              <div class="flex items-start gap-3">
                <div :class="[
                  'w-10 h-10 rounded-lg flex items-center justify-center',
                  sblocchi.kpiSettoriali ? 'bg-green-500' : 'bg-gray-300'
                ]">
                  <Icon
                    :name="sblocchi.kpiSettoriali ? 'heroicons:check' : 'heroicons:lock-closed'"
                    class="w-5 h-5 text-white"
                  />
                </div>
                <div>
                  <h3 :class="['font-semibold', sblocchi.kpiSettoriali ? 'text-green-700' : 'text-gray-600']">
                    KPI Settoriali ATECO
                  </h3>
                  <p class="text-sm text-gray-500 mt-1">
                    {{ sblocchi.kpiSettoriali ? 'Sbloccato!' : '7 inviti necessari' }}
                  </p>
                </div>
              </div>
              <div v-if="!sblocchi.kpiSettoriali" class="mt-3">
                <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                  <div
                    class="h-full bg-blue-500 rounded-full transition-all"
                    :style="{ width: `${Math.min(referralCount / 7, 1) * 100}%` }"
                  ></div>
                </div>
                <p class="text-xs text-gray-500 mt-1">{{ referralCount }} / 7 inviti</p>
              </div>
            </div>

            <!-- Multi-Azienda -->
            <div :class="[
              'p-4 rounded-xl border-2 transition-all',
              sblocchi.multiAzienda ? 'bg-green-50 border-green-200' : 'bg-gray-50 border-gray-200'
            ]">
              <div class="flex items-start gap-3">
                <div :class="[
                  'w-10 h-10 rounded-lg flex items-center justify-center',
                  sblocchi.multiAzienda ? 'bg-green-500' : 'bg-gray-300'
                ]">
                  <Icon
                    :name="sblocchi.multiAzienda ? 'heroicons:check' : 'heroicons:lock-closed'"
                    class="w-5 h-5 text-white"
                  />
                </div>
                <div>
                  <h3 :class="['font-semibold', sblocchi.multiAzienda ? 'text-green-700' : 'text-gray-600']">
                    Multi-Azienda
                  </h3>
                  <p class="text-sm text-gray-500 mt-1">
                    {{ sblocchi.multiAzienda ? 'Sbloccato!' : '10 inviti necessari' }}
                  </p>
                </div>
              </div>
              <div v-if="!sblocchi.multiAzienda" class="mt-3">
                <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                  <div
                    class="h-full bg-blue-500 rounded-full transition-all"
                    :style="{ width: `${Math.min(referralCount / 10, 1) * 100}%` }"
                  ></div>
                </div>
                <p class="text-xs text-gray-500 mt-1">{{ referralCount }} / 10 inviti</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- How It Works -->
      <div class="bg-white rounded-xl shadow-sm border p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Come funziona</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div class="text-center">
            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
              <span class="text-xl font-bold text-blue-600">1</span>
            </div>
            <h3 class="font-medium text-gray-900">Condividi il link</h3>
            <p class="text-sm text-gray-500 mt-1">Invia il tuo link a colleghi imprenditori</p>
          </div>
          <div class="text-center">
            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
              <span class="text-xl font-bold text-blue-600">2</span>
            </div>
            <h3 class="font-medium text-gray-900">Si registrano gratis</h3>
            <p class="text-sm text-gray-500 mt-1">Il collega crea un account gratuito</p>
          </div>
          <div class="text-center">
            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
              <span class="text-xl font-bold text-blue-600">3</span>
            </div>
            <h3 class="font-medium text-gray-900">Sblocchi funzionalità</h3>
            <p class="text-sm text-gray-500 mt-1">+3 mesi storico + sblocchi automatici</p>
          </div>
        </div>
      </div>
    </template>
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  layout: 'dashboard'
})

const config = useRuntimeConfig()
const loading = ref(true)
const copied = ref(false)

// Referral data
const referralCode = ref('')
const referralCount = ref(0)
const storicoMesiTotali = ref(3)
const sblocchi = ref({
  exportPdf: false,
  alerts: false,
  kpiSettoriali: false,
  multiAzienda: false
})

// Computed
const referralLink = computed(() => {
  return `https://adeguatiassettiimpresa.it/register?ref=${referralCode.value}`
})

const nextUnlock = computed(() => {
  if (referralCount.value < 3) return `${3 - referralCount.value} inviti per Export PDF`
  if (referralCount.value < 5) return `${5 - referralCount.value} inviti per Alert`
  if (referralCount.value < 7) return `${7 - referralCount.value} inviti per KPI Settoriali`
  if (referralCount.value < 10) return `${10 - referralCount.value} inviti per Multi-Azienda`
  return 'Tutto sbloccato!'
})

// Methods
const copyLink = async () => {
  try {
    await navigator.clipboard.writeText(referralLink.value)
    copied.value = true
    setTimeout(() => {
      copied.value = false
    }, 2000)
  } catch (err) {
    console.error('Errore copia:', err)
  }
}

const fetchReferralData = async () => {
  const token = localStorage.getItem('aa_token')
  if (!token) return

  try {
    const response = await $fetch<{ success: boolean; data: any }>(
      `${config.public.apiBase}/account`,
      {
        headers: {
          'Authorization': `Bearer ${token}`,
          'X-API-Key': config.public.apiKey,
          'Accept': 'application/json'
        }
      }
    )

    if (response.success && response.data) {
      const { referral, freemium } = response.data

      if (referral) {
        referralCode.value = referral.code || ''
        referralCount.value = referral.count || 0
        sblocchi.value = {
          exportPdf: referral.sblocchi?.exportPdf || false,
          alerts: referral.sblocchi?.alerts || false,
          kpiSettoriali: referral.sblocchi?.kpiSettoriali || false,
          multiAzienda: referral.sblocchi?.multiAzienda || false
        }
      }

      if (freemium) {
        storicoMesiTotali.value = freemium.storico_mesi_totali || 3
      }
    }
  } catch (error) {
    console.error('Errore fetch referral data:', error)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchReferralData()
})
</script>
