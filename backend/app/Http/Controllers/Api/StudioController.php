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
            'p_iva' => 'nullable|string|max:20',
            'indirizzo' => 'nullable|string',
            'telefono' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'logo_url' => 'nullable|string|max:500',
            'colore_primario' => 'nullable|string|max:7',
        ]);

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
