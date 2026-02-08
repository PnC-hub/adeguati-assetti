# PRD: Monetizzazione Adeguati Assetti Impresa

**Obiettivo:** Portare Adeguati Assetti Impresa da 0 a revenue ricorrente
**Target:** Primi €1000 MRR entro 60 giorni
**Owner:** Piero Civero
**Data:** 03/02/2026

---

## Stato Attuale

### Completato ✅
- Stripe Product creato (prod_TuOHu3WrI6qvJh)
- 4 Prices configurati (Pro/Studio x Monthly/Annual)
- Backend API funzionante con Stripe Checkout
- Registrazione + Trial 14gg + Upgrade flow
- PM2 + ProxyPass architecture

### Da Completare

---

## FASE 1: Stripe Production Ready (Priorità CRITICA)

### 1.1 Webhook Stripe
**URL:** https://adeguatiassettiimpresa.it/api/webhook/stripe

Eventi da gestire:
- `checkout.session.completed` → Attiva abbonamento
- `customer.subscription.deleted` → Downgrade a Free
- `invoice.payment_failed` → Notifica + retry logic

**Task:**
- [ ] Creare webhook endpoint in Stripe Dashboard (Test mode)
- [ ] Ottenere webhook secret e aggiungerlo a .env
- [ ] Testare con Stripe CLI: `stripe listen --forward-to https://adeguatiassettiimpresa.it/api/webhook/stripe`
- [ ] Verificare che checkout.session.completed aggiorni il piano utente

### 1.2 Stripe LIVE Mode
**Requisiti:**
- Verifica identità completata
- Dati aziendali inseriti
- Conto bancario collegato

**Task:**
- [ ] Completare verifica identità su Stripe Dashboard
- [ ] Creare Product e Prices in LIVE mode (stessi valori di test)
- [ ] Aggiornare .env con chiavi LIVE
- [ ] Testare un pagamento reale di €1

---

## FASE 2: Landing Page Ottimizzata (Priorità ALTA)

### 2.1 Analisi Conversione Attuale
La landing attuale ha:
- Hero con value proposition
- Demo KPI
- Pricing 3 piani
- FAQ

### 2.2 Miglioramenti Richiesti

**Above the fold:**
- [ ] Headline più aggressivo: "Evita l'Ispezione Giudiziaria Art. 2409 c.c."
- [ ] Sottotitolo con pain point: "Il 78% delle PMI italiane non è conforme"
- [ ] CTA primario: "Verifica Gratis la Tua Azienda" (trial 14gg)
- [ ] Trust badges: "Conforme CNDCEC", "7 KPI Obbligatori"

**Social Proof:**
- [ ] Sezione giurisprudenza con sentenze reali (già presente, enfatizzare)
- [ ] Counter: "127 aziende monitorate" (anche se mock iniziale)
- [ ] Logo commercialisti partner (quando disponibili)

**Urgenza:**
- [ ] Banner: "Dal 2024 i controlli sono aumentati del 340%"
- [ ] Riferimento a sanzioni concrete

**Form Capture:**
- [ ] Exit intent popup con lead magnet: "Checklist Gratuita Art. 2086"
- [ ] Form newsletter in footer

### 2.3 File da Modificare
- `frontend/pages/index.vue` — Landing page principale
- `frontend/components/` — Eventuali nuovi componenti

---

## FASE 3: Google Ads Setup (Priorità ALTA)

### 3.1 Keyword Research
**Keyword transazionali (high intent):**
- "adeguati assetti obbligatori" — ~720 ricerche/mese
- "art 2086 codice civile" — ~590 ricerche/mese
- "monitoraggio kpi aziendali" — ~320 ricerche/mese
- "compliance aziendale software" — ~210 ricerche/mese
- "controllo gestione obbligatorio" — ~170 ricerche/mese

**Keyword informazionali (per content):**
- "adeguati assetti cosa sono"
- "art 2086 sanzioni"
- "indicatori crisi d'impresa"

### 3.2 Campagna Google Ads
**Budget iniziale:** €500/mese
**CPC stimato:** €1.50-3.00
**Conversion target:** 5% landing → trial

**Struttura campagna:**
```
Campagna: Adeguati Assetti - Search
├── Ad Group: Compliance Obbligatoria
│   ├── KW: adeguati assetti obbligatori
│   ├── KW: art 2086 compliance
│   └── KW: monitoraggio assetti organizzativi
├── Ad Group: Software KPI
│   ├── KW: software kpi aziendali
│   ├── KW: monitoraggio kpi PMI
│   └── KW: dashboard compliance aziendale
└── Ad Group: Commercialisti
    ├── KW: software commercialisti compliance
    ├── KW: strumenti commercialista 2086
    └── KW: gestione clienti commercialista
```

**Task:**
- [ ] Creare account Google Ads (se non esiste)
- [ ] Setup campagna Search
- [ ] Creare 3 Ad Groups con keyword
- [ ] Scrivere 3 annunci per Ad Group
- [ ] Configurare conversion tracking
- [ ] Collegare Google Analytics 4

### 3.3 Tracking
- [ ] Google Tag Manager setup
- [ ] GA4 eventi: page_view, sign_up, begin_checkout, purchase
- [ ] Meta Pixel (opzionale per remarketing)

---

## FASE 4: Automazioni Email (Priorità MEDIA)

### 4.1 Welcome Sequence (Trial)
**Giorno 0:** Welcome email + come iniziare
**Giorno 3:** "Hai inserito i tuoi dati economici?"
**Giorno 7:** Case study / testimonianza
**Giorno 10:** "Il tuo trial scade tra 4 giorni"
**Giorno 13:** "Ultimo giorno per mantenere le funzionalità Pro"

### 4.2 Setup Tecnico
- [ ] Configurare SMTP in Laravel (Aruba o SendGrid)
- [ ] Creare Mailable templates
- [ ] Implementare job schedulati per email sequence
- [ ] Tabella `email_logs` per tracking

---

## FASE 5: Content Marketing (Priorità MEDIA)

### 5.1 Blog SEO
Target: posizionarsi per keyword informazionali

**Articoli prioritari:**
1. "Adeguati Assetti: Guida Completa per PMI 2026"
2. "Art. 2086 c.c.: Cosa Rischia l'Amministratore"
3. "I 7 KPI Obbligatori secondo il CNDCEC"
4. "Come Prepararsi a un'Ispezione Giudiziaria"
5. "Checklist Compliance Art. 2086"

### 5.2 Lead Magnet
- PDF "Checklist Completa Art. 2086" (in cambio di email)
- Webinar "Come Evitare l'Ispezione Giudiziaria" (per nurturing)

---

## Metriche di Successo

| Metrica | Target 30gg | Target 60gg |
|---------|-------------|-------------|
| Visite landing | 1.000 | 3.000 |
| Trial registrati | 50 | 150 |
| Conversione trial→paid | 10% | 15% |
| MRR | €250 | €1.000 |
| CAC | <€50 | <€40 |

---

## Timeline

| Settimana | Focus | Deliverable |
|-----------|-------|-------------|
| W1 (3-9 Feb) | Stripe LIVE + Webhook | Pagamenti reali attivi |
| W1 (3-9 Feb) | Landing optimization | Nuova landing deployata |
| W2 (10-16 Feb) | Google Ads | Campagna attiva |
| W2 (10-16 Feb) | Tracking | GA4 + conversioni |
| W3 (17-23 Feb) | Email sequence | Automazioni attive |
| W4 (24-28 Feb) | Content | 2 articoli blog |

---

## Note Tecniche

### File Backend Chiave
- `app/Http/Controllers/Api/AdeguatiAssettiStandaloneController.php`
- `config/stripe.php`
- `routes/api.php`

### File Frontend Chiave
- `frontend/pages/index.vue` — Landing
- `frontend/pages/dashboard/account.vue` — Upgrade UI
- `frontend/pages/register.vue` — Registrazione

### Server
- PM2: adeguati-assetti-api (porta 3020)
- Apache: ProxyPass /api → 127.0.0.1:3020

### Credenziali
- Stripe Test: sk_test_51SuO5o...
- Webhook URL: https://adeguatiassettiimpresa.it/api/webhook/stripe
