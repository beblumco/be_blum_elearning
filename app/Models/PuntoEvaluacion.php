<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PuntoEvaluacion extends Model
{
    protected $table = 'punto_evaluacion';
    protected $fillable = [
        'id',
        'nombre',
        'correo',
        'segundo_correo',
        'tercer_correo',
        'cuarto_correo',
        'quinto_correo',
        'sexto_correo',
        'codigo',
        'direccion',
        'telefono',	
        'pais_id',
        'departamentos_id',
        'ciudad_id',
        'parent_type',
        'asignacion_id',
        'unidad_id',
        'estado',
        'estado_creacion',
        'nit_tercero_trazapp',
        'ruta_categoria_id',
        'latitud',
        'longitud',
        'main_account_id'
    ];

    public static function GetIdOperationCenterByIdUser($idUser)
    {
        $idCompanyGroup = PuntoEvaluacion::select(
            'centro_operacion.*'
        )
        ->Join('unidad', 'punto_evaluacion.unidad_id', '=', 'unidad.id')
        ->Join('centro_operacion', 'unidad.centro_operacion_id', '=', 'centro_operacion.id')
        ->Join('usuario_punto', 'punto_evaluacion.id', '=', 'usuario_punto.punto_id')
        ->Join('usuarios', 'usuario_punto.usuario_id', '=', 'usuarios.id')
        ->where('usuarios.id', '=', $idUser)
        ->first();

        return $idCompanyGroup;
    }

    public static function ToValidateAssignPDVs($idUser)
    {
        $has = self::Join('savk_lideres_centro_de_costos', 'punto_evaluacion.id', '=', 'savk_lideres_centro_de_costos.id_centro_de_costo')
        ->Join('usuarios', 'savk_lideres_centro_de_costos.id_usuario', '=', 'usuarios.id')
        ->where('usuarios.id', '=', $idUser)
        ->count();

        return ($has == 0 ? false : true);
    }

}