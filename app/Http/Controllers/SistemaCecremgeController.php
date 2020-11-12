<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class SistemaCecremgeController extends Controller
{
    public function index()
    {
        $sistemasCecremge = DB::table('SistemasCecremge')
            ->get();

        return response()->json($sistemasCecremge);
    }

    public function permissaoContaDominio($codigoSistema, $contaDominio)
    {
        $usuario = DB::table('PessoasFisicasSistemasCecremge')
            ->join('PessoasFisicas', 'PessoasFisicasSistemasCecremge.CodigoPessoaFisica', '=', 'PessoasFisicas.Codigo')
            ->where('PessoasFisicasSistemasCecremge.CodigoSistemaCecremge', $codigoSistema)
            ->where('PessoasFisicas.ContaDominio', $contaDominio)
            ->select('PessoasFisicas.Codigo', 'PessoasFisicas.ContaDominio', 'PessoasFisicas.Nome')
            ->first();

        if (!$usuario) return response()->json([
            'status' => false,
            'mensagem' => 'Usuário não possuí permissão de acesso ao sistema'
        ]);

        return response()->json($usuario);
    }
}
