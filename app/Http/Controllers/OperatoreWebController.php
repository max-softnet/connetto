<?php

namespace App\Http\Controllers;

use App\Models\Operatore;

class OperatoreWebController extends Controller
{
    public function index()
    {
        $operatori = Operatore::orderBy('nome')->get();

        return view('operatori.index', compact('operatori'));
    }

    public function toggleAutomazioni(Operatore $operatore)
    {
        $operatore->update(['abilitato_automazioni' => ! $operatore->abilitato_automazioni]);

        return redirect()->route('operatori.index')->with('successo', 'Operatore aggiornato.');
    }
}
