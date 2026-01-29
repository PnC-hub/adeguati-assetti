# PRD - Monetizzazione Completa Adeguati Assetti Impresa

## Sprint: Sistema Piani, Stripe, Restrizioni, Billing

---

### FASE 1: Database + Backend Piani
- [ ] 1.1 ALTER TABLE aa_users: ADD piano_stripe_id, stripe_customer_id, piano_attivo_dal, piano_scade_il
- [ ] 1.2 CREATE TABLE aa_piani: id, codice (free/pro/studio), nome, prezzo, max_aziende, features JSON
- [ ] 1.3 Seed tabella aa_piani con i 3 piani
- [ ] 1.4 Controller: GET /piani (lista piani pubblici)
- [ ] 1.5 Controller: GET /account (profilo utente + piano attuale + limiti)
- [ ] 1.6 Controller: POST /upgrade (crea Stripe Checkout session)
- [ ] 1.7 Controller: POST /webhook/stripe (gestione eventi Stripe)
- [ ] 1.8 Controller: POST /billing-portal (redirect Stripe billing portal)
- [ ] 1.9 Middleware: checkPiano() - verifica limiti piano su ogni API call

### FASE 2: Feature Gating Backend
- [ ] 2.1 Dashboard: se piano=free, ritorna solo kpi_obbligatori (no settoriali)
- [ ] 2.2 Export: se piano=free, blocca con errore "Upgrade a Pro"
- [ ] 2.3 Alert: se piano=free, non generare alert
- [ ] 2.4 Aziende: limitare numero aziende per piano (free=1, pro=1, studio=20)
- [ ] 2.5 Trial: dopo 14gg senza upgrade, downgrade a free

### FASE 3: Frontend - Pagina Account/Abbonamento
- [ ] 3.1 Creare /dashboard/account.vue con info profilo + piano attuale
- [ ] 3.2 Sezione upgrade/downgrade con card piani
- [ ] 3.3 Pulsante "Gestisci Abbonamento" → Stripe Billing Portal
- [ ] 3.4 Banner trial scadenza nel layout dashboard

### FASE 4: Frontend - Feature Gating UI
- [ ] 4.1 Dashboard: bloccare KPI settoriali con overlay "Upgrade a Pro"
- [ ] 4.2 Dashboard: bloccare export PDF con lock icon
- [ ] 4.3 Banner upgrade in dashboard per utenti free
- [ ] 4.4 Store piano utente nel localStorage e usarlo per UI gating

### FASE 5: Rimuovere pricing duplicato dalla landing
- [ ] 5.1 Unificare le 2 sezioni pricing in una sola (Free/Pro/Studio)

### FASE 6: Build, Deploy, Verify
- [ ] 6.1 Build frontend
- [ ] 6.2 Deploy backend + frontend
- [ ] 6.3 Git commit + push
- [ ] 6.4 Verifica Chrome completa di tutte le pagine
