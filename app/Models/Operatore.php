<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Operatore extends Model
{
    protected $table = 'operatori';

    protected $fillable = [
        'nome',
        'colore',
        'abilitato_automazioni',
    ];

    protected $casts = [
        'abilitato_automazioni' => 'boolean',
    ];

    public function appuntamenti()
    {
        return $this->hasMany(Appuntamento::class, 'operatore', 'nome');
    }
}
