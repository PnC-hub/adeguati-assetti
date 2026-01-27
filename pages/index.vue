<template>
  <div class="min-h-screen">
    <!-- Urgency Banner -->
    <div class="bg-red-600 text-white py-2 text-center text-sm font-medium">
      Art. 2086 C.C.: Gli amministratori sono PERSONALMENTE responsabili. Verifica subito la tua situazione.
    </div>

    <!-- Hero Section -->
    <div class="bg-gradient-to-br from-blue-600 to-blue-800 text-white">
      <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div class="flex justify-between items-center">
          <div class="flex items-center gap-2">
            <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
              <Icon name="heroicons:shield-check" class="w-6 h-6 text-white" />
            </div>
            <span class="font-bold text-xl">Adeguati Assetti</span>
          </div>
          <div class="flex items-center gap-4">
            <NuxtLink to="/login" class="text-white/80 hover:text-white">Accedi</NuxtLink>
            <NuxtLink to="/register" class="bg-white text-blue-600 px-4 py-2 rounded-lg font-medium hover:bg-blue-50">
              Prova Gratis
            </NuxtLink>
          </div>
        </div>
      </nav>

      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-24">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
          <div>
            <div class="inline-flex items-center gap-2 bg-white/10 px-3 py-1 rounded-full text-sm mb-6">
              <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
              Oltre 150 aziende monitorate
            </div>
            <h1 class="text-4xl md:text-5xl font-bold mb-6 leading-tight">
              Evita sanzioni e proteggi il tuo patrimonio personale
            </h1>
            <p class="text-xl text-blue-100 mb-8">
              Il Codice della Crisi d'Impresa obbliga OGNI azienda a monitorare i propri assetti.
              Con Adeguati Assetti lo fai in <strong>5 minuti al mese</strong>.
            </p>
            <div class="flex flex-col sm:flex-row gap-4">
              <NuxtLink to="/register" class="bg-white text-blue-600 px-8 py-4 rounded-lg font-bold hover:bg-blue-50 transition text-center">
                Inizia Prova Gratuita
              </NuxtLink>
              <button @click="showDemo = true" class="border-2 border-white text-white px-8 py-4 rounded-lg font-bold hover:bg-white/10 transition">
                Guarda Demo Live
              </button>
            </div>
            <p class="text-blue-200 text-sm mt-4">14 giorni gratis. Nessuna carta richiesta.</p>
          </div>

          <!-- Mini Dashboard Preview -->
          <div class="bg-white rounded-2xl shadow-2xl p-6 text-gray-900">
            <div class="flex items-center justify-between mb-6">
              <h3 class="font-bold text-lg">Dashboard KPI - Gennaio 2026</h3>
              <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm font-medium">Situazione OK</span>
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div class="bg-green-50 rounded-lg p-4 border-l-4 border-green-500">
                <div class="text-sm text-gray-600">DSCR</div>
                <div class="text-2xl font-bold text-gray-900">1.45</div>
                <div class="text-xs text-green-600">Sopra soglia</div>
              </div>
              <div class="bg-green-50 rounded-lg p-4 border-l-4 border-green-500">
                <div class="text-sm text-gray-600">Current Ratio</div>
                <div class="text-2xl font-bold text-gray-900">2.67</div>
                <div class="text-xs text-green-600">Eccellente</div>
              </div>
              <div class="bg-yellow-50 rounded-lg p-4 border-l-4 border-yellow-500">
                <div class="text-sm text-gray-600">OF/Ricavi</div>
                <div class="text-2xl font-bold text-gray-900">4.2%</div>
                <div class="text-xs text-yellow-600">Da monitorare</div>
              </div>
              <div class="bg-green-50 rounded-lg p-4 border-l-4 border-green-500">
                <div class="text-sm text-gray-600">PN/Debiti</div>
                <div class="text-2xl font-bold text-gray-900">0.79</div>
                <div class="text-xs text-green-600">Buono</div>
              </div>
            </div>
            <div class="mt-4 pt-4 border-t border-gray-100 flex items-center justify-between">
              <div class="flex items-center gap-2">
                <Icon name="heroicons:bell" class="w-5 h-5 text-yellow-500" />
                <span class="text-sm text-gray-600">1 alert attivo</span>
              </div>
              <span class="text-blue-600 text-sm font-medium">Vedi dettagli &rarr;</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Problem Section -->
    <section class="py-16 bg-gray-900 text-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-3 gap-8">
          <div class="text-center">
            <div class="text-5xl font-bold text-red-400 mb-2">73%</div>
            <p class="text-gray-400">delle PMI italiane non monitora gli indicatori di crisi</p>
          </div>
          <div class="text-center">
            <div class="text-5xl font-bold text-red-400 mb-2">€50k+</div>
            <p class="text-gray-400">sanzione media per mancato adeguamento</p>
          </div>
          <div class="text-center">
            <div class="text-5xl font-bold text-red-400 mb-2">100%</div>
            <p class="text-gray-400">responsabilità personale dell'amministratore</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Social Proof -->
    <section class="py-12 bg-gray-50 border-y border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-wrap justify-center items-center gap-8 text-gray-400">
          <span class="text-sm uppercase tracking-wide">Usato da studi e aziende in tutta Italia</span>
          <div class="flex items-center gap-1">
            <Icon name="heroicons:star-solid" class="w-5 h-5 text-yellow-400" />
            <Icon name="heroicons:star-solid" class="w-5 h-5 text-yellow-400" />
            <Icon name="heroicons:star-solid" class="w-5 h-5 text-yellow-400" />
            <Icon name="heroicons:star-solid" class="w-5 h-5 text-yellow-400" />
            <Icon name="heroicons:star-solid" class="w-5 h-5 text-yellow-400" />
            <span class="ml-2 text-gray-900 font-medium">4.9/5</span>
          </div>
        </div>
      </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-20 bg-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
          <h2 class="text-3xl font-bold text-gray-900 mb-4">Compliance in 3 semplici passi</h2>
          <p class="text-gray-600 max-w-2xl mx-auto">
            Non serve essere esperti di finanza. Inserisci i dati, il sistema calcola tutto automaticamente.
          </p>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
          <div class="relative">
            <div class="absolute -top-4 -left-4 w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center text-white text-xl font-bold">1</div>
            <div class="bg-gray-50 rounded-xl p-8 pt-12">
              <h3 class="text-xl font-bold text-gray-900 mb-3">Inserisci i dati</h3>
              <p class="text-gray-600">
                Bilancio, debiti, ricavi: inserisci i dati una volta al mese. Bastano 5 minuti.
              </p>
            </div>
          </div>

          <div class="relative">
            <div class="absolute -top-4 -left-4 w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center text-white text-xl font-bold">2</div>
            <div class="bg-gray-50 rounded-xl p-8 pt-12">
              <h3 class="text-xl font-bold text-gray-900 mb-3">KPI calcolati</h3>
              <p class="text-gray-600">
                Il sistema calcola automaticamente tutti gli indicatori CNDCEC con semaforo verde/giallo/rosso.
              </p>
            </div>
          </div>

          <div class="relative">
            <div class="absolute -top-4 -left-4 w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center text-white text-xl font-bold">3</div>
            <div class="bg-gray-50 rounded-xl p-8 pt-12">
              <h3 class="text-xl font-bold text-gray-900 mb-3">Sei protetto</h3>
              <p class="text-gray-600">
                Report PDF pronti per commercialista e collegio sindacale. Documentazione perfetta.
              </p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Testimonials -->
    <section class="py-20 bg-gray-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
          <h2 class="text-3xl font-bold text-gray-900 mb-4">Cosa dicono i nostri clienti</h2>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
          <div class="bg-white rounded-xl p-6 shadow-sm">
            <div class="flex items-center gap-1 mb-4">
              <Icon name="heroicons:star-solid" class="w-5 h-5 text-yellow-400" />
              <Icon name="heroicons:star-solid" class="w-5 h-5 text-yellow-400" />
              <Icon name="heroicons:star-solid" class="w-5 h-5 text-yellow-400" />
              <Icon name="heroicons:star-solid" class="w-5 h-5 text-yellow-400" />
              <Icon name="heroicons:star-solid" class="w-5 h-5 text-yellow-400" />
            </div>
            <p class="text-gray-600 mb-4">
              "Finalmente capisco se la mia azienda e a rischio senza dover aspettare il commercialista. Dashboard chiarissima."
            </p>
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                <span class="text-blue-600 font-bold">MR</span>
              </div>
              <div>
                <div class="font-medium text-gray-900">Marco R.</div>
                <div class="text-sm text-gray-500">Titolare, Edilizia</div>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-xl p-6 shadow-sm">
            <div class="flex items-center gap-1 mb-4">
              <Icon name="heroicons:star-solid" class="w-5 h-5 text-yellow-400" />
              <Icon name="heroicons:star-solid" class="w-5 h-5 text-yellow-400" />
              <Icon name="heroicons:star-solid" class="w-5 h-5 text-yellow-400" />
              <Icon name="heroicons:star-solid" class="w-5 h-5 text-yellow-400" />
              <Icon name="heroicons:star-solid" class="w-5 h-5 text-yellow-400" />
            </div>
            <p class="text-gray-600 mb-4">
              "Gestisco 35 clienti con il piano Studio. Report automatici, risparmio ore ogni mese. Consigliatissimo."
            </p>
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                <span class="text-green-600 font-bold">LB</span>
              </div>
              <div>
                <div class="font-medium text-gray-900">Laura B.</div>
                <div class="text-sm text-gray-500">Commercialista, Roma</div>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-xl p-6 shadow-sm">
            <div class="flex items-center gap-1 mb-4">
              <Icon name="heroicons:star-solid" class="w-5 h-5 text-yellow-400" />
              <Icon name="heroicons:star-solid" class="w-5 h-5 text-yellow-400" />
              <Icon name="heroicons:star-solid" class="w-5 h-5 text-yellow-400" />
              <Icon name="heroicons:star-solid" class="w-5 h-5 text-yellow-400" />
              <Icon name="heroicons:star-solid" class="w-5 h-5 text-yellow-400" />
            </div>
            <p class="text-gray-600 mb-4">
              "Gli alert mi hanno avvisato di un problema di liquidita in anticipo. Ho potuto intervenire prima che fosse tardi."
            </p>
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                <span class="text-purple-600 font-bold">GP</span>
              </div>
              <div>
                <div class="font-medium text-gray-900">Giuseppe P.</div>
                <div class="text-sm text-gray-500">CFO, Manifattura</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- KPI Section -->
    <section class="py-20 bg-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
          <h2 class="text-3xl font-bold text-gray-900 mb-4">Indicatori obbligatori monitorati</h2>
          <p class="text-gray-600">Conformi alle linee guida CNDCEC per la prevenzione della crisi</p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
          <div class="bg-gray-50 rounded-lg p-5 border border-gray-200 hover:border-blue-300 transition">
            <div class="flex items-center justify-between">
              <span class="font-medium text-gray-900">DSCR</span>
              <span class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded">Obbligatorio</span>
            </div>
            <p class="text-sm text-gray-500 mt-1">Debt Service Coverage Ratio</p>
          </div>
          <div class="bg-gray-50 rounded-lg p-5 border border-gray-200 hover:border-blue-300 transition">
            <div class="flex items-center justify-between">
              <span class="font-medium text-gray-900">Patrimonio Netto / Debiti</span>
              <span class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded">Obbligatorio</span>
            </div>
            <p class="text-sm text-gray-500 mt-1">Autonomia finanziaria</p>
          </div>
          <div class="bg-gray-50 rounded-lg p-5 border border-gray-200 hover:border-blue-300 transition">
            <div class="flex items-center justify-between">
              <span class="font-medium text-gray-900">Oneri Finanziari / Ricavi</span>
              <span class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded">Obbligatorio</span>
            </div>
            <p class="text-sm text-gray-500 mt-1">Sostenibilita del debito</p>
          </div>
          <div class="bg-gray-50 rounded-lg p-5 border border-gray-200 hover:border-blue-300 transition">
            <div class="flex items-center justify-between">
              <span class="font-medium text-gray-900">Current Ratio</span>
              <span class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded">Obbligatorio</span>
            </div>
            <p class="text-sm text-gray-500 mt-1">Liquidita corrente</p>
          </div>
          <div class="bg-gray-50 rounded-lg p-5 border border-gray-200 hover:border-blue-300 transition">
            <div class="flex items-center justify-between">
              <span class="font-medium text-gray-900">Cash Flow / Attivo</span>
              <span class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded">Obbligatorio</span>
            </div>
            <p class="text-sm text-gray-500 mt-1">Generazione di cassa</p>
          </div>
          <div class="bg-gray-50 rounded-lg p-5 border border-gray-200 hover:border-blue-300 transition">
            <div class="flex items-center justify-between">
              <span class="font-medium text-gray-900">Debiti Tributari / Attivo</span>
              <span class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded">Obbligatorio</span>
            </div>
            <p class="text-sm text-gray-500 mt-1">Esposizione fiscale</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Pricing Section -->
    <section id="pricing" class="py-20 bg-gray-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
          <h2 class="text-3xl font-bold text-gray-900 mb-4">Investi meno di un caffe al giorno</h2>
          <p class="text-gray-600">Proteggi la tua azienda e il tuo patrimonio personale</p>
        </div>

        <div class="grid md:grid-cols-3 gap-8 max-w-5xl mx-auto">
          <!-- Starter -->
          <div class="bg-white rounded-2xl p-8 border border-gray-200 hover:border-blue-300 transition">
            <h3 class="text-xl font-bold text-gray-900 mb-1">Starter</h3>
            <p class="text-gray-500 mb-6">Per micro-imprese e ditte individuali</p>
            <div class="mb-6">
              <span class="text-5xl font-bold">€19</span>
              <span class="text-gray-500">/mese</span>
            </div>
            <ul class="space-y-4 mb-8">
              <li class="flex items-start gap-3">
                <Icon name="heroicons:check-circle-solid" class="w-6 h-6 text-green-500 flex-shrink-0" />
                <span class="text-gray-600">1 azienda monitorata</span>
              </li>
              <li class="flex items-start gap-3">
                <Icon name="heroicons:check-circle-solid" class="w-6 h-6 text-green-500 flex-shrink-0" />
                <span class="text-gray-600">6 KPI obbligatori</span>
              </li>
              <li class="flex items-start gap-3">
                <Icon name="heroicons:check-circle-solid" class="w-6 h-6 text-green-500 flex-shrink-0" />
                <span class="text-gray-600">Alert email automatici</span>
              </li>
              <li class="flex items-start gap-3">
                <Icon name="heroicons:check-circle-solid" class="w-6 h-6 text-green-500 flex-shrink-0" />
                <span class="text-gray-600">Supporto email</span>
              </li>
            </ul>
            <NuxtLink to="/register" class="block w-full text-center bg-gray-100 text-gray-900 py-3 rounded-lg font-semibold hover:bg-gray-200 transition">
              Inizia Prova Gratuita
            </NuxtLink>
          </div>

          <!-- Professional -->
          <div class="bg-blue-600 rounded-2xl p-8 text-white transform md:scale-105 shadow-xl relative">
            <div class="absolute -top-3 left-1/2 -translate-x-1/2 bg-yellow-400 text-yellow-900 text-xs font-bold px-4 py-1 rounded-full">
              PIU SCELTO
            </div>
            <h3 class="text-xl font-bold mb-1">Professional</h3>
            <p class="text-blue-200 mb-6">Per PMI e societa strutturate</p>
            <div class="mb-6">
              <span class="text-5xl font-bold">€39</span>
              <span class="text-blue-200">/mese</span>
            </div>
            <ul class="space-y-4 mb-8">
              <li class="flex items-start gap-3">
                <Icon name="heroicons:check-circle-solid" class="w-6 h-6 text-white flex-shrink-0" />
                <span>1 azienda monitorata</span>
              </li>
              <li class="flex items-start gap-3">
                <Icon name="heroicons:check-circle-solid" class="w-6 h-6 text-white flex-shrink-0" />
                <span><strong>22 KPI</strong> (base + settoriali)</span>
              </li>
              <li class="flex items-start gap-3">
                <Icon name="heroicons:check-circle-solid" class="w-6 h-6 text-white flex-shrink-0" />
                <span>Trend storici e grafici</span>
              </li>
              <li class="flex items-start gap-3">
                <Icon name="heroicons:check-circle-solid" class="w-6 h-6 text-white flex-shrink-0" />
                <span><strong>Export PDF</strong> professionale</span>
              </li>
              <li class="flex items-start gap-3">
                <Icon name="heroicons:check-circle-solid" class="w-6 h-6 text-white flex-shrink-0" />
                <span>Supporto prioritario</span>
              </li>
            </ul>
            <NuxtLink to="/register" class="block w-full text-center bg-white text-blue-600 py-3 rounded-lg font-semibold hover:bg-blue-50 transition">
              Inizia Prova Gratuita
            </NuxtLink>
          </div>

          <!-- Studio -->
          <div class="bg-white rounded-2xl p-8 border border-gray-200 hover:border-blue-300 transition">
            <h3 class="text-xl font-bold text-gray-900 mb-1">Studio</h3>
            <p class="text-gray-500 mb-6">Per commercialisti e consulenti</p>
            <div class="mb-6">
              <span class="text-5xl font-bold">€99</span>
              <span class="text-gray-500">/mese</span>
            </div>
            <ul class="space-y-4 mb-8">
              <li class="flex items-start gap-3">
                <Icon name="heroicons:check-circle-solid" class="w-6 h-6 text-green-500 flex-shrink-0" />
                <span class="text-gray-600"><strong>5 aziende</strong> incluse</span>
              </li>
              <li class="flex items-start gap-3">
                <Icon name="heroicons:check-circle-solid" class="w-6 h-6 text-green-500 flex-shrink-0" />
                <span class="text-gray-600">+€9/mese per azienda extra</span>
              </li>
              <li class="flex items-start gap-3">
                <Icon name="heroicons:check-circle-solid" class="w-6 h-6 text-green-500 flex-shrink-0" />
                <span class="text-gray-600">Dashboard multi-cliente</span>
              </li>
              <li class="flex items-start gap-3">
                <Icon name="heroicons:check-circle-solid" class="w-6 h-6 text-green-500 flex-shrink-0" />
                <span class="text-gray-600">Report white-label</span>
              </li>
              <li class="flex items-start gap-3">
                <Icon name="heroicons:check-circle-solid" class="w-6 h-6 text-green-500 flex-shrink-0" />
                <span class="text-gray-600">Supporto dedicato</span>
              </li>
            </ul>
            <NuxtLink to="/register" class="block w-full text-center bg-gray-100 text-gray-900 py-3 rounded-lg font-semibold hover:bg-gray-200 transition">
              Inizia Prova Gratuita
            </NuxtLink>
          </div>
        </div>

        <p class="text-center text-gray-500 mt-8">
          Tutti i piani includono 14 giorni di prova gratuita. Disdici quando vuoi.
        </p>
      </div>
    </section>

    <!-- FAQ Section -->
    <section class="py-20 bg-white">
      <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
          <h2 class="text-3xl font-bold text-gray-900 mb-4">Domande frequenti</h2>
        </div>

        <div class="space-y-6">
          <div class="border border-gray-200 rounded-lg p-6">
            <h3 class="font-bold text-gray-900 mb-2">Sono obbligato a monitorare gli assetti?</h3>
            <p class="text-gray-600">
              Si. L'Art. 2086 del Codice Civile impone a TUTTE le imprese (SRL, SPA, anche ditte individuali sopra certe soglie) di istituire assetti adeguati. L'amministratore e personalmente responsabile.
            </p>
          </div>
          <div class="border border-gray-200 rounded-lg p-6">
            <h3 class="font-bold text-gray-900 mb-2">Cosa rischio se non mi adeguo?</h3>
            <p class="text-gray-600">
              Responsabilita personale dell'amministratore per i debiti sociali, sanzioni, e in caso di crisi l'impossibilita di accedere agli strumenti di composizione negoziata.
            </p>
          </div>
          <div class="border border-gray-200 rounded-lg p-6">
            <h3 class="font-bold text-gray-900 mb-2">Quanto tempo richiede?</h3>
            <p class="text-gray-600">
              5 minuti al mese per inserire i dati. Il sistema calcola automaticamente tutti i KPI e genera i report.
            </p>
          </div>
          <div class="border border-gray-200 rounded-lg p-6">
            <h3 class="font-bold text-gray-900 mb-2">Posso provare prima di pagare?</h3>
            <p class="text-gray-600">
              Certo! Offriamo 14 giorni di prova gratuita senza carta di credito. Puoi anche accedere alla demo live per vedere il prodotto in azione.
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-blue-600 text-white">
      <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl md:text-4xl font-bold mb-4">Non aspettare che sia troppo tardi</h2>
        <p class="text-xl text-blue-100 mb-8">
          Ogni mese senza monitoraggio e un rischio per te e la tua azienda. Inizia oggi stesso.
        </p>
        <div class="flex flex-col sm:flex-row justify-center gap-4">
          <NuxtLink to="/register" class="bg-white text-blue-600 px-8 py-4 rounded-lg font-bold hover:bg-blue-50 transition">
            Inizia Prova Gratuita
          </NuxtLink>
          <button @click="showDemo = true" class="border-2 border-white text-white px-8 py-4 rounded-lg font-bold hover:bg-white/10 transition">
            Accedi alla Demo
          </button>
        </div>
        <p class="text-blue-200 text-sm mt-6">
          Unisciti a oltre 150 aziende che hanno gia scelto Adeguati Assetti
        </p>
      </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-400 py-12">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-4 gap-8 mb-8">
          <div>
            <div class="flex items-center gap-2 mb-4">
              <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                <Icon name="heroicons:shield-check" class="w-5 h-5 text-white" />
              </div>
              <span class="font-bold text-white">Adeguati Assetti</span>
            </div>
            <p class="text-sm">
              Monitoraggio degli assetti organizzativi, amministrativi e contabili per PMI italiane.
            </p>
          </div>
          <div>
            <h4 class="font-semibold text-white mb-4">Prodotto</h4>
            <ul class="space-y-2 text-sm">
              <li><a href="#features" class="hover:text-white">Funzionalita</a></li>
              <li><a href="#pricing" class="hover:text-white">Prezzi</a></li>
              <li><button @click="showDemo = true" class="hover:text-white">Demo</button></li>
            </ul>
          </div>
          <div>
            <h4 class="font-semibold text-white mb-4">Risorse</h4>
            <ul class="space-y-2 text-sm">
              <li><a href="#" class="hover:text-white">Art. 2086 C.C.</a></li>
              <li><a href="#" class="hover:text-white">Linee guida CNDCEC</a></li>
              <li><a href="#" class="hover:text-white">Blog</a></li>
            </ul>
          </div>
          <div>
            <h4 class="font-semibold text-white mb-4">Contatti</h4>
            <ul class="space-y-2 text-sm">
              <li>info@adeguatiassetti.it</li>
              <li>+39 06 1234567</li>
            </ul>
          </div>
        </div>
        <div class="border-t border-gray-800 pt-8 flex flex-col md:flex-row justify-between items-center text-sm">
          <p>&copy; 2026 Adeguati Assetti. Tutti i diritti riservati.</p>
          <div class="flex gap-6 mt-4 md:mt-0">
            <a href="#" class="hover:text-white">Privacy Policy</a>
            <a href="#" class="hover:text-white">Termini di Servizio</a>
          </div>
        </div>
      </div>
    </footer>

    <!-- Demo Modal -->
    <Teleport to="body">
      <div v-if="showDemo" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4" @click.self="showDemo = false">
        <div class="bg-white rounded-2xl max-w-md w-full p-8">
          <div class="text-center mb-6">
            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
              <Icon name="heroicons:play" class="w-8 h-8 text-blue-600" />
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-2">Accedi alla Demo</h3>
            <p class="text-gray-600">Scegli uno scenario da esplorare</p>
          </div>

          <div class="space-y-3">
            <button
              @click="accessDemo('sana')"
              :disabled="demoLoading !== null"
              class="block w-full p-4 border border-gray-200 rounded-lg hover:border-green-500 hover:bg-green-50 transition text-left disabled:opacity-50"
            >
              <div class="flex items-center gap-3">
                <div v-if="demoLoading === 'sana'" class="w-3 h-3 rounded-full border-2 border-green-500 border-t-transparent animate-spin"></div>
                <div v-else class="w-3 h-3 rounded-full bg-green-500"></div>
                <div>
                  <div class="font-semibold text-gray-900">Azienda Sana</div>
                  <div class="text-sm text-gray-500">KPI tutti verdi - situazione ottimale</div>
                </div>
              </div>
            </button>

            <button
              @click="accessDemo('critica')"
              :disabled="demoLoading !== null"
              class="block w-full p-4 border border-gray-200 rounded-lg hover:border-red-500 hover:bg-red-50 transition text-left disabled:opacity-50"
            >
              <div class="flex items-center gap-3">
                <div v-if="demoLoading === 'critica'" class="w-3 h-3 rounded-full border-2 border-red-500 border-t-transparent animate-spin"></div>
                <div v-else class="w-3 h-3 rounded-full bg-red-500"></div>
                <div>
                  <div class="font-semibold text-gray-900">Azienda a Rischio</div>
                  <div class="text-sm text-gray-500">Alert critici - intervento necessario</div>
                </div>
              </div>
            </button>

            <button
              @click="accessDemo('studio')"
              :disabled="demoLoading !== null"
              class="block w-full p-4 border border-gray-200 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition text-left disabled:opacity-50"
            >
              <div class="flex items-center gap-3">
                <div v-if="demoLoading === 'studio'" class="w-3 h-3 rounded-full border-2 border-blue-500 border-t-transparent animate-spin"></div>
                <div v-else class="w-3 h-3 rounded-full bg-blue-500"></div>
                <div>
                  <div class="font-semibold text-gray-900">Studio Commercialista</div>
                  <div class="text-sm text-gray-500">Multi-azienda - gestione clienti</div>
                </div>
              </div>
            </button>
          </div>

          <div class="mt-6 pt-6 border-t border-gray-100">
            <p class="text-xs text-gray-500 text-center mb-4">
              Credenziali demo: demo.[tipo]@adeguatiassetti.it / Demo2025!
            </p>
            <button @click="showDemo = false" class="w-full text-gray-500 hover:text-gray-700">
              Chiudi
            </button>
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  layout: 'default'
})

const showDemo = ref(false)
const demoLoading = ref<string | null>(null)
const router = useRouter()
const config = useRuntimeConfig()

const demoCredentials: Record<string, { email: string; password: string }> = {
  sana: { email: 'demo.sana@adeguatiassetti.it', password: 'Demo2025!' },
  critica: { email: 'demo.critica@adeguatiassetti.it', password: 'Demo2025!' },
  studio: { email: 'demo.studio@adeguatiassetti.it', password: 'Demo2025!' }
}

const accessDemo = async (tipo: string) => {
  demoLoading.value = tipo
  try {
    const creds = demoCredentials[tipo]
    const response = await $fetch<{ success: boolean; data: { token: string; user: any }; message?: string }>(
      `${config.public.apiBase}/adeguati-assetti/auth/login`,
      {
        method: 'POST',
        headers: {
          'X-API-Key': config.public.apiKey,
          'Content-Type': 'application/x-www-form-urlencoded',
          'Accept': 'application/json'
        },
        body: new URLSearchParams({
          email: creds.email,
          password: creds.password
        }).toString()
      }
    )

    if (response.success) {
      localStorage.setItem('aa_token', response.data.token)
      localStorage.setItem('aa_user', JSON.stringify(response.data.user))
      showDemo.value = false
      router.push('/dashboard')
    }
  } catch (e) {
    // Fallback to login page if API fails
    router.push(`/login?demo=${tipo}`)
  } finally {
    demoLoading.value = null
  }
}
</script>
