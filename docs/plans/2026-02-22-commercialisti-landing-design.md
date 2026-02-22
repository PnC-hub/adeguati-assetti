# Design: Landing Commercialisti + Aggiornamento Homepage

**Data:** 2026-02-22
**Stato:** Approvato

---

## Obiettivo

Migliorare la parte sales/marketing del sito con due ramificazioni:
1. **Homepage** → parla agli imprenditori (acquirenti)
2. **`/commercialisti`** → pagina separata che vende il servizio ai commercialisti

Approccio scelto: **"Strumento per i tuoi clienti"** — il commercialista non compra per sé, offre uno strumento professionale ai propri clienti. Il riconoscimento economico (20% revenue share) è citato in modo vago e istituzionale, senza cifre.

---

## Modifiche Homepage (`/`)

### Navbar
- Aggiungere link **"Per i Commercialisti"** tra "Accedi" e "Registrati Gratis"
- Stile: ghost/outlined button o link testuale con freccia ↗
- Link a: `/commercialisti`

### Sezione Pricing
- Rimuovere badge "100% Gratuito" e testo "Gratis, per sempre"
- Aggiornare con modello reale: registrazione gratuita, poi €49/mese
- Mostrare due card affiancate: una per Imprenditore (€49/mese) e una per Commercialista (gratuito)

---

## Nuova Pagina `/commercialisti`

### 1. Hero
- **Headline:** "Dai ai tuoi clienti uno strumento professionale per la compliance Art. 2086"
- **Sottotitolo:** "Li aiuti a rispettare la legge. Senza lavoro extra per te."
- **CTA primaria:** "Inizia — è gratuito per te" → `/register`
- **Subtext CTA:** "Nessun costo. Nessun impegno."

### 2. Il Problema (3 card)
- I tribunali italiani emettono già decreti d'ispezione per mancanza di assetti adeguati
- I tuoi clienti sono esposti a sanzioni personali e responsabilità dell'amministratore
- Hanno bisogno di un monitoraggio continuo — e si rivolgono a te per orientarsi

### 3. Come Funziona (3 step)
1. **Registrati gratuitamente** — crea il tuo account studio in 2 minuti
2. **Invita i tuoi clienti** — condividi il tuo link personale, loro si iscrivono autonomamente
3. **Supervisiona dal tuo pannello** — vedi lo stato di compliance di tutti i clienti in un colpo d'occhio

### 4. Cosa Ottieni (benefit list)
- ✅ Accesso completamente gratuito per te
- ✅ Pannello studio con tutti i clienti collegati
- ✅ Report mensili automatici per ogni azienda
- ✅ Un rapporto professionale più solido con i tuoi clienti
- 💛 *(Sezione discreta)* "Come riconoscimento per il valore che porti ai nostri utenti, è previsto un benefit per ogni cliente attivo che hai invitato."

### 5. CTA Finale
- **Headline:** "I tuoi clienti ti ringrazieranno"
- **Sottotitolo:** "Inizia oggi — sono già oltre 150 le aziende monitorate con Adeguati Assetti."
- **CTA:** "Registrati gratuitamente" → `/register`

---

## Note Tecniche

- Nuova route Nuxt: `frontend/pages/commercialisti.vue`
- Nessuna nuova API necessaria — la pagina è statica/marketing
- Il link navbar va aggiunto al componente header esistente
- La sezione pricing homepage va riscritta con due card side-by-side
- Deploy: `nuxt generate` → rsync → `/var/www/vhosts/geniusmile.com/adeguati-assetti/`
