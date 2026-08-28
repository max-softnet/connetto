<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ModelloMessaggio;
use Illuminate\Http\Request;

class ModelloMessaggioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return ModelloMessaggio::orderBy('nome')->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'canale' => ['required', 'in:email,sms,whatsapp'],
            'oggetto' => ['nullable', 'string', 'max:255', 'required_if:canale,email'],
            'corpo' => ['required', 'string'],
            'whatsapp_template_nome' => ['nullable', 'string', 'max:255'],
            'whatsapp_template_lingua' => ['nullable', 'string', 'max:20'],
            'whatsapp_formato_parametri' => ['nullable', 'in:posizionale,nominale'],
            'whatsapp_nome_parametro' => ['nullable', 'string', 'max:255'],
            'whatsapp_header_parametro' => ['nullable', 'boolean'],
            'tipo_appuntamento' => ['nullable', 'string', 'exists:tipi_appuntamento,nome'],
        ]);

        $modello = ModelloMessaggio::create($data);

        return response()->json($modello, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(ModelloMessaggio $modelloMessaggio)
    {
        return $modelloMessaggio;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ModelloMessaggio $modelloMessaggio)
    {
        $data = $request->validate([
            'nome' => ['sometimes', 'required', 'string', 'max:255'],
            'canale' => ['sometimes', 'required', 'in:email,sms,whatsapp'],
            'oggetto' => ['nullable', 'string', 'max:255'],
            'corpo' => ['sometimes', 'required', 'string'],
            'whatsapp_template_nome' => ['nullable', 'string', 'max:255'],
            'whatsapp_template_lingua' => ['nullable', 'string', 'max:20'],
            'whatsapp_formato_parametri' => ['nullable', 'in:posizionale,nominale'],
            'whatsapp_nome_parametro' => ['nullable', 'string', 'max:255'],
            'whatsapp_header_parametro' => ['nullable', 'boolean'],
            'tipo_appuntamento' => ['nullable', 'string', 'exists:tipi_appuntamento,nome'],
        ]);

        $modelloMessaggio->update($data);

        return $modelloMessaggio;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ModelloMessaggio $modelloMessaggio)
    {
        $modelloMessaggio->delete();

        return response()->noContent();
    }
}
