<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdeguatiAssettiKpi extends Model
{
    // Tabella con prefisso aa_ per Adeguati Assetti
    protected $table = 'aa_kpi';

    protected $fillable = [
        'id_centro',
        'codice',
        'nome',
        'descrizione',
        'formula',
        'categoria',
        'soglia_verde',
        'soglia_gialla',
        'soglia_rossa',
        'direzione',
        'unita_misura',
        'frequenza_calcolo',
        'attivo',
        'ordine',
    ];

    protected $casts = [
        'soglia_verde' => 'decimal:4',
        'soglia_gialla' => 'decimal:4',
        'soglia_rossa' => 'decimal:4',
        'attivo' => 'boolean',
    ];

    public function valori()
    {
        return $this->hasMany(AdeguatiAssettiValore::class, 'id_kpi');
    }

    public function alerts()
    {
        return $this->hasMany(AdeguatiAssettiAlert::class, 'id_kpi');
    }

    /**
     * Scope per KPI attivi
     */
    public function scopeAttivi($query)
    {
        return $query->where('attivo', true);
    }

    /**
     * Scope per centro
     */
    public function scopePerCentro($query, int $centroId)
    {
        return $query->where('id_centro', $centroId);
    }

    /**
     * Scope per categoria
     */
    public function scopeObbligatori($query)
    {
        return $query->where('categoria', 'obbligatorio');
    }

    public function scopeSettoriali($query)
    {
        return $query->where('categoria', 'settoriale');
    }
}
