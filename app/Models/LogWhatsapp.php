<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogWhatsapp extends Model
{
    protected $table = 'log_whatsapp';

    protected $fillable = [
        'messaggio_id',
        'endpoint',
        'richiesta',
        'risposta_status',
        'risposta',
        'esito',
    ];

    protected $casts = [
        'richiesta' => 'array',
        'risposta' => 'array',
    ];

    public function messaggio()
    {
        return $this->belongsTo(Messaggio::class);
    }
}
