# PRD: Demo Scenarios per Adeguati Assetti

## Obiettivo
Creare 3 scenari demo distinti che mostrino situazioni aziendali diverse quando l'utente clicca sui rispettivi pulsanti nella landing page.

## Problema Attuale
Tutti e 3 gli utenti demo (demo.sana, demo.critica, demo.studio) puntano allo stesso `id_centro` e mostrano gli stessi dati (situazione critica).

## Requisiti

### 1. Creare 3 Centri Demo Distinti

#### Centro "Demo Azienda Sana" (id da assegnare)
- Azienda manifatturiera in ottima salute
- KPI tutti verdi:
  - DSCR > 1.5
  - Current Ratio > 2.0
  - PN/Debiti > 0.8
  - OF/Ricavi < 3%
  - Cash Flow positivo
  - Zero debiti scaduti
- Score complessivo: 85-95 (OTTIMO)

#### Centro "Demo Azienda Critica" (id da assegnare)
- Azienda in difficoltà finanziaria
- KPI rossi/gialli:
  - DSCR < 1.0
  - Current Ratio < 1.0
  - PN/Debiti < 0.3
  - OF/Ricavi > 5%
  - Debiti scaduti presenti
- Score complessivo: 25-40 (CRITICO)
- Alert attivi

#### Centro "Demo Studio" (id da assegnare)
- Vista multi-cliente per commercialista
- Accesso a più aziende (o simulazione)

### 2. Dati Economici da Inserire

Per ogni centro, inserire in `aa_dati_economici` (periodo Gennaio 2026):

**Azienda Sana:**
```
ricavi_vendite: 1,200,000
totale_ricavi: 1,250,000
patrimonio_netto: 450,000
debiti_totali: 400,000
debiti_breve_termine: 150,000
attivo_circolante: 380,000
disponibilita_liquide: 120,000
oneri_finanziari: 25,000
cash_flow_operativo: 180,000
totale_attivo: 900,000
debiti_tributari: 30,000
debiti_scaduti_*: 0
```

**Azienda Critica:**
```
ricavi_vendite: 800,000
totale_ricavi: 820,000
patrimonio_netto: 80,000
debiti_totali: 650,000
debiti_breve_termine: 450,000
attivo_circolante: 280,000
disponibilita_liquide: 25,000
oneri_finanziari: 55,000
cash_flow_operativo: 40,000
totale_attivo: 750,000
debiti_tributari: 85,000
debiti_scaduti_fornitori: 45,000
debiti_scaduti_fisco: 30,000
```

### 3. Aggiornare Utenti Demo

Modificare nella tabella utenti (`afts5498_utenti` o equivalente):
- `demo.sana@adeguatiassetti.it` → `id_centro` = Centro Sana
- `demo.critica@adeguatiassetti.it` → `id_centro` = Centro Critica
- `demo.studio@adeguatiassetti.it` → `id_centro` = Centro Studio

### 4. Calcolare KPI

Dopo inserimento dati, chiamare l'endpoint `/adeguati-assetti/calcola` per ogni centro per generare i valori KPI nella tabella `aa_valori`.

## Database Target
- Host: 93.186.255.213
- DB: geniusmile_production
- User: geniusmile
- Prefisso tabelle: afts5498_

## Tabelle Coinvolte
- `afts5498_utenti` o `aa_utenti` - utenti con id_centro
- `aa_dati_economici` - dati finanziari per centro/periodo
- `aa_kpi` - definizioni KPI
- `aa_valori` - valori KPI calcolati
- `aa_alert` - alert generati

## Steps di Implementazione
1. Verificare struttura tabelle esistenti
2. Creare centri demo se non esistono
3. Inserire dati economici per ogni centro
4. Aggiornare id_centro degli utenti demo
5. Triggerare calcolo KPI via API
6. Verificare risultato accedendo alle demo
