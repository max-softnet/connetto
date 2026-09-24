<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConversazioneWhatsapp extends Model
{
    protected $table = 'whatsapp_conversazioni';

    protected $fillable = [
        'numero',
        'nome_contatto',
        'ultimo_messaggio_at',
    ];

    protected $casts = [
        'ultimo_messaggio_at' => 'datetime',
    ];

    public function messaggi()
    {
        return $this->hasMany(MessaggioWhatsapp::class, 'conversazione_id')->orderBy('created_at');
    }

    /**
     * Ultimo appuntamento collegato a questo numero, se presente, per
     * mostrare subito chi ha scritto (confronto sulle ultime 9 cifre,
     * per non dipendere dal formato con cui è salvato il cellulare).
     */
    public function appuntamentoCollegato(): ?Appuntamento
    {
        $ultimeCifre = substr(preg_replace('/[^0-9]/', '', $this->numero), -9);

        if (strlen($ultimeCifre) < 9) {
            return null;
        }

        return Appuntamento::where('cellulare', 'like', "%{$ultimeCifre}")
            ->orderByDesc('data')
            ->first();
    }
}
