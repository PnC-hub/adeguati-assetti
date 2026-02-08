/**
 * API endpoint per ottenere il punteggio di Continuità Aziendale (Going Concern)
 *
 * Questo endpoint può essere chiamato da sistemi esterni (es. Imperium Compliance Dashboard)
 * per integrare il punteggio di continuità aziendale nei cruscotti di compliance.
 *
 * Query params:
 * - azienda_id: ID dell'azienda (required)
 * - anno: Anno di riferimento (optional, default: anno corrente)
 * - mese: Mese di riferimento (optional, default: mese corrente)
 */

export default defineEventHandler(async (event) => {
  const query = getQuery(event)
  const aziendaId = query.azienda_id

  if (!aziendaId) {
    throw createError({
      statusCode: 400,
      statusMessage: 'azienda_id is required'
    })
  }

  const anno = query.anno ? Number(query.anno) : new Date().getFullYear()
  const mese = query.mese ? Number(query.mese) : new Date().getMonth() + 1

  // TODO: In produzione, questi dati verranno calcolati dal database
  // basandosi sui dati economici inseriti dall'utente
  // Per ora ritorniamo dati mock realistici

  const continuita = {
    azienda_id: Number(aziendaId),
    anno,
    mese,
    periodo: `${getMeseLabel(mese)} ${anno}`,

    // KPI principali
    dscr: 1.45, // Debt Service Coverage Ratio
    debtSustainability: 2.8, // PFN / EBITDA
    cashRunway: 8, // mesi
    score: 78, // punteggio 0-100

    // Dettaglio calcolo
    ebitda: 180000,
    capitalePiuInteressi: 124000,
    pfn: 504000,
    liquiditaDisponibile: 95000,
    burnRateMensile: 11875,

    // Checklist ISA 570
    checklistScore: 9, // items OK
    checklistTotal: 10, // items totali
    checklistItems: [
      { label: 'Patrimonio netto positivo', ok: true },
      { label: 'Posizione finanziaria netta sostenibile', ok: true },
      { label: 'Cash flow operativo positivo', ok: true },
      { label: 'Capacità di rimborso debiti a scadenza', ok: true },
      { label: 'Assenza di perdite operative significative', ok: true },
      { label: 'Piano industriale aggiornato', ok: false, note: 'Da aggiornare entro 30/06/2026' },
      { label: 'Clientela diversificata', ok: true },
      { label: 'Assenza di contenziosi rilevanti', ok: true },
      { label: 'Conformità normativa', ok: true },
      { label: 'Copertura assicurativa adeguata', ok: true }
    ],

    // Indici CCII (Codice Crisi d'Impresa)
    cciiAlerts: 0, // numero di alert attivi
    cciiIndicators: [
      { code: 'PN_NEGATIVO', value: false, ok: true },
      { code: 'DSCR_6M', value: 1.45, threshold: 1.0, ok: true },
      { code: 'RITARDI_INPS', value: false, ok: true },
      { code: 'RITARDI_INAIL', value: false, ok: true },
      { code: 'RITARDI_ADE', value: false, ok: true }
    ],

    // Cash flow prospettico 6 mesi
    cashFlowProspettico: [
      { mese: 'Feb 2026', entrate: 120000, uscite: 95000, saldo: 25000 },
      { mese: 'Mar 2026', entrate: 115000, uscite: 98000, saldo: 17000 },
      { mese: 'Apr 2026', entrate: 125000, uscite: 92000, saldo: 33000 },
      { mese: 'Mag 2026', entrate: 118000, uscite: 100000, saldo: 18000 },
      { mese: 'Giu 2026', entrate: 130000, uscite: 95000, saldo: 35000 },
      { mese: 'Lug 2026', entrate: 110000, uscite: 90000, saldo: 20000 }
    ],

    // Stato generale
    stato: getStatoContinuita(78),
    statoLabel: getStatoLabel(78),

    // Timestamp
    calcolato_il: new Date().toISOString()
  }

  return {
    success: true,
    data: continuita
  }
})

function getMeseLabel(mese: number): string {
  const mesi = [
    'Gennaio', 'Febbraio', 'Marzo', 'Aprile', 'Maggio', 'Giugno',
    'Luglio', 'Agosto', 'Settembre', 'Ottobre', 'Novembre', 'Dicembre'
  ]
  return mesi[mese - 1] || 'N/D'
}

function getStatoContinuita(score: number): string {
  if (score >= 70) return 'solida'
  if (score >= 50) return 'a_rischio'
  return 'critica'
}

function getStatoLabel(score: number): string {
  if (score >= 70) return 'Continuità Solida'
  if (score >= 50) return 'Continuità a Rischio'
  return 'Continuità Critica'
}
