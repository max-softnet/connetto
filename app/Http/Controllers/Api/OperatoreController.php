<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Operatore;
use Illuminate\Http\Request;

class OperatoreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Operatore::orderBy('nome')->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nome' => ['required', 'string', 'max:255', 'unique:operatori,nome'],
            'colore' => ['required', 'string', 'max:255'],
        ]);

        $operatore = Operatore::create($data);

        return response()->json($operatore, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Operatore $operatore)
    {
        return $operatore;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Operatore $operatore)
    {
        $data = $request->validate([
            'nome' => ['sometimes', 'required', 'string', 'max:255', 'unique:operatori,nome,' . $operatore->id],
            'colore' => ['sometimes', 'required', 'string', 'max:255'],
        ]);

        $operatore->update($data);

        return $operatore;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Operatore $operatore)
    {
        $operatore->delete();

        return response()->noContent();
    }
}
