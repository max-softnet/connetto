<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\FileMakerAppuntamentoImporter;
use Illuminate\Http\Request;

class FileMakerImportController extends Controller
{
    public function store(Request $request, FileMakerAppuntamentoImporter $importer)
    {
        $righe = $request->json()->all();

        if (! is_array($righe) || array_is_list($righe) === false) {
            return response()->json([
                'message' => 'Il payload deve essere un array JSON di appuntamenti.',
            ], 422);
        }

        return response()->json($importer->importa($righe));
    }
}
