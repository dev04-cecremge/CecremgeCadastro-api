<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use Importer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;



class JEDIController extends Controller
{
    public function primeiro(Request $request)
    {
        $excel = Importer::make('Excel');
        $excel->load($request->planilha);

        $planilha = $excel->getCollection();

        //Cabeçalho - vai ate a 8ª linha
        $agencia = $request->agencia;

        //Pegar informações das pessoas na planilha
        $pessoasFisicas = array();
        $cpfs = array();
        for ($i = 12; $i <= sizeof($planilha) - 1; $i++ ){
            if ( !empty($planilha[$i][0]) ){
                $linha = array( $planilha[$i][0], $planilha[$i][6], $planilha[$i][12], $planilha[$i][13] );

                array_push( $cpfs, $planilha[$i][0]);
                array_push( $pessoasFisicas, $linha );
            }
        }

        //Cooperativa relativa a pessoa
        //---------------------------------------------------------------------------Adicionar exceção quando vem 1
        $cooperativa = DB::table('Cooperativas')
            ->where('Agencia', $agencia)
            ->first();



        //Atualizar ou adicionar
        foreach($pessoasFisicas as $item){
            //Zero os valores do codpessoafisica e cod membrospessoasjuridicas
            $idPessoaFisica = -1;
            $idMembroPessoaJuridica = -1;

            $pessoa = DB::table('PessoasFisicas')
            ->where('CPF', $item[0])
            ->first();
            
            //Adicionar nova pessoa e membroPessoaFisica
            if ( !$pessoa ){
                //Pessoasfisicas
                $idPessoaFisica = DB::table('PessoasFisicas')
                ->insertGetId([
                    'CPF' => $item[0],
                    'ContaDominio' => strtolower($item[1]),
                    'Nome' => $item[2],
                    'Email' => $item[3],
                    'DataCriacao' => Carbon::now()->toDateTimeString(),
                    'Criador' => 'RPA'
                ]);

            }else{
                //Atualize o pessoafisica
                $pessoaFisica = DB::table('PessoasFisicas')
                    ->where('CPF', $pessoa->CPF)
                    ->update(
                        array(
                            'Nome' => $item[2],
                            'EMail' => $item[3], //-------------------------------------Modificar quando nao tem email!
                            'ContaDominio' => strtolower($item[1]),
                            'DataAlteracao' => Carbon::now()->toDateTimeString(),
                            'Editor' => 'RPA'
                        )
                    );  
                
                $idPessoaFisica = DB::table('PessoasFisicas')
                ->where('CPF', $pessoa->CPF)
                ->first();
                
                //Atualizo o ID
                $idPessoaFisica = $idPessoaFisica->Codigo;
            }

            //-------------------------------
            //---MembroPessoaJuridica
            $pessoaJuridica = DB::table('MembrosPessoasJuridicas')
                ->where('CodigoPessoaFisica', $idPessoaFisica )
                ->first();

            //Inserir Pessoa Juridica
            if ( !$pessoaJuridica ){

                //Inserir nova pessoa Juridica
                $membroPessoaJuridica = DB::table('MembrosPessoasJuridicas')
                    ->insertGetId([
                        'DataCriacao' => Carbon::now()->toDateTimeString(),
                        'Criador' => 'RPA',
                        'CodigoPessoaFisica' => $idPessoaFisica,
                        'CodigoTipoPessoaFisica' => 1,
                        'CodigoPessoaJuridica' => $cooperativa->CodigoPessoaJuridica
                    ]);
     
            //Atualizar linha da pessoa juridica
            }else{
                //Atualizo se o status estiver desabilitado
                if ( $pessoaJuridica->CodigoTipoPessoaFisica == 4 ){

                    $membroPessoaJuridica = DB::table('MembrosPessoasJuridicas')
                    ->where('CodigoPessoaFisica', $idPessoaFisica)
                    ->where('CodigoPessoaJuridica', $cooperativa->CodigoPessoaJuridica)
                    ->update(
                        array(
                            'DataAlteracao' => Carbon::now()->toDateTimeString(),
                            'Editor' => 'RPA',
                            'CodigoTipoPessoaFisica' => 1
                        )
                    );
                }
            }
        }
        
        //Desativar as diferenças entre Cadastro e Planilha
        return response()->json( [
            'Mensagem' => 'OK'
        ]);
    }

    
}
