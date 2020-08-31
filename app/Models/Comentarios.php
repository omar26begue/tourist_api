<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comentarios extends Model
{
    protected $table = 'tourist_comentarios';

    protected $primaryKey = 'idcomentarios';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'idcomentarios',
        'texto',
        'idusuario',
        'fecha',
    ];

    protected $casts = [
        'idcomentarios' => 'string',
        'texto' => 'text',
        'idusuario' => 'uuid',
        'fecha' => 'datetime:Y-m-d H:m',
    ];
}
