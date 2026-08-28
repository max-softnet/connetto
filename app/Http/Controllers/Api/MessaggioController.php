<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appuntamento;
use App\Models\Messaggio;
use App\Models\ModelloMessaggio;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class MessaggioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Messaggio::with(['appuntamento', 'modello'])
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'appuntamento_id' => ['required', 'exists:appuntamenti,id'],
            'modello_id' => ['required', 'exists:modelli_messaggio,id'],
            'oggetto' => ['nullable', 'string', 'max:255'],
            'corpo' => ['nullable', 'string'],
        ]);

        $appuntamento = Appuntamento::findOrFail($data['appuntamento_id']);
        $modello = ModelloMessaggio::findOrFail($data['modello_id']);

        $destinatario = $modello->canale === 'email' ? $appuntamento->email : $appuntamento->cellulare;

        if (! $destinatario) {
            throw ValidationException::withMessages([
                'appuntamento_id' => "L'appuntamento non ha un " . ($modello->canale === 'email' ? 'indirizzo email' : 'numero di cellulare') . ' registrato.',
            ]);
        }

        $messaggio = Messaggio::create([
            'appuntamento_id' => $appuntamento->id,
            'modello_id' => $modello->id,
            'canale' => $modello->canale,
            'destinatario' => $destinatario,
            'oggetto' => $modello->canale === 'email' ? ($data['oggetto'] ?? $modello->renderOggettoPer($appuntamento)) : null,
            'corpo' => $data['corpo'] ?? $modello->renderPer($appuntamento),
        ]);

        return response()->json($messaggio->load(['appuntamento', 'modello']), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Messaggio $messaggio)
    {
        return $messaggio->load(['appuntamento', 'modello']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Messaggio $messaggio)
    {
        $data = $request->validate([
            'oggetto' => ['nullable', 'string', 'max:255'],
            'corpo' => ['sometimes', 'required', 'string'],
            'stato' => ['sometimes', 'in:bozza,inviato,fallito'],
        ]);

        $messaggio->update($data);

        return $messaggio->load(['appuntamento', 'modello']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Messaggio $messaggio)
    {
        $messaggio->delete();

        return response()->noContent();
    }
}
