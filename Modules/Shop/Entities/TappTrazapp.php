<?php

namespace Modules\Shop\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TappTrazapp extends Model
{
    protected $table = 'tapp_trazapp';
    protected $fillable = [
        'conductor',
        'placa',
        'hora_salida',
        'observacion',
        'estado',
        'usuario_id'
    ];
}
