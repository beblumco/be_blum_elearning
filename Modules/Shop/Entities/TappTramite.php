<?php

namespace Modules\Shop\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TappTramite extends Model
{
    protected $table = 'tapp_tramites';
    protected $fillable = [
        'tercero_id', 'tapp_tipo_tramite_id', 'direccion', 'observacion', 'fecha_limite', 'tapp_tipo_pago', 'usuario_id', 'tapp_trazapp_id',
        'costo', 'n_factura', 'punto_evaluacion_id', 'consecutivo_portal', 'n_documento', 'tapp_tipo_documento_id', 'ciudad'
    ];

}
