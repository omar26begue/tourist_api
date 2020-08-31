<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SitiosTuristicos extends Model
{
    protected $table = 'tourist_sitios';

    protected $primaryKey = 'idsitio';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'idsitio',
        'nombre',
        'ubicacion',
        'distancia',
        'tiempo',
        'poblacion',
        'descripcion',
        'hora_apertura',
        'hora_cierre',
        'calificacion'
    ];

    protected $casts = [
        'idsitio' => 'string',
        'nombre' => 'string',
        'ubicacion' => 'string',
        'distancia' => 'float',
        'tiempo' => 'int',
        'poblacion' => 'int',
        'descripcion' => 'string',
        'hora_apertura' => 'string',
        'hora_cierre' => 'string',
        'calificacion' => 'int'
    ];
}
