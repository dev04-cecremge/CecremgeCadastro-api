<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrilhaDeAuditoria extends Model
{
    public $timestamps = false;

    protected $table = 'TrilhaDeAuditoria_API';
    protected $fillable = [
        'contaDominio',
        'codigoCooperativa',
        'tokenGeradoEm',
        'tokenExpiradoEm',
    ];
}
