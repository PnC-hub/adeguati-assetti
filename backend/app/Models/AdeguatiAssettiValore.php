<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdeguatiAssettiValore extends Model
{
    // Tabella con prefisso aa_ per Adeguati Assetti
    protected $table = 'aa_valori';

    public $timestamps = false;

    protected $fillable = [
        'id_centro',
        'id_kpi',
        'periodo_anno',
        'periodo_mese',
        'valore',
        'stato',
        'delta_precedente',
        'delta_anno_precedente',
        'note',
        'calcolato_il',
        'calcolato_da',
    ];

    protected $casts = [
        'valore' => 'decimal:4',
        'delta_precedente' => 'decimal:4',
        'delta_anno_precedente' => 'decimal:4',
        'calcolato_il' => 'datetime',
    ];

    public function kpi()
    {
        return $this->belongsTo(AdeguatiAssettiKpi::class, 'id_kpi');
    }

    /**
     * Scope per periodo
     */
    public function scopePeriodo($query, int $anno, int $mese)
    {
        return $query->where('periodo_anno', $anno)->where('periodo_mese', $mese);
    }

    /**
     * Scope per centro
     */
    public function scopePerCentro($query, int $centroId)
    {
        return $query->where('id_centro', $centroId);
    }

    /**
     * Accessor per colore semaforo (PHP 7.4 compatible)
     */
    public function getColoreAttribute(): string
    {
        switch ($this->stato) {
            case 'verde':
                return '#10b981';
            case 'giallo':
                return '#f59e0b';
            case 'rosso':
                return '#ef4444';
            default:
                return '#9ca3af';
        }
    }

    /**
     * Accessor per icona stato (PHP 7.4 compatible)
     */
    public function getIconaAttribute(): string
    {
        switch ($this->stato) {
            case 'verde':
                return 'check-circle';
            case 'giallo':
                return 'exclamation-triangle';
            case 'rosso':
                return 'times-circle';
            default:
                return 'question-circle';
        }
    }
}
