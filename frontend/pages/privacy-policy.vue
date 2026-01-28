<template>
  <div class="bg-white rounded-2xl shadow-xl p-8 w-full max-w-4xl mx-auto my-8">
    <div class="text-center mb-8">
      <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center mx-auto mb-4">
        <Icon name="heroicons:shield-check" class="w-7 h-7 text-white" />
      </div>
      <h1 class="text-3xl font-bold text-gray-900">Informativa sulla Privacy</h1>
      <p class="text-gray-500 mt-2">Ai sensi degli Artt. 13 e 14 del Regolamento UE 2016/679 (GDPR)</p>
      <p class="text-gray-400 text-sm mt-1">Ultimo aggiornamento: 28 Gennaio 2026</p>
    </div>

    <div class="prose prose-gray max-w-none text-sm leading-relaxed space-y-6">
      <section>
        <h2 class="text-xl font-semibold text-gray-800 mb-3">1. Titolare del Trattamento</h2>
        <div class="bg-gray-50 p-4 rounded-lg">
          <p class="font-medium">Smiledoc S.r.l.</p>
          <p>Sede legale: Via Gianni Metello 37, 51100 Pistoia (PT)</p>
          <p>P.IVA: IT15131801001</p>
          <p>Email: privacy@smiledoc.it</p>
          <p>PEC: smiledoc@pec.it</p>
        </div>
      </section>

      <section>
        <h2 class="text-xl font-semibold text-gray-800 mb-3">2. Categorie di Dati Personali Trattati</h2>
        <p>Nell'ambito dell'erogazione del Servizio "Adeguati Assetti Impresa", trattiamo le seguenti categorie di dati:</p>

        <h3 class="text-lg font-medium text-gray-700 mt-4 mb-2">2.1 Dati identificativi e di contatto</h3>
        <ul class="list-disc pl-6 space-y-1">
          <li>Nome, cognome, indirizzo email</li>
          <li>Denominazione aziendale, partita IVA, codice fiscale aziendale</li>
          <li>Codice ATECO dell'attivita</li>
        </ul>

        <h3 class="text-lg font-medium text-gray-700 mt-4 mb-2">2.2 Dati economico-finanziari aziendali</h3>
        <ul class="list-disc pl-6 space-y-1">
          <li>Dati di bilancio (patrimonio netto, attivo, passivo)</li>
          <li>Dati di conto economico (ricavi, costi, margini)</li>
          <li>Indicatori finanziari (debiti, crediti, oneri finanziari)</li>
          <li>Dati operativi settoriali (se applicabili al settore dell'Utente)</li>
        </ul>

        <h3 class="text-lg font-medium text-gray-700 mt-4 mb-2">2.3 Dati tecnici e di navigazione</h3>
        <ul class="list-disc pl-6 space-y-1">
          <li>Indirizzo IP, tipo di browser, sistema operativo</li>
          <li>Log di accesso, timestamp delle operazioni</li>
          <li>Cookie tecnici necessari al funzionamento</li>
        </ul>
      </section>

      <section>
        <h2 class="text-xl font-semibold text-gray-800 mb-3">3. Finalita e Base Giuridica del Trattamento</h2>
        <div class="overflow-x-auto">
          <table class="w-full border-collapse border border-gray-200 text-sm">
            <thead>
              <tr class="bg-gray-50">
                <th class="border border-gray-200 px-4 py-2 text-left">Finalita</th>
                <th class="border border-gray-200 px-4 py-2 text-left">Base Giuridica</th>
                <th class="border border-gray-200 px-4 py-2 text-left">Art. GDPR</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td class="border border-gray-200 px-4 py-2">Erogazione del Servizio (registrazione, calcolo KPI, alert, report)</td>
                <td class="border border-gray-200 px-4 py-2">Esecuzione del contratto</td>
                <td class="border border-gray-200 px-4 py-2">Art. 6(1)(b)</td>
              </tr>
              <tr class="bg-gray-50">
                <td class="border border-gray-200 px-4 py-2">Adempimenti fiscali e contabili</td>
                <td class="border border-gray-200 px-4 py-2">Obbligo legale</td>
                <td class="border border-gray-200 px-4 py-2">Art. 6(1)(c)</td>
              </tr>
              <tr>
                <td class="border border-gray-200 px-4 py-2">Sicurezza della Piattaforma e prevenzione frodi</td>
                <td class="border border-gray-200 px-4 py-2">Interesse legittimo</td>
                <td class="border border-gray-200 px-4 py-2">Art. 6(1)(f)</td>
              </tr>
              <tr class="bg-gray-50">
                <td class="border border-gray-200 px-4 py-2">Comunicazioni di servizio (aggiornamenti, alert, manutenzione)</td>
                <td class="border border-gray-200 px-4 py-2">Esecuzione del contratto</td>
                <td class="border border-gray-200 px-4 py-2">Art. 6(1)(b)</td>
              </tr>
              <tr>
                <td class="border border-gray-200 px-4 py-2">Comunicazioni commerciali e marketing</td>
                <td class="border border-gray-200 px-4 py-2">Consenso esplicito</td>
                <td class="border border-gray-200 px-4 py-2">Art. 6(1)(a)</td>
              </tr>
              <tr class="bg-gray-50">
                <td class="border border-gray-200 px-4 py-2">Miglioramento del Servizio e analisi statistiche aggregate</td>
                <td class="border border-gray-200 px-4 py-2">Interesse legittimo</td>
                <td class="border border-gray-200 px-4 py-2">Art. 6(1)(f)</td>
              </tr>
            </tbody>
          </table>
        </div>
      </section>

      <section>
        <h2 class="text-xl font-semibold text-gray-800 mb-3">4. Periodo di Conservazione</h2>
        <ul class="list-disc pl-6 space-y-1">
          <li><strong>Dati dell'Account:</strong> per tutta la durata del rapporto contrattuale e per 10 anni dalla cessazione (obblighi fiscali ex art. 2220 C.C.);</li>
          <li><strong>Dati economico-finanziari:</strong> per tutta la durata del rapporto e per 10 anni dalla cessazione;</li>
          <li><strong>Log di accesso:</strong> 6 mesi dalla raccolta;</li>
          <li><strong>Dati per marketing (se consenso prestato):</strong> fino alla revoca del consenso.</li>
        </ul>
      </section>

      <section>
        <h2 class="text-xl font-semibold text-gray-800 mb-3">5. Destinatari dei Dati</h2>
        <p>I dati personali possono essere comunicati a:</p>
        <ul class="list-disc pl-6 space-y-1">
          <li><strong>Fornitori di servizi IT</strong> (hosting, manutenzione server) operanti come Responsabili del Trattamento ex Art. 28 GDPR, con server situati in UE;</li>
          <li><strong>Consulenti professionali</strong> (commercialista, avvocato) per adempimenti legali e fiscali;</li>
          <li><strong>Autorita competenti</strong> quando richiesto dalla legge.</li>
        </ul>
        <p class="mt-2">I dati <strong>NON</strong> sono venduti a terzi ne utilizzati per profilazione commerciale da parte di terzi.</p>
      </section>

      <section>
        <h2 class="text-xl font-semibold text-gray-800 mb-3">6. Trasferimento Dati Extra-UE</h2>
        <p>I dati personali sono trattati e conservati su server situati nell'Unione Europea. Non e previsto alcun trasferimento di dati personali verso Paesi terzi al di fuori dello Spazio Economico Europeo (SEE).</p>
        <p>Qualora in futuro fosse necessario un trasferimento extra-UE, questo avverra esclusivamente in conformita al Capo V del GDPR (decisioni di adeguatezza, clausole contrattuali standard o altre garanzie appropriate).</p>
      </section>

      <section>
        <h2 class="text-xl font-semibold text-gray-800 mb-3">7. Diritti dell'Interessato</h2>
        <p>Ai sensi degli Artt. 15-22 del GDPR, l'Utente ha diritto di:</p>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-3">
          <div class="bg-blue-50 p-3 rounded-lg">
            <p class="font-medium text-blue-800">Diritto di accesso (Art. 15)</p>
            <p class="text-blue-600 text-xs mt-1">Ottenere conferma del trattamento e copia dei dati</p>
          </div>
          <div class="bg-blue-50 p-3 rounded-lg">
            <p class="font-medium text-blue-800">Diritto di rettifica (Art. 16)</p>
            <p class="text-blue-600 text-xs mt-1">Correggere dati inesatti o incompleti</p>
          </div>
          <div class="bg-blue-50 p-3 rounded-lg">
            <p class="font-medium text-blue-800">Diritto alla cancellazione (Art. 17)</p>
            <p class="text-blue-600 text-xs mt-1">Richiedere la cancellazione dei dati ("diritto all'oblio")</p>
          </div>
          <div class="bg-blue-50 p-3 rounded-lg">
            <p class="font-medium text-blue-800">Diritto di limitazione (Art. 18)</p>
            <p class="text-blue-600 text-xs mt-1">Limitare il trattamento in determinate circostanze</p>
          </div>
          <div class="bg-blue-50 p-3 rounded-lg">
            <p class="font-medium text-blue-800">Diritto alla portabilita (Art. 20)</p>
            <p class="text-blue-600 text-xs mt-1">Ricevere i dati in formato strutturato e leggibile</p>
          </div>
          <div class="bg-blue-50 p-3 rounded-lg">
            <p class="font-medium text-blue-800">Diritto di opposizione (Art. 21)</p>
            <p class="text-blue-600 text-xs mt-1">Opporsi al trattamento per motivi legittimi</p>
          </div>
        </div>
        <p class="mt-3">Per esercitare i propri diritti, l'Utente puo inviare richiesta scritta a <strong>privacy@smiledoc.it</strong> o tramite PEC a <strong>smiledoc@pec.it</strong>. La risposta sara fornita entro 30 giorni dalla ricezione della richiesta.</p>
        <p class="mt-2">L'Utente ha inoltre il diritto di proporre reclamo all'Autorita Garante per la Protezione dei Dati Personali (www.garanteprivacy.it).</p>
      </section>

      <section>
        <h2 class="text-xl font-semibold text-gray-800 mb-3">8. Cookie</h2>
        <p>La Piattaforma utilizza esclusivamente <strong>cookie tecnici</strong> necessari al funzionamento del servizio (autenticazione, sessione, preferenze). Non vengono utilizzati cookie di profilazione o di terze parti per finalita di marketing.</p>
        <p class="mt-2">I cookie tecnici non richiedono il consenso dell'Utente ai sensi dell'Art. 122 del D.Lgs. 196/2003 (Codice Privacy), come modificato dal D.Lgs. 101/2018.</p>
      </section>

      <section>
        <h2 class="text-xl font-semibold text-gray-800 mb-3">9. Misure di Sicurezza</h2>
        <p>Smiledoc S.r.l. adotta misure tecniche e organizzative appropriate ai sensi dell'Art. 32 del GDPR per garantire la sicurezza dei dati, tra cui:</p>
        <ul class="list-disc pl-6 space-y-1">
          <li>Crittografia dei dati in transito (HTTPS/TLS)</li>
          <li>Hashing delle credenziali di accesso</li>
          <li>Accesso ai dati limitato al personale autorizzato</li>
          <li>Backup periodici dei dati</li>
          <li>Monitoraggio degli accessi e delle operazioni</li>
        </ul>
      </section>

      <section>
        <h2 class="text-xl font-semibold text-gray-800 mb-3">10. Modifiche all'Informativa</h2>
        <p>La presente informativa puo essere aggiornata periodicamente. Eventuali modifiche sostanziali saranno comunicate tramite email o avviso sulla Piattaforma. La data dell'ultimo aggiornamento e sempre indicata in cima al documento.</p>
      </section>

      <section>
        <h2 class="text-xl font-semibold text-gray-800 mb-3">11. Contatti</h2>
        <p>Per qualsiasi domanda o richiesta relativa al trattamento dei dati personali:</p>
        <div class="bg-gray-50 p-4 rounded-lg mt-2">
          <p><strong>Smiledoc S.r.l. - Ufficio Privacy</strong></p>
          <p>Via Gianni Metello 37, 51100 Pistoia (PT)</p>
          <p>Email: privacy@smiledoc.it</p>
          <p>PEC: smiledoc@pec.it</p>
        </div>
      </section>
    </div>

    <div class="mt-8 text-center">
      <NuxtLink to="/" class="text-blue-600 hover:text-blue-700 font-medium">
        Torna alla home
      </NuxtLink>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  layout: 'auth'
})

useHead({
  title: 'Privacy Policy - Adeguati Assetti Impresa'
})
</script>
