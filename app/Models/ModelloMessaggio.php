<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModelloMessaggio extends Model
{
    protected $table = 'modelli_messaggio';

    protected $fillable = [
        'nome',
        'canale',
        'oggetto',
        'corpo',
        'whatsapp_template_nome',
        'whatsapp_template_lingua',
        'whatsapp_formato_parametri',
        'whatsapp_nome_parametro',
        'whatsapp_header_parametro',
        'tipo_appuntamento',
    ];

    protected $casts = [
        'whatsapp_header_parametro' => 'boolean',
    ];

    public function tipoAppuntamento()
    {
        return $this->belongsTo(TipoAppuntamento::class, 'tipo_appuntamento', 'nome');
    }

    public function renderPer(Appuntamento $appuntamento): string
    {
        return strtr($this->corpo, $this->segnapostiConParentesi($appuntamento));
    }

    public function renderOggettoPer(Appuntamento $appuntamento): ?string
    {
        return $this->oggetto ? strtr($this->oggetto, $this->segnapostiConParentesi($appuntamento)) : null;
    }

    /**
     * Nomi dei segnaposto usati nel corpo, nell'ordine in cui compaiono
     * (es. ['titolo', 'data', 'ora_inizio']). Servono per mappare i
     * parametri posizionali {{1}}, {{2}}, {{3}} richiesti dai template WhatsApp.
     */
    public function segnaposti(): array
    {
        preg_match_all('/\{\{(\w+)\}\}/', $this->corpo, $corrispondenze);

        return array_values(array_unique($corrispondenze[1]));
    }

    /**
     * Parametri da inviare a WhatsApp per un template approvato: array
     * indicizzato (posizionale, {{1}} {{2}} ...) oppure associativo
     * nome => valore (nominale, {{nome}}), a seconda del formato del template.
     *
     * Per il formato nominale: se "whatsapp_nome_parametro" è impostato, tutti i
     * segnaposto del corpo vengono accorpati in quell'unico parametro con nome
     * (caso di template con un solo parametro dal nome diverso dai nostri, es.
     * {{nome}}). Se è vuoto, ogni segnaposto del corpo diventa un parametro con
     * lo stesso nome (caso di template con più parametri nominati come i nostri
     * segnaposto, es. {{titolo}}, {{data}}, {{ora_inizio}}).
     */
    public function parametriWhatsAppPer(Appuntamento $appuntamento): array
    {
        $valori = $this->valoriSegnaposto($appuntamento);

        if ($this->whatsapp_formato_parametri === 'nominale') {
            if ($this->whatsapp_nome_parametro) {
                $primoSegnaposto = $this->segnaposti()[0] ?? null;

                return [$this->whatsapp_nome_parametro => $primoSegnaposto ? ($valori[$primoSegnaposto] ?? '') : ''];
            }

            $risultato = [];

            foreach ($this->segnaposti() as $nome) {
                $risultato[$nome] = $valori[$nome] ?? '';
            }

            return $risultato;
        }

        return array_map(fn (string $nome) => $valori[$nome] ?? '', $this->segnaposti());
    }

    private function segnapostiConParentesi(Appuntamento $appuntamento): array
    {
        $conParentesi = [];

        foreach ($this->valoriSegnaposto($appuntamento) as $nome => $valore) {
            $conParentesi["{{{$nome}}}"] = $valore;
        }

        return $conParentesi;
    }

    private function valoriSegnaposto(Appuntamento $appuntamento): array
    {
        return [
            'titolo' => $appuntamento->titolo,
            'data' => $appuntamento->data->translatedFormat('j F Y'),
            'ora_inizio' => \Illuminate\Support\Carbon::parse($appuntamento->ora_inizio)->format('H:i'),
            'ora_fine' => \Illuminate\Support\Carbon::parse($appuntamento->ora_fine)->format('H:i'),
            'tipo' => $appuntamento->tipo,
        ];
    }
}
