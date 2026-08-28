<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appuntamento extends Model
{
    protected $table = 'appuntamenti';

    protected $fillable = [
        'google_event_id',
        'filemaker_id',
        'filemaker_persona_id',
        'titolo',
        'descrizione',
        'email',
        'cellulare',
        'tipo',
        'operatore',
        'data',
        'ora_inizio',
        'ora_fine',
        'status',
    ];

    protected $casts = [
        'data' => 'date',
    ];

    public function tipoAppuntamento()
    {
        return $this->belongsTo(TipoAppuntamento::class, 'tipo', 'nome');
    }

    public function operatoreAppuntamento()
    {
        return $this->belongsTo(Operatore::class, 'operatore', 'nome');
    }

    public function messaggi()
    {
        return $this->hasMany(Messaggio::class);
    }
}
