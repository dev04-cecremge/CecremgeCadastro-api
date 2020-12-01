<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'sessions'], function () {
    Route::get('/', 'SessionController@index')->middleware('jwt.auth');
    Route::post('/', 'SessionController@store');
});

Route::group(['prefix' => 'central', 'namespace' => 'Central'], function () {
    Route::group(['prefix' => 'departamentos'], function () {
        Route::get('/', 'DepartamentoController@index');
        Route::get('/{codigoDepartamento}/gerente', 'DepartamentoController@gerenteDoDepartamento');
        Route::get('/{codigoDepartamento}/funcionarios', 'DepartamentoController@funcionariosDoDepartamento');
    });

    Route::group(['prefix' => 'pessoas-fisicas'], function () {
        Route::get('/', 'PessoaFisicaController@index');
        Route::get('/gerentes', 'PessoaFisicaController@gerentes');
        Route::get('/supervisores', 'PessoaFisicaController@supervisores');
        Route::get('/{cpf}/departamento', 'PessoaFisicaController@departamentoDaPessoaFisica');
    });
});
