<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'pessoasFisicas'], function () {
    Route::get('/', 'PessoaFisicaController@index');
    Route::get('/{cpf}', 'PessoaFisicaController@buscarPorCpf');
});

Route::group(['prefix' => 'cooperativas'], function () {
    Route::get('/', 'CooperativaController@index');
    Route::get('/{codigoCooperativa}/funcionarios', 'CooperativaController@funcionarios');
    Route::get('/{codigoCooperativa}/funcionariosPorTipo', 'CooperativaController@funcionariosPorTipo');
});

Route::group(['prefix' => 'sistemasCecremge'], function () {
    Route::get('/', 'SistemaCecremgeController@index');
    Route::get('/{codigoSistema}/{contaDominio}', 'SistemaCecremgeController@permissaoContaDominio');
});

Route::group(['prefix' => 'pessoasJuridicas'], function () {
    Route::get('/', 'PessoaJuridicaController@index');
});

Route::group(['prefix' => 'tiposPessoasFisicas'], function () {
    Route::get('/', 'TipoPessoaFisicaController@index');
    Route::get('/{codigoTipo}', 'TipoPessoaFisicaController@pessoasFisicasPorTipo');
});
