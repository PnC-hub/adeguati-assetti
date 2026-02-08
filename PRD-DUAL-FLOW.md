# PRD: Dual Flow Imprenditore/Consulente - Adeguati Assetti

## Executive Summary

Implementazione di due flussi utente separati per massimizzare acquisizione e monetizzazione:
1. **Imprenditore**: Free forever con upsell a Pro €29/mese
2. **Consulente**: Trial 30gg Business, poi €79-199/mese

---

## 1. Architettura Database

### Nuove Tabelle

```sql
-- Studi (per consulenti)
CREATE TABLE aa_studi (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    nome VARCHAR(255) NOT NULL,
    p_iva VARCHAR(20),
    indirizzo TEXT,
    telefono VARCHAR(50),
    email VARCHAR(255),
    logo_url VARCHAR(500),
    colore_primario VARCHAR(7) DEFAULT '#DC2626',
    white_label_attivo BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX(user_id)
);

-- Aziende cliente (gestite da consulenti)
CREATE TABLE aa_aziende_cliente (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    studio_id BIGINT UNSIGNED NOT NULL,
    nome VARCHAR(255) NOT NULL,
    p_iva VARCHAR(20),
    codice_fiscale VARCHAR(20),
    settore_ateco VARCHAR(20),
    indirizzo TEXT,
    citta VARCHAR(100),
    cap VARCHAR(10),
    provincia VARCHAR(5),
    email_referente VARCHAR(255),
    telefono_referente VARCHAR(50),
    note TEXT,
    attiva BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX(studio_id),
    INDEX(p_iva)
);

-- Inviti clienti (readonly access)
CREATE TABLE aa_inviti_cliente (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    azienda_cliente_id BIGINT UNSIGNED NOT NULL,
    email VARCHAR(255) NOT NULL,
    token VARCHAR(64) NOT NULL UNIQUE,
    user_id BIGINT UNSIGNED NULL,
    stato ENUM('pending', 'accepted', 'revoked') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    accepted_at TIMESTAMP NULL,
    INDEX(token),
    INDEX(email)
);

-- Dati economici per aziende cliente
CREATE TABLE aa_dati_economici_cliente (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    azienda_cliente_id BIGINT UNSIGNED NOT NULL,
    anno INT NOT NULL,
    mese INT NOT NULL,
    patrimonio_netto DECIMAL(15,2),
    totale_attivo DECIMAL(15,2),
    totale_debiti DECIMAL(15,2),
    debiti_finanziari DECIMAL(15,2),
    debiti_tributari DECIMAL(15,2),
    ricavi DECIMAL(15,2),
    oneri_finanziari DECIMAL(15,2),
    cash_flow_operativo DECIMAL(15,2),
    rata_debiti DECIMAL(15,2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_periodo (azienda_cliente_id, anno, mese),
    INDEX(azienda_cliente_id)
);
```

### Modifiche Tabella Esistente aa_users

```sql
ALTER TABLE aa_users
ADD COLUMN tipo_utente ENUM('imprenditore', 'consulente', 'cliente_readonly') DEFAULT 'imprenditore',
ADD COLUMN studio_id BIGINT UNSIGNED NULL,
ADD COLUMN azienda_cliente_id BIGINT UNSIGNED NULL;
```

### Aggiornamento aa_piani

```sql
-- Nuovi piani
INSERT INTO aa_piani (codice, nome, prezzo_mensile, prezzo_annuale, max_aziende, features, stripe_price_id_mensile, stripe_price_id_annuale, target) VALUES
('free', 'Free Forever', 0, 0, 1, '["7_kpi","calcolo_manuale","1_mese_storico"]', NULL, NULL, 'imprenditore'),
('pro', 'Pro', 29, 290, 1, '["7_kpi","kpi_ateco","alert_email","export_pdf","24_mesi_storico","simulazioni"]', 'price_pro_monthly', 'price_pro_yearly', 'imprenditore'),
('business', 'Business', 79, 790, 20, '["dashboard_aggregata","alert_tutti","export_batch","invita_clienti","report_commercialista"]', 'price_business_monthly', 'price_business_yearly', 'consulente'),
('enterprise', 'Enterprise', 199, 1990, -1, '["white_label","api","sub_account","supporto_prioritario","formazione"]', 'price_enterprise_monthly', 'price_enterprise_yearly', 'consulente');
```

---

## 2. API Endpoints

### Autenticazione e Registrazione

```
POST /api/register
Body: {
    email, password, nome, cognome,
    tipo_utente: 'imprenditore' | 'consulente',
    // Se imprenditore:
    nome_azienda, p_iva_azienda, settore_ateco,
    // Se consulente:
    nome_studio, p_iva_studio
}

POST /api/accept-invite/{token}
Body: { email, password, nome, cognome }
```

### Gestione Studio (solo consulenti)

```
GET    /api/studio                    → Info studio corrente
PUT    /api/studio                    → Aggiorna studio
GET    /api/studio/stats              → Statistiche aggregate
```

### Gestione Aziende Cliente (solo consulenti)

```
GET    /api/aziende-cliente           → Lista aziende del mio studio
POST   /api/aziende-cliente           → Crea nuova azienda cliente
GET    /api/aziende-cliente/{id}      → Dettaglio azienda
PUT    /api/aziende-cliente/{id}      → Aggiorna azienda
DELETE /api/aziende-cliente/{id}      → Disattiva azienda
```

### Dati Economici Aziende Cliente

```
GET    /api/aziende-cliente/{id}/dati              → Lista dati economici
POST   /api/aziende-cliente/{id}/dati              → Inserisci dati mese
GET    /api/aziende-cliente/{id}/kpi               → Calcola KPI
GET    /api/aziende-cliente/{id}/export-pdf        → Genera PDF
```

### Inviti Cliente

```
POST   /api/aziende-cliente/{id}/invita            → Invia invito email
GET    /api/inviti                                  → Lista inviti del mio studio
DELETE /api/inviti/{id}                            → Revoca invito
```

### Dashboard Aggregata (solo consulenti)

```
GET    /api/dashboard-aggregata       → KPI di tutte le aziende con semafori
GET    /api/alert-aggregati           → Alert critici di tutte le aziende
```

---

## 3. Pagine Frontend

### Pubbliche
- `/` - Landing page (esistente, aggiornare CTA)
- `/register` - Scelta tipo utente + form registrazione
- `/login` - Login (esistente)
- `/invite/{token}` - Accettazione invito cliente

### Dashboard Imprenditore (esistenti, feature-gated)
- `/dashboard` - Dashboard singola azienda
- `/dashboard/inserimento` - Inserimento dati
- `/dashboard/checklist` - Checklist 2086
- `/dashboard/account` - Account e billing
- `/dashboard/referral` - Sistema referral

### Dashboard Consulente (NUOVE)
- `/consulente` - Dashboard aggregata con tabella aziende
- `/consulente/aziende` - Gestione aziende clienti
- `/consulente/aziende/nuova` - Crea nuova azienda
- `/consulente/aziende/{id}` - Dettaglio azienda con KPI
- `/consulente/aziende/{id}/dati` - Inserimento dati
- `/consulente/studio` - Impostazioni studio
- `/consulente/inviti` - Gestione inviti
- `/consulente/report` - Report batch
- `/consulente/account` - Account e billing

### Dashboard Cliente Readonly (NUOVE)
- `/cliente` - Dashboard readonly della propria azienda
- `/cliente/kpi` - Visualizza KPI (no edit)

---

## 4. Feature Gating

### Matrice Features per Piano

| Feature | Free | Pro | Business | Enterprise |
|---------|------|-----|----------|------------|
| 7 KPI obbligatori | ✅ | ✅ | ✅ | ✅ |
| KPI settoriali ATECO | ❌ | ✅ | ✅ | ✅ |
| Alert email | ❌ | ✅ | ✅ | ✅ |
| Export PDF | ❌ | ✅ | ✅ | ✅ |
| Storico 24 mesi | ❌ | ✅ | ✅ | ✅ |
| Simulazioni what-if | ❌ | ✅ | ✅ | ✅ |
| Max aziende | 1 | 1 | 20 | ∞ |
| Dashboard aggregata | ❌ | ❌ | ✅ | ✅ |
| Export batch | ❌ | ❌ | ✅ | ✅ |
| Invita clienti | ❌ | ❌ | ✅ | ✅ |
| White-label | ❌ | ❌ | ❌ | ✅ |
| API access | ❌ | ❌ | ❌ | ✅ |

---

## 5. Flussi Utente

### Flusso Imprenditore

```
Landing → Click "Imprenditore"
    → Form: email, password, nome azienda, P.IVA, ATECO
    → Account creato (piano Free)
    → Dashboard singola azienda
    → Inserisce dati → Vede KPI
    → Vuole alert? → Upgrade Pro €29
    → Vuole referral? → Invita amici → Sblocca features
```

### Flusso Consulente

```
Landing → Click "Commercialista"
    → Form: email, password, nome studio, P.IVA studio
    → Account creato (Trial Business 30gg)
    → Dashboard aggregata (vuota)
    → Aggiunge aziende clienti
    → Inserisce dati per ciascuna
    → Dopo 30gg → Paga €79/mese o account congelato
```

### Flusso Cliente Invitato

```
Email invito → Click link
    → Form: email (pre-filled), password, nome
    → Account creato (cliente_readonly)
    → Dashboard readonly della sua azienda
    → Vuole modificare/alert? → Deve passare a imprenditore Pro
```

---

## 6. Implementazione Step-by-Step

### Step 1: Database (backend)
- [x] Migration nuove tabelle
- [x] Modifica aa_users
- [x] Seed piani aggiornati

### Step 2: API Registrazione (backend)
- [ ] Endpoint register con tipo_utente
- [ ] Creazione automatica studio/azienda
- [ ] Endpoint accept-invite

### Step 3: API Consulente (backend)
- [ ] CRUD aziende-cliente
- [ ] CRUD dati-economici-cliente
- [ ] Dashboard aggregata
- [ ] Sistema inviti
- [ ] Export batch PDF

### Step 4: Frontend Registrazione
- [ ] Nuova pagina register con scelta tipo
- [ ] Form imprenditore
- [ ] Form consulente
- [ ] Pagina accept-invite

### Step 5: Frontend Consulente
- [ ] Layout consulente
- [ ] Dashboard aggregata
- [ ] Gestione aziende
- [ ] Dettaglio azienda con KPI
- [ ] Inserimento dati
- [ ] Impostazioni studio
- [ ] Gestione inviti

### Step 6: Frontend Cliente Readonly
- [ ] Layout cliente
- [ ] Dashboard readonly

### Step 7: Feature Gating
- [ ] Middleware backend per check piano
- [ ] Composable frontend useFeatures()
- [ ] UI lock per features bloccate

### Step 8: Billing
- [ ] Prezzi Stripe aggiornati
- [ ] Checkout per 4 piani
- [ ] Trial 30gg consulenti
- [ ] Gestione scadenza trial

---

## 7. File da Creare/Modificare

### Backend (Laravel)

```
database/migrations/
├── 2026_02_08_create_aa_studi_table.php
├── 2026_02_08_create_aa_aziende_cliente_table.php
├── 2026_02_08_create_aa_inviti_cliente_table.php
├── 2026_02_08_create_aa_dati_economici_cliente_table.php
├── 2026_02_08_add_tipo_utente_to_aa_users.php
└── 2026_02_08_update_aa_piani.php

app/Http/Controllers/Api/
├── StudioController.php (NUOVO)
├── AziendeClienteController.php (NUOVO)
├── InvitiController.php (NUOVO)
└── AdeguatiAssettiStandaloneController.php (MODIFICA register)

routes/api.php (MODIFICA - nuove routes)
```

### Frontend (Nuxt)

```
pages/
├── register.vue (RISCRIVERE)
├── invite/
│   └── [token].vue (NUOVO)
├── consulente/
│   ├── index.vue (NUOVO - dashboard aggregata)
│   ├── aziende/
│   │   ├── index.vue (NUOVO)
│   │   ├── nuova.vue (NUOVO)
│   │   └── [id]/
│   │       ├── index.vue (NUOVO)
│   │       └── dati.vue (NUOVO)
│   ├── studio.vue (NUOVO)
│   ├── inviti.vue (NUOVO)
│   ├── report.vue (NUOVO)
│   └── account.vue (NUOVO)
├── cliente/
│   ├── index.vue (NUOVO)
│   └── kpi.vue (NUOVO)

layouts/
├── consulente.vue (NUOVO)
└── cliente.vue (NUOVO)

composables/
├── useAuth.ts (MODIFICA - aggiungere tipo_utente)
├── useFeatures.ts (NUOVO)
├── useStudio.ts (NUOVO)
└── useAziendeCliente.ts (NUOVO)

components/
├── KpiCard.vue (NUOVO - riusabile)
├── AziendaCard.vue (NUOVO)
├── InviteModal.vue (NUOVO)
└── FeatureLock.vue (NUOVO)
```

---

## 8. Test Checklist

### Backend
- [ ] POST /register tipo=imprenditore → crea user + azienda
- [ ] POST /register tipo=consulente → crea user + studio
- [ ] GET /studio → ritorna dati studio
- [ ] CRUD /aziende-cliente funziona
- [ ] POST /aziende-cliente/{id}/invita → invia email
- [ ] GET /dashboard-aggregata → ritorna tutte le aziende con KPI

### Frontend
- [ ] /register mostra scelta tipo utente
- [ ] Form imprenditore funziona
- [ ] Form consulente funziona
- [ ] /consulente mostra dashboard aggregata
- [ ] /consulente/aziende/nuova crea azienda
- [ ] /consulente/aziende/{id} mostra KPI
- [ ] Feature lock funziona per piano Free
- [ ] /cliente mostra dashboard readonly

---

## 9. Deploy Checklist

- [ ] Migration eseguite su server
- [ ] Seed piani eseguito
- [ ] Frontend buildato e deployato
- [ ] Test su staging
- [ ] Go-live

