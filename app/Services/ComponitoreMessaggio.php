<?php

namespace App\Services;

use App\Exceptions\DestinatarioMancanteException;
use App\Models\Appuntamento;
use App\Models\Messaggio;
use App\Models\ModelloMessaggio;

class ComponitoreMessaggio
{
    /**
     * Crea un Messaggio a partire da un modello e un appuntamento.
     *
     * @throws DestinatarioMancanteException se manca l'email/cellulare richiesti dal canale del modello.
     */
    public function componi(
        Appuntamento $appuntamento,
        ModelloMessaggio $modello,
        ?string $corpo = null,
        ?string $oggetto = null,
        string $origine = 'manuale',
    ): Messaggio {
        $destinatario = $modello->canale === 'email' ? $appuntamento->email : $appuntamento->cellulare;

        if (! $destinatario) {
            $etichetta = $modello->canale === 'email' ? 'indirizzo email' : 'numero di cellulare';

            throw new DestinatarioMancanteException("L'appuntamento non ha un {$etichetta} registrato.");
        }

        return Messaggio::create([
            'appuntamento_id' => $appuntamento->id,
            'modello_id' => $modello->id,
            'canale' => $modello->canale,
            'destinatario' => $destinatario,
            'oggetto' => $modello->canale === 'email' ? ($oggetto ?? $modello->renderOggettoPer($appuntamento)) : null,
            'corpo' => $corpo ?? $modello->renderPer($appuntamento),
            'whatsapp_template_nome' => $modello->whatsapp_template_nome,
            'whatsapp_template_lingua' => $modello->whatsapp_template_lingua,
            'whatsapp_parametri' => $modello->whatsapp_template_nome
                ? $modello->parametriWhatsAppPer($appuntamento)
                : null,
            'whatsapp_formato_parametri' => $modello->whatsapp_formato_parametri,
            'whatsapp_header_parametro' => $modello->whatsapp_header_parametro,
            'origine' => $origine,
        ]);
    }
}
