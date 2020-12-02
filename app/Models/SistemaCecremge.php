<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SistemaCecremge extends Model
{
    protected $primaryKey = 'Codigo';
    protected $table = 'SistemasCecremge';

    protected $fillable = [
        'Codigo',
        'Nome',
        'Descricao',
        'DataAlteracao',
        'Editor',
        'DataCriacao',
        'Criador',
        'URL',
        'URLImagemSistema',
        'IndicadorAtivo',
        'OrdemApresentacao',
    ];
}
