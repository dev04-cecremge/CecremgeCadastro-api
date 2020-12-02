<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;

use App\Models\Cooperativa;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CooperativaController extends Controller
{
    public function index()
    {
        return response()->json(
            Cooperativa::all()
        );
    }

    public function show($codigoCooperativa)
    {
        return response()->json(
            Cooperativa::findOrFail($codigoCooperativa)
        );
    }

    public function store(Request $request)
    {
        return $request;
    }

    public function update(Request $request, $codigoCooperativa)
    {
        return $codigoCooperativa;
    }

    public function destroy($codigoCooperativa)
    {
        return $codigoCooperativa;
    }
}
