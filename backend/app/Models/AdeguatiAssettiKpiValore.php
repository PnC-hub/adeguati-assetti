<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdeguatiAssettiKpiValore extends Model
{
    protected $table = 'aa_kpi_valori';

    protected $fillable = [
        'id_centro',
        'id_kpi',
        'anno',
        'mese',
        'valore',
        'stato',
        'dettaglio_calcolo',
        'data_calcolo',
    ];

    protected $casts = [
        'valore' => 'decimal:4',
        'dettaglio_calcolo' => 'array',
        'data_calcolo' => 'datetime',
    ];

    // ==================== RELAZIONI ====================

    public function kpi()
    {
        return $this->belongsTo(AdeguatiAssettiKpiDefinizione::class, 'id_kpi');
    }

    // ==================== SCOPES ====================

    public function scopeDelCentro($query, int $centroId)
    {
        return $query->where('id_centro', $centroId);
    }

    public function scopeDelPeriodo($query, int $anno, int $mese)
    {
        return $query->where('anno', $anno)->where('mese', $mese);
    }

    public function scopeVerdi($query)
    {
        return $query->where('stato', 'verde');
    }

    public function scopeGialli($query)
    {
        return $query->where('stato', 'giallo');
    }

    public function scopeRossi($query)
    {
        return $query->where('stato', 'rosso');
    }

    // ==================== ACCESSORS ====================

    public function getValoreFormattato(): string
    {
        if (!$this->kpi) {
            return (string) $this->valore;
        }
        return $this->kpi->formattaValore($this->valore);
    }

    public function getStatoIcon(): string
    {
        $icons = [
            'verde' => '🟢',
            'giallo' => '🟡',
            'rosso' => '🔴',
        ];
        return $icons[$this->stato] ?? '⚪';
    }
}
