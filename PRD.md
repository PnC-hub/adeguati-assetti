# PRD - Adeguati Assetti Impresa - Sprint Completo

## Task List

### A. Codice ATECO e Campi Condizionali
- [ ] A1. ALTER TABLE aa_aziende ADD COLUMN codice_ateco VARCHAR(10)
- [ ] A2. API endpoint per aggiornare codice ATECO azienda
- [ ] A3. Frontend: select ATECO nel form inserimento (sopra i campi)
- [ ] A4. Frontend: campi operativi condizionali (dentisti solo se ATECO 86.2x)
- [ ] A5. Controller: collegare soglie CNDCEC al codice ATECO reale
- [ ] A6. API aziende: restituire codice_ateco nella risposta

### B. Pagine Legali
- [ ] B1. Creare /termini-servizio (pagina completa a norma)
- [ ] B2. Creare /privacy-policy (informativa GDPR completa)

### C. Link e Footer
- [ ] C1. Fix link in register.vue (NuxtLink a termini e privacy)
- [ ] C2. Link Termini|Privacy nel footer auth.vue
- [ ] C3. Link Termini|Privacy nel footer dashboard.vue
- [ ] C4. Link Termini|Privacy nel footer index.vue (landing)

### D. Pricing Landing Page
- [ ] D1. Sezione pricing con 3 piani (Free/Pro/Studio)

### E. Build, Deploy, Verifica
- [ ] E1. Build frontend
- [ ] E2. Deploy backend + frontend
- [ ] E3. Git commit + push
- [ ] E4. Verifica Chrome tutte le pagine
