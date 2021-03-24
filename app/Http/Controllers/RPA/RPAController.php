<?php

namespace App\Http\Controllers\RPA;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RPAController extends Controller{


    public function indexHistoricos(){
        $allHistorico = DB::table('RPAHistorico')
        ->get();


        return response()->json( $allHistorico );
    }
    public function indexExcecoes(){
        $allHistorico = DB::table('RPAListaExcecao')
        ->get();


        return response()->json( $allHistorico );
    }
    public function indexStatus(){
        $allHistorico = DB::table('RPATiposStatus')
        ->get();
        return response()->json( $allHistorico );
    }
    public function indexConfiguracoes(){
        $allHistorico = DB::table('RPAConfiguracao')
        ->get();
        return response()->json( $allHistorico );
    }
    public function indexHistoricosDaData($data){
        return response()->response('Não implementado');
    }
    

    public function showHistoricos($codigoHistorico){
        $historico = DB::table('RPAHistorico')
            ->where('codigo', $codigoHistorico)
            ->first();

        return response()->json($historico);
    }
    public function showExcecoes($codigoExcecao){
        $historico = DB::table('RPAListaExcecao')
            ->where('Codigo', $codigoExcecao)
            ->first();

        return response()->json($historico);
    }
    public function showConfiguracoes($codigoConfiguracao){
        $historico = DB::table('RPAConfiguracao')
            ->where('Codigo', $codigoConfiguracao)
            ->first();
        return response()->json($historico);
    }

    
    public function storeHistoricos(Request $request){

        if (!$request->Agencia)
            return response()->json([
                'mensagem' => 'Erro. Falta parâmetro Agencia'
            ]);

        if (!$request->Data)
            return response()->json([
                'mensagem' => 'Erro. Falta parâmetro Data'
            ]);

        if (!$request->CodigoRPATiposStatus)
            return response()->json([
                'mensagem' => 'Erro. Falta parâmetro CodigoRPATiposStatus'
            ]);

        //Inserir
        $id = DB::table('RPAHistorico')
        ->insertGetId([
            "Agencia" => $request->Agencia,
            "Data" => $request->Data,
            "CodigoRPATiposStatus" => $request->CodigoRPATiposStatus
        ]);

        $novo = DB::table('RPAHistorico')
        ->where('Codigo', $id)
        ->first();

        return response()->json([
            'mensagem' => 'Novo Historico Criado!',
            'Novo' => $novo
        ]);
    }
    public function storeExcecoes(Request $request){

        if (!$request->Agencia)
            return response()->json([
                'mensagem' => 'Erro. Falta parâmetro Agência'
            ]);

        if (!$request->DataInicio)
            return response()->json([
                'mensagem' => 'Erro. Falta parâmetro DataInicio'
            ]);

        //Inserir
        $id = DB::table('RPAListaExcecao')
        ->insertGetId([
            "Agencia" => $request->Agencia,
            "DataInicio" => $request->DataInicio,
            "DataFim" => $request->DataFim
        ]);

        $novo = DB::table('RPAListaExcecao')
        ->where('Codigo', $id)
        ->first();

        return response()->json([
            'mensagem' => 'Nova configuração de excecao Criada!',
            'Nova' => $novo
        ]);
    }
    public function storeConfiguracoes(Request $request){

        if (!$request->Nome)
            return response()->json([
                'mensagem' => 'Erro. Falta parâmetro Nome'
            ]);

        if (!$request->DataIni)
            return response()->json([
                'mensagem' => 'Erro. Falta parâmetro DataIni'
            ]);
        if (!$request->DiasParaReinicioDasAtualizacoes){
            return response()->json([
                'mensagem' => 'Erro. Falta parâmetro DiasParaReinicioDasAtualizacoes'
            ]);
        }

        //Inserir
        $id = DB::table('RPAConfiguracao')
        ->insertGetId([
            "Nome" => $request->Nome,
            "Descricao" => $request->Descricao,
            "DataIni" => $request->DataIni,
            "DataFim" => $request->DataFim,
            "DiasParaReinicioDasAtualizacoes" => $request->DiasParaReinicioDasAtualizacoes
        ]);

        $novo = DB::table('RPAConfiguracao')
        ->where('Codigo', $id)
        ->first();

        return response()->json([
            'mensagem' => 'Nova configuração RPA Criada!',
            'Nova' => $novo
        ]);
    }

    public function updateHistoricos(Request $request, $codigoHistorico){

        $item = DB::table('RPAHistorico')
        ->where('Codigo', $codigoHistorico)
        ->first();

        if (!$item)
            return response()->json([
                'Mensagem' => 'Erro. Historico ão existe.'
            ]);
        
        //Atualizo
        DB::table('RPAHistorico')
            ->where('Codigo', $codigoHistorico)
            ->update([
                'Agencia' => $request->Agencia,
                'Data' => $request->Data,
                'CodigoRPATiposStatus' => $request->CodigoRPATiposStatus
            ]);

        return response()->json([
            'menssagem' => 'Sucesso. Historico '.$codigoHistorico.' Atualizado',
        ]);
    }
    public function updateExcecoes(Request $request, $codigoExcecao){

        $item = DB::table('RPAListaExcecao')
        ->where('Codigo', $codigoExcecao)
        ->first();

        if (!$item)
            return response()->json([
                'Mensagem' => 'Erro. Excecao não existe.'
            ]);
        
        if ( !$request->Agencia and !$request->DataInicio and !$request->DataFim)
            return response()->json(['Mensagem' => 'Nao existem parâmetros a serem atualizados, favor criar Json com parâmetros']);

        //Atualizo
        DB::table('RPAListaExcecao')
            ->where('Codigo', $codigoExcecao)
            ->update([
                'Agencia' => $request->Agencia,
                'DataInicio' => $request->DataInicio,
                'DataFim' => $request->DataFim
            ]);

        return response()->json([
            'menssagem' => 'Sucesso. Lsita de Exceção '.$codigoExcecao.' foi atualizada',
        ]);
    }
    public function updateConfiguracoes(Request $request, $codigoConfiguracao){

        $item = DB::table('RPAConfiguracao')
        ->where('Codigo', $codigoConfiguracao)
        ->first();

        if (!$item)
            return response()->json([
                'Mensagem' => 'Erro. Configuração não existe.'
            ]);
        
        if ( !$request->Nome or !$request->DataIni or !$request->DiasParaReinicioDasAtualizacoes)
            return response()->json(['Mensagem' => 'Nao existem parâmetros a serem atualizados, favor criar Json com parâmetros']);

        //Atualizo
        DB::table('RPAConfiguracao')
            ->where('Codigo', $codigoConfiguracao)
            ->update([
                'Nome' => $request->Nome,
                'Descricao' => $request->Descricao,
                'DataIni' => $request->DataIni,
                'DataFim' => $request->DataFim,
                'DiasParaReinicioDasAtualizacoes' => $request->DiasParaReinicioDasAtualizacoes
            ]);

        return response()->json([
            'menssagem' => 'Sucesso. Lsita de Exceção '.$codigoConfiguracao.' foi atualizada',
        ]);
    }

    public function destroyHistoricos($codigoHistorico){

        DB::table('RPAHistorico')
            ->where('Codigo', $codigoHistorico)
            ->delete();
        
        return response()->json([
            'Mensagem' => 'Sucesso. Historico '.$codigoHistorico.' foi deletado'
        ]);

    }
    public function destroyExcecoes($codigoExcecao){

        DB::table('RPAListaExcecao')
            ->where('Codigo', $codigoExcecao)
            ->delete();
        
        return response()->json([
            'Mensagem' => 'Sucesso. Lista de Exceções '.$codigoExcecao.' foi deletada'
        ]);

    }
    public function destroyConfiguracoes($codigoConfiguracao){

        DB::table('RPAConfiguracao')
            ->where('Codigo', $codigoConfiguracao)
            ->delete();
        
        return response()->json([
            'Mensagem' => 'Sucesso. Configuração '.$codigoConfiguracao.' foi deletada'
        ]);

    }



}