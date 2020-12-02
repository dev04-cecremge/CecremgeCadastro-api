<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dependente extends Model
{
    protected $primaryKey = 'Codigo';
    protected $table = 'Dependentes';

    protected $fillable = [
        'Codigo',
        'Nome',
        'CPF',
        'TelefoneContato',
        'TelefoneCelular',
        'Email',
        'DataNascimento',
        'Sexo',
        'DocumentoIdentidade',
        'OrgaoEmissorIdentidade',
        'DataAlteracao',
        'Editor',
        'DataCriacao',
        'Criador',
        'MatriculaUnimed',
        'CodigoPessoaFisicaTitular',
    ];
}
