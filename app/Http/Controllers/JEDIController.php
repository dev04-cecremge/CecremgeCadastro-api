<?php

namespace App\Http\Controllers;

use Importer;
use Carbon\Carbon;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JEDIController extends Controller
{
    public function atualizarFuncionariosCooperativa(Request $request, $agenciaCooperativa)
    {
        if (!$request->planilha)
            return response()->json([
                'Erro' => 'Planilha não foi informada'
            ], 401);

        $excel = Importer::make('Excel');
        $excel->load($request->planilha);

        $planilha = $excel->getCollection();

        // Cabeçalho - vai ate a 8ª linha
        $agencia = $agenciaCooperativa;

        // Agencia da planilha diferente do URL
        //9 - agencia
        //1 - intituicao
        $num = 1;
        foreach ($planilha as $linha) {
            if ($linha[1] != 'Instituição:') $num++;
            else break;
        }

        if ( $agencia !=  trim($planilha[$num][9]) ){
            //Agencia informada é diferente da planilha que vem no Excel
            return response()->json([
                'Erro' => 'Agencia na url é diferente da agencia na planilha'
            ], 401);
        }
        // Nao informou a agencia
        if (!$agenciaCooperativa)
            return response()->json([
                'Erro' => 'A agencia nao foi informada na URL'
            ], 401);

        // Agencia diferente da agencia do documento
        $linha = 1;

        // Resolvendo a cooperativa
        $cooperativa = [];

        // Agencia 2003 - Excecao para Central
        $CodigoPessoaJuridica = 1;
        if ($agencia === '2003') {
            //---------------------------------------------------------------------------------------------
            $cooperativa = [
                'CodigoPessoaJuridica' => 1,
                'Agencia' => 2003,
                'Nome' => 'Sicoob Central Cecremge'
            ];
        } else {

            // Verificar se essa cooperativa existe
            $agenciaCooperativaValida = DB::table('Cooperativas')
            ->where('Agencia', $agenciaCooperativa)
            ->first();

            if (!$agenciaCooperativaValida)
                return response()->json([
                    'Erro' => 'Essa Cooperativa não existe no cadastro!'
                ], 401);

            $cooperativa = DB::table('Cooperativas')
                ->where('Agencia', $agencia)
                ->select('CodigoPessoaJuridica', 'Agencia', 'Sigla AS Nome')
                ->first();

            $CodigoPessoaJuridica = $cooperativa->CodigoPessoaJuridica;
        }
        
        // Pegar informações das pessoas na planilha
        $cpfs = [];
        $pessoasFisicas = [];

        $primeiraLinha = 1;
        foreach ($planilha as $linha) {
            if ($linha[0] !== 'CPF') $primeiraLinha++;
            else break;
        }

        for ($i = $primeiraLinha; $i <= sizeof($planilha) - 1; $i++) {
            if (!empty($planilha[$i][0])) {
                $linha = array($planilha[$i][0], $planilha[$i][6], $planilha[$i][12], $planilha[$i][13]);

                array_push($cpfs, $planilha[$i][0]);
                array_push($pessoasFisicas, $linha);
            }
        }

        // Atualizar ou adicionar
        foreach ($pessoasFisicas as $item) {
            $pessoa = DB::table('PessoasFisicas')
                ->where('CPF', $item[0])
                ->first();

            // Adicionar nova pessoa e membroPessoaFisica
            if (!$pessoa) {
                // Pessoasfisicas
                $idPessoaFisica = DB::table('PessoasFisicas')
                    ->insertGetId([
                        'CPF' => $item[0],
                        'ContaDominio' => strtolower($item[1]),
                        'Nome' => $item[2],
                        'Email' => $item[3],
                        'DataCriacao' => Carbon::now()->toDateTimeString(),
                        'Criador' => 'RPA'
                    ]);
            } else {
                //Atualize o pessoafisica
                DB::table('PessoasFisicas')
                    ->where('CPF', $pessoa->CPF)
                    ->update([
                        'Nome' => $item[2],
                        'EMail' => $item[3] === "" ? $pessoa->EMail : $item[3],
                        'ContaDominio' => strtolower($item[1]),
                        'DataAlteracao' => Carbon::now()->toDateTimeString(),
                        'Editor' => 'RPA'
                    ]);
            }

            // ---MembroPessoaJuridica
            //---------------------------------------------------------------------------------------------
            $pessoaJuridica = DB::table('MembrosPessoasJuridicas')
                ->where('CodigoPessoaFisica', $pessoa ? $pessoa->Codigo : $idPessoaFisica)
                ->where('CodigoPessoaJuridica', $CodigoPessoaJuridica)
                ->first();

            //----------------------------
            //Inserir Pessoa Juridica
            if (!$pessoaJuridica) {
                //Inserir nova pessoa Juridica
                    DB::table('MembrosPessoasJuridicas')
                    ->insertGetId([
                        'DataCriacao' => Carbon::now()->toDateTimeString(),
                        'Criador' => 'RPA',
                        'CodigoPessoaFisica' => $pessoa ? $pessoa->Codigo : $idPessoaFisica,
                        'CodigoTipoPessoaFisica' => 1,
                        'CodigoPessoaJuridica' => $CodigoPessoaJuridica
                    ]);

            } else {
                //Atualizar se o status estiver desabilitado
                if ($pessoaJuridica->CodigoTipoPessoaFisica == 4) {
                    DB::table('MembrosPessoasJuridicas')
                        ->where('CodigoPessoaFisica', $pessoa ? $pessoa->Codigo : $idPessoaFisica)
                        ->where('CodigoPessoaJuridica', $CodigoPessoaJuridica)
                        ->update([
                            'DataAlteracao' => Carbon::now()->toDateTimeString(),
                            'Editor' => 'RPA',
                            'CodigoTipoPessoaFisica' => 1
                        ]);
                }
            }
        }

        DB::table('MembrosPessoasJuridicas')
            ->join('PessoasFisicas', 'PessoasFisicas.Codigo', '=', 'MembrosPessoasJuridicas.CodigoPessoaFisica')
            ->where('MembrosPessoasJuridicas.CodigoPessoaJuridica', $CodigoPessoaJuridica)
            ->where('MembrosPessoasJuridicas.CodigoTipoPessoaFisica', 1)
            ->whereNotIn('PessoasFisicas.CPF', $cpfs)
            ->update([
                'CodigoTipoPessoaFisica' => 4,
                'Editor' => 'RPA',
                'DataAlteracao' => Carbon::now()->toDateTimeString(),
            ]);

        //Desativar as diferenças entre Cadastro e Planilha
        return response()->json([
            'Mensagem' => 'Ok'
        ]);
    }
}
