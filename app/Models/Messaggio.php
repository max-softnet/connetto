<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Messaggio extends Model
{
    protected $table = 'messaggi';

    protected $fillable = [
        'appuntamento_id',
        'modello_id',
        'canale',
        'destinatario',
        'oggetto',
        'corpo',
        'whatsapp_template_nome',
        'whatsapp_template_lingua',
        'whatsapp_parametri',
        'whatsapp_formato_parametri',
        'whatsapp_header_parametro',
        'stato',
        'errore',
        'origine',
        'inviato_at',
    ];

    protected $casts = [
        'inviato_at' => 'datetime',
        'whatsapp_parametri' => 'array',
        'whatsapp_header_parametro' => 'boolean',
    ];

    public function appuntamento()
    {
        return $this->belongsTo(Appuntamento::class);
    }

    public function modello()
    {
        return $this->belongsTo(ModelloMessaggio::class, 'modello_id');
    }
}
