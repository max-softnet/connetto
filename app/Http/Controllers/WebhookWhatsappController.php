<?php

namespace App\Http\Controllers;

use App\Models\ConversazioneWhatsapp;
use App\Models\Impostazione;
use App\Models\MessaggioWhatsapp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookWhatsappController extends Controller
{
    /**
     * Handshake di verifica richiesto da Meta quando si configura il webhook.
     */
    public function verifica(Request $request)
    {
        $modo = $request->query('hub_mode');
        $token = (string) $request->query('hub_verify_token');
        $challenge = $request->query('hub_challenge');

        $tokenAtteso = (string) Impostazione::corrente()->whatsapp_webhook_verify_token;

        if ($modo === 'subscribe' && $tokenAtteso !== '' && hash_equals($tokenAtteso, $token)) {
            return response($challenge, 200);
        }

        return response('Verifica fallita.', 403);
    }

    /**
     * Riceve gli eventi (messaggi in arrivo, aggiornamenti di stato).
     */
    public function ricevi(Request $request)
    {
        try {
            foreach ($request->input('entry', []) as $entry) {
                foreach ($entry['changes'] ?? [] as $change) {
                    $value = $change['value'] ?? [];

                    foreach ($value['messages'] ?? [] as $messaggioIn) {
                        $this->gestisciMessaggioInArrivo($messaggioIn, $value['contacts'][0] ?? null);
                    }

                    foreach ($value['statuses'] ?? [] as $stato) {
                        $this->gestisciAggiornamentoStato($stato);
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::error('Errore elaborazione webhook WhatsApp: ' . $e->getMessage(), ['payload' => $request->all()]);
        }

        // Rispondere sempre 200: se rispondiamo con un errore, Meta continua a ritentare la consegna.
        return response()->json(['status' => 'ok']);
    }

    private function gestisciMessaggioInArrivo(array $messaggioIn, ?array $contatto): void
    {
        $wamid = $messaggioIn['id'] ?? null;
        $numero = $messaggioIn['from'] ?? null;

        if (! $wamid || ! $numero) {
            return;
        }

        $conversazione = ConversazioneWhatsapp::firstOrCreate(['numero' => $numero]);

        $nomeContatto = $contatto['profile']['name'] ?? null;

        if ($nomeContatto && $conversazione->nome_contatto !== $nomeContatto) {
            $conversazione->update(['nome_contatto' => $nomeContatto]);
        }

        $testo = $messaggioIn['text']['body'] ?? match ($messaggioIn['type'] ?? null) {
            'button' => $messaggioIn['button']['text'] ?? '[risposta a bottone]',
            'interactive' => $messaggioIn['interactive']['button_reply']['title']
                ?? $messaggioIn['interactive']['list_reply']['title']
                ?? '[risposta interattiva]',
            default => '[messaggio non testuale: ' . ($messaggioIn['type'] ?? 'sconosciuto') . ']',
        };

        MessaggioWhatsapp::firstOrCreate(
            ['wamid' => $wamid],
            [
                'conversazione_id' => $conversazione->id,
                'direzione' => 'in',
                'corpo' => $testo,
                'stato' => 'ricevuto',
            ]
        );

        $conversazione->update(['ultimo_messaggio_at' => now()]);
    }

    private function gestisciAggiornamentoStato(array $stato): void
    {
        $wamid = $stato['id'] ?? null;

        if (! $wamid) {
            return;
        }

        MessaggioWhatsapp::where('wamid', $wamid)->update([
            'stato' => $stato['status'] ?? null,
        ]);
    }
}
