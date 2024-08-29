<?php

namespace Modules\Shop\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TappPedidoDetalle extends Model
{
    protected $table = 'tapp_pedido_detalle';

    protected $fillable = [
        'nombre', 'valor_total', 'cantidad', 'cod_referencia', 'tapp_tramite_id', 'valor_unitario', 'descripcion',
        'impuesto'
    ];
}
