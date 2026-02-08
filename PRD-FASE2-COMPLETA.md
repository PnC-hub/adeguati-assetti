# PRD - Adeguati Assetti: Completamento Monetizzazione

**Versione:** 2.0
**Data:** 03/02/2026
**Obiettivo:** Portare Adeguati Assetti da TEST a LIVE e raggiungere €1000 MRR

---

## STATO ATTUALE (Fase 1 Completata)

- ✅ Stripe TEST mode funzionante (checkout, webhook handler)
- ✅ 5 email templates creati
- ✅ Landing page deployata e verificata
- ✅ Backend PM2 + artisan serve funzionante
- ⏳ Webhook secret non configurato
- ⏳ Email automation jobs non implementati
- ⏳ LIVE mode non attivo

---

## FASE 2: STRIPE PRODUCTION READY (Priorità CRITICA)

### 2.1 Webhook Configuration
**File:** Nessuno (Stripe Dashboard)
**Azione:** Documentare URL webhook e eventi da ascoltare

```
URL: https://adeguatiassettiimpresa.it/api/webhook/stripe
Eventi:
- checkout.session.completed
- customer.subscription.deleted
- invoice.payment_failed
- customer.subscription.updated
```

### 2.2 Aggiornare stripe_price_id nel DB
**Tabella:** aa_piani
**Azione:** UPDATE con price IDs reali

```sql
UPDATE afts5498_aa_piani SET stripe_price_id = 'price_1SwZUo1Aln4lEvRNJLHjSYPn' WHERE codice = 'pro';
UPDATE afts5498_aa_piani SET stripe_price_id = 'price_1SwZUp1Aln4lEvRNzhbFJ1AA' WHERE codice = 'studio';
```

### 2.3 Config stripe.php - aggiungere billing period
**File:** `backend/config/stripe.php`

```php
return [
    'secret_key' => env('STRIPE_SECRET_KEY'),
    'public_key' => env('STRIPE_PUBLIC_KEY'),
    'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
    'prices' => [
        'pro' => [
            'monthly' => env('STRIPE_PRICE_PRO_MONTHLY', 'price_1SwZUo1Aln4lEvRNJLHjSYPn'),
            'annual' => env('STRIPE_PRICE_PRO_ANNUAL', 'price_1SwZUp1Aln4lEvRNxNO23A98'),
        ],
        'studio' => [
            'monthly' => env('STRIPE_PRICE_STUDIO_MONTHLY', 'price_1SwZUp1Aln4lEvRNzhbFJ1AA'),
            'annual' => env('STRIPE_PRICE_STUDIO_ANNUAL', 'price_1SwZUp1Aln4lEvRNdSVXzzwK'),
        ],
    ],
];
```

---

## FASE 3: EMAIL AUTOMATION SYSTEM

### 3.1 Mailable Classes
**Directory:** `backend/app/Mail/`

Creare:
- `WelcomeTrialMail.php`
- `TrialReminderMail.php` (con parametro $day)

### 3.2 Job per invio email
**File:** `backend/app/Jobs/SendTrialRemindersJob.php`

```php
<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\TrialReminderMail;
use Carbon\Carbon;

class SendTrialRemindersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $reminderDays = [3, 7, 10, 13];

        foreach ($reminderDays as $day) {
            $targetDate = Carbon::now()->subDays(14 - $day)->startOfDay();

            $users = DB::table('aa_users')
                ->where('piano', 'trial')
                ->whereDate('created_at', $targetDate)
                ->whereNotExists(function ($query) use ($day) {
                    $query->select(DB::raw(1))
                        ->from('aa_email_logs')
                        ->whereColumn('aa_email_logs.user_id', 'aa_users.id')
                        ->where('aa_email_logs.email_type', "trial_reminder_day{$day}");
                })
                ->get();

            foreach ($users as $user) {
                try {
                    Mail::to($user->email)->send(new TrialReminderMail($user, $day));

                    DB::table('aa_email_logs')->insert([
                        'user_id' => $user->id,
                        'email_type' => "trial_reminder_day{$day}",
                        'sent_at' => now(),
                        'status' => 'sent',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } catch (\Exception $e) {
                    DB::table('aa_email_logs')->insert([
                        'user_id' => $user->id,
                        'email_type' => "trial_reminder_day{$day}",
                        'sent_at' => now(),
                        'status' => 'failed',
                        'error_message' => $e->getMessage(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}
```

### 3.3 Scheduler
**File:** `backend/routes/console.php`

```php
use Illuminate\Support\Facades\Schedule;
use App\Jobs\SendTrialRemindersJob;

Schedule::job(new SendTrialRemindersJob)->dailyAt('09:00');
```

### 3.4 Cron sul server
```bash
* * * * * cd /var/www/vhosts/geniusmile.com/adeguati-assetti-api && php artisan schedule:run >> /dev/null 2>&1
```

---

## FASE 4: LANDING PAGE OPTIMIZATION

### 4.1 Exit Intent Popup
**File:** `frontend/components/ExitIntentPopup.vue`

```vue
<template>
  <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60">
    <div class="bg-white rounded-2xl p-8 max-w-md mx-4 relative animate-scale-in">
      <button @click="close" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>

      <div class="text-center">
        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
          <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
          </svg>
        </div>

        <h3 class="text-2xl font-bold text-gray-900 mb-2">Aspetta!</h3>
        <p class="text-gray-600 mb-6">
          Il 73% delle PMI non monitora i propri assetti. Non rischiare sanzioni personali fino a €50.000.
        </p>

        <div class="bg-blue-50 rounded-lg p-4 mb-6">
          <p class="text-blue-800 font-semibold">🎁 Offerta Esclusiva</p>
          <p class="text-blue-600 text-sm">14 giorni di prova PRO gratuita + Setup guidato</p>
        </div>

        <NuxtLink
          to="/register"
          class="block w-full bg-red-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-red-700 transition mb-3"
        >
          Inizia Ora - È Gratis
        </NuxtLink>

        <button @click="close" class="text-gray-500 text-sm hover:text-gray-700">
          No grazie, preferisco rischiare
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'

const show = ref(false)
const hasShown = ref(false)

const handleMouseLeave = (e) => {
  if (e.clientY < 10 && !hasShown.value) {
    const dismissed = localStorage.getItem('exitPopupDismissed')
    if (!dismissed) {
      show.value = true
      hasShown.value = true
    }
  }
}

const close = () => {
  show.value = false
  localStorage.setItem('exitPopupDismissed', Date.now().toString())
}

onMounted(() => {
  document.addEventListener('mouseleave', handleMouseLeave)
})

onUnmounted(() => {
  document.removeEventListener('mouseleave', handleMouseLeave)
})
</script>

<style scoped>
.animate-scale-in {
  animation: scaleIn 0.3s ease-out;
}
@keyframes scaleIn {
  from { transform: scale(0.9); opacity: 0; }
  to { transform: scale(1); opacity: 1; }
}
</style>
```

### 4.2 Urgency Banner migliorato
**File:** `frontend/pages/index.vue` - Aggiungere countdown

### 4.3 Social Proof dinamico
Aggiungere contatore "X aziende registrate questa settimana"

---

## FASE 5: ANALYTICS & TRACKING

### 5.1 Google Analytics 4
**File:** `frontend/nuxt.config.ts`

```typescript
// Aggiungere in head
script: [
  {
    src: 'https://www.googletagmanager.com/gtag/js?id=G-XXXXXXXXXX',
    async: true,
  },
  {
    children: `
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', 'G-XXXXXXXXXX');
    `,
  },
],
```

### 5.2 Conversion Tracking
Eventi da tracciare:
- `sign_up` - Registrazione completata
- `begin_trial` - Trial iniziato
- `upgrade_click` - Click su upgrade
- `purchase` - Acquisto completato

### 5.3 Meta Pixel
```html
<!-- Meta Pixel Code -->
<script>
!function(f,b,e,v,n,t,s)
{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};
if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];
s.parentNode.insertBefore(t,s)}(window, document,'script',
'https://connect.facebook.net/en_US/fbevents.js');
fbq('init', 'PIXEL_ID');
fbq('track', 'PageView');
</script>
```

---

## FASE 6: GOOGLE ADS SETUP

### 6.1 Campagna Search
**Budget:** €20/giorno
**Keyword groups:**

**Gruppo 1 - Brand/Compliance**
- adeguati assetti impresa
- art 2086 codice civile
- monitoraggio assetti aziendali
- compliance aziendale obbligatoria

**Gruppo 2 - Pain Points**
- sanzioni amministratore srl
- responsabilità personale amministratore
- crisi aziendale prevenzione
- indicatori crisi impresa

**Gruppo 3 - KPI/Strumenti**
- DSCR calcolo
- indici CNDCEC
- software controllo gestione pmi
- monitoraggio kpi aziendali

### 6.2 Landing Pages dedicate
Creare varianti per A/B test:
- `/lp/commercialisti` - Target commercialisti
- `/lp/imprenditori` - Target imprenditori
- `/lp/sanzioni` - Focus sulle sanzioni

### 6.3 Ad Copy Examples

**Ad 1 - Urgenza**
```
Titolo: Art. 2086 - I Tribunali Stanno Già Intervenendo
Descrizione: Il 73% delle PMI non è in regola. Verifica la conformità della tua azienda in 5 minuti. Prova gratuita 14 giorni.
```

**Ad 2 - Soluzione**
```
Titolo: Adeguati Assetti in 5 Minuti al Mese
Descrizione: Dashboard automatica KPI CNDCEC. Report pronti per commercialista. Da €49/mese. Inizia gratis.
```

**Ad 3 - Paura**
```
Titolo: Sanzioni Fino a €50.000 per Mancato Adeguamento
Descrizione: L'amministratore risponde personalmente. Proteggi il tuo patrimonio con Adeguati Assetti. Trial gratuito.
```

---

## FASE 7: CONTENT MARKETING

### 7.1 Blog Posts (SEO)
Creare sezione `/blog` con articoli:

1. **"Art. 2086 Codice Civile: Guida Completa per Imprenditori"**
   - Keyword: art 2086, adeguati assetti
   - 2500+ parole

2. **"DSCR: Cos'è e Come Calcolarlo"**
   - Keyword: dscr calcolo, debt service coverage ratio
   - 1500+ parole

3. **"Responsabilità Amministratore SRL: Cosa Rischi"**
   - Keyword: responsabilità amministratore srl
   - 2000+ parole

4. **"Indicatori Crisi CNDCEC: I 7 KPI Obbligatori"**
   - Keyword: indicatori crisi cndcec
   - 1800+ parole

5. **"Decreto Tribunale Catanzaro 2024: Cosa Significa"**
   - Keyword: tribunale catanzaro adeguati assetti
   - 1200+ parole

### 7.2 Lead Magnet
**PDF:** "Checklist Adeguati Assetti - I 15 Elementi che i Tribunali Verificano"
- Gate con email
- Nurturing sequence post-download

---

## TIMELINE ESECUZIONE

| Fase | Task | Priorità | Giorni |
|------|------|----------|--------|
| 2 | Stripe production config | CRITICA | 1 |
| 3 | Email automation | ALTA | 2 |
| 4 | Exit popup + landing opt | MEDIA | 1 |
| 5 | GA4 + Meta Pixel | ALTA | 1 |
| 6 | Google Ads setup | ALTA | 2 |
| 7 | Blog 5 articoli | MEDIA | 5 |

**Totale:** 12 giorni di sviluppo

---

## METRICHE SUCCESS

| Metrica | Target 30gg | Target 60gg |
|---------|-------------|-------------|
| Registrazioni | 100 | 300 |
| Trial → Paid | 10% | 15% |
| MRR | €500 | €1000 |
| CAC | <€50 | <€40 |
| Churn | <5% | <3% |

---

## FILE DA CREARE/MODIFICARE

### Backend
1. `config/stripe.php` - Aggiornare con billing periods
2. `app/Mail/WelcomeTrialMail.php` - NUOVO
3. `app/Mail/TrialReminderMail.php` - NUOVO
4. `app/Jobs/SendTrialRemindersJob.php` - NUOVO
5. `routes/console.php` - Scheduler

### Frontend
1. `components/ExitIntentPopup.vue` - NUOVO
2. `pages/index.vue` - Aggiungere popup + miglioramenti
3. `nuxt.config.ts` - GA4 + Meta Pixel
4. `pages/blog/index.vue` - NUOVO
5. `pages/blog/[slug].vue` - NUOVO
6. `pages/lp/commercialisti.vue` - NUOVO
7. `pages/lp/imprenditori.vue` - NUOVO

### Server
1. Cron per Laravel scheduler
2. Update DB aa_piani con stripe_price_id

---

## NOTE TECNICHE

- Email: usare SMTP Aruba (smtps.aruba.it:465)
- Queue: sync driver per semplicità (no Redis)
- GA4: creare property in Google Analytics
- Meta: creare pixel in Business Manager
- Ads: collegare a Google Ads account esistente
