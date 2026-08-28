<?php

namespace App\Http\Controllers;

use App\Models\Impostazione;
use Illuminate\Http\Request;

class ImpostazioniWebController extends Controller
{
    public function mostra()
    {
        $impostazioni = Impostazione::corrente();

        return view('impostazioni.mostra', compact('impostazioni'));
    }

    public function salva(Request $request)
    {
        $data = $request->validate([
            'whatsapp_phone_number_id' => ['nullable', 'string', 'max:255'],
            'whatsapp_token' => ['nullable', 'string'],
        ]);

        $impostazioni = Impostazione::corrente();

        if (empty($data['whatsapp_token'])) {
            unset($data['whatsapp_token']);
        }

        $impostazioni->update($data);

        return redirect()->route('impostazioni.mostra')->with('successo', 'Impostazioni salvate.');
    }
}
