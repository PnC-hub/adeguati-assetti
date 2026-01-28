<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdeguatiAssettiKpiDefinizione extends Model
{
    protected $table = 'aa_kpi_definizioni';

    protected $fillable = [
        'codice',
        'nome',
        'descrizione',
        'categoria',
        'tipo_valore',
        'formula',
        'soglia_verde',
        'soglia_gialla',
        'soglia_rossa',
        'direzione',
        'attivo',
        'ordine',
    ];

    protected $casts = [
        'soglia_verde' => 'decimal:4',
        'soglia_gialla' => 'decimal:4',
        'soglia_rossa' => 'decimal:4',
        'attivo' => 'boolean',
    ];

    // ==================== RELAZIONI ====================

    public function valori()
    {
        return $this->hasMany(AdeguatiAssettiKpiValore::class, 'id_kpi');
    }

    public function alert()
    {
        return $this->hasMany(AdeguatiAssettiAlert::class, 'id_kpi');
    }

    // ==================== SCOPES ====================

    public function scopeAttivi($query)
    {
        return $query->where('attivo', true);
    }

    public function scopeObbligatori($query)
    {
        return $query->where('categoria', 'obbligatorio');
    }

    public function scopeSettoriali($query)
    {
        return $query->where('categoria', 'settoriale');
    }

    public function scopeOperativi($query)
    {
        return $query->where('categoria', 'operativo');
    }

    public function scopeOrdinati($query)
    {
        return $query->orderBy('ordine');
    }

    // ==================== METODI ====================

    /**
     * Calcola lo stato (verde/giallo/rosso) per un valore
     */
    public function calcolaStato(float $valore): string
    {
        if ($this->direzione === 'maggiore') {
            // Verde se >= soglia_verde
            if ($valore >= $this->soglia_verde) {
                return 'verde';
            }
            if ($valore >= $this->soglia_gialla) {
                return 'giallo';
            }
            return 'rosso';
        } else {
            // Verde se <= soglia_verde (per KPI dove meno è meglio)
            if ($valore <= $this->soglia_verde) {
                return 'verde';
            }
            if ($valore <= $this->soglia_gialla) {
                return 'giallo';
            }
            return 'rosso';
        }
    }

    /**
     * Formatta il valore per la visualizzazione
     */
    public function formattaValore(float $valore): string
    {
        switch ($this->tipo_valore) {
            case 'percentuale':
                return number_format($valore, 1) . '%';
            case 'ratio':
                return number_format($valore, 2);
            case 'importo':
                return '€ ' . number_format($valore, 0, ',', '.');
            case 'giorni':
                return (int) $valore . ' gg';
            case 'numero':
                return number_format($valore, 0, ',', '.');
            default:
                return (string) $valore;
        }
    }
}
