import { getAuthUser, getUserPlan } from '../utils/auth'
import { findOne, findAll, query, PREFIX } from '../utils/db'
import { calcolaKpi, calcolaScoreSalute, getMessaggioAlert, KpiData } from '../utils/kpi'

export default defineEventHandler(async (event) => {
  const user = await getAuthUser(event)

  if (!user) {
    throw createError({
      statusCode: 401,
      message: 'Non autorizzato'
    })
  }

  const pianoEffettivo = getUserPlan(user)

  // Get user's first active azienda
  const azienda = await findOne<any>('aziende', { user_id: user.id, attiva: true })

  if (!azienda) {
    return {
      success: true,
      data: {
        kpis: [],
        score_salute: 0,
        alerts: [],
        azienda: null,
        piano: pianoEffettivo
      }
    }
  }

  // Get latest economic data
  const now = new Date()
  const anno = now.getFullYear()
  const mese = now.getMonth() + 1

  const datiEconomici = await findOne<any>('dati_economici', {
    azienda_id: azienda.id,
    anno,
    mese
  })

  // If no data, try previous month
  let dati = datiEconomici
  if (!dati) {
    const prevMese = mese === 1 ? 12 : mese - 1
    const prevAnno = mese === 1 ? anno - 1 : anno
    dati = await findOne<any>('dati_economici', {
      azienda_id: azienda.id,
      anno: prevAnno,
      mese: prevMese
    })
  }

  let kpis: any[] = []
  let scoreSalute = 0
  let alerts: any[] = []

  if (dati) {
    const kpiData: KpiData = {
      ricavi: parseFloat(dati.ricavi) || 0,
      patrimonio_netto: parseFloat(dati.patrimonio_netto) || 0,
      attivo_circolante: parseFloat(dati.attivo_circolante) || 0,
      passivo_circolante: parseFloat(dati.passivo_circolante) || 0,
      debiti_totali: parseFloat(dati.debiti_totali) || 0,
      debiti_breve: parseFloat(dati.debiti_breve) || 0,
      debiti_medio_lungo: parseFloat(dati.debiti_medio_lungo) || 0,
      debiti_banche: parseFloat(dati.debiti_banche) || 0,
      debiti_tributari: parseFloat(dati.debiti_tributari) || 0,
      debiti_previdenziali: parseFloat(dati.debiti_previdenziali) || 0,
      oneri_finanziari: parseFloat(dati.oneri_finanziari) || 0,
      cash_flow_operativo: parseFloat(dati.cash_flow_operativo) || 0,
      liquidita_immediate: parseFloat(dati.liquidita_immediate) || 0,
      crediti_commerciali: parseFloat(dati.crediti_commerciali) || 0,
      rimanenze: parseFloat(dati.rimanenze) || 0,
      ebitda: parseFloat(dati.ebitda) || 0
    }

    kpis = calcolaKpi(kpiData, azienda.codice_ateco)
    scoreSalute = calcolaScoreSalute(kpis)

    // Generate alerts for non-green KPIs
    for (const kpi of kpis) {
      if (kpi.stato !== 'verde') {
        const messaggio = getMessaggioAlert(kpi)
        if (messaggio) {
          alerts.push({
            tipo: kpi.stato === 'rosso' ? 'critico' : 'attenzione',
            kpi: kpi.codice,
            messaggio,
            valore: kpi.valore,
            soglia: kpi.soglia
          })
        }
      }
    }
  }

  // Filter KPIs based on plan (free = only 7 obbligatori, pro/studio = settoriali inclusi)
  const kpiObbligatori = ['PN_DEB', 'OF_RIC', 'CF_ATT', 'CR', 'DEBFISC_ATT', 'DSCR', 'QR']
  let filteredKpis = kpis

  if (pianoEffettivo === 'free') {
    filteredKpis = kpis.filter(k => kpiObbligatori.includes(k.codice))
  }

  return {
    success: true,
    data: {
      kpis: filteredKpis,
      score_salute: scoreSalute,
      alerts,
      azienda: {
        id: azienda.id,
        nome: azienda.nome,
        settore: azienda.settore,
        codice_ateco: azienda.codice_ateco
      },
      piano: pianoEffettivo,
      ultimo_aggiornamento: dati ? `${dati.anno}-${String(dati.mese).padStart(2, '0')}` : null
    }
  }
})
