<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'sessions'], function () {
    Route::get('/', 'SessionController@index')->middleware('jwt.auth');
    Route::post('/', 'SessionController@store');
    Route::post('/refresh', 'SessionController@refresh')->middleware('jwt.auth');
    Route::post('/destroy', 'SessionController@destroy')->middleware('jwt.auth');
});

Route::group(['prefix' => 'jedi', 'middleware' => 'jwt.auth'], function () {
    Route::post('/atualizarAssociadosCooperativa', 'JEDIController@atualizarAssociadosCooperativa');
});

Route::group(['prefix' => 'central', 'namespace' => 'Central', 'middleware' => 'jwt.auth'], function () {
    //

    Route::group(['prefix' => 'departamentos'], function () {
        Route::get('/', 'DepartamentoController@index');
        Route::get('/{codigoDepartamento}', 'DepartamentoController@show');
        Route::post('/', 'DepartamentoController@store');
        Route::put('/{codigoDepartamento}', 'DepartamentoController@update');
        Route::delete('/{codigoDepartamento}', 'DepartamentoController@destroy');

        Route::get('/{codigoDepartamento}/gerente', 'DepartamentoController@gerenteDoDepartamento');
        Route::get('/{codigoDepartamento}/funcionarios', 'DepartamentoController@funcionariosDoDepartamento');
    });

    Route::group(['prefix' => 'pessoas-juridicas'], function () {
        Route::get('/', 'PessoaJuridicaController@index');
        Route::get('/{codigoPessoaJuridica}', 'PessoaJuridicaController@show');
        Route::post('/', 'PessoaJuridicaController@store');
        Route::put('/{codigoPessoaJuridica}', 'PessoaJuridicaController@update');
        Route::delete('/{codigoPessoaJuridica}', 'PessoaJuridicaController@destroy');
    });

    Route::group(['prefix' => 'pessoas-fisicas'], function () {
        Route::get('/', 'PessoaFisicaController@index');
        Route::get('/{codigoPessoaFisica}', 'PessoaFisicaController@show');
        Route::post('/', 'PessoaFisicaController@store');
        Route::put('/{codigoPessoaFisica}', 'PessoaFisicaController@update');
        Route::delete('/{codigoPessoaFisica}', 'PessoaFisicaController@destroy');

        Route::get('/gerentes', 'PessoaFisicaController@gerentes');
        Route::get('/supervisores', 'PessoaFisicaController@supervisores');
        Route::get('/{cpf}/departamento', 'PessoaFisicaController@departamentoDaPessoaFisica');
        Route::put('/{cpf}/departamento', 'PessoaFisicaController@atualizarDepartamento');
    });

    Route::group(['prefix' => 'dependentes'], function () {
        Route::get('/', 'DependenteController@index');
        Route::get('/{codigoDependente}', 'DependenteController@show');
        Route::post('/', 'DependenteController@store');
        Route::put('/{codigoDependente}', 'DependenteController@update');
        Route::delete('/{codigoDependente}', 'DependenteController@destroy');
    });

    Route::group(['prefix' => 'sistemas-cecremge'], function () {
        Route::get('/', 'SistemaCecremgeController@index');
        Route::get('/{codigoSistema}', 'SistemaCecremgeController@show');
        Route::post('/', 'SistemaCecremgeController@store');
        Route::put('/{codigoSistema}', 'SistemaCecremgeController@update');
        Route::delete('/{codigoSistema}', 'SistemaCecremgeController@destroy');
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
