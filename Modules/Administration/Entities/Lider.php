<?php

namespace Modules\Administration\Entities;

// use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Lider extends Pivot
{
    use HasFactory;

    protected $table = 'savk_organizacion_lideres';

    protected $fillable = [
        'id', 'usuario_id', 'empresa_id', 'punto_evaluacion_id'
    ];

    
}
