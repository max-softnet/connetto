<?php

namespace App\Services;

use App\Models\Appuntamento;
use App\Models\Operatore;
use App\Models\TipoAppuntamento;
use Illuminate\Support\Carbon;

class FileMakerAppuntamentoImporter
{
    public const TIPO_DEFAULT = 'Importato da FileMaker';

    private const PALETTE_OPERATORI = [
        '#0d6efd', '#198754', '#dc3545', '#fd7e14', '#6f42c1',
        '#20c997', '#d63384', '#0dcaf0', '#ffc107', '#6610f2',
        '#495057', '#e83e8c',
    ];

    public function importa(array $righe): array
    {
        TipoAppuntamento::firstOrCreate(
            ['nome' => self::TIPO_DEFAULT],
            ['colore' => '#6c757d']
        );

        $creati = 0;
        $aggiornati = 0;
        $saltati = [];

        foreach ($righe as $riga) {
            $esito = $this->importaRiga($riga);

            if ($esito === 'creato') {
                $creati++;
            } elseif ($esito === 'aggiornato') {
                $aggiornati++;
            } else {
                $saltati[] = [
                    'id' => $riga['id'] ?? null,
                    'motivo' => $esito,
                ];
            }
        }

        $idPresenti = array_values(array_filter(array_column($righe, 'id'), fn ($id) => $id !== null && $id !== ''));

        // Se l'invio arriva vuoto o senza id validi, non tocchiamo nulla: evita di
        // annullare in massa tutti gli appuntamenti futuri per un payload anomalo.
        $cancellati = 0;

        if (! empty($idPresenti)) {
            $cancellati = Appuntamento::whereNotNull('filemaker_id')
                ->whereNotIn('filemaker_id', $idPresenti)
                ->whereDate('data', '>=', Carbon::today())
                ->where('status', '!=', 'annullato')
                ->update(['status' => 'annullato']);
        }

        return [
            'creati' => $creati,
            'aggiornati' => $aggiornati,
            'cancellati' => $cancellati,
            'saltati' => $saltati,
        ];
    }

    private function importaRiga(array $riga): string
    {
        foreach (['id', 'title', 'sday', 'stime', 'eday', 'etime'] as $campo) {
            if (! array_key_exists($campo, $riga) || $riga[$campo] === null || $riga[$campo] === '') {
                return "campo obbligatorio mancante: {$campo}";
            }
        }

        if ((int) $riga['sday'] !== (int) $riga['eday']) {
            return 'appuntamento a cavallo di più giorni non supportato';
        }

        $data = $this->serialeAData((int) $riga['sday']);

        if (! $data) {
            return 'giorno seriale non valido';
        }

        $cancellato = filter_var($riga['cancellato'] ?? false, FILTER_VALIDATE_BOOLEAN);

        $datiSync = [
            'titolo' => $riga['title'],
            'descrizione' => $riga['descrizione'] ?: null,
            'email' => ($riga['mail'] ?? null) ?: null,
            'cellulare' => ($riga['cellulare'] ?? null) ?: null,
            'filemaker_persona_id' => $riga['idp'] ?? null,
            'operatore' => $this->operatorePer($riga['operatore'] ?? null),
            'data' => $data->toDateString(),
            'ora_inizio' => $this->secondiAOra((int) $riga['stime']),
            'ora_fine' => $this->secondiAOra((int) $riga['etime']),
        ];

        if ($cancellato) {
            $datiSync['status'] = 'annullato';
        }

        $appuntamento = Appuntamento::where('filemaker_id', $riga['id'])->first();

        if ($appuntamento) {
            $appuntamento->update($datiSync);

            return 'aggiornato';
        }

        Appuntamento::create($datiSync + [
            'filemaker_id' => $riga['id'],
            'tipo' => self::TIPO_DEFAULT,
            'status' => $cancellato ? 'annullato' : 'confermato',
        ]);

        return 'creato';
    }

    private function operatorePer(?string $nome): ?string
    {
        $nome = trim((string) $nome);

        if ($nome === '') {
            return null;
        }

        $operatore = Operatore::where('nome', $nome)->first();

        if (! $operatore) {
            $colore = self::PALETTE_OPERATORI[Operatore::count() % count(self::PALETTE_OPERATORI)];
            $operatore = Operatore::create(['nome' => $nome, 'colore' => $colore]);
        }

        return $operatore->nome;
    }

    private function serialeAData(int $seriale): ?Carbon
    {
        if ($seriale < 1) {
            return null;
        }

        return Carbon::create(1, 1, 1)->addDays($seriale - 1);
    }

    private function secondiAOra(int $secondi): string
    {
        $secondi = max(0, min(86399, $secondi));

        return sprintf('%02d:%02d:%02d', intdiv($secondi, 3600), intdiv($secondi % 3600, 60), $secondi % 60);
    }
}
