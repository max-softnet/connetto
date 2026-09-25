<?php

namespace App\Services;

use App\Exceptions\DestinatarioMancanteException;
use App\Models\Appuntamento;
use App\Models\Automazione;
use App\Models\LogAutomazione;
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
     * Esegue le automazioni attive.
     *
     * Ogni automazione può essere eseguita una sola volta al giorno: se è già presente
     * un record nel registro per la giornata corrente viene saltata, a meno che $forza
     * non sia true (esecuzione manuale con conferma esplicita dell'utente).
     *
     * @return array<int, array{automazione: string, appuntamento: ?string, esito: string}>
     */
    public function esegui(string $origine = 'automatica', bool $forza = false, ?int $soloAutomazioneId = null): array
    {
        $risultati = [];

        $query = Automazione::where('attiva', true)->with('modello');

        if ($soloAutomazioneId) {
            $query->where('id', $soloAutomazioneId);
        }

        $automazioni = $query->get();

        foreach ($automazioni as $automazione) {
            $eseguitaOggi = $automazione->eseguitaOggi();

            if ($eseguitaOggi && ! $forza) {
                $risultati[] = [
                    'automazione' => $automazione->nome,
                    'appuntamento' => null,
                    'esito' => "saltata: già eseguita oggi alle {$eseguitaOggi->eseguita_at->format('H:i')}",
                ];

                continue;
            }

            $risultati = array_merge($risultati, $this->eseguiUnaAutomazione($automazione, $origine));
        }

        return $risultati;
    }

    /**
     * @return array<int, array{automazione: string, appuntamento: ?string, esito: string}>
     */
    private function eseguiUnaAutomazione(Automazione $automazione, string $origine): array
    {
        $risultati = [];
        $inviati = 0;
        $falliti = 0;
        $bozze = 0;
        $giaElaborati = 0;

        $data = Carbon::today()->addDays($automazione->giorni_prima);
        $modello = $automazione->modello;

        $query = Appuntamento::where('status', 'confermato')
            ->whereDate('data', $data->toDateString());

        if ($automazione->tipo_appuntamento) {
            $query->where('tipo', $automazione->tipo_appuntamento);
        }

        if ($automazione->operatore) {
            $query->where('operatore', $automazione->operatore);
        }

        $appuntamenti = $query->get();

        if ($appuntamenti->isEmpty()) {
            $risultati[] = [
                'automazione' => $automazione->nome,
                'appuntamento' => null,
                'esito' => "nessun appuntamento per il {$data->format('d/m/Y')}",
            ];

            $this->registra($automazione, $origine, 'nessun_appuntamento', "nessun appuntamento per il {$data->format('d/m/Y')}");

            return $risultati;
        }

        foreach ($appuntamenti as $appuntamento) {
            $giaInviato = Messaggio::where('appuntamento_id', $appuntamento->id)
                ->where('modello_id', $modello->id)
                ->where('origine', 'automatico')
                ->exists();

            if ($giaInviato) {
                $giaElaborati++;
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
                $falliti++;
                $risultati[] = [
                    'automazione' => $automazione->nome,
                    'appuntamento' => $appuntamento->titolo,
                    'esito' => $e->getMessage(),
                ];

                continue;
            }

            if ($modello->canale === 'whatsapp') {
                $successo = $this->whatsAppSender->invia($messaggio);
                $successo ? $inviati++ : $falliti++;
                $esito = $successo ? 'inviato' : 'fallito — ' . $messaggio->fresh()->errore;
            } else {
                $bozze++;
                $esito = "messaggio creato come bozza (invio automatico non ancora disponibile per {$modello->canale})";
            }

            $risultati[] = [
                'automazione' => $automazione->nome,
                'appuntamento' => $appuntamento->titolo,
                'esito' => $esito,
            ];
        }

        if ($falliti > 0 && $inviati === 0 && $bozze === 0) {
            $esitoComplessivo = 'fallito';
        } elseif ($falliti > 0) {
            $esitoComplessivo = 'parziale';
        } else {
            $esitoComplessivo = 'successo';
        }

        $dettagli = "{$inviati} inviati, {$falliti} falliti, {$bozze} in bozza, {$giaElaborati} già elaborati";

        $this->registra($automazione, $origine, $esitoComplessivo, $dettagli);

        return $risultati;
    }

    private function registra(Automazione $automazione, string $origine, string $esito, string $dettagli): void
    {
        LogAutomazione::create([
            'automazione_id' => $automazione->id,
            'eseguita_at' => now(),
            'origine' => $origine,
            'esito' => $esito,
            'dettagli' => $dettagli,
        ]);
    }
}
