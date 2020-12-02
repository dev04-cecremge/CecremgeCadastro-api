<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PessoaFisica extends Model
{
    protected $primaryKey = 'Codigo';
    protected $table = 'PessoasFisicas';

    protected $fillable = [
        'Codigo',
        'Nome',
        'CPF',
        'TelefoneContato',
        'EMail',
        'DataNascimento',
        'Nacionalidade',
        'Sexo',
        'DocumentoIdentidade',
        'OrgaoEmissorIdentidade',
        'Profissao',
        'FiliacaoPai',
        'FiliacaoMae',
        'ContaDominio',
        'DataEmissaoIdentidade',
        'CodigoNaturalidade',
        'CodigoEndereco',
        'CodigoEstadoEmissorIdentidade',
        'CodigoGrauEscolaridade',
        'CodigoRegimeCasamento',
        'CodigoEstadoCivil',
        'DataAlteracao',
        'Editor',
        'DataCriacao',
        'Criador',
        'NaturalidadeEstrangeira',
        'TelefoneCelular',
        'MatriculaUnimed',
    ];
}
