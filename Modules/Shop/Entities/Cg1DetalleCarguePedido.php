<?php

namespace Modules\Shop\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cg1DetalleCarguePedido extends Model
{
    protected $table = 'cg1_detalle_cargue_pedido';

    protected $fillable = [
        'pedido_id',
        'producto_referencia',
        'valor',
        'nombre_archivo_id',
        'oc',
        'estado'
    ];
}
