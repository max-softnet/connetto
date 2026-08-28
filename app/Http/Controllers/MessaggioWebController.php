<?php

namespace App\Http\Controllers;

use App\Exceptions\DestinatarioMancanteException;
use App\Models\Appuntamento;
use App\Models\Messaggio;
use App\Models\ModelloMessaggio;
use App\Services\ComponitoreMessaggio;
use App\Services\WhatsAppSender;
use Illuminate\Http\Request;

class MessaggioWebController extends Controller
{
    public function index()
    {
        $messaggi = Messaggio::with(['appuntamento', 'modello'])
            ->orderByDesc('created_at')
            ->get();

        return view('messaggi.index', compact('messaggi'));
    }

    public function crea(Request $request, Appuntamento $appuntamento)
    {
        $modelli = ModelloMessaggio::where(function ($query) use ($appuntamento) {
            $query->whereNull('tipo_appuntamento')->orWhere('tipo_appuntamento', $appuntamento->tipo);
        })->orderBy('nome')->get();

        $modelloSelezionato = null;
        $corpo = null;
        $oggetto = null;

        if ($request->filled('modello_id')) {
            $modelloSelezionato = ModelloMessaggio::find($request->integer('modello_id'));

            if ($modelloSelezionato) {
                $corpo = $modelloSelezionato->renderPer($appuntamento);
                $oggetto = $modelloSelezionato->renderOggettoPer($appuntamento);
            }
        }

        return view('messaggi.crea', compact('appuntamento', 'modelli', 'modelloSelezionato', 'corpo', 'oggetto'));
    }

    public function salva(Request $request, Appuntamento $appuntamento, ComponitoreMessaggio $componitore)
    {
        $data = $request->validate([
            'modello_id' => ['required', 'exists:modelli_messaggio,id'],
            'oggetto' => ['nullable', 'string', 'max:255'],
            'corpo' => ['required', 'string'],
        ]);

        $modello = ModelloMessaggio::findOrFail($data['modello_id']);

        try {
            $componitore->componi($appuntamento, $modello, $data['corpo'], $data['oggetto'] ?? null);
        } catch (DestinatarioMancanteException $e) {
            return back()->withErrors(['modello_id' => $e->getMessage()])->withInput();
        }

        return redirect()->route('messaggi.index')->with('successo', 'Messaggio salvato come bozza.');
    }

    public function invia(Messaggio $messaggio, WhatsAppSender $whatsAppSender)
    {
        if ($messaggio->canale !== 'whatsapp') {
            return back()->withErrors(['messaggio' => "L'invio automatico è disponibile per ora solo per il canale WhatsApp."]);
        }

        $successo = $whatsAppSender->invia($messaggio);

        return back()->with(
            $successo ? 'successo' : 'errore_invio',
            $successo ? 'Messaggio inviato.' : 'Invio fallito: ' . $messaggio->fresh()->errore
        );
    }

    public function destroy(Messaggio $messaggio)
    {
        $messaggio->delete();

        return back()->with('successo', 'Messaggio eliminato.');
    }
}
