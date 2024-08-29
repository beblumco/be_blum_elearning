<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CentroOperacion extends Model
{
    protected $table = 'centro_operacion';
    protected $fillable = [
        'id',
        'nombre',
        'identificacion',
        'ciudad_id',
        'usuario_id',
        'estado',
        'estado_creacion',
        'sector_id',
        'espacio_mb',
        'asesor_id',
        'main_account_id'
    ];

    public static function GetAllOperationCenter()
    {
        $operation_center = self::select(
            'centro_operacion.*'
        )
        ->where('estado', '=', 1)
        ->get();

        return $operation_center;
    }

    public static function GetAllOperationCenterByMainAccount($main_account_id)
    {
        $operation_center = self::select(
            'centro_operacion.*'
        )
        ->where([
            ['estado', '=', 1],
            ['main_account_id', '=', $main_account_id]
        ])
        ->get();

        return $operation_center;
    }

    public static function GetOperationCenterByUser()
    {
        $operation_center = self::select('centro_operacion.id', 'centro_operacion.sector_id')
        ->join('unidad as u', 'u.centro_operacion_id', 'centro_operacion.id')
        ->join('punto_evaluacion as p', 'p.unidad_id', 'u.id')
        ->where('p.id', auth()->user()->id_punto)
        ->first();
        return $operation_center;
    }
    public static function GetOperationCenterByPointer($id_punto)
    {
        $operation_center = self::select('centro_operacion.id')
        ->join('unidad as u', 'u.centro_operacion_id', 'centro_operacion.id')
        ->join('punto_evaluacion as p', 'p.unidad_id', 'u.id')
        ->where('p.id', $id_punto)
        ->first();
        return $operation_center;
    }
}
