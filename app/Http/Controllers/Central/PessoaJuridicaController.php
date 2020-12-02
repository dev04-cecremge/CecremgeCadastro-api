<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;

use App\Models\PessoaJuridica;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PessoaJuridicaController extends Controller
{
    public function index()
    {
        return response()->json(
            PessoaJuridica::all()
        );
    }

    public function show($codigoPessoaJuridica)
    {
        return response()->json(
            PessoaJuridica::findOrFail($codigoPessoaJuridica)
        );
    }

    public function store(Request $request)
    {
        return $request;
    }

    public function update(Request $request, $codigoPessoaJuridica)
    {
        return $codigoPessoaJuridica;
    }

    public function destroy($codigoPessoaJuridica)
    {
        return $codigoPessoaJuridica;
    }
}
