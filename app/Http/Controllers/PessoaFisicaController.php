<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class PessoaFisicaController extends Controller
{
    public function index()
    {
        $pessoasFisicas = DB::table('PessoasFisicas')
            ->get();

        return response()->json($pessoasFisicas);
    }

    public function buscarPorCpf($cpf)
    {
        $pessoasFisica = DB::table('PessoasFisicas')
            ->where('CPF', $cpf)
            ->first();

        return response()->json($pessoasFisica);
    }
}
