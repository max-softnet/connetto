<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Automazione extends Model
{
    protected $table = 'automazioni';

    protected $fillable = [
        'nome',
        'modello_id',
        'giorni_prima',
        'tipo_appuntamento',
        'attiva',
    ];

    protected $casts = [
        'attiva' => 'boolean',
    ];

    public function modello()
    {
        return $this->belongsTo(ModelloMessaggio::class, 'modello_id');
    }

    public function tipoAppuntamento()
    {
        return $this->belongsTo(TipoAppuntamento::class, 'tipo_appuntamento', 'nome');
    }

    public function logEsecuzioni()
    {
        return $this->hasMany(LogAutomazione::class);
    }

    /**
     * L'ultima esecuzione registrata per oggi, se presente.
     */
    public function eseguitaOggi(): ?LogAutomazione
    {
        return $this->logEsecuzioni()
            ->whereDate('eseguita_at', Carbon::today())
            ->latest('eseguita_at')
            ->first();
    }
}
