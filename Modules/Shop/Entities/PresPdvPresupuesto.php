<?php

namespace Modules\Shop\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Modules\Shop\Entities\DocumentacionProducto;

class PresPdvPresupuesto extends Model
{
    protected $table = 'pres_pdv_presupuesto';

    protected $fillable = [
        'id',
        'id_pdv',
        'id_ano',
        'id_mes',
        'presupuesto',
        'estado',
        'dia_corte'
    ];

    public static function GetPdvsAssign($id_company_group)
    {
        $pdvs = \DB::table('punto_evaluacion')
        ->select(
            "punto_evaluacion.*",
            \DB::raw("(SELECT COUNT(1) FROM pres_pdv_presupuesto ppp WHERE ppp.id_pdv = punto_evaluacion.id AND ppp.estado = 1) AS has_assign_pres")
        )
        ->Join("unidad", "punto_evaluacion.unidad_id", "=", "unidad.id")
        ->Join("centro_operacion", "unidad.centro_operacion_id", "=", "centro_operacion.id")
        ->where('centro_operacion.id', '=', $id_company_group)
        ->whereRaw('(SELECT COUNT(1) FROM pres_pdv_presupuesto ppp WHERE ppp.id_pdv = punto_evaluacion.id AND ppp.estado = 1) = 0')
        ->get();

        return $pdvs;
        
    }
}
