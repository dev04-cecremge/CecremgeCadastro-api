<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class TipoPessoaFisicaController extends Controller
{
    public function index()
    {
        $tipos = DB::table('TiposPessoaFisica')
            ->get();

        return response()->json($tipos);
    }

    public function pessoasFisicasPorTipo($codigoTipo)
    {
        $pessoasFisicas = DB::table('MembrosPessoasJuridicas')
            ->join('PessoasFisicas', 'PessoasFisicas.Codigo', '=', 'MembrosPessoasJuridicas.CodigoPessoaFisica')
            ->where('MembrosPessoasJuridicas.CodigoTipoPessoaFisica', $codigoTipo)
            ->get();

        return response()->json($pessoasFisicas);
    }
}
