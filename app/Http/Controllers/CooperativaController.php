<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CooperativaController extends Controller
{
    public function index()
    {
        $cooperativas = DB::table('Cooperativas')
            ->get();

        return response()->json($cooperativas);
    }

    public function funcionarios($codigoCooperativa)
    {
        $cooperativa = DB::table('Cooperativas')
            ->where('Agencia', $codigoCooperativa)
            ->first();

        $pessoaJuridica = DB::table('PessoasJuridicas')
            ->where('Codigo', $cooperativa->CodigoPessoaJuridica)
            ->first();

        $funcionarios = DB::table('MembrosPessoasJuridicas')
            ->where('CodigoPessoaJuridica', $pessoaJuridica->Codigo)
            ->get();

        return response()->json($funcionarios);
    }

    public function funcionariosPorTipo(Request $request, $codigoCooperativa)
    {
        $cooperativa = DB::table('Cooperativas')
            ->where('Agencia', $codigoCooperativa)
            ->first();

        $pessoaJuridica = DB::table('PessoasJuridicas')
            ->where('Codigo', $cooperativa->CodigoPessoaJuridica)
            ->first();

        $funcionarios = DB::table('MembrosPessoasJuridicas')
            ->where('CodigoPessoaJuridica', $pessoaJuridica->Codigo)
            ->whereIn('CodigoTipoPessoaFisica', $request->tiposConsiderados)
            ->get();

        return response()->json(sizeof($funcionarios));
    }
}
