<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoAppuntamento extends Model
{
    protected $table = 'tipi_appuntamento';

    protected $fillable = [
        'nome',
        'colore',
    ];

    public function appuntamenti()
    {
        return $this->hasMany(Appuntamento::class, 'tipo', 'nome');
    }
}
