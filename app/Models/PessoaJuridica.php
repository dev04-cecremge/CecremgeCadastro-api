<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PessoaJuridica extends Model
{
    protected $primaryKey = 'Codigo';
    protected $table = 'PessoasJuridicas';

    protected $fillable = [
        'Codigo',
        'NomeFantasia',
        'RazaoSocial',
        'CNPJ',
        'Contato',
        'EMail',
        'Telefone',
        'CodigoEndereco',
        'CodigoTipoPessoaJuridica',
        'DataAlteracao',
        'Editor',
        'DataCriacao',
        'Criador',
        'DataAdesaoCadeiaSites',
        'DataPublicacaoSiteCadeia',
    ];
}
