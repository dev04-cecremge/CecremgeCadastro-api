<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MembroPessoaJuridica extends Model
{
    protected $primaryKey = 'Codigo';
    protected $table = 'MembrosPoessoasJuridicas';

    protected $fillable = [
        'Codigo',
        'DataAlteracao',
        'Editor',
        'DataCriacao',
        'Criador',
        'CodigoPessoaFisica',
        'CodigoTipoPessoaFisica',
        'CodigoPessoaJuridica',
        'CodigoCargo',
        'CodigoDepartamento',
        'NomeDoCargo',
    ];
}
