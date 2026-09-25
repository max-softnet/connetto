<?php

namespace App\Http\Controllers;

use App\Models\Appuntamento;

class AppuntamentoWebController extends Controller
{
    public function destroy(Appuntamento $appuntamento)
    {
        $data = $appuntamento->data->format('Y-m-d');

        $appuntamento->delete();

        return redirect()->route('calendario.giorno', ['data' => $data])
            ->with('successo', 'Appuntamento eliminato.');
    }
}
