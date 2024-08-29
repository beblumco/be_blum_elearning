<?php

namespace Modules\Shop\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cg1NombreArchivo extends Model
{
    protected $table = 'cg1_nombre_archivo';

    protected $fillable = [
        'nombre_pedido',
        'cliente',
        'consecutivo',
        'oc',
        'fecha_solicitud',
        'estado',
        'unidad',
        'modo_cargue'
    ];
}
