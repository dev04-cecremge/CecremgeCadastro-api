<?php

namespace App\Http\Controllers;

use Importer;
use Illuminate\Http\Request;

class JEDIController extends Controller
{
    public function atualizarAssociadosCooperativa(Request $request)
    {
        // BUSCAR COOPERATIVA

        $excel = Importer::make('Excel');
        $excel->load($request->planilha);

        return response()->json($excel->getCollection());
    }
}
