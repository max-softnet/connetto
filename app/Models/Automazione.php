<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
