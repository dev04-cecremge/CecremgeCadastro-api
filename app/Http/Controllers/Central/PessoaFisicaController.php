<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PessoaFisicaController extends Controller
{
    public function index()
    {
        $pessoasFisicas = DB::table('Departamentos')
            ->join('MembrosPessoasJuridicas', 'MembrosPessoasJuridicas.CodigoDepartamento', '=', 'Departamentos.Codigo')
            ->join('PessoasFisicas', 'PessoasFisicas.Codigo', '=', 'MembrosPessoasJuridicas.CodigoPessoaFisica')
            ->join('Cargos', 'Cargos.Codigo', '=', 'MembrosPessoasJuridicas.CodigoCargo')
            ->where('MembrosPessoasJuridicas.CodigoPessoaJuridica', 1)
            ->where('MembrosPessoasJuridicas.CodigoTipoPessoaFisica', '!=', 4)
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

    public function show($cpf)
    {
        $pessoaFisica = DB::table('Departamentos')
            ->join('MembrosPessoasJuridicas', 'MembrosPessoasJuridicas.CodigoDepartamento', '=', 'Departamentos.Codigo')
            ->join('PessoasFisicas', 'PessoasFisicas.Codigo', '=', 'MembrosPessoasJuridicas.CodigoPessoaFisica')
            ->join('Cargos', 'Cargos.Codigo', '=', 'MembrosPessoasJuridicas.CodigoCargo')
            ->where('MembrosPessoasJuridicas.CodigoTipoPessoaFisica', '!=', 4)
            ->where('MembrosPessoasJuridicas.CodigoPessoaJuridica', 1)
            ->where('PessoasFisicas.CPF', $cpf)
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
            ->first();

        return response()->json([
            'codigoPessoaFisica' => intval($pessoaFisica->CodigoPessoaFisicaFisica),
            'cpf' => $pessoaFisica->CPF,
            'nome' => $pessoaFisica->Nome,
            'contaDominio' => $pessoaFisica->ContaDominio,
            'cargo' => $pessoaFisica->NomeCargo,
            'departamento' => [
                'codigo' => intval($pessoaFisica->CodigoDepartamento),
                'nome' => $pessoaFisica->NomeDepartamento,
            ],
        ]);
    }

    public function supervisores()
    {
        $supervisores = DB::table('Departamentos')
            ->join('MembrosPessoasJuridicas', 'MembrosPessoasJuridicas.CodigoDepartamento', '=', 'Departamentos.Codigo')
            ->join('PessoasFisicas', 'PessoasFisicas.Codigo', '=', 'MembrosPessoasJuridicas.CodigoPessoaFisica')
            ->join('Cargos', 'Cargos.Codigo', '=', 'MembrosPessoasJuridicas.CodigoCargo')
            ->where('MembrosPessoasJuridicas.CodigoTipoPessoaFisica', '!=', 4)
            ->where('MembrosPessoasJuridicas.CodigoPessoaJuridica', 1)
            ->where('Cargos.Nome', 'like', 'Super%')
            ->orderBy('PessoasFisicas.Nome')
            ->select([
                'PessoasFisicas.CPF',
                'PessoasFisicas.Nome',
                'PessoasFisicas.Codigo',
                'PessoasFisicas.ContaDominio',
            ])
            ->get();

        $response = [];
        foreach ($supervisores as $supervisor)
            array_push($response, [
                'codigoPessoaFisica' => intval($supervisor->Codigo),
                'cpf' => $supervisor->CPF,
                'nome' => $supervisor->Nome,
                'contaDominio' => $supervisor->ContaDominio,
            ]);

        return response()->json($response);
    }

    public function departamentoDaPessoaFisica($cpf)
    {
        $pessoaFisica = DB::table('Departamentos')
            ->join('MembrosPessoasJuridicas', 'MembrosPessoasJuridicas.CodigoDepartamento', '=', 'Departamentos.Codigo')
            ->join('PessoasFisicas', 'PessoasFisicas.Codigo', '=', 'MembrosPessoasJuridicas.CodigoPessoaFisica')
            ->where('PessoasFisicas.CPF', $cpf)
            ->where('MembrosPessoasJuridicas.CodigoPessoaJuridica', 1)
            ->where('MembrosPessoasJuridicas.CodigoTipoPessoaFisica', '!=', 4)
            ->select([
                'PessoasFisicas.Codigo AS CodigoPessoaFisica',
                'PessoasFisicas.Nome',
                'PessoasFisicas.CPF',
                'PessoasFisicas.ContaDominio',
                'Departamentos.Codigo AS CodigoDepartamento',
                'Departamentos.Nome AS NomeDepartamento',
            ])
            ->first();

        if (!$pessoaFisica)
            return response()->json([
                'mensagem' => 'Não existe departamento para a pessoa física ' . $pessoaFisica->Nome
            ], 401);

        return response()->json([
            'codigoPessoaFisica' => intval($pessoaFisica->CodigoPessoaFisica),
            'cpf' => $pessoaFisica->CPF,
            'contaDominio' => $pessoaFisica->ContaDominio,
            'nome' => $pessoaFisica->Nome,
            'departamento' => [
                'codigo' => intval($pessoaFisica->CodigoDepartamento),
                'nome' => $pessoaFisica->NomeDepartamento,
            ],
        ]);
    }
}
