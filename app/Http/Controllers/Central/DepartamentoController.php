<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;

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

    public function gerenteDoDepartamento($codigoDepartamento)
    {
        $gerente = DB::table('MembrosPessoasJuridicas')
            ->join('PessoasFisicas', 'PessoasFisicas.Codigo', '=', 'MembrosPessoasJuridicas.CodigoPessoaFisica')
            ->join('Departamentos', 'Departamentos.Codigo', '=', 'MembrosPessoasJuridicas.CodigoDepartamento')
            ->join('Cargos', 'Cargos.Codigo', '=', 'MembrosPessoasJuridicas.CodigoCargo')
            ->where('MembrosPessoasJuridicas.CodigoTipoPessoaFisica', '!=', 4)
            ->where('MembrosPessoasJuridicas.CodigoPessoaJuridica', 1)
            ->where('Departamentos.Codigo', $codigoDepartamento)
            ->where('Cargos.Nome', 'like', 'Gerente%')
            ->select([
                'Departamentos.Nome AS NomeDepartamento',
                'PessoasFisicas.Codigo AS CodigoPessoaFisica',
                'PessoasFisicas.ContaDominio',
                'PessoasFisicas.Nome',
                'PessoasFisicas.CPF',
                'Cargos.Nome AS Cargo',
            ])
            ->first();

        return response()->json([
            'codigoPessoaFisica' => intval($gerente->CodigoPessoaFisica),
            'cpf' => $gerente->CPF,
            'contaDominio' => $gerente->ContaDominio,
            'nome' => $gerente->Nome,
            'cargo' => $gerente->Cargo,
            'gerente' => [
                'codigo' => intval($codigoDepartamento),
                'nome' => $gerente->NomeDepartamento,
            ],
        ]);
    }

    public function funcionariosDoDepartamento($codigoDepartamento)
    {
        $pessoasFisicas = DB::table('Departamentos')
            ->join('MembrosPessoasJuridicas', 'MembrosPessoasJuridicas.CodigoDepartamento', '=', 'Departamentos.Codigo')
            ->join('PessoasFisicas', 'PessoasFisicas.Codigo', '=', 'MembrosPessoasJuridicas.CodigoPessoaFisica')
            ->join('Cargos', 'Cargos.Codigo', '=', 'MembrosPessoasJuridicas.CodigoCargo')
            ->where('MembrosPessoasJuridicas.CodigoTipoPessoaFisica', '!=', 4)
            ->where('MembrosPessoasJuridicas.CodigoPessoaJuridica', 1)
            ->where('Departamentos.Codigo', $codigoDepartamento)
            ->orderBy('PessoasFisicas.Nome')
            ->select([
                'PessoasFisicas.Codigo AS CodigoPessoaFisica',
                'PessoasFisicas.ContaDominio',
                'PessoasFisicas.CPF',
                'PessoasFisicas.Nome',
                'Cargos.Nome AS NomeCargo',
                'Departamentos.Nome AS NomeDepartamento',
                'Departamentos.Codigo AS CodigoDepartamento',
            ])
            ->get();

        $response = [];
        foreach ($pessoasFisicas as $pessoa)
            array_push($response, [
                'codigoPessoaFisica' => intval($pessoa->CodigoPessoaFisica),
                'cpf' => $pessoa->CPF,
                'nome' => $pessoa->Nome,
                'contaDominio' => $pessoa->ContaDominio,
                'cargo' => $pessoa->NomeCargo,
                'departamento' => [
                    'codigo' => intval($pessoa->CodigoDepartamento),
                    'nome' => $pessoa->NomeDepartamento,
                ],
            ]);

        return response()->json($response);
    }
}
