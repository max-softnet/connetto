<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MessaggioWhatsapp extends Model
{
    protected $table = 'whatsapp_thread_messaggi';

    protected $fillable = [
        'conversazione_id',
        'direzione',
        'corpo',
        'wamid',
        'stato',
    ];

    public function conversazione()
    {
        return $this->belongsTo(ConversazioneWhatsapp::class, 'conversazione_id');
    }
}
