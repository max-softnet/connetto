<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appuntamento;
use Illuminate\Http\Request;

class AppuntamentoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Appuntamento::with(['tipoAppuntamento', 'operatoreAppuntamento'])
            ->orderBy('data')
            ->orderBy('ora_inizio')
            ->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'google_event_id' => ['nullable', 'string', 'max:255', 'unique:appuntamenti,google_event_id'],
            'titolo' => ['required', 'string', 'max:255'],
            'descrizione' => ['nullable', 'string'],
            'email' => ['nullable', 'email', 'max:255'],
            'cellulare' => ['nullable', 'string', 'max:30'],
            'tipo' => ['required', 'string', 'exists:tipi_appuntamento,nome'],
            'operatore' => ['nullable', 'string', 'exists:operatori,nome'],
            'data' => ['required', 'date'],
            'ora_inizio' => ['required', 'date_format:H:i'],
            'ora_fine' => ['required', 'date_format:H:i', 'after:ora_inizio'],
            'status' => ['nullable', 'in:confermato,annullato'],
        ]);

        $appuntamento = Appuntamento::create($data);

        return response()->json($appuntamento, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Appuntamento $appuntamento)
    {
        return $appuntamento->load(['tipoAppuntamento', 'operatoreAppuntamento']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Appuntamento $appuntamento)
    {
        $data = $request->validate([
            'google_event_id' => ['nullable', 'string', 'max:255', 'unique:appuntamenti,google_event_id,' . $appuntamento->id],
            'titolo' => ['sometimes', 'required', 'string', 'max:255'],
            'descrizione' => ['nullable', 'string'],
            'email' => ['nullable', 'email', 'max:255'],
            'cellulare' => ['nullable', 'string', 'max:30'],
            'tipo' => ['sometimes', 'required', 'string', 'exists:tipi_appuntamento,nome'],
            'operatore' => ['nullable', 'string', 'exists:operatori,nome'],
            'data' => ['sometimes', 'required', 'date'],
            'ora_inizio' => ['sometimes', 'required', 'date_format:H:i'],
            'ora_fine' => ['sometimes', 'required', 'date_format:H:i', 'after:ora_inizio'],
            'status' => ['nullable', 'in:confermato,annullato'],
        ]);

        $appuntamento->update($data);

        return $appuntamento->load(['tipoAppuntamento', 'operatoreAppuntamento']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Appuntamento $appuntamento)
    {
        $appuntamento->delete();

        return response()->noContent();
    }
}
