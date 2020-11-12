<?php

namespace App\Models;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Foundation\Auth\User as Authenticatable;

class TrilhaDeAuditoria extends Authenticatable implements JWTSubject
{
    public $timestamps = false;

    protected $table = 'TrilhaDeAuditoria_API';
    protected $fillable = [
        'contaDominio',
        'codigoCooperativa',
        'tokenGeradoEm',
        'tokenExpiradoEm',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
