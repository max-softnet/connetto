<?php

namespace App\Services;

use App\Models\Impostazione;
use App\Models\LogWhatsapp;
use App\Models\Messaggio;
use Illuminate\Support\Facades\Http;

class WhatsAppSender
{
    private const VERSIONE_API = 'v21.0';

    public function invia(Messaggio $messaggio): bool
    {
        if ($messaggio->canale !== 'whatsapp') {
            throw new \InvalidArgumentException('Questo servizio invia solo messaggi con canale whatsapp.');
        }

        $impostazioni = Impostazione::corrente();

        if (! $impostazioni->whatsapp_token || ! $impostazioni->whatsapp_phone_number_id) {
            $messaggio->update([
                'stato' => 'fallito',
                'errore' => 'Credenziali WhatsApp non configurate. Vai in Impostazioni per inserirle.',
            ]);

            return false;
        }

        $numero = $this->normalizzaNumero($messaggio->destinatario);

        if (! $numero) {
            $messaggio->update([
                'stato' => 'fallito',
                'errore' => 'Numero di cellulare del destinatario mancante o non valido.',
            ]);

            return false;
        }

        $corpoRichiesta = $messaggio->whatsapp_template_nome
            ? $this->corpoTemplate($messaggio, $numero)
            : [
                'messaging_product' => 'whatsapp',
                'to' => $numero,
                'type' => 'text',
                'text' => ['body' => $messaggio->corpo],
            ];

        $endpoint = 'https://graph.facebook.com/' . self::VERSIONE_API . "/{$impostazioni->whatsapp_phone_number_id}/messages";

        $risposta = Http::withToken($impostazioni->whatsapp_token)->post($endpoint, $corpoRichiesta);

        LogWhatsapp::create([
            'messaggio_id' => $messaggio->id,
            'endpoint' => $endpoint,
            'richiesta' => $corpoRichiesta,
            'risposta_status' => $risposta->status(),
            'risposta' => $risposta->json(),
            'esito' => $risposta->successful() ? 'successo' : 'fallito',
        ]);

        if ($risposta->successful()) {
            $messaggio->update([
                'stato' => 'inviato',
                'errore' => null,
                'inviato_at' => now(),
            ]);

            return true;
        }

        $messaggio->update([
            'stato' => 'fallito',
            'errore' => $risposta->json('error.message') ?? 'Errore sconosciuto restituito dal servizio WhatsApp.',
        ]);

        return false;
    }

    private function corpoTemplate(Messaggio $messaggio, string $numero): array
    {
        $components = $messaggio->whatsapp_formato_parametri === 'nominale'
            ? $this->componentiNominali($messaggio)
            : $this->componentiPosizionali($messaggio);

        return [
            'messaging_product' => 'whatsapp',
            'to' => $numero,
            'type' => 'template',
            'template' => [
                'name' => $messaggio->whatsapp_template_nome,
                'language' => ['code' => strtolower($messaggio->whatsapp_template_lingua ?? 'it')],
                'components' => $components,
            ],
        ];
    }

    private function componentiPosizionali(Messaggio $messaggio): array
    {
        $parametri = collect($messaggio->whatsapp_parametri ?? [])
            ->map(fn ($valore) => ['type' => 'text', 'text' => (string) $valore])
            ->values()
            ->all();

        if (empty($parametri)) {
            return [];
        }

        return [
            ['type' => 'body', 'parameters' => $parametri],
        ];
    }

    private function componentiNominali(Messaggio $messaggio): array
    {
        $parametri = collect($messaggio->whatsapp_parametri ?? [])
            ->map(fn ($valore, $nome) => ['type' => 'text', 'parameter_name' => $nome, 'text' => (string) $valore])
            ->values()
            ->all();

        if (empty($parametri)) {
            return [];
        }

        $components = [];

        if ($messaggio->whatsapp_header_parametro) {
            $components[] = ['type' => 'header', 'parameters' => $parametri];
        }

        $components[] = ['type' => 'body', 'parameters' => $parametri];

        return $components;
    }

    private function normalizzaNumero(?string $numero): ?string
    {
        if (! $numero) {
            return null;
        }

        $pulito = preg_replace('/[^0-9]/', '', $numero);

        if ($pulito === '') {
            return null;
        }

        // Un cellulare italiano locale (senza prefisso) ha tipicamente 9-10 cifre.
        // Se non sembra già avere un prefisso internazionale, assumiamo l'Italia.
        if (! str_starts_with($pulito, '39') && strlen($pulito) <= 10) {
            $pulito = '39' . $pulito;
        }

        return $pulito;
    }
}
