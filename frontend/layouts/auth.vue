<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-600 to-blue-800 flex flex-col">
    <div class="flex-1 flex items-center justify-center p-4">
      <slot />
    </div>

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
