<?php

namespace Modules\Administration\Entities;

// use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GruposSubPuntos extends Pivot
{
    use HasFactory;

    protected $table = 'grupos_sub_puntos';

    protected $fillable = [
        'grupo_punto_id',
        'sub_grupo_id',
        'punto_id',
    ];


}
