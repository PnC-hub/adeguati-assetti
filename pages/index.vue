<template>
  <div class="min-h-screen">
    <!-- Hero Section -->
    <div class="bg-gradient-to-br from-blue-600 to-blue-800 text-white">
      <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div class="flex justify-between items-center">
          <div class="flex items-center gap-2">
            <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
              <Icon name="heroicons:chart-bar" class="w-6 h-6 text-white" />
            </div>
            <span class="font-bold text-xl">Adeguati Assetti</span>
          </div>
          <div class="flex items-center gap-4">
            <NuxtLink to="/login" class="text-white/80 hover:text-white">Accedi</NuxtLink>
            <NuxtLink to="/register" class="bg-white text-blue-600 px-4 py-2 rounded-lg font-medium hover:bg-blue-50">
              Inizia Gratis
            </NuxtLink>
          </div>
        </div>
      </nav>

      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
        <div class="text-center max-w-3xl mx-auto">
          <h1 class="text-4xl md:text-5xl font-bold mb-6">
            Monitora la salute della tua azienda
          </h1>
          <p class="text-xl text-blue-100 mb-8">
            Sistema automatico di monitoraggio degli Adeguati Assetti Organizzativi, Amministrativi e Contabili.
            Conforme all'Art. 2086 del Codice Civile.
          </p>
          <div class="flex justify-center gap-4">
            <NuxtLink to="/register" class="bg-white text-blue-600 px-8 py-3 rounded-lg font-bold hover:bg-blue-50 transition">
              Prova Gratuita 14 giorni
            </NuxtLink>
            <a href="#features" class="border border-white/50 text-white px-8 py-3 rounded-lg font-medium hover:bg-white/10 transition">
              Scopri di più
            </a>
          </div>
        </div>
      </div>
    </div>

    <!-- Features Section -->
    <section id="features" class="py-20 bg-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
          <h2 class="text-3xl font-bold text-gray-900 mb-4">Tutto sotto controllo con un click</h2>
          <p class="text-gray-600 max-w-2xl mx-auto">
            Dashboard intuitiva con indicatori a semaforo per capire immediatamente lo stato di salute della tua azienda.
          </p>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
          <div class="bg-gray-50 rounded-xl p-6">
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-4">
              <Icon name="heroicons:chart-pie" class="w-6 h-6 text-green-600" />
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">KPI Automatici</h3>
            <p class="text-gray-600">
              Calcolo automatico degli indicatori di crisi d'impresa secondo le linee guida CNDCEC.
            </p>
          </div>

          <div class="bg-gray-50 rounded-xl p-6">
            <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center mb-4">
              <Icon name="heroicons:bell-alert" class="w-6 h-6 text-yellow-600" />
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Alert Preventivi</h3>
            <p class="text-gray-600">
              Ricevi notifiche quando un indicatore supera le soglie di allerta, prima che sia troppo tardi.
            </p>
          </div>

          <div class="bg-gray-50 rounded-xl p-6">
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
              <Icon name="heroicons:document-chart-bar" class="w-6 h-6 text-blue-600" />
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Report PDF</h3>
            <p class="text-gray-600">
              Genera report professionali per il commercialista, il collegio sindacale o il revisore.
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- KPI Section -->
    <section class="py-20 bg-gray-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
          <h2 class="text-3xl font-bold text-gray-900 mb-4">Indicatori monitorati</h2>
          <p class="text-gray-600">Basati sulle linee guida del Consiglio Nazionale Dottori Commercialisti</p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
          <div class="bg-white rounded-lg p-4 border border-gray-200">
            <div class="flex items-center gap-3">
              <div class="w-3 h-3 rounded-full bg-green-500"></div>
              <span class="font-medium">DSCR - Debt Service Coverage Ratio</span>
            </div>
          </div>
          <div class="bg-white rounded-lg p-4 border border-gray-200">
            <div class="flex items-center gap-3">
              <div class="w-3 h-3 rounded-full bg-green-500"></div>
              <span class="font-medium">Patrimonio Netto / Debiti Totali</span>
            </div>
          </div>
          <div class="bg-white rounded-lg p-4 border border-gray-200">
            <div class="flex items-center gap-3">
              <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
              <span class="font-medium">Oneri Finanziari / Ricavi</span>
            </div>
          </div>
          <div class="bg-white rounded-lg p-4 border border-gray-200">
            <div class="flex items-center gap-3">
              <div class="w-3 h-3 rounded-full bg-green-500"></div>
              <span class="font-medium">Attivo Circolante / Passivo Corrente</span>
            </div>
          </div>
          <div class="bg-white rounded-lg p-4 border border-gray-200">
            <div class="flex items-center gap-3">
              <div class="w-3 h-3 rounded-full bg-green-500"></div>
              <span class="font-medium">Cash Flow / Attivo</span>
            </div>
          </div>
          <div class="bg-white rounded-lg p-4 border border-gray-200">
            <div class="flex items-center gap-3">
              <div class="w-3 h-3 rounded-full bg-red-500"></div>
              <span class="font-medium">Debiti Previdenziali / Retribuzioni</span>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Pricing Section -->
    <section id="pricing" class="py-20 bg-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
          <h2 class="text-3xl font-bold text-gray-900 mb-4">Prezzi semplici e trasparenti</h2>
          <p class="text-gray-600">Scegli il piano più adatto alle tue esigenze</p>
        </div>

        <div class="grid md:grid-cols-3 gap-8 max-w-5xl mx-auto">
          <!-- Starter -->
          <div class="bg-gray-50 rounded-xl p-6 border border-gray-200">
            <h3 class="text-xl font-bold text-gray-900 mb-2">Starter</h3>
            <p class="text-gray-600 mb-4">Per micro-imprese</p>
            <div class="mb-6">
              <span class="text-4xl font-bold">€19</span>
              <span class="text-gray-600">/mese</span>
            </div>
            <ul class="space-y-3 mb-6">
              <li class="flex items-center gap-2 text-gray-600">
                <Icon name="heroicons:check" class="w-5 h-5 text-green-500" />
                1 azienda
              </li>
              <li class="flex items-center gap-2 text-gray-600">
                <Icon name="heroicons:check" class="w-5 h-5 text-green-500" />
                KPI base
              </li>
              <li class="flex items-center gap-2 text-gray-600">
                <Icon name="heroicons:check" class="w-5 h-5 text-green-500" />
                Alert email
              </li>
            </ul>
            <NuxtLink to="/register" class="block w-full text-center bg-gray-900 text-white py-2 rounded-lg font-medium hover:bg-gray-800">
              Inizia ora
            </NuxtLink>
          </div>

          <!-- Professional -->
          <div class="bg-blue-600 rounded-xl p-6 text-white transform scale-105">
            <div class="bg-white/20 text-xs font-bold px-2 py-1 rounded inline-block mb-2">CONSIGLIATO</div>
            <h3 class="text-xl font-bold mb-2">Professional</h3>
            <p class="text-blue-100 mb-4">Per PMI</p>
            <div class="mb-6">
              <span class="text-4xl font-bold">€39</span>
              <span class="text-blue-100">/mese</span>
            </div>
            <ul class="space-y-3 mb-6">
              <li class="flex items-center gap-2 text-blue-100">
                <Icon name="heroicons:check" class="w-5 h-5 text-white" />
                1 azienda
              </li>
              <li class="flex items-center gap-2 text-blue-100">
                <Icon name="heroicons:check" class="w-5 h-5 text-white" />
                Tutti i KPI
              </li>
              <li class="flex items-center gap-2 text-blue-100">
                <Icon name="heroicons:check" class="w-5 h-5 text-white" />
                Trend storici
              </li>
              <li class="flex items-center gap-2 text-blue-100">
                <Icon name="heroicons:check" class="w-5 h-5 text-white" />
                Export PDF
              </li>
            </ul>
            <NuxtLink to="/register" class="block w-full text-center bg-white text-blue-600 py-2 rounded-lg font-medium hover:bg-blue-50">
              Inizia ora
            </NuxtLink>
          </div>

          <!-- Studio -->
          <div class="bg-gray-50 rounded-xl p-6 border border-gray-200">
            <h3 class="text-xl font-bold text-gray-900 mb-2">Studio</h3>
            <p class="text-gray-600 mb-4">Per commercialisti</p>
            <div class="mb-6">
              <span class="text-4xl font-bold">€99</span>
              <span class="text-gray-600">/mese</span>
            </div>
            <ul class="space-y-3 mb-6">
              <li class="flex items-center gap-2 text-gray-600">
                <Icon name="heroicons:check" class="w-5 h-5 text-green-500" />
                Multi-azienda
              </li>
              <li class="flex items-center gap-2 text-gray-600">
                <Icon name="heroicons:check" class="w-5 h-5 text-green-500" />
                €9/azienda extra
              </li>
              <li class="flex items-center gap-2 text-gray-600">
                <Icon name="heroicons:check" class="w-5 h-5 text-green-500" />
                White-label
              </li>
              <li class="flex items-center gap-2 text-gray-600">
                <Icon name="heroicons:check" class="w-5 h-5 text-green-500" />
                Report clienti
              </li>
            </ul>
            <NuxtLink to="/register" class="block w-full text-center bg-gray-900 text-white py-2 rounded-lg font-medium hover:bg-gray-800">
              Contattaci
            </NuxtLink>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-blue-600 text-white">
      <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl font-bold mb-4">Pronto a proteggere la tua azienda?</h2>
        <p class="text-blue-100 mb-8">
          Inizia oggi con 14 giorni di prova gratuita. Nessuna carta di credito richiesta.
        </p>
        <NuxtLink to="/register" class="inline-block bg-white text-blue-600 px-8 py-3 rounded-lg font-bold hover:bg-blue-50 transition">
          Inizia Gratis
        </NuxtLink>
      </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-400 py-12">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row justify-between items-center">
          <div class="flex items-center gap-2 mb-4 md:mb-0">
            <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
              <Icon name="heroicons:chart-bar" class="w-5 h-5 text-white" />
            </div>
            <span class="font-bold text-white">Adeguati Assetti</span>
          </div>
          <div class="text-sm">
            Powered by <a href="https://geniusmile.com" target="_blank" class="text-blue-400 hover:text-blue-300">Geniusmile</a>
          </div>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  layout: 'default'
})
</script>
