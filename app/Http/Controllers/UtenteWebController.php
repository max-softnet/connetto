<?php

namespace App\Http\Controllers;

use App\Models\Operatore;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UtenteWebController extends Controller
{
    private function rules(?User $utente = null): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($utente)],
            'password' => [$utente ? 'nullable' : 'required', 'string', 'min:6'],
            'ruolo' => ['required', 'in:admin,operatore,paziente'],
            'operatore_id' => ['required_if:ruolo,operatore', 'nullable', 'exists:operatori,id'],
            'filemaker_persona_id' => ['required_if:ruolo,paziente', 'nullable', 'integer'],
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $utenti = User::with('operatore')->orderBy('name')->get();

        return view('utenti.index', compact('utenti'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $operatori = Operatore::orderBy('nome')->get();
        $utente = new User();

        return view('utenti.form', compact('operatori', 'utente'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate($this->rules());
        $data['password'] = bcrypt($data['password']);

        if ($data['ruolo'] !== 'operatore') {
            $data['operatore_id'] = null;
        }

        if ($data['ruolo'] !== 'paziente') {
            $data['filemaker_persona_id'] = null;
        }

        User::create($data);

        return redirect()->route('utenti.index')->with('successo', 'Utente creato.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $utente)
    {
        $operatori = Operatore::orderBy('nome')->get();

        return view('utenti.form', compact('operatori', 'utente'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $utente)
    {
        $data = $request->validate($this->rules($utente));

        if (! empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }

        if ($data['ruolo'] !== 'operatore') {
            $data['operatore_id'] = null;
        }

        if ($data['ruolo'] !== 'paziente') {
            $data['filemaker_persona_id'] = null;
        }

        $utente->update($data);

        return redirect()->route('utenti.index')->with('successo', 'Utente aggiornato.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $utente)
    {
        if ($utente->id === auth()->id()) {
            return back()->withErrors(['utente' => 'Non puoi eliminare il tuo stesso account.']);
        }

        $utente->delete();

        return redirect()->route('utenti.index')->with('successo', 'Utente eliminato.');
    }
}
