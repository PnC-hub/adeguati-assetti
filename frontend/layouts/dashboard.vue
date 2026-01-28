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
              <span class="font-bold text-gray-900">Adeguati Assetti Impresa</span>
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

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-400 py-12">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Disclaimer -->
        <div class="bg-gray-800/50 rounded-lg p-6 mb-8">
          <h4 class="text-white font-semibold mb-3 flex items-center gap-2">
            <Icon name="heroicons:exclamation-triangle" class="w-5 h-5 text-yellow-500" />
            Avvertenza
          </h4>
          <p class="text-sm leading-relaxed">
            Le informazioni, i dati, gli indicatori (KPI) e le analisi forniti da questo servizio hanno esclusivamente finalita informative e di supporto gestionale.
            Non costituiscono in alcun modo consulenza professionale di natura legale, fiscale, contabile o finanziaria, ne sostituiscono il parere di professionisti
            abilitati quali commercialisti, revisori contabili, avvocati o consulenti del lavoro iscritti ai rispettivi albi professionali.
          </p>
          <p class="text-sm leading-relaxed mt-3">
            L'utente e tenuto a verificare autonomamente l'accuratezza dei dati inseriti e delle elaborazioni ottenute, e a rivolgersi a professionisti qualificati
            per ogni decisione aziendale, adempimento normativo o valutazione degli assetti organizzativi, amministrativi e contabili ai sensi dell'Art. 2086 del Codice Civile.
          </p>
          <p class="text-sm leading-relaxed mt-3">
            Smiledoc S.r.l. declina ogni responsabilita per danni diretti o indiretti derivanti dall'utilizzo delle informazioni fornite dal servizio.
          </p>
        </div>

        <!-- Copyright e Dati Legali -->
        <div class="border-t border-gray-800 pt-8 text-center">
          <div class="flex justify-center gap-4 mb-4 text-sm">
            <NuxtLink to="/termini-servizio" class="text-gray-400 hover:text-white">Termini di Servizio</NuxtLink>
            <span class="text-gray-600">|</span>
            <NuxtLink to="/privacy-policy" class="text-gray-400 hover:text-white">Privacy Policy</NuxtLink>
          </div>
          <p class="text-sm mb-4">&copy; 2026 Adeguati Assetti Impresa. Tutti i diritti riservati.</p>
          <div class="text-xs text-gray-500 space-y-1">
            <p>Smiledoc S.r.l. - Via Gianni Metello 37, 51100 Pistoia (PT)</p>
            <p>P.IVA: IT15131801001</p>
          </div>
        </div>
      </div>
    </footer>
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
