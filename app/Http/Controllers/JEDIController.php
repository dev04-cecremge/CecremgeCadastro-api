<?php

namespace App\Http\Controllers;

use Importer;
use Carbon\Carbon;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JEDIController extends Controller
{

    public function atualizarFuncionariosCooperativa(Request $request)
    {
        if (!$request->planilha)
            return response()->json([
                'Mensagem' => 'Planilha não foi informada'
            ], 401);

        $excel = Importer::make('Excel');
        $excel->load($request->planilha);
        
        $planilha = $excel->getCollection();

        // Agencia da planilha diferente do URL
        //9 - agencia
        //1 - intituicao
        $num = 1;
        foreach ($planilha as $linha) {
            if ($linha[1] != 'Instituição:') $num++;
            else break;
        }
        
        //Pego a agencia
        $agencia =  trim($planilha[$num][9]);
        
        // Agencia diferente da agencia do documento
        $linha = 1;

        // Resolvendo a cooperativa
        $cooperativa = [];

        // Pego o numero de dias dinâmico da configuração:
        $configuracaoAtual = DB::table('RPAConfiguracao')
            ->where('DataIni', '<=', now()) 
            ->where(function($query){
                $query->where('DataFim', '>=', now())
                ->orWhere('DataFim', NULL);
            })
            ->first();
        
        // Agencia 2003 - Excecao para Central
        $CodigoPessoaJuridica = 1;
        if ($agencia == '2003') {
            //---------------------------------------------------------------------------------------------
            $cooperativa = [
                'CodigoPessoaJuridica' => 1,
                'Agencia' => 2003,
                'Nome' => 'Sicoob Central Cecremge'
            ];
        } else {

            // Verificar se essa cooperativa existe
            $agenciaCooperativaValida = DB::table('Cooperativas')
            ->where('Agencia', $agencia)
            ->first();

            if (!$agenciaCooperativaValida){
                //Atualizar DB - Agencia Nao existe!
                DB::table('RPAHistorico')->insert(
                    [
                        'Agencia' => $agencia,
                        'Data' => now(),
                        'CodigoRPATiposStatus' => 5
                    ]    
                );
    
                //Retorna erro
                return response()->json([
                    'Mensagem' => 'Cooperativa - '.$agencia.' - não existe no cadastro!'
                ], 201);

            }
                
            $cooperativa = DB::table('Cooperativas')
                ->where('Agencia', $agencia)
                ->select('CodigoPessoaJuridica', 'Agencia', 'Sigla AS Nome')
                ->first();

            $CodigoPessoaJuridica = $cooperativa->CodigoPessoaJuridica;
        }   

        // Checar se essa Cooperativa está  'Barrada' na data de hoje
        $cooperativaBarrada = DB::table('RPAListaExcecao')
            ->where('Agencia', $agencia)
            ->where('DataInicio', '<=', now())
            ->where(function($query){
                $query->where('DataFim', '>=', now())
                ->orWhere('DataFim', NULL);
            })
            ->first();
        
        //Checar se cooperativa esta barrada hoje
        if ($cooperativaBarrada){
            //Atualiza DB com "Barrada"
            DB::Table('RPAHistorico')->insert(
                [
                    'Agencia' => $agencia,
                    'Data' => now(),
                    'CodigoRPATiposStatus' => 2
                ]    
            );

            return response()->json([
                'Mensagem' => 'Cooperativa - '.$agencia.' - Está barrada quanto a atualizações na data de hoje'
            ],200);

        }

        //Checar se cooperativa já foi atualizada nos últimos 7 dias
        $cooperativaAtualizadaNaUltimaSemana = DB::table('RPAHistorico')
            ->where('Agencia', $agencia)
            ->where('Data', '>=', now()->subDays($configuracaoAtual->DiasParaReinicioDasAtualizacoes + 1))
            ->where('CodigoRPATiposStatus', 1)
            ->first();


        //Checar se a cooperativa foi atualizada na ultima semana
        if ($cooperativaAtualizadaNaUltimaSemana){
            //Retorna sucesso
            return response()->json([
                'Mensagem' => 'Cooperativa - '.$agencia.' - já foi atualizada nos ultimos '.$configuracaoAtual->DiasParaReinicioDasAtualizacoes.' dias!'
            ], 200);
        }
        
        // Pegar informações das pessoas na planilha
        $cpfs = [];
        $pessoasFisicas = [];
        $CPFUnicos = [];

        $primeiraLinha = 1;
        foreach ($planilha as $linha) {
            if ($linha[0] !== 'CPF') $primeiraLinha++;
            else break;
        }

        for ($i = $primeiraLinha; $i <= sizeof($planilha) - 1; $i++) {
            if (!empty($planilha[$i][0])) {
                $linha = array($planilha[$i][0], $planilha[$i][6], $planilha[$i][12], $planilha[$i][13], $planilha[$i][21]);

                array_push($cpfs, $planilha[$i][0]);
                array_push($pessoasFisicas, $linha);
            }
        }

        //---------Agrupar e pegar somente CPFS Válidos
        //1 - Criar grupo de CPFS unicos:
        $CPFUnicos = array_unique($cpfs);
        //2 - Andar por ese grupo para filtrar somente os campos necessários
        $pessoasFisicasNecessarias = [];
        $ativo = false;
        $exemploFalso = "";
        foreach($CPFUnicos as $cpf){
            //Pego todas as pessoas com ese cpf e dou preferencia se existir uma com status ativo:
            foreach($pessoasFisicas as $pessoa){
                if ($pessoa[0] == $cpf and $pessoa[4] == 'Ativo'){
                    array_push($pessoasFisicasNecessarias, $pessoa);
                    $ativo = true;
                    break;
                }else if ($pessoa[0] == $cpf and $pessoa[4] != 'Ativo'){ 
                    //Pego qualquer um, pois ela só vai ser desativada!
                    $exemploFalso = $pessoa;
                }
            }
            //Ver se tem apenas nao ativos:
            if (!$ativo){
                array_push($pessoasFisicasNecessarias, $exemploFalso);
            }

            $ativo = false;
        }
        
        // Atualizar ou adicionar
        foreach ($pessoasFisicasNecessarias as $item) {

            $pessoa = DB::table('PessoasFisicas')
                ->where('CPF', $item[0])
                ->first();

            //Atualizo de acordo com o status da planilha. Diferente de ativo, será desabilitado
            $novoStatus = 1;
            //Basta comentar esse IF, para que nao desabilite as pessoas!
            if ( $item[4] != 'Ativo' ){
                $novoStatus = 4;
            }
            
            $idPessoaFisica = '';
            // Adicionar nova pessoa e membroPessoaFisica
            if (!$pessoa) {
                
                //Adiciono SOMENTE se estiver ATIVA! 
                //Pra que adicionar alguem que está inativo em uma cooperativa se ele nao sera colocado na MPJ?
                if ($novoStatus == 1){
                    // Pessoasfisicas
                    $idPessoaFisica = DB::table('PessoasFisicas')
                        ->insertGetId([
                            'CPF' => $item[0],
                            'ContaDominio' => strtolower($item[1]),
                            'Nome' => trim(ucwords(mb_strtolower($item[2]))),
                            'Email' => $item[3],
                            'DataCriacao' => Carbon::now()->toDateTimeString(),
                            'Criador' => 'RPA'
                        ]);
                }
            } else {

                //Existe pessoa física cadastrada, mas só atualizo os dados se ela estiver "ATIVA"
                //Se eu modificar esses dados para as desativas, isso vai gerar contas de domínio erradas.
                if ($novoStatus == 1){
                    //Atualize o pessoafisica
                    //Atualize quando a conta de dominio for diferente
                    if ( $pessoa->ContaDominio != strtolower($item[1]) ){
                        DB::table('PessoasFisicas')
                            ->where('CPF', $pessoa->CPF)
                            ->update([
                                'ContaDominio' => strtolower($item[1]),
                                'DataAlteracao' => Carbon::now()->toDateTimeString(),
                                'Editor' => 'RPA'
                            ]);
                    }
                }

            }

            // ---MembroPessoaJuridica
            //---------------------------------------------------------------------------------------------
            //Se a pessoa nao existe, eu nao tem nada a se fazer na conta de dominio!
            if (!$pessoa and !$idPessoaFisica) 
                continue;

            $pessoaJuridica = DB::table('MembrosPessoasJuridicas')
                ->where('CodigoPessoaFisica', $pessoa? $pessoa->Codigo : $idPessoaFisica)
                ->where('CodigoPessoaJuridica', $CodigoPessoaJuridica)
                ->first();
            //----------------------------
            
            //Inserir Pessoa Juridica
            if (!$pessoaJuridica) {
                //Inserir nova pessoa Juridica APENAS se for ATIVA, inativa não será inserida!
                if ($novoStatus == 1){
                    DB::table('MembrosPessoasJuridicas')
                    ->insertGetId([
                        'DataCriacao' => Carbon::now()->toDateTimeString(),
                        'Criador' => 'RPA',
                        'CodigoPessoaFisica' => $pessoa? $pessoa->Codigo: $idPessoaFisica,
                        'CodigoTipoPessoaFisica' => $novoStatus,
                        'CodigoPessoaJuridica' => $CodigoPessoaJuridica
                    ]);
                }

            } else {

                //Atualizar membroPessoaJuridica SOMENTE se o status anteriro do cadastro for 1 ou 4, para não 
                //afetar conselheiros, presidentes...
                if (
                    ($pessoaJuridica->CodigoTipoPessoaFisica == 1 or  $pessoaJuridica->CodigoTipoPessoaFisica == 4)
                    and $pessoaJuridica->CodigoTipoPessoaFisica != $novoStatus
                    )
                    {
                    DB::table('MembrosPessoasJuridicas')
                        ->where('CodigoPessoaFisica', $pessoa ? $pessoa->Codigo : $idPessoaFisica)
                        ->where('CodigoPessoaJuridica', $CodigoPessoaJuridica)
                        ->update([
                            'DataAlteracao' => Carbon::now()->toDateTimeString(),
                            'Editor' => 'RPA',
                            'CodigoTipoPessoaFisica' => $novoStatus
                        ]);
                }
            }
        }

        /*
        //Isso desativa as pessoas que não aparecem na listagem de CPF do SISBR.
        //O problema aqui, é que quando a pessoa não acessa o SISBR por muito tempo ela fica desativada, entao
        //desativaria pessoas importantes.
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
        */

        //Inserir no banco, com ostauts OK:
        DB::Table('RPAHistorico')->insert(
            [
                'Agencia' => $agencia,
                'Data' => now(),
                'CodigoRPATiposStatus' => 1
            ]    
        );

        return response()->json([
            'Mensagem!' => 'Cooperativa - '.$agencia.' - Atualizada!'
        ], 200);
        

    }

    public function tratarListaCooperativas(Request $request){

        //Ver se todas coopertivas estão validas
        $listaCooperativas = $request->cooperativas;

        if (!$listaCooperativas){
            return response()->json([
                'Mensagem' => 'Erro. A lista: cooperativas não foi encontrada. '
            ]);
        }

        //dias de não re-atualização dinâmica
        $diasParareset = DB::table('RPAConfiguracao')
            ->where('DataIni', '<=', now()) 
            ->where(function($query){
                $query->where('DataFim', '>=', now())
                ->orWhere('DataFim', NULL);
            })
            ->first();

        //Todas Agencias que nao devem atualizar:
        $naoAtualizar = DB::table('RPAHistorico')
            ->where('Data', '>=', now()->subDays($diasParareset->DiasParaReinicioDasAtualizacoes + 1))
            ->where('CodigoRPATiposStatus', 1)
            ->get();
        $listaNaoAtualizar = [];

        foreach($naoAtualizar as $item){
            array_push($listaNaoAtualizar, $item->Agencia);
        }
        //Todas Agencias que nao devem atualizar:

        //todas cooperativas do sistema, para encontrar cooperativas invalidas:
        $todas = DB::table('cooperativas')->get();
        $listaTodas = [];
        foreach($todas as $item){
            array_push($listaTodas, $item->Agencia);
        }

        //Agencias barradas via Excecao
        $viaExcecao = DB::table('RPAListaExcecao')
            ->where('DataInicio', '<=', now())
            ->where(function($query){
                $query->where('DataFim', '>=', now())
                ->orWhere('DataFim', NULL);
            })
            ->get();

        $listaExcecao = [];
        foreach($viaExcecao as $item){
            array_push($listaExcecao, $item->Agencia);
        }

        //Lista todos ja pegos nohsitorico hoje
        $listaDeHojeHistorico = DB::table('RPAHistorico')
            ->where('Data', '>=', now()->format('Y-m-d'))
            ->whereIn('CodigoRPATiposStatus', ([2,3,5]))
            ->get();
        $listaDeJaInclusosNohistorico = [];
        foreach($listaDeHojeHistorico as $item){
            array_push($listaDeJaInclusosNohistorico , $item->Agencia);
        }
        

        $listaDeretorno = [];
        //Lista de agencias ja realizadas atualizacoes na data X
        foreach($listaCooperativas as $item){
            
            //Aparece nas barradas?
            if ( in_array($item, $listaExcecao) ){
                //Inserir no historico, como erro por ser barrada
                //Insiro apenas se nao existir um log dessa mesma cooperativa hoje:
                if (in_array($item, $listaDeJaInclusosNohistorico) == false){

                    DB::table('RPAHistorico')
                        ->insert([
                            'Agencia' => $item,
                            'Data' => now(),
                            'CodigoRPATiposStatus' => 2
                        ]);
                }

            }else{
                if ( in_array($item, $listaNaoAtualizar) == false ){
                    //Nao foi barrada, nem atualizada nos ultimos X dias!

                    //Mas é uma coopertiva valida?
                    if (in_array($item, $listaTodas) ==false ){
                        //Agencia invalida!
                        //Insira somente, se nao aparecer um log dela hoje!
                        if (in_array($item, $listaDeJaInclusosNohistorico) == false){
                            DB::table('RPAHistorico')
                            ->insert([
                                'Agencia' => $item,
                                'Data' => now(),
                                'CodigoRPATiposStatus' => 5
                            ]);
                        }
                    }else{
                        //Agencia valida!
                        array_push($listaDeretorno, $item);
                    }
                }
            }

        }

        return response()->json([
            'cooperativas' => $listaDeretorno
        ]);
    }

    public function inserirInativa($agencia){
        DB::table('RPAHistorico')
            ->insert([
                'Agencia' => $agencia,
                'Data' => now(),
                'CodigoRPATiposStatus' => 3
            ]);
        
        return response()->json([
            'Mensagem' => 'Sucesso. Angencia '.$agencia.' foi inserida como inativa'
        ]);
    }

    public function inserirErroSISBR($agencia){
        DB::table('RPAHistorico')
            ->insert([
                'Agencia' => $agencia,
                'Data' => now(),
                'CodigoRPATiposStatus' => 4
            ]);
        
        return response()->json([
            'Mensagem' => 'Sucesso. Angencia '.$agencia.' foi inserida como ERROSISBR'
        ]);
    }


}
