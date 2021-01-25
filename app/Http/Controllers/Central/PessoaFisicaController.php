<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;

use Carbon\Carbon;

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

    public function gerentes()
    {
        $gerentes = DB::table('MembrosPessoasJuridicas')
            ->join('PessoasFisicas', 'PessoasFisicas.Codigo', '=', 'MembrosPessoasJuridicas.CodigoPessoaFisica')
            ->join('Departamentos', 'Departamentos.Codigo', '=', 'MembrosPessoasJuridicas.CodigoDepartamento')
            ->join('Cargos', 'Cargos.Codigo', '=', 'MembrosPessoasJuridicas.CodigoCargo')
            ->where('MembrosPessoasJuridicas.CodigoTipoPessoaFisica', '!=', 4)
            ->where('MembrosPessoasJuridicas.CodigoPessoaJuridica', 1)
            ->where('Cargos.Nome', 'like', 'Gerente%')
            ->orderBy('Departamentos.Nome')
            ->select([
                'Cargos.Nome AS Cargo',
                'PessoasFisicas.CPF',
                'PessoasFisicas.Nome',
                'PessoasFisicas.ContaDominio',
                'PessoasFisicas.Codigo AS CodigoPessoaFisica',
                'Departamentos.Nome AS NomeDepartamento',
                'MembrosPessoasJuridicas.CodigoDepartamento',
            ])
            ->get();

        $response = [];
        foreach ($gerentes as $gerente)
            array_push($response, [
                'codigoPessoaFisica' => intval($gerente->CodigoPessoaFisica),
                'cpf' => $gerente->CPF,
                'contaDominio' => $gerente->ContaDominio,
                'nome' => $gerente->Nome,
                'cargo' => $gerente->Cargo,
                'departamento' => [
                    'codigo' => intval($gerente->CodigoDepartamento),
                    'nome' => $gerente->NomeDepartamento,
                ],
            ]);
        
        return response()->json($response);
    }

    public function show($cpf){
        $pessoaFisica = DB::table('PessoasFisicas')
        ->where('cpf', $cpf)
        ->select([
            'Codigo',
            'cpf',
            'contadominio',
            'nome',
            'email',
            'dataalteracao',
            'editor'
        ])
        ->get();
      
        return response()->json($pessoaFisica);    

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
                'mensagem' => 'Não existe departamento para a pessoa física'
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

    public function departamentoDaPessoaFisicaContaDominio($contadominio)
    {
        $pessoaFisica = DB::table('Departamentos')
            ->join('MembrosPessoasJuridicas', 'MembrosPessoasJuridicas.CodigoDepartamento', '=', 'Departamentos.Codigo')
            ->join('PessoasFisicas', 'PessoasFisicas.Codigo', '=', 'MembrosPessoasJuridicas.CodigoPessoaFisica')
            ->where('PessoasFisicas.ContaDominio', $contadominio)
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
                'mensagem' => 'Não existe departamento para a pessoa física'
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

    public function atualizarDepartamento(Request $request, $cpf)
    {
        $pessoaFisica = DB::table('PessoasFisicas')
            ->where('CPF', $cpf)
            ->first();

        if (!$pessoaFisica)
            return response()->json([
                'mensagem' => 'Pessoa física não encontrada'
            ], 401);

        

        DB::table('MembrosPessoasJuridicas')
            ->where('CodigoPessoaFisica', $pessoaFisica->Codigo)
            ->where('CodigoPessoaJuridica', 1)
            ->update([
                'CodigoDepartamento' => $request->codigoDepartamento,
                'DataAlteracao' => Carbon::now()->toDateTimeString(),
                'Editor' => 'mateusl2003_00',
            ]);

        return response()->json([
            'mensagem' => 'Departamento atualizado'
        ]);
    }

    public function atualizarContaDeDominio(Request $request, $cpf){
        $pessoa = DB::table('PessoasFisicas')
            ->where('CPF', $cpf)
            ->first();

        if (!$request->contaDeDominio){
            return response()->json([
                'Erro' => 'conta de dominio nao informada2'
            ], 401);
        }
        

        if (!$pessoa){
            return response()->json([
                'Erro' => 'Não existe pessoa cadastrada com esse CPF!'
            ], 401);
        }

        //Atualiza o cadastro
        DB::table('PessoasFisicas')
            ->where('CPF', $pessoa->CPF)
            ->update([
                'ContaDominio' => $request->contaDeDominio,
                'DataAlteracao' => Carbon::now()->toDateTimeString(),
                'Editor' => 'CadastroAPI'
            ]);

            return response()->json([
                'mensagem' => 'Conta de domínio atualizada!'
            ]);

    }


}
