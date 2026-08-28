<?php

namespace App\Http\Controllers;

use App\Models\ModelloMessaggio;
use App\Models\TipoAppuntamento;
use Illuminate\Http\Request;

class ModelloMessaggioWebController extends Controller
{
    private function rules(): array
    {
        return [
            'nome' => ['required', 'string', 'max:255'],
            'canale' => ['required', 'in:email,sms,whatsapp'],
            'oggetto' => ['nullable', 'string', 'max:255', 'required_if:canale,email'],
            'corpo' => ['required', 'string'],
            'whatsapp_template_nome' => ['nullable', 'string', 'max:255'],
            'whatsapp_template_lingua' => ['nullable', 'string', 'max:20'],
            'whatsapp_formato_parametri' => ['nullable', 'in:posizionale,nominale'],
            'whatsapp_nome_parametro' => ['nullable', 'string', 'max:255'],
            'tipo_appuntamento' => ['nullable', 'string', 'exists:tipi_appuntamento,nome'],
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $modelli = ModelloMessaggio::orderBy('nome')->get();

        return view('modelli-messaggio.index', compact('modelli'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $tipiAppuntamento = TipoAppuntamento::orderBy('nome')->get();
        $modello = new ModelloMessaggio();

        return view('modelli-messaggio.form', compact('tipiAppuntamento', 'modello'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate($this->rules());
        $data['whatsapp_header_parametro'] = $request->boolean('whatsapp_header_parametro');

        ModelloMessaggio::create($data);

        return redirect()->route('modelli-messaggio.index')->with('successo', 'Modello creato.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ModelloMessaggio $modelloMessaggio)
    {
        $tipiAppuntamento = TipoAppuntamento::orderBy('nome')->get();
        $modello = $modelloMessaggio;

        return view('modelli-messaggio.form', compact('tipiAppuntamento', 'modello'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ModelloMessaggio $modelloMessaggio)
    {
        $data = $request->validate($this->rules());
        $data['whatsapp_header_parametro'] = $request->boolean('whatsapp_header_parametro');

        $modelloMessaggio->update($data);

        return redirect()->route('modelli-messaggio.index')->with('successo', 'Modello aggiornato.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ModelloMessaggio $modelloMessaggio)
    {
        $modelloMessaggio->delete();

        return redirect()->route('modelli-messaggio.index')->with('successo', 'Modello eliminato.');
    }
}
