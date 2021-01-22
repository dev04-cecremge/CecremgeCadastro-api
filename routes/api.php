<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'sessions'], function () {
    Route::get('/', 'SessionController@index')->middleware('jwt.auth');
    Route::post('/', 'SessionController@store');
    Route::post('/refresh', 'SessionController@refresh')->middleware('jwt.auth');
    Route::post('/destroy', 'SessionController@destroy')->middleware('jwt.auth');
});

Route::group(['prefix' => 'jedi'], function () {
    Route::post('/cooperativas/atualizar-funcionarios', 'JEDIController@atualizarFuncionariosCooperativa');
});

Route::group(['prefix' => 'central', 'namespace' => 'Central', 'middleware' => 'jwt.auth'], function () {
    //

    Route::group(['prefix' => 'departamentos'], function () {
        Route::get('/', 'DepartamentoController@index');
        Route::post('/', 'DepartamentoController@store');

        Route::get('/{codigoDepartamento}', 'DepartamentoController@show');

        Route::put('/{codigoDepartamento}', 'DepartamentoController@update');
        Route::delete('/{codigoDepartamento}', 'DepartamentoController@destroy');

        Route::get('/{codigoDepartamento}/gerente', 'DepartamentoController@gerenteDoDepartamento');
        Route::get('/{codigoDepartamento}/funcionarios', 'DepartamentoController@funcionariosDoDepartamento');
    });

    Route::group(['prefix' => 'pessoas-fisicas'], function () {

        Route::get('/gerentes', 'PessoaFisicaController@gerentes');
        Route::get('/supervisores', 'PessoaFisicaController@supervisores');
        Route::get('/{codigoPessoaFisica}', 'PessoaFisicaController@show');

        Route::get('/', 'PessoaFisicaController@index');
        Route::post('/', 'PessoaFisicaController@store');
        Route::put('/{codigoPessoaFisica}', 'PessoaFisicaController@update');
        Route::delete('/{codigoPessoaFisica}', 'PessoaFisicaController@destroy');

        Route::get('/{cpf}/departamento', 'PessoaFisicaController@departamentoDaPessoaFisica');
        Route::put('/{cpf}/departamento', 'PessoaFisicaController@atualizarDepartamento');
        Route::get('/{contaDominio}/departamentoContaDominio', 'PessoaFisicaController@departamentoDaPessoaFisicaContaDominio');
        
        Route::put('/{cpf}/contaDeDominio', 'PessoaFisicaController@atualizarContaDeDominio');
    });

    Route::group(['prefix' => 'sistemas-cecremge'], function () {
        Route::put('/{codigoSistema}', 'SistemaCecremgeController@update');
        Route::delete('/{codigoSistema}', 'SistemaCecremgeController@destroy');
        Route::get('/{codigoSistema}', 'SistemaCecremgeController@show');
        
        Route::get('/', 'SistemaCecremgeController@index');

        Route::post('/', 'SistemaCecremgeController@store');

    });

    Route::group(['prefix' => 'cooperativas'], function () {
        Route::get('/', 'CooperativaController@index');
        Route::get('/{codigoCooperativa}', 'CooperativaController@show');
        Route::post('/', 'CooperativaController@store');
        Route::put('/{codigoCooperativa}', 'CooperativaController@update');
        Route::delete('/{codigoCooperativa}', 'CooperativaController@destroy');
    });

    //
});
