<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdeguatiAssettiAlert extends Model
{
    protected $table = 'aa_alert';

    protected $fillable = [
        'id_centro',
        'id_kpi',
        'tipo',
        'titolo',
        'messaggio',
        'dati',
        'anno',
        'mese',
        'letto',
        'data_lettura',
        'id_utente_lettura',
    ];

    protected $casts = [
        'dati' => 'array',
        'letto' => 'boolean',
        'data_lettura' => 'datetime',
    ];

    public function kpi()
    {
        return $this->belongsTo(AdeguatiAssettiKpiDefinizione::class, 'id_kpi');
    }

    /**
     * Scope per alert non letti
     */
    public function scopeNonLetti($query)
    {
        return $query->where('letto', false);
    }

    /**
     * Scope per centro
     */
    public function scopePerCentro($query, int $centroId)
    {
        return $query->where('id_centro', $centroId);
    }

    /**
     * Scope per livello critico
     */
    public function scopeCritici($query)
    {
        return $query->where('tipo', 'critical');
    }

    /**
     * Segna come letto
     */
    public function segnaLetto(int $userId): void
    {
        $this->update([
            'letto' => true,
            'id_utente_lettura' => $userId,
            'data_lettura' => now(),
        ]);
    }

    /**
     * Accessor per colore badge
     */
    public function getColoreAttribute(): string
    {
        return $this->tipo === 'critical' ? '#ef4444' : '#f59e0b';
    }
}
