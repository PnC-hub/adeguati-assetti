<template>
  <div class="min-h-screen bg-white">

    <!-- Navbar -->
    <nav class="bg-white border-b border-gray-100 sticky top-0 z-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div class="flex justify-between items-center">
          <NuxtLink to="/" class="flex items-center gap-2">
            <div class="w-9 h-9 bg-blue-600 rounded-lg flex items-center justify-center">
              <Icon name="heroicons:shield-check" class="w-5 h-5 text-white" />
            </div>
            <span class="font-bold text-gray-900">Adeguati Assetti Impresa</span>
          </NuxtLink>
          <div class="flex items-center gap-3">
            <NuxtLink to="/login" class="text-gray-600 hover:text-gray-900 text-sm">Accedi</NuxtLink>
            <NuxtLink to="/register" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition">
              Registrati gratuitamente →
            </NuxtLink>
          </div>
        </div>
      </div>
    </nav>

    <!-- Hero -->
    <section class="bg-gradient-to-br from-slate-900 to-blue-900 text-white py-24">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="inline-flex items-center gap-2 bg-yellow-400/20 border border-yellow-400/30 px-4 py-2 rounded-full text-yellow-300 text-sm font-medium mb-8">
          <Icon name="heroicons:building-office-2" class="w-4 h-4" />
          Per Commercialisti e Consulenti
        </div>
        <h1 class="text-4xl md:text-5xl font-bold leading-tight mb-6">
          Dai ai tuoi clienti uno strumento professionale per la compliance Art. 2086
        </h1>
        <p class="text-xl text-blue-200 mb-10 max-w-2xl mx-auto">
          Li aiuti a rispettare la legge. Senza lavoro extra per te.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
          <NuxtLink to="/register" class="bg-yellow-400 text-gray-900 px-8 py-4 rounded-xl font-bold hover:bg-yellow-300 transition text-lg">
            Inizia — è gratuito per te
          </NuxtLink>
          <a href="#come-funziona" class="border-2 border-white/30 text-white px-8 py-4 rounded-xl font-medium hover:bg-white/10 transition">
            Scopri come funziona ↓
          </a>
        </div>
        <p class="text-blue-300 text-sm mt-6">Nessun costo. Nessun impegno. Nessuna carta richiesta.</p>
      </div>
    </section>

    <!-- Problema -->
    <section class="py-20 bg-gray-50">
      <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
          <h2 class="text-3xl font-bold text-gray-900 mb-4">I tuoi clienti sono a rischio. E si rivolgono a te.</h2>
          <p class="text-gray-600 max-w-2xl mx-auto">
            L'Art. 2086 del Codice Civile impone agli amministratori di adottare assetti adeguati. Chi non monitora è esposto a sanzioni, responsabilità personale e — nei casi peggiori — al fallimento.
          </p>
        </div>
        <div class="grid md:grid-cols-3 gap-6">
          <div class="bg-red-50 border border-red-100 rounded-2xl p-6">
            <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center mb-4">
              <Icon name="heroicons:building-library" class="w-6 h-6 text-red-600" />
            </div>
            <h3 class="font-bold text-gray-900 mb-2">I tribunali si sono mossi</h3>
            <p class="text-gray-600 text-sm">Ispezioni giudiziarie in corso in tutta Italia per imprese prive di sistemi di monitoraggio. Non è più teoria.</p>
          </div>
          <div class="bg-orange-50 border border-orange-100 rounded-2xl p-6">
            <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center mb-4">
              <Icon name="heroicons:exclamation-triangle" class="w-6 h-6 text-orange-600" />
            </div>
            <h3 class="font-bold text-gray-900 mb-2">Responsabilità personale</h3>
            <p class="text-gray-600 text-sm">L'amministratore risponde con il proprio patrimonio. I tuoi clienti spesso non lo sanno — fino a quando è troppo tardi.</p>
          </div>
          <div class="bg-blue-50 border border-blue-100 rounded-2xl p-6">
            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center mb-4">
              <Icon name="heroicons:user-group" class="w-6 h-6 text-blue-600" />
            </div>
            <h3 class="font-bold text-gray-900 mb-2">Cercano una guida</h3>
            <p class="text-gray-600 text-sm">Il tuo cliente sa che esiste un obbligo, non sa come adempierlo. Il punto di riferimento sei tu.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Come funziona -->
    <section id="come-funziona" class="py-20 bg-white">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
          <h2 class="text-3xl font-bold text-gray-900 mb-4">Come funziona per te</h2>
          <p class="text-gray-600">In tre passaggi, i tuoi clienti sono monitorati in autonomia.</p>
        </div>
        <div class="space-y-12">
          <div class="flex gap-6 items-start">
            <div class="flex-shrink-0 w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold text-lg">1</div>
            <div>
              <h3 class="text-xl font-bold text-gray-900 mb-2">Registrati gratuitamente</h3>
              <p class="text-gray-600">Crei il tuo account studio in due minuti. Ottieni subito il tuo link di invito personalizzato e il pannello di supervisione.</p>
            </div>
          </div>
          <div class="flex gap-6 items-start">
            <div class="flex-shrink-0 w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold text-lg">2</div>
            <div>
              <h3 class="text-xl font-bold text-gray-900 mb-2">Invita i tuoi clienti</h3>
              <p class="text-gray-600">Condividi il link con i tuoi clienti — via email, WhatsApp o come preferisci. Si registrano in autonomia e si collegano al tuo studio.</p>
            </div>
          </div>
          <div class="flex gap-6 items-start">
            <div class="flex-shrink-0 w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold text-lg">3</div>
            <div>
              <h3 class="text-xl font-bold text-gray-900 mb-2">Supervisionali da un unico pannello</h3>
              <p class="text-gray-600">Vedi lo stato di compliance di tutti i tuoi clienti in un colpo d'occhio. Nessun file da aprire, nessun aggiornamento manuale.</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Benefit -->
    <section class="py-20 bg-slate-50">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
          <h2 class="text-3xl font-bold text-gray-900 mb-4">Cosa ottieni</h2>
        </div>
        <div class="grid md:grid-cols-2 gap-4 mb-10">
          <div class="flex items-start gap-3 bg-white rounded-xl p-5 shadow-sm">
            <Icon name="heroicons:check-circle" class="w-6 h-6 text-green-500 flex-shrink-0 mt-0.5" />
            <div>
              <p class="font-semibold text-gray-900">Accesso completamente gratuito</p>
              <p class="text-gray-500 text-sm">Per te, per sempre. Senza limite di clienti.</p>
            </div>
          </div>
          <div class="flex items-start gap-3 bg-white rounded-xl p-5 shadow-sm">
            <Icon name="heroicons:check-circle" class="w-6 h-6 text-green-500 flex-shrink-0 mt-0.5" />
            <div>
              <p class="font-semibold text-gray-900">Pannello studio centralizzato</p>
              <p class="text-gray-500 text-sm">Tutti i tuoi clienti, lo stato di ognuno, in una sola schermata.</p>
            </div>
          </div>
          <div class="flex items-start gap-3 bg-white rounded-xl p-5 shadow-sm">
            <Icon name="heroicons:check-circle" class="w-6 h-6 text-green-500 flex-shrink-0 mt-0.5" />
            <div>
              <p class="font-semibold text-gray-900">Report mensili automatici</p>
              <p class="text-gray-500 text-sm">Ogni cliente riceve il riepilogo KPI. Tu sei sempre aggiornato senza fare nulla.</p>
            </div>
          </div>
          <div class="flex items-start gap-3 bg-white rounded-xl p-5 shadow-sm">
            <Icon name="heroicons:check-circle" class="w-6 h-6 text-green-500 flex-shrink-0 mt-0.5" />
            <div>
              <p class="font-semibold text-gray-900">Un rapporto professionale più solido</p>
              <p class="text-gray-500 text-sm">Offri ai tuoi clienti uno strumento concreto per un obbligo reale. La tua consulenza vale di più.</p>
            </div>
          </div>
        </div>

        <!-- Benefit economico - discreto -->
        <div class="bg-blue-50 border border-blue-100 rounded-2xl p-6 text-center">
          <Icon name="heroicons:gift" class="w-8 h-8 text-blue-600 mx-auto mb-3" />
          <h3 class="font-bold text-gray-900 mb-2">Un riconoscimento per il tuo lavoro</h3>
          <p class="text-gray-600 text-sm max-w-xl mx-auto">
            Come forma di ringraziamento per il valore che porti ai tuoi clienti — e indirettamente a noi — è previsto un benefit economico ricorrente per ogni cliente attivo che hai invitato sulla piattaforma.
          </p>
        </div>
      </div>
    </section>

    <!-- CTA Finale -->
    <section class="py-24 bg-gradient-to-br from-blue-700 to-blue-900 text-white">
      <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl md:text-4xl font-bold mb-4">I tuoi clienti ti ringrazieranno</h2>
        <p class="text-xl text-blue-200 mb-10">
          Inizia oggi. Sono già oltre 150 le aziende monitorate con Adeguati Assetti.
        </p>
        <NuxtLink to="/register" class="inline-block bg-yellow-400 text-gray-900 px-10 py-4 rounded-xl font-bold text-lg hover:bg-yellow-300 transition">
          Registrati gratuitamente →
        </NuxtLink>
        <p class="text-blue-300 text-sm mt-6">Nessun costo. Nessun impegno.</p>
      </div>
    </section>

    <!-- Footer minimal -->
    <footer class="bg-gray-900 text-gray-400 py-8">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row justify-between items-center gap-4">
        <NuxtLink to="/" class="flex items-center gap-2 text-white">
          <Icon name="heroicons:shield-check" class="w-5 h-5" />
          <span class="font-semibold">Adeguati Assetti Impresa</span>
        </NuxtLink>
        <div class="flex gap-6 text-sm">
          <NuxtLink to="/" class="hover:text-white transition">Per gli Imprenditori</NuxtLink>
          <NuxtLink to="/login" class="hover:text-white transition">Accedi</NuxtLink>
          <NuxtLink to="/register" class="hover:text-white transition">Registrati</NuxtLink>
        </div>
        <p class="text-xs">© 2026 Adeguati Assetti Impresa</p>
      </div>
    </footer>

  </div>
</template>

<script setup>
useHead({
  title: 'Per i Commercialisti — Adeguati Assetti Impresa',
  meta: [
    { name: 'description', content: 'Strumento gratuito per commercialisti e consulenti. Supervisiona la compliance Art. 2086 di tutti i tuoi clienti da un unico pannello.' }
  ]
})
</script>
