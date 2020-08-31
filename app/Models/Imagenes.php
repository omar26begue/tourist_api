<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Imagenes extends Model
{
    protected $table = 'tourist_imagenes';

    protected $primaryKey = 'idimagenes';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'idimagenes',
        'idsitio',
        'nombre',
        'calificacion',
        'lat',
        'lon',
        'zoom'
    ];
}
