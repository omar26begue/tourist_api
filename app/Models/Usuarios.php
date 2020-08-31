<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Usuarios extends Authenticatable implements JWTSubject
{
    const USUARIO_VERIFICADO = '1';
    const USUARIO_NO_VERIFICADO = '0';

    const USUARIO_ACTIVO = '1';
    const USUARIO_NOACTIVO = '0';

    const ROL_ADMIN = 'admin';
    const ROL_TOURIST = 'tourist';

    protected $table = 'tourist_usuarios';

    protected $primaryKey = 'idusuario';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'idusuario',
        'rol',
        'nombres',
        'apellido1',
        'apellido2',
        'email',
        'activo',
        'verificado',
        'password',
        'fecha_creacion',
        'fecha_ultimo_acceso',
        'photo',
        'token',
    ];

    protected $hidden = [
        'password'
    ];

    protected $casts = [
        'idusuario' => 'string',
        'rol' => 'string',
        'nombres' => 'string',
        'apellido1' => 'string',
        'apellido2' => 'string',
        'email' => 'string',
        'activo' => 'string',
        'verificado' => 'string',
        'password' => 'string',
        'fecha_creacion' => 'date:Y-m-d',
        'fecha_ultimo_acceso' => 'datetime:Y-m-d H:mm',
        'photo' => 'string',
        'token' => 'string',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function getNombresAttribute($value)
    {
        return ucfirst($value);
    }

    public function setNombresAttribute($value)
    {
        $this->attributes['nombres'] = strtolower($value);
    }

    public function getApellido1Attribute($value)
    {
        return ucfirst($value);
    }

    public function setApellido1Attribute($value)
    {
        $this->attributes['apellido1'] = strtolower($value);
    }

    public function getApellido2Attribute($value)
    {
        return ucfirst($value);
    }

    public function setApellido2Attribute($value)
    {
        $this->attributes['apellido2'] = strtolower($value);
    }

    public function setEmailAttribute($value)
    {
        $this->attributes['email'] = strtolower($value);
    }
}
