<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudioController extends Controller
{
    /**
     * Get current user's studio
     */
    public function show(Request $request)
    {
        $user = $this->getAuthUser($request);
        if (!$user || $user->tipo_utente !== 'consulente') {
            return response()->json(['success' => false, 'message' => 'Accesso negato'], 403);
        }

        $studio = DB::table('aa_studi')->where('user_id', $user->id)->first();
        if (!$studio) {
            return response()->json(['success' => false, 'message' => 'Studio non trovato'], 404);
        }

        return response()->json(['success' => true, 'data' => $studio]);
    }

    /**
     * Update studio
     */
    public function update(Request $request)
    {
        $user = $this->getAuthUser($request);
        if (!$user || $user->tipo_utente !== 'consulente') {
            return response()->json(['success' => false, 'message' => 'Accesso negato'], 403);
        }

        $validated = $request->validate([
            'nome' => 'sometimes|string|max:255',
            'partita_iva' => 'nullable|string|max:20',
            'codice_fiscale' => 'nullable|string|max:20',
            'indirizzo' => 'nullable|string',
            'telefono' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'sito_web' => 'nullable|url|max:255',
            'logo_url' => 'nullable|string|max:500',
            'colore_primario' => 'nullable|string|max:7',
            'notifica_kpi_critico' => 'nullable|boolean',
            'notifica_report_settimanale' => 'nullable|boolean',
            'notifica_invito_accettato' => 'nullable|boolean',
            'notifica_scadenze' => 'nullable|boolean',
        ]);

        // Map frontend field name to DB field name
        if (isset($validated['partita_iva'])) {
            $validated['p_iva'] = $validated['partita_iva'];
            unset($validated['partita_iva']);
        }

        $validated['updated_at'] = now();

        DB::table('aa_studi')
            ->where('user_id', $user->id)
            ->update($validated);

        $studio = DB::table('aa_studi')->where('user_id', $user->id)->first();

        return response()->json(['success' => true, 'data' => $studio]);
    }

    /**
     * Get aggregated stats for studio
     */
    public function stats(Request $request)
    {
        $user = $this->getAuthUser($request);
        if (!$user || $user->tipo_utente !== 'consulente') {
            return response()->json(['success' => false, 'message' => 'Accesso negato'], 403);
        }

        $studio = DB::table('aa_studi')->where('user_id', $user->id)->first();
        if (!$studio) {
            return response()->json(['success' => false, 'message' => 'Studio non trovato'], 404);
        }

        $totaleAziende = DB::table('aa_aziende_cliente')
            ->where('studio_id', $studio->id)
            ->where('attiva', true)
            ->count();

        $aziende = DB::table('aa_aziende_cliente')
            ->where('studio_id', $studio->id)
            ->where('attiva', true)
            ->get();

        $kpiVerdi = 0;
        $kpiGialli = 0;
        $kpiRossi = 0;

        foreach ($aziende as $azienda) {
            $kpi = $this->calcolaKpiAzienda($azienda->id);
            foreach ($kpi as $k) {
                if ($k['stato'] === 'verde') $kpiVerdi++;
                elseif ($k['stato'] === 'giallo') $kpiGialli++;
                elseif ($k['stato'] === 'rosso') $kpiRossi++;
            }
        }

        $inviti = DB::table('aa_inviti_cliente as i')
            ->join('aa_aziende_cliente as a', 'i.azienda_cliente_id', '=', 'a.id')
            ->where('a.studio_id', $studio->id)
            ->count();

        return response()->json([
            'success' => true,
            'data' => [
                'totale_aziende' => $totaleAziende,
                'kpi_verdi' => $kpiVerdi,
                'kpi_gialli' => $kpiGialli,
                'kpi_rossi' => $kpiRossi,
                'inviti_inviati' => $inviti,
            ]
        ]);
    }

    private function getAuthUser(Request $request)
    {
        $token = $request->bearerToken();
        if (!$token) return null;

        return DB::table('aa_users')
            ->where('remember_token', $token)
            ->first();
    }

    /**
     * Upload studio logo
     */
    public function uploadLogo(Request $request)
    {
        $user = $this->getAuthUser($request);
        if (!$user || $user->tipo_utente !== 'consulente') {
            return response()->json(['success' => false, 'message' => 'Accesso negato'], 403);
        }

        $request->validate([
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif|max:1024'
        ]);

        $studio = DB::table('aa_studi')->where('user_id', $user->id)->first();
        if (!$studio) {
            return response()->json(['success' => false, 'message' => 'Studio non trovato'], 404);
        }

        $file = $request->file('logo');
        $filename = 'studio_' . $studio->id . '_' . time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('logos', $filename, 'public');

        $url = '/storage/' . $path;

        DB::table('aa_studi')
            ->where('id', $studio->id)
            ->update(['logo_url' => $url, 'updated_at' => now()]);

        return response()->json([
            'success' => true,
            'data' => ['url' => $url]
        ]);
    }

    /**
     * Regenerate API key for studio
     */
    public function regenerateApiKey(Request $request)
    {
        $user = $this->getAuthUser($request);
        if (!$user || $user->tipo_utente !== 'consulente') {
            return response()->json(['success' => false, 'message' => 'Accesso negato'], 403);
        }

        $studio = DB::table('aa_studi')->where('user_id', $user->id)->first();
        if (!$studio) {
            return response()->json(['success' => false, 'message' => 'Studio non trovato'], 404);
        }

        $newApiKey = 'aa_sk_' . bin2hex(random_bytes(24));

        DB::table('aa_studi')
            ->where('id', $studio->id)
            ->update(['api_key' => $newApiKey, 'updated_at' => now()]);

        return response()->json([
            'success' => true,
            'data' => ['api_key' => $newApiKey]
        ]);
    }

    /**
     * Delete all data for studio (danger zone)
     */
    public function deleteAllData(Request $request)
    {
        $user = $this->getAuthUser($request);
        if (!$user || $user->tipo_utente !== 'consulente') {
            return response()->json(['success' => false, 'message' => 'Accesso negato'], 403);
        }

        $studio = DB::table('aa_studi')->where('user_id', $user->id)->first();
        if (!$studio) {
            return response()->json(['success' => false, 'message' => 'Studio non trovato'], 404);
        }

        // Get all aziende cliente for this studio
        $aziendeIds = DB::table('aa_aziende_cliente')
            ->where('studio_id', $studio->id)
            ->pluck('id');

        // Delete dati economici
        DB::table('aa_dati_economici_cliente')
            ->whereIn('azienda_cliente_id', $aziendeIds)
            ->delete();

        // Delete inviti
        DB::table('aa_inviti_cliente')
            ->whereIn('azienda_cliente_id', $aziendeIds)
            ->delete();

        // Delete aziende cliente
        DB::table('aa_aziende_cliente')
            ->where('studio_id', $studio->id)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Tutti i dati sono stati eliminati'
        ]);
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

        // PN - Patrimonio Netto
        $pn = $dati->patrimonio_netto ?? 0;
        $kpi[] = [
            'codice' => 'PN',
            'nome' => 'Patrimonio Netto',
            'valore' => $pn,
            'stato' => $pn > 0 ? 'verde' : ($pn >= -10000 ? 'giallo' : 'rosso')
        ];

        // DSCR - Debt Service Coverage Ratio
        if ($dati->cash_flow_operativo && $dati->rata_debiti && $dati->rata_debiti > 0) {
            $dscr = $dati->cash_flow_operativo / $dati->rata_debiti;
            $kpi[] = [
                'codice' => 'DSCR',
                'nome' => 'DSCR',
                'valore' => round($dscr, 2),
                'stato' => $dscr >= 1.1 ? 'verde' : ($dscr >= 1 ? 'giallo' : 'rosso')
            ];
        }

        // CR - Current Ratio (semplificato)
        if ($dati->totale_attivo && $dati->totale_debiti && $dati->totale_debiti > 0) {
            $cr = $dati->totale_attivo / $dati->totale_debiti;
            $kpi[] = [
                'codice' => 'CR',
                'nome' => 'Current Ratio',
                'valore' => round($cr, 2),
                'stato' => $cr >= 1.5 ? 'verde' : ($cr >= 1 ? 'giallo' : 'rosso')
            ];
        }

        return $kpi;
    }
}
