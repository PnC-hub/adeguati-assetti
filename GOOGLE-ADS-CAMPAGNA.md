# Google Ads Campaign Structure - Adeguati Assetti Impresa

## Campaign Overview
- **Campaign Name:** Adeguati Assetti - Search
- **Campaign Type:** Search Network
- **Budget:** EUR 500/month (EUR 16.67/day)
- **Bidding Strategy:** Maximize Conversions (initially), then Target CPA
- **Target CPA:** EUR 30-40
- **Location:** Italy
- **Language:** Italian
- **Ad Schedule:** Mon-Fri 8:00-20:00 (business hours)

---

## Ad Group 1: Compliance Obbligatoria

### Keywords (Phrase Match)
- "adeguati assetti obbligatori"
- "art 2086 codice civile"
- "art 2086 compliance"
- "assetti organizzativi obbligatori"
- "monitoraggio assetti aziendali"
- "obbligo monitoraggio kpi"

### Negative Keywords
- gratis
- free
- corso
- formazione
- libro
- pdf download

### Responsive Search Ad 1

**Headlines (max 30 chars each):**
1. Adeguati Assetti Art. 2086
2. Conformita in 5 Minuti/Mese
3. Evita Sanzioni Personali
4. 7 KPI CNDCEC Automatici
5. Prova Gratuita 14 Giorni
6. Tribunali Gia Intervengono
7. Dashboard KPI Automatica
8. Proteggi il Tuo Patrimonio
9. Nessuna Carta Richiesta
10. Oltre 150 Aziende Italiane
11. Alert Email Automatici
12. Report PDF Pronti
13. Conforme CNDCEC 2024
14. Per PMI e Commercialisti
15. Inizia Oggi Stesso

**Descriptions (max 90 chars each):**
1. I Tribunali italiani stanno gia disponendo ispezioni giudiziarie. Verifica la tua azienda oggi.
2. 73% delle PMI non monitora gli indicatori di crisi. Non essere tra queste. Prova gratis 14 giorni.
3. Inserisci i dati in 5 minuti, il sistema calcola tutti i KPI obbligatori. Dashboard chiarissima.
4. Responsabilita personale dellammnistratore ex Art. 2086 c.c. Proteggiti con il monitoraggio KPI.

**Display Path:** adeguatiassetti.it/compliance

---

## Ad Group 2: Software KPI Aziendali

### Keywords (Phrase Match)
- "software kpi aziendali"
- "monitoraggio kpi PMI"
- "dashboard compliance aziendale"
- "software indicatori crisi"
- "monitoraggio indicatori finanziari"
- "software DSCR"

### Responsive Search Ad 2

**Headlines:**
1. Software KPI Aziendale
2. Dashboard Indicatori Crisi
3. DSCR Calcolato in Automatico
4. Current Ratio Monitorato
5. KPI Settoriali ATECO
6. Alert Soglie CNDCEC
7. Export PDF Istantaneo
8. Solo EUR49/Mese Piano Pro
9. Prova 14 Giorni Gratis
10. Setup in 5 Minuti
11. Per Imprenditori Attenti
12. Nessun Excel Necessario
13. Dati al Sicuro in Cloud
14. Storico Illimitato
15. Supporto in Italiano

**Descriptions:**
1. Dashboard automatica per monitorare tutti gli indicatori di crisi. Soglie CNDCEC integrate.
2. Calcola DSCR, Current Ratio, PN/Debiti in automatico. Report pronti per il commercialista.
3. Semaforo verde/giallo/rosso per ogni KPI. Sai subito se la tua azienda e a rischio.
4. Oltre 150 aziende italiane monitorano i loro assetti con noi. Inizia la prova gratuita.

**Display Path:** adeguatiassetti.it/kpi

---

## Ad Group 3: Per Commercialisti

### Keywords (Phrase Match)
- "software commercialisti compliance"
- "strumenti commercialista 2086"
- "gestione clienti commercialista"
- "software multi-azienda"
- "monitoraggio clienti kpi"
- "report compliance clienti"

### Responsive Search Ad 3

**Headlines:**
1. Per Studi Commercialisti
2. Gestisci Fino a 20 Clienti
3. Report con Tuo Logo
4. Dashboard Multi-Azienda
5. Piano Studio EUR149/Mese
6. Alert Automatici Clienti
7. Export PDF Brandizzato
8. Prova Gratuita 14 Giorni
9. Supporto Prioritario
10. Dashboard Comparativa
11. Un Click per Ogni Cliente
12. Risparmia Ore Ogni Mese
13. Conforme Art. 2086 c.c.
14. Setup Rapido Clienti
15. Fatturazione Annuale -17%

**Descriptions:**
1. Gestisci tutti i tuoi clienti da unica dashboard. Report brandizzati con il tuo logo.
2. Alert automatici per ogni cliente in soglia critica. Mai piu sorprese dallAgenzia Entrate.
3. Piano Studio: fino a 20 aziende, supporto prioritario, report personalizzati. Prova gratis.
4. Risparmia ore ogni mese nella gestione degli assetti dei tuoi clienti. Dashboard comparativa inclusa.

**Display Path:** adeguatiassetti.it/commercialisti

---

## Conversion Tracking

### Primary Conversion: Sign Up (Trial)
- **Action:** Form submission on /register
- **Value:** EUR 30 (estimated LTV x conversion rate)

### Secondary Conversions:
- **Begin Checkout:** Click on upgrade button
- **Purchase:** Stripe webhook confirmation

### GTM Events to Configure:
```javascript
// Sign Up Complete
dataLayer.push({
  event: 'sign_up',
  method: 'email'
});

// Begin Checkout
dataLayer.push({
  event: 'begin_checkout',
  currency: 'EUR',
  value: 49,
  items: [{
    item_name: 'Piano Pro Mensile',
    price: 49
  }]
});

// Purchase (server-side via webhook)
dataLayer.push({
  event: 'purchase',
  transaction_id: '{{subscription_id}}',
  currency: 'EUR',
  value: 49
});
```

---

## Landing Page A/B Test Ideas

### Version A (Current)
- Hero: "Evita sanzioni e proteggi il tuo patrimonio personale"
- CTA: "Inizia Prova Gratuita"

### Version B (Fear-based)
- Hero: "Evita lIspezione Giudiziaria Art. 2409 c.c."
- CTA: "Verifica Gratis la Tua Azienda"

### Version C (Benefit-focused)
- Hero: "Monitora i KPI Obbligatori in 5 Minuti al Mese"
- CTA: "Prova Gratis 14 Giorni"

---

## Budget Allocation

| Ad Group | Daily Budget | Monthly | CPC Est. |
|----------|-------------|---------|----------|
| Compliance Obbligatoria | EUR 8 | EUR 240 | EUR 2.50 |
| Software KPI | EUR 5 | EUR 150 | EUR 2.00 |
| Commercialisti | EUR 3.67 | EUR 110 | EUR 3.00 |
| **Total** | **EUR 16.67** | **EUR 500** | - |

---

## Launch Checklist

- [ ] Create Google Ads account (if not exists)
- [ ] Link Google Analytics 4
- [ ] Configure conversion tracking in GTM
- [ ] Upload ad copy
- [ ] Set up ad extensions (sitelinks, callouts)
- [ ] Configure audience targeting (if retargeting)
- [ ] Set bid strategy to Maximize Conversions
- [ ] Review and launch

---

## Ad Extensions

### Sitelink Extensions
1. **Dashboard Demo** - Guarda la demo live - /demo
2. **Prezzi** - Da EUR49/mese, prova gratis - /prezzi
3. **Per Commercialisti** - Piano Studio multi-cliente - /commercialisti
4. **FAQ** - Risposte alle domande comuni - /faq

### Callout Extensions
- Prova Gratuita 14 Giorni
- Nessuna Carta Richiesta
- Conforme CNDCEC
- Supporto in Italiano
- Over 150 Aziende

### Structured Snippets
**Type:** Types
**Values:** KPI DSCR, Current Ratio, PN/Debiti, OF/Ricavi, Cash Flow, Alert Email

---

## Notes

- Campaign to be created via Google Ads web interface (requires browser access)
- Start with broad match modified, then refine to phrase/exact based on search terms report
- Weekly optimization: pause low-performing keywords, add negatives
- Monthly review: adjust bids, test new ad copy, expand keyword list
