<?php

namespace App\Services;

use App\Exceptions\DestinatarioMancanteException;
use App\Models\Appuntamento;
use App\Models\Automazione;
use App\Models\Messaggio;
use Illuminate\Support\Carbon;

class EsecutoreAutomazioni
{
    public function __construct(
        private ComponitoreMessaggio $componitore,
        private WhatsAppSender $whatsAppSender,
    ) {
    }

    /**
     * Esegue tutte le automazioni attive.
     *
     * @return array<int, array{automazione: string, appuntamento: ?string, esito: string}>
     */
    public function esegui(): array
    {
        $risultati = [];

        $automazioni = Automazione::where('attiva', true)->with('modello')->get();

        foreach ($automazioni as $automazione) {
            $data = Carbon::today()->addDays($automazione->giorni_prima);
            $modello = $automazione->modello;

            $query = Appuntamento::where('status', 'confermato')
                ->whereDate('data', $data->toDateString());

            if ($automazione->tipo_appuntamento) {
                $query->where('tipo', $automazione->tipo_appuntamento);
            }

            $appuntamenti = $query->get();

            if ($appuntamenti->isEmpty()) {
                $risultati[] = [
                    'automazione' => $automazione->nome,
                    'appuntamento' => null,
                    'esito' => "nessun appuntamento per il {$data->format('d/m/Y')}",
                ];

                continue;
            }

            foreach ($appuntamenti as $appuntamento) {
                $giaInviato = Messaggio::where('appuntamento_id', $appuntamento->id)
                    ->where('modello_id', $modello->id)
                    ->where('origine', 'automatico')
                    ->exists();

                if ($giaInviato) {
                    $risultati[] = [
                        'automazione' => $automazione->nome,
                        'appuntamento' => $appuntamento->titolo,
                        'esito' => 'già elaborato in precedenza, saltato',
                    ];

                    continue;
                }

                try {
                    $messaggio = $this->componitore->componi($appuntamento, $modello, origine: 'automatico');
                } catch (DestinatarioMancanteException $e) {
                    $risultati[] = [
                        'automazione' => $automazione->nome,
                        'appuntamento' => $appuntamento->titolo,
                        'esito' => $e->getMessage(),
                    ];

                    continue;
                }

                if ($modello->canale === 'whatsapp') {
                    $successo = $this->whatsAppSender->invia($messaggio);
                    $esito = $successo ? 'inviato' : 'fallito — ' . $messaggio->fresh()->errore;
                } else {
                    $esito = "messaggio creato come bozza (invio automatico non ancora disponibile per {$modello->canale})";
                }

                $risultati[] = [
                    'automazione' => $automazione->nome,
                    'appuntamento' => $appuntamento->titolo,
                    'esito' => $esito,
                ];
            }
        }

        return $risultati;
    }
}
