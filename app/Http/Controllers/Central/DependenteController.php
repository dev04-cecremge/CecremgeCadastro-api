<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;

use App\Models\Dependente;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DependenteController extends Controller
{
    public function index()
    {
        return response()->json(
            Dependente::all()
        );
    }

    public function show($codigoDependente)
    {
        return response()->json(
            Dependente::findOrFail($codigoDependente)
        );
    }

    public function store(Request $request)
    {
        return $request;
    }

    public function update(Request $request, $codigoDependente)
    {
        return $codigoDependente;
    }

    public function destroy($codigoDependente)
    {
        return $codigoDependente;
    }
}
