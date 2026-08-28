<?php

namespace App\Console\Commands;

use App\Services\EsecutoreAutomazioni;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('automazioni:esegui')]
#[Description('Esegue le automazioni attive: crea (e per WhatsApp invia) i messaggi per gli appuntamenti in target.')]
class EseguiAutomazioni extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(EsecutoreAutomazioni $esecutore)
    {
        $risultati = $esecutore->esegui();

        if (empty($risultati)) {
            $this->info('Nessuna automazione attiva.');

            return;
        }

        foreach ($risultati as $riga) {
            $prefisso = $riga['appuntamento'] ? "  {$riga['appuntamento']}: " : '  ';
            $this->line("[{$riga['automazione']}] {$prefisso}{$riga['esito']}");
        }
    }
}
