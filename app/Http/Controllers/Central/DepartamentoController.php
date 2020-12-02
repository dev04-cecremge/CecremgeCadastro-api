<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;

use App\Models\Departamento;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DepartamentoController extends Controller
{
    public function index()
    {
        $departamentos = DB::table('MembrosPessoasJuridicas')
            ->join('PessoasFisicas', 'PessoasFisicas.Codigo', '=', 'MembrosPessoasJuridicas.CodigoPessoaFisica')
            ->join('Departamentos', 'Departamentos.Codigo', '=', 'MembrosPessoasJuridicas.CodigoDepartamento')
            ->join('Cargos', 'Cargos.Codigo', '=', 'MembrosPessoasJuridicas.CodigoCargo')
            ->where('MembrosPessoasJuridicas.CodigoPessoaJuridica', 1)
            ->groupBy('Departamentos.Codigo', 'Departamentos.Nome')
            ->select('Departamentos.Codigo', 'Departamentos.Nome')
            ->get();

        $response = [];
        foreach ($departamentos as $departamento)
            array_push($response, [
                'codigo' => intval($departamento->Codigo),
                'nome' => $departamento->Nome,
            ]);

        return response()->json($response);
    }

    public function show($codigoDepartamento)
    {
        return response()->json(
            Departamento::findOrFail($codigoDepartamento)
        );
    }

    public function store(Request $request)
    {
        //
    }

    public function update(Request $request, $codigoDepartamento)
    {
        //
    }

    public function destroy($codigoDepartamento)
    {
        //
    }

    public function gerenteDoDepartamento($codigoDepartamento)
    {
        return response()->json(
            Departamento::findOrFail($codigoDepartamento)
                ->gerente()
        );
    }

    public function funcionariosDoDepartamento($codigoDepartamento)
    {
        return response()->json(
            Departamento::findOrFail($codigoDepartamento)
                ->funcionarios()
        );
    }
}
