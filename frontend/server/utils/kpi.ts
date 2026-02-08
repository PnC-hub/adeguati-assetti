// Soglie CNDCEC per settore ATECO
export const SOGLIE_SETTORIALI: Record<string, Record<string, number>> = {
  'A': { of_ric: 2.8, pn_deb: 9.4, cf_att: 0.3, cr: 92.1, debfisc_att: 5.6 },
  'B-D': { of_ric: 3.0, pn_deb: 7.6, cf_att: 0.5, cr: 93.7, debfisc_att: 4.9 },
  'E': { of_ric: 2.6, pn_deb: 6.7, cf_att: 1.9, cr: 84.2, debfisc_att: 6.5 },
  'F41': { of_ric: 3.8, pn_deb: 4.9, cf_att: 0.4, cr: 108.0, debfisc_att: 3.8 },
  'F42-F43': { of_ric: 2.8, pn_deb: 5.3, cf_att: 1.4, cr: 101.1, debfisc_att: 5.3 },
  'G45-G46': { of_ric: 2.1, pn_deb: 6.3, cf_att: 0.6, cr: 101.4, debfisc_att: 2.9 },
  'G47-I56': { of_ric: 1.5, pn_deb: 4.2, cf_att: 1.0, cr: 89.8, debfisc_att: 7.8 },
  'H-I55': { of_ric: 1.5, pn_deb: 4.1, cf_att: 1.4, cr: 86.0, debfisc_att: 10.2 },
  'JMN': { of_ric: 1.8, pn_deb: 5.2, cf_att: 1.7, cr: 95.4, debfisc_att: 11.9 },
  'PQRS': { of_ric: 2.7, pn_deb: 2.3, cf_att: 0.5, cr: 69.8, debfisc_att: 14.6 }, // Servizi sanitari
  'DEFAULT': { of_ric: 2.5, pn_deb: 5.0, cf_att: 1.0, cr: 90.0, debfisc_att: 8.0 },
}

// Mappa codice ATECO al gruppo settoriale CNDCEC
export function mapAtecoToSettore(codiceAteco: string | null): string {
  if (!codiceAteco) return 'DEFAULT'

  const prefisso = codiceAteco.substring(0, 2)

  // Mappatura ATECO → settore CNDCEC
  if (['01', '02', '03'].includes(prefisso)) return 'A'
  if (['05', '06', '07', '08', '09', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23', '24', '25', '26', '27', '28', '29', '30', '31', '32', '33', '35'].includes(prefisso)) return 'B-D'
  if (['36', '37', '38', '39'].includes(prefisso)) return 'E'
  if (prefisso === '41') return 'F41'
  if (['42', '43'].includes(prefisso)) return 'F42-F43'
  if (['45', '46'].includes(prefisso)) return 'G45-G46'
  if (prefisso === '47' || prefisso === '56') return 'G47-I56'
  if (['49', '50', '51', '52', '53', '55'].includes(prefisso)) return 'H-I55'
  if (['58', '59', '60', '61', '62', '63', '69', '70', '71', '72', '73', '74', '75', '77', '78', '79', '80', '81', '82'].includes(prefisso)) return 'JMN'
  if (['84', '85', '86', '87', '88', '90', '91', '92', '93', '94', '95', '96'].includes(prefisso)) return 'PQRS'

  return 'DEFAULT'
}

// 7 KPI obbligatori CNDCEC
export interface KpiData {
  ricavi: number
  patrimonio_netto: number
  attivo_circolante: number
  passivo_circolante: number
  debiti_totali: number
  debiti_breve: number
  debiti_medio_lungo: number
  debiti_banche: number
  debiti_tributari: number
  debiti_previdenziali: number
  oneri_finanziari: number
  cash_flow_operativo: number
  liquidita_immediate: number
  crediti_commerciali: number
  rimanenze: number
  ebitda: number
}

export interface KpiResult {
  codice: string
  nome: string
  valore: number
  soglia: number
  stato: 'verde' | 'giallo' | 'rosso'
  descrizione: string
}

export function calcolaKpi(dati: KpiData, codiceAteco: string | null): KpiResult[] {
  const settore = mapAtecoToSettore(codiceAteco)
  const soglie = SOGLIE_SETTORIALI[settore]

  const kpis: KpiResult[] = []

  // 1. Patrimonio Netto / Debiti Totali (PN_DEB)
  const pnDeb = dati.debiti_totali > 0 ? (dati.patrimonio_netto / dati.debiti_totali) * 100 : 0
  kpis.push({
    codice: 'PN_DEB',
    nome: 'Patrimonio Netto / Debiti',
    valore: Math.round(pnDeb * 100) / 100,
    soglia: soglie.pn_deb,
    stato: pnDeb >= soglie.pn_deb ? 'verde' : pnDeb >= soglie.pn_deb * 0.7 ? 'giallo' : 'rosso',
    descrizione: 'Solidità patrimoniale'
  })

  // 2. Oneri Finanziari / Ricavi (OF_RIC)
  const ofRic = dati.ricavi > 0 ? (dati.oneri_finanziari / dati.ricavi) * 100 : 0
  kpis.push({
    codice: 'OF_RIC',
    nome: 'Oneri Finanziari / Ricavi',
    valore: Math.round(ofRic * 100) / 100,
    soglia: soglie.of_ric,
    stato: ofRic <= soglie.of_ric ? 'verde' : ofRic <= soglie.of_ric * 1.3 ? 'giallo' : 'rosso',
    descrizione: 'Sostenibilità del debito finanziario'
  })

  // 3. Cash Flow / Attivo (CF_ATT)
  const cfAtt = dati.attivo_circolante > 0 ? (dati.cash_flow_operativo / dati.attivo_circolante) * 100 : 0
  kpis.push({
    codice: 'CF_ATT',
    nome: 'Cash Flow / Attivo',
    valore: Math.round(cfAtt * 100) / 100,
    soglia: soglie.cf_att,
    stato: cfAtt >= soglie.cf_att ? 'verde' : cfAtt >= soglie.cf_att * 0.7 ? 'giallo' : 'rosso',
    descrizione: 'Capacità di generare cassa'
  })

  // 4. Current Ratio (CR)
  const cr = dati.passivo_circolante > 0 ? (dati.attivo_circolante / dati.passivo_circolante) * 100 : 0
  kpis.push({
    codice: 'CR',
    nome: 'Current Ratio',
    valore: Math.round(cr * 100) / 100,
    soglia: soglie.cr,
    stato: cr >= soglie.cr ? 'verde' : cr >= soglie.cr * 0.8 ? 'giallo' : 'rosso',
    descrizione: 'Liquidità a breve termine'
  })

  // 5. Debiti Fiscali / Attivo (DEBFISC_ATT)
  const debFiscAtt = dati.attivo_circolante > 0 ? ((dati.debiti_tributari + dati.debiti_previdenziali) / dati.attivo_circolante) * 100 : 0
  kpis.push({
    codice: 'DEBFISC_ATT',
    nome: 'Debiti Fiscali / Attivo',
    valore: Math.round(debFiscAtt * 100) / 100,
    soglia: soglie.debfisc_att,
    stato: debFiscAtt <= soglie.debfisc_att ? 'verde' : debFiscAtt <= soglie.debfisc_att * 1.3 ? 'giallo' : 'rosso',
    descrizione: 'Esposizione verso erario'
  })

  // 6. DSCR (Debt Service Coverage Ratio)
  const debitoServizio = dati.debiti_breve + (dati.oneri_finanziari * 12)
  const dscr = debitoServizio > 0 ? dati.cash_flow_operativo / debitoServizio : 0
  kpis.push({
    codice: 'DSCR',
    nome: 'DSCR',
    valore: Math.round(dscr * 100) / 100,
    soglia: 1.0,
    stato: dscr >= 1.0 ? 'verde' : dscr >= 0.8 ? 'giallo' : 'rosso',
    descrizione: 'Capacità di ripagare il debito'
  })

  // 7. Quick Ratio
  const quickRatio = dati.passivo_circolante > 0 ? ((dati.liquidita_immediate + dati.crediti_commerciali) / dati.passivo_circolante) * 100 : 0
  kpis.push({
    codice: 'QR',
    nome: 'Quick Ratio',
    valore: Math.round(quickRatio * 100) / 100,
    soglia: 80,
    stato: quickRatio >= 80 ? 'verde' : quickRatio >= 60 ? 'giallo' : 'rosso',
    descrizione: 'Liquidità senza rimanenze'
  })

  return kpis
}

export function calcolaScoreSalute(kpis: KpiResult[]): number {
  const pesi = {
    'PN_DEB': 15,
    'OF_RIC': 15,
    'CF_ATT': 15,
    'CR': 15,
    'DEBFISC_ATT': 10,
    'DSCR': 20,
    'QR': 10
  }

  let score = 0
  for (const kpi of kpis) {
    const peso = pesi[kpi.codice as keyof typeof pesi] || 10
    if (kpi.stato === 'verde') {
      score += peso
    } else if (kpi.stato === 'giallo') {
      score += peso * 0.5
    }
    // rosso = 0 punti
  }

  return Math.round(score)
}

export function getMessaggioAlert(kpi: KpiResult): string {
  const messaggi: Record<string, Record<string, string>> = {
    'PN_DEB': {
      rosso: 'Patrimonio netto insufficiente rispetto ai debiti. Valutare ricapitalizzazione.',
      giallo: 'Solidità patrimoniale ai limiti. Monitorare rapporto PN/Debiti.'
    },
    'OF_RIC': {
      rosso: 'Oneri finanziari troppo elevati rispetto al fatturato. Rinegoziare condizioni bancarie.',
      giallo: 'Oneri finanziari in aumento. Verificare linee di credito.'
    },
    'CF_ATT': {
      rosso: 'Generazione di cassa insufficiente. Rivedere ciclo monetario.',
      giallo: 'Cash flow sotto la media di settore.'
    },
    'CR': {
      rosso: 'Liquidità critica. Rischio insolvenza a breve termine.',
      giallo: 'Liquidità in tensione. Ottimizzare capitale circolante.'
    },
    'DEBFISC_ATT': {
      rosso: 'Esposizione eccessiva verso erario e previdenza.',
      giallo: 'Debiti fiscali in crescita. Pianificare pagamenti.'
    },
    'DSCR': {
      rosso: 'DSCR critico: impossibile servire il debito con i flussi operativi.',
      giallo: 'DSCR sotto soglia di sicurezza.'
    },
    'QR': {
      rosso: 'Liquidità immediata insufficiente.',
      giallo: 'Quick ratio sotto la media.'
    }
  }

  return messaggi[kpi.codice]?.[kpi.stato] || ''
}
