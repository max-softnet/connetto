<?php

namespace App\Http\Controllers;

use App\Models\ConversazioneWhatsapp;
use App\Models\MessaggioWhatsapp;
use App\Services\WhatsAppSender;
use Illuminate\Http\Request;

class ConversazioneWhatsappWebController extends Controller
{
    public function index()
    {
        $righe = ConversazioneWhatsapp::orderByDesc('ultimo_messaggio_at')
            ->get()
            ->map(fn (ConversazioneWhatsapp $conversazione) => [
                'conversazione' => $conversazione,
                'appuntamento' => $conversazione->appuntamentoCollegato(),
                'ultimoMessaggio' => $conversazione->messaggi()->reorder('created_at', 'desc')->first(),
            ]);

        return view('whatsapp-inbox.index', compact('righe'));
    }

    public function mostra(ConversazioneWhatsapp $conversazione)
    {
        $conversazione->load('messaggi');
        $appuntamento = $conversazione->appuntamentoCollegato();

        return view('whatsapp-inbox.mostra', compact('conversazione', 'appuntamento'));
    }

    public function rispondi(Request $request, ConversazioneWhatsapp $conversazione, WhatsAppSender $whatsAppSender)
    {
        $data = $request->validate([
            'corpo' => ['required', 'string', 'max:4096'],
        ]);

        $esito = $whatsAppSender->inviaTestoLibero($conversazione->numero, $data['corpo']);

        MessaggioWhatsapp::create([
            'conversazione_id' => $conversazione->id,
            'direzione' => 'out',
            'corpo' => $data['corpo'],
            'wamid' => $esito['wamid'],
            'stato' => $esito['successo'] ? 'inviato' : 'fallito',
        ]);

        $conversazione->update(['ultimo_messaggio_at' => now()]);

        if (! $esito['successo']) {
            return back()->withErrors(['corpo' => 'Invio fallito: ' . $esito['errore']]);
        }

        return back()->with('successo', 'Risposta inviata.');
    }
}
