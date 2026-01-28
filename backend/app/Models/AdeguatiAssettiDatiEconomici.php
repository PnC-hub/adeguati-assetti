<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdeguatiAssettiDatiEconomici extends Model
{
    protected $table = 'aa_dati_economici';

    protected $fillable = [
        'id_centro',
        'anno',
        'mese',
        // Stato Patrimoniale - Attivo
        'attivo_circolante',
        'crediti_commerciali',
        'crediti_vs_clienti',
        'disponibilita_liquide',
        'rimanenze',
        'immobilizzazioni',
        'totale_attivo',
        // Stato Patrimoniale - Passivo
        'patrimonio_netto',
        'capitale_sociale',
        'utile_esercizio',
        'debiti_totali',
        'debiti_finanziari',
        'debiti_breve_termine',
        'debiti_medio_lungo',
        'debiti_tributari',
        'debiti_vs_fornitori',
        'tfr',
        // Conto Economico
        'ricavi_vendite',
        'altri_ricavi',
        'totale_ricavi',
        'costi_materie_prime',
        'costi_servizi',
        'costi_personale',
        'ammortamenti',
        'altri_costi',
        'oneri_finanziari',
        'ebitda',
        'ebit',
        'risultato_ante_imposte',
        'imposte',
        'risultato_netto',
        // Cash Flow
        'cash_flow_operativo',
        'cash_flow_investimenti',
        'cash_flow_finanziamenti',
        'free_cash_flow',
        // Debiti Scaduti
        'debiti_scaduti_fornitori',
        'debiti_scaduti_dipendenti',
        'debiti_scaduti_fisco',
        'debiti_scaduti_banche',
        // Meta
        'note',
        'fonte',
        'id_operatore',
    ];

    protected $casts = [
        'attivo_circolante' => 'decimal:2',
        'crediti_commerciali' => 'decimal:2',
        'crediti_vs_clienti' => 'decimal:2',
        'disponibilita_liquide' => 'decimal:2',
        'rimanenze' => 'decimal:2',
        'immobilizzazioni' => 'decimal:2',
        'totale_attivo' => 'decimal:2',
        'patrimonio_netto' => 'decimal:2',
        'capitale_sociale' => 'decimal:2',
        'utile_esercizio' => 'decimal:2',
        'debiti_totali' => 'decimal:2',
        'debiti_finanziari' => 'decimal:2',
        'debiti_breve_termine' => 'decimal:2',
        'debiti_medio_lungo' => 'decimal:2',
        'debiti_tributari' => 'decimal:2',
        'debiti_vs_fornitori' => 'decimal:2',
        'tfr' => 'decimal:2',
        'ricavi_vendite' => 'decimal:2',
        'altri_ricavi' => 'decimal:2',
        'totale_ricavi' => 'decimal:2',
        'costi_materie_prime' => 'decimal:2',
        'costi_servizi' => 'decimal:2',
        'costi_personale' => 'decimal:2',
        'ammortamenti' => 'decimal:2',
        'altri_costi' => 'decimal:2',
        'oneri_finanziari' => 'decimal:2',
        'ebitda' => 'decimal:2',
        'ebit' => 'decimal:2',
        'risultato_ante_imposte' => 'decimal:2',
        'imposte' => 'decimal:2',
        'risultato_netto' => 'decimal:2',
        'cash_flow_operativo' => 'decimal:2',
        'cash_flow_investimenti' => 'decimal:2',
        'cash_flow_finanziamenti' => 'decimal:2',
        'free_cash_flow' => 'decimal:2',
        'debiti_scaduti_fornitori' => 'decimal:2',
        'debiti_scaduti_dipendenti' => 'decimal:2',
        'debiti_scaduti_fisco' => 'decimal:2',
        'debiti_scaduti_banche' => 'decimal:2',
    ];

    /**
     * Scope per periodo
     */
    public function scopePeriodo($query, int $anno, int $mese)
    {
        return $query->where('anno', $anno)->where('mese', $mese);
    }

    /**
     * Scope per centro
     */
    public function scopePerCentro($query, int $centroId)
    {
        return $query->where('id_centro', $centroId);
    }

    /**
     * Accessor per totale debiti scaduti
     */
    public function getDebitiScadutiTotaliAttribute(): float
    {
        return ($this->debiti_scaduti_fornitori ?? 0)
            + ($this->debiti_scaduti_dipendenti ?? 0)
            + ($this->debiti_scaduti_fisco ?? 0)
            + ($this->debiti_scaduti_banche ?? 0);
    }
}
