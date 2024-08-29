<?php

namespace Modules\Shop\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cg1Configuracion extends Model
{
    protected $table = 'cg1_configuracion';

    protected $fillable = [
        'activo',
    ];

    // Desactiva los timestamps automáticos
    public $timestamps = false;
}
