<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class PessoaJuridicaController extends Controller
{
    public function index()
    {
        $pessoasJuridicas = DB::table('PessoasJuridicas')
            ->get();

        return response()->json($pessoasJuridicas);
    }
}
