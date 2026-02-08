<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AziendeClienteController extends Controller
{
    /**
     * List all aziende for current studio
     */
    public function index(Request $request)
    {
        $user = $this->getAuthUser($request);
        if (!$user || $user->tipo_utente !== 'consulente') {
            return response()->json(['success' => false, 'message' => 'Accesso negato'], 403);
        }

        $studio = DB::table('aa_studi')->where('user_id', $user->id)->first();
        if (!$studio) {
            return response()->json(['success' => false, 'message' => 'Studio non trovato'], 404);
        }

        $aziende = DB::table('aa_aziende_cliente')
            ->where('studio_id', $studio->id)
            ->orderBy('nome')
            ->get();

        // Aggiungi KPI a ciascuna azienda
        foreach ($aziende as $azienda) {
            $azienda->kpi = $this->calcolaKpiAzienda($azienda->id);
            $azienda->score = $this->calcolaScore($azienda->kpi);
        }

        return response()->json(['success' => true, 'data' => $aziende]);
    }

    /**
     * Create new azienda cliente
     */
    public function store(Request $request)
    {
        $user = $this->getAuthUser($request);
        if (!$user || $user->tipo_utente !== 'consulente') {
            return response()->json(['success' => false, 'message' => 'Accesso negato'], 403);
        }

        $studio = DB::table('aa_studi')->where('user_id', $user->id)->first();
        if (!$studio) {
            return response()->json(['success' => false, 'message' => 'Studio non trovato'], 404);
        }

        // Check piano limit
        $piano = $this->getUserPlan($user);
        $currentCount = DB::table('aa_aziende_cliente')
            ->where('studio_id', $studio->id)
            ->where('attiva', true)
            ->count();

        if ($piano->max_aziende > 0 && $currentCount >= $piano->max_aziende) {
            return response()->json([
                'success' => false,
                'message' => "Hai raggiunto il limite di {$piano->max_aziende} aziende per il piano {$piano->nome}. Effettua l'upgrade."
            ], 403);
        }

        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'p_iva' => 'nullable|string|max:20',
            'codice_fiscale' => 'nullable|string|max:20',
            'settore_ateco' => 'nullable|string|max:20',
            'indirizzo' => 'nullable|string',
            'citta' => 'nullable|string|max:100',
            'cap' => 'nullable|string|max:10',
            'provincia' => 'nullable|string|max:5',
            'email_referente' => 'nullable|email|max:255',
            'telefono_referente' => 'nullable|string|max:50',
            'note' => 'nullable|string',
        ]);

        $validated['studio_id'] = $studio->id;
        $validated['attiva'] = true;
        $validated['created_at'] = now();
        $validated['updated_at'] = now();

        $id = DB::table('aa_aziende_cliente')->insertGetId($validated);

        $azienda = DB::table('aa_aziende_cliente')->find($id);

        return response()->json(['success' => true, 'data' => $azienda], 201);
    }

    /**
     * Get single azienda
     */
    public function show(Request $request, $id)
    {
        $user = $this->getAuthUser($request);
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Non autenticato'], 401);
        }

        $azienda = DB::table('aa_aziende_cliente')->find($id);
        if (!$azienda) {
            return response()->json(['success' => false, 'message' => 'Azienda non trovata'], 404);
        }

        // Check access
        if ($user->tipo_utente === 'consulente') {
            $studio = DB::table('aa_studi')->where('user_id', $user->id)->first();
            if (!$studio || $azienda->studio_id !== $studio->id) {
                return response()->json(['success' => false, 'message' => 'Accesso negato'], 403);
            }
        } elseif ($user->tipo_utente === 'cliente_readonly') {
            if ($user->azienda_cliente_id !== (int)$id) {
                return response()->json(['success' => false, 'message' => 'Accesso negato'], 403);
            }
        } else {
            return response()->json(['success' => false, 'message' => 'Accesso negato'], 403);
        }

        $azienda->kpi = $this->calcolaKpiAzienda($azienda->id);
        $azienda->score = $this->calcolaScore($azienda->kpi);

        return response()->json(['success' => true, 'data' => $azienda]);
    }

    /**
     * Update azienda
     */
    public function update(Request $request, $id)
    {
        $user = $this->getAuthUser($request);
        if (!$user || $user->tipo_utente !== 'consulente') {
            return response()->json(['success' => false, 'message' => 'Accesso negato'], 403);
        }

        $studio = DB::table('aa_studi')->where('user_id', $user->id)->first();
        $azienda = DB::table('aa_aziende_cliente')->find($id);

        if (!$azienda || $azienda->studio_id !== $studio->id) {
            return response()->json(['success' => false, 'message' => 'Azienda non trovata'], 404);
        }

        $validated = $request->validate([
            'nome' => 'sometimes|string|max:255',
            'p_iva' => 'nullable|string|max:20',
            'codice_fiscale' => 'nullable|string|max:20',
            'settore_ateco' => 'nullable|string|max:20',
            'indirizzo' => 'nullable|string',
            'citta' => 'nullable|string|max:100',
            'cap' => 'nullable|string|max:10',
            'provincia' => 'nullable|string|max:5',
            'email_referente' => 'nullable|email|max:255',
            'telefono_referente' => 'nullable|string|max:50',
            'note' => 'nullable|string',
            'attiva' => 'sometimes|boolean',
        ]);

        $validated['updated_at'] = now();

        DB::table('aa_aziende_cliente')->where('id', $id)->update($validated);

        $azienda = DB::table('aa_aziende_cliente')->find($id);

        return response()->json(['success' => true, 'data' => $azienda]);
    }

    /**
     * Deactivate azienda
     */
    public function destroy(Request $request, $id)
    {
        $user = $this->getAuthUser($request);
        if (!$user || $user->tipo_utente !== 'consulente') {
            return response()->json(['success' => false, 'message' => 'Accesso negato'], 403);
        }

        $studio = DB::table('aa_studi')->where('user_id', $user->id)->first();
        $azienda = DB::table('aa_aziende_cliente')->find($id);

        if (!$azienda || $azienda->studio_id !== $studio->id) {
            return response()->json(['success' => false, 'message' => 'Azienda non trovata'], 404);
        }

        DB::table('aa_aziende_cliente')->where('id', $id)->update([
            'attiva' => false,
            'updated_at' => now()
        ]);

        return response()->json(['success' => true, 'message' => 'Azienda disattivata']);
    }

    /**
     * Get dati economici for azienda
     */
    public function getDati(Request $request, $id)
    {
        $user = $this->getAuthUser($request);
        if (!$this->canAccessAzienda($user, $id)) {
            return response()->json(['success' => false, 'message' => 'Accesso negato'], 403);
        }

        $dati = DB::table('aa_dati_economici_cliente')
            ->where('azienda_cliente_id', $id)
            ->orderBy('anno', 'desc')
            ->orderBy('mese', 'desc')
            ->get();

        return response()->json(['success' => true, 'data' => $dati]);
    }

    /**
     * Store dati economici
     */
    public function storeDati(Request $request, $id)
    {
        $user = $this->getAuthUser($request);
        if (!$user || $user->tipo_utente !== 'consulente') {
            return response()->json(['success' => false, 'message' => 'Accesso negato'], 403);
        }

        if (!$this->canAccessAzienda($user, $id)) {
            return response()->json(['success' => false, 'message' => 'Accesso negato'], 403);
        }

        $validated = $request->validate([
            'anno' => 'required|integer|min:2020|max:2030',
            'mese' => 'required|integer|min:1|max:12',
            'patrimonio_netto' => 'nullable|numeric',
            'totale_attivo' => 'nullable|numeric',
            'totale_debiti' => 'nullable|numeric',
            'debiti_finanziari' => 'nullable|numeric',
            'debiti_tributari' => 'nullable|numeric',
            'ricavi' => 'nullable|numeric',
            'oneri_finanziari' => 'nullable|numeric',
            'cash_flow_operativo' => 'nullable|numeric',
            'rata_debiti' => 'nullable|numeric',
        ]);

        $validated['azienda_cliente_id'] = $id;
        $validated['updated_at'] = now();

        // Upsert
        $existing = DB::table('aa_dati_economici_cliente')
            ->where('azienda_cliente_id', $id)
            ->where('anno', $validated['anno'])
            ->where('mese', $validated['mese'])
            ->first();

        if ($existing) {
            DB::table('aa_dati_economici_cliente')
                ->where('id', $existing->id)
                ->update($validated);
            $datiId = $existing->id;
        } else {
            $validated['created_at'] = now();
            $datiId = DB::table('aa_dati_economici_cliente')->insertGetId($validated);
        }

        $dati = DB::table('aa_dati_economici_cliente')->find($datiId);

        return response()->json(['success' => true, 'data' => $dati]);
    }

    /**
     * Get KPI for azienda
     */
    public function getKpi(Request $request, $id)
    {
        $user = $this->getAuthUser($request);
        if (!$this->canAccessAzienda($user, $id)) {
            return response()->json(['success' => false, 'message' => 'Accesso negato'], 403);
        }

        $kpi = $this->calcolaKpiAzienda($id);
        $score = $this->calcolaScore($kpi);

        return response()->json([
            'success' => true,
            'data' => [
                'kpi' => $kpi,
                'score' => $score
            ]
        ]);
    }

    /**
     * Dashboard aggregata
     */
    public function dashboardAggregata(Request $request)
    {
        $user = $this->getAuthUser($request);
        if (!$user || $user->tipo_utente !== 'consulente') {
            return response()->json(['success' => false, 'message' => 'Accesso negato'], 403);
        }

        $studio = DB::table('aa_studi')->where('user_id', $user->id)->first();
        if (!$studio) {
            return response()->json(['success' => false, 'message' => 'Studio non trovato'], 404);
        }

        $aziende = DB::table('aa_aziende_cliente')
            ->where('studio_id', $studio->id)
            ->where('attiva', true)
            ->orderBy('nome')
            ->get();

        $risultati = [];
        $totaleVerdi = 0;
        $totaleGialli = 0;
        $totaleRossi = 0;

        foreach ($aziende as $azienda) {
            $kpi = $this->calcolaKpiAzienda($azienda->id);
            $score = $this->calcolaScore($kpi);

            $verdi = 0;
            $gialli = 0;
            $rossi = 0;
            foreach ($kpi as $k) {
                if ($k['stato'] === 'verde') { $verdi++; $totaleVerdi++; }
                elseif ($k['stato'] === 'giallo') { $gialli++; $totaleGialli++; }
                elseif ($k['stato'] === 'rosso') { $rossi++; $totaleRossi++; }
            }

            $risultati[] = [
                'id' => $azienda->id,
                'nome' => $azienda->nome,
                'p_iva' => $azienda->p_iva,
                'settore_ateco' => $azienda->settore_ateco,
                'score' => $score,
                'kpi_verdi' => $verdi,
                'kpi_gialli' => $gialli,
                'kpi_rossi' => $rossi,
                'stato' => $rossi > 0 ? 'critico' : ($gialli > 0 ? 'attenzione' : 'ok'),
            ];
        }

        // Ordina per score (peggiori prima)
        usort($risultati, fn($a, $b) => $a['score'] - $b['score']);

        return response()->json([
            'success' => true,
            'data' => [
                'aziende' => $risultati,
                'totali' => [
                    'aziende' => count($risultati),
                    'kpi_verdi' => $totaleVerdi,
                    'kpi_gialli' => $totaleGialli,
                    'kpi_rossi' => $totaleRossi,
                ]
            ]
        ]);
    }

    private function getAuthUser(Request $request)
    {
        $token = $request->bearerToken();
        if (!$token) return null;

        // Look up hashed token in aa_tokens table
        $tokenRecord = DB::table('aa_tokens')
            ->where('token', hash('sha256', $token))
            ->where(function($q) {
                $q->whereNull('expires_at')
                   ->orWhere('expires_at', '>', now());
            })
            ->first();

        if (!$tokenRecord) return null;

        return DB::table('aa_users')->where('id', $tokenRecord->user_id)->first();
    }

    private function canAccessAzienda($user, $aziendaId)
    {
        if (!$user) return false;

        $azienda = DB::table('aa_aziende_cliente')->find($aziendaId);
        if (!$azienda) return false;

        if ($user->tipo_utente === 'consulente') {
            $studio = DB::table('aa_studi')->where('user_id', $user->id)->first();
            return $studio && $azienda->studio_id === $studio->id;
        }

        if ($user->tipo_utente === 'cliente_readonly') {
            return $user->azienda_cliente_id === (int)$aziendaId;
        }

        return false;
    }

    private function getUserPlan($user)
    {
        return DB::table('aa_piani')->where('codice', $user->piano ?? 'free')->first();
    }

    private function calcolaKpiAzienda($aziendaId)
    {
        $dati = DB::table('aa_dati_economici_cliente')
            ->where('azienda_cliente_id', $aziendaId)
            ->orderBy('anno', 'desc')
            ->orderBy('mese', 'desc')
            ->first();

        if (!$dati) return [];

        $kpi = [];

        // PN
        $pn = $dati->patrimonio_netto ?? 0;
        $kpi[] = [
            'codice' => 'PN',
            'nome' => 'Patrimonio Netto',
            'valore' => $pn,
            'stato' => $pn > 0 ? 'verde' : ($pn >= -10000 ? 'giallo' : 'rosso')
        ];

        // DSCR
        if ($dati->cash_flow_operativo !== null && $dati->rata_debiti && $dati->rata_debiti > 0) {
            $dscr = $dati->cash_flow_operativo / $dati->rata_debiti;
            $kpi[] = [
                'codice' => 'DSCR',
                'nome' => 'DSCR',
                'valore' => round($dscr, 2),
                'stato' => $dscr >= 1.1 ? 'verde' : ($dscr >= 1 ? 'giallo' : 'rosso')
            ];
        }

        // CR
        if ($dati->totale_attivo && $dati->totale_debiti && $dati->totale_debiti > 0) {
            $cr = $dati->totale_attivo / $dati->totale_debiti;
            $kpi[] = [
                'codice' => 'CR',
                'nome' => 'Current Ratio',
                'valore' => round($cr, 2),
                'stato' => $cr >= 1.5 ? 'verde' : ($cr >= 1 ? 'giallo' : 'rosso')
            ];
        }

        // OF/RIC
        if ($dati->oneri_finanziari !== null && $dati->ricavi && $dati->ricavi > 0) {
            $ofRic = ($dati->oneri_finanziari / $dati->ricavi) * 100;
            $kpi[] = [
                'codice' => 'OF_RIC',
                'nome' => 'Oneri Fin./Ricavi',
                'valore' => round($ofRic, 2),
                'stato' => $ofRic <= 3 ? 'verde' : ($ofRic <= 5 ? 'giallo' : 'rosso')
            ];
        }

        // PN/DEB
        if ($dati->patrimonio_netto !== null && $dati->totale_debiti && $dati->totale_debiti > 0) {
            $pnDeb = $dati->patrimonio_netto / $dati->totale_debiti;
            $kpi[] = [
                'codice' => 'PN_DEB',
                'nome' => 'PN/Debiti',
                'valore' => round($pnDeb, 2),
                'stato' => $pnDeb >= 0.5 ? 'verde' : ($pnDeb >= 0.25 ? 'giallo' : 'rosso')
            ];
        }

        // DEB_FISC/ATT
        if ($dati->debiti_tributari !== null && $dati->totale_attivo && $dati->totale_attivo > 0) {
            $debFiscAtt = ($dati->debiti_tributari / $dati->totale_attivo) * 100;
            $kpi[] = [
                'codice' => 'DEBFISC_ATT',
                'nome' => 'Deb.Fiscali/Attivo',
                'valore' => round($debFiscAtt, 2),
                'stato' => $debFiscAtt <= 5 ? 'verde' : ($debFiscAtt <= 10 ? 'giallo' : 'rosso')
            ];
        }

        // CF/ATT
        if ($dati->cash_flow_operativo !== null && $dati->totale_attivo && $dati->totale_attivo > 0) {
            $cfAtt = ($dati->cash_flow_operativo / $dati->totale_attivo) * 100;
            $kpi[] = [
                'codice' => 'CF_ATT',
                'nome' => 'Cash Flow/Attivo',
                'valore' => round($cfAtt, 2),
                'stato' => $cfAtt >= 5 ? 'verde' : ($cfAtt >= 2 ? 'giallo' : 'rosso')
            ];
        }

        return $kpi;
    }

    private function calcolaScore($kpi)
    {
        if (empty($kpi)) return 0;

        $punti = 0;
        foreach ($kpi as $k) {
            if ($k['stato'] === 'verde') $punti += 10;
            elseif ($k['stato'] === 'giallo') $punti += 5;
        }

        return round(($punti / (count($kpi) * 10)) * 100);
    }
}
