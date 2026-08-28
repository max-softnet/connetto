<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TipoAppuntamento;
use Illuminate\Http\Request;

class TipoAppuntamentoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return TipoAppuntamento::orderBy('nome')->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nome' => ['required', 'string', 'max:255', 'unique:tipi_appuntamento,nome'],
            'colore' => ['required', 'string', 'max:255'],
        ]);

        $tipoAppuntamento = TipoAppuntamento::create($data);

        return response()->json($tipoAppuntamento, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(TipoAppuntamento $tipoAppuntamento)
    {
        return $tipoAppuntamento;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TipoAppuntamento $tipoAppuntamento)
    {
        $data = $request->validate([
            'nome' => ['sometimes', 'required', 'string', 'max:255', 'unique:tipi_appuntamento,nome,' . $tipoAppuntamento->id],
            'colore' => ['sometimes', 'required', 'string', 'max:255'],
        ]);

        $tipoAppuntamento->update($data);

        return $tipoAppuntamento;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TipoAppuntamento $tipoAppuntamento)
    {
        $tipoAppuntamento->delete();

        return response()->noContent();
    }
}
