<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;

use App\Models\SistemaCecremge;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SistemaCecremgeController extends Controller
{
    public function index()
    {
        return response()->json(
            SistemaCecremge::all()
        );
    }

    public function show($codigoSistema)
    {
        return response()->json(
            SistemaCecremge::findOrFail($codigoSistema)
        );
    }

    public function store(Request $request)
    {
        return $request;
    }

    public function update(Request $request, $codigoSistema)
    {
        return $codigoSistema;
    }

    public function destroy($codigoSistema)
    {
        return $codigoSistema;
    }
}
