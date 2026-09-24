<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogAutomazione extends Model
{
    protected $table = 'log_automazioni';

    protected $fillable = [
        'automazione_id',
        'eseguita_at',
        'origine',
        'esito',
        'dettagli',
    ];

    protected $casts = [
        'eseguita_at' => 'datetime',
    ];

    public function automazione()
    {
        return $this->belongsTo(Automazione::class);
    }
}
