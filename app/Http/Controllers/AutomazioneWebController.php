<?php

namespace App\Http\Controllers;

use App\Models\Automazione;
use App\Models\ModelloMessaggio;
use App\Models\Operatore;
use App\Models\TipoAppuntamento;
use App\Services\EsecutoreAutomazioni;
use Illuminate\Http\Request;

class AutomazioneWebController extends Controller
{
    private function rules(): array
    {
        return [
            'nome' => ['required', 'string', 'max:255'],
            'modello_id' => ['required', 'exists:modelli_messaggio,id'],
            'giorni_prima' => ['required', 'integer', 'min:0', 'max:60'],
            'tipo_appuntamento' => ['nullable', 'string', 'exists:tipi_appuntamento,nome'],
            'operatore' => ['nullable', 'string', 'exists:operatori,nome'],
        ];
    }

    public function index()
    {
        $automazioni = Automazione::with('modello')->orderBy('nome')->get();

        return view('automazioni.index', compact('automazioni'));
    }

    public function create()
    {
        $modelli = ModelloMessaggio::orderBy('nome')->get();
        $tipiAppuntamento = TipoAppuntamento::orderBy('nome')->get();
        $operatori = $this->operatoriSelezionabili();
        $automazione = new Automazione();

        return view('automazioni.form', compact('modelli', 'tipiAppuntamento', 'operatori', 'automazione'));
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());
        $data['attiva'] = $request->boolean('attiva');

        Automazione::create($data);

        return redirect()->route('automazioni.index')->with('successo', 'Automazione creata.');
    }

    public function edit(Automazione $automazione)
    {
        $modelli = ModelloMessaggio::orderBy('nome')->get();
        $tipiAppuntamento = TipoAppuntamento::orderBy('nome')->get();
        $operatori = $this->operatoriSelezionabili($automazione->operatore);

        return view('automazioni.form', compact('modelli', 'tipiAppuntamento', 'operatori', 'automazione'));
    }

    /**
     * Operatori abilitati come criterio automazioni, includendo comunque quello
     * eventualmente già selezionato su questa automazione anche se nel frattempo
     * è stato disabilitato (altrimenti sparirebbe dal form in modifica).
     */
    private function operatoriSelezionabili(?string $operatoreCorrente = null)
    {
        return Operatore::where('abilitato_automazioni', true)
            ->orWhere('nome', $operatoreCorrente)
            ->orderBy('nome')
            ->get();
    }

    public function update(Request $request, Automazione $automazione)
    {
        $data = $request->validate($this->rules());
        $data['attiva'] = $request->boolean('attiva');

        $automazione->update($data);

        return redirect()->route('automazioni.index')->with('successo', 'Automazione aggiornata.');
    }

    public function destroy(Automazione $automazione)
    {
        $automazione->delete();

        return redirect()->route('automazioni.index')->with('successo', 'Automazione eliminata.');
    }

    public function esegui(EsecutoreAutomazioni $esecutore)
    {
        $risultati = $esecutore->esegui(origine: 'manuale');

        if (empty($risultati)) {
            return redirect()->route('automazioni.index')->with('successo', 'Nessuna automazione attiva da eseguire.');
        }

        return redirect()->route('automazioni.index')->with('risultatiEsecuzione', $risultati);
    }

    public function eseguiForzata(Automazione $automazione, EsecutoreAutomazioni $esecutore)
    {
        $risultati = $esecutore->esegui(origine: 'manuale', forza: true, soloAutomazioneId: $automazione->id);

        return redirect()->route('automazioni.index')->with('risultatiEsecuzione', $risultati);
    }
}
