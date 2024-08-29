<?php

namespace Modules\Trainings\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Trainings\Entities\CaRespuestas;
use Modules\Trainings\Entities\CaPreguntas;
use Modules\Trainings\Entities\CaContenido;

class CaModulos extends Model
{
    use HasFactory;

    protected $table = 'ca_modulos';
    protected $fillable = [
        'id',
        'nombre',
        'descripcion',
        'imagen',
        'orden',
        'porcentaje_aprueba',
        'estado',
        'id_capacitacion'
    ];

    public function GetAllModules($id_training)
    {
        $modules = $this->select(
            'ca_modulos.*',
            'ca_capacitaciones.evaluara_por',
            'ca_capacitaciones.tipo_capacitacion',
            'ca_capacitaciones.nombre AS capacitacion',
            \DB::raw('IF(ca_modulos.nombre IS NULL, "Sin nombre", ca_modulos.nombre) AS nombre'),
            \DB::raw('IF(ca_modulos.descripcion IS NULL, "Sin descripción", ca_modulos.descripcion) AS descripcion'),
            \DB::raw('IF(ca_modulos.estado = 1, "Activo", "Inactivo") as estado_texto'),
            \DB::raw('IF((SELECT COUNT(*) FROM ca_preguntas cps WHERE cps.id_modulo = ca_modulos.id) = 0, 0, 1) as tiene_preguntas'),
            \DB::raw('IF((SELECT COUNT(*) FROM ca_contenido ccs WHERE ccs.id_modulo = ca_modulos.id and tipo_contenido <> 3) = 0, 0, 1) as tiene_contenido'),
            \DB::raw('IF((SELECT COUNT(*) FROM ca_contenido ccs WHERE ccs.id_modulo = ca_modulos.id and tipo_contenido = 3) = 0, 0, 1) as tiene_video')
        )
            ->Join('ca_capacitaciones', 'ca_modulos.id_capacitacion', '=', 'ca_capacitaciones.id')
            ->where('ca_capacitaciones.id', '=', $id_training)
            ->get();

        foreach ($modules as $module) {
            $module->hasVideo = null;
            $module->hasRecursos = null;
            $module->hasTest = null;

            $contenidos = CaContenido::select('tipo_contenido')->where('id_modulo', $module->id)->get();

            foreach ($contenidos as $contenido) {
                if ($contenido->tipo_contenido == 3) {
                    $module->hasVideo = true;
                } else {
                    $module->hasRecursos = true;
                }
            }

            $test = CaPreguntas::select(
                'ca_preguntas.id'
            )
                ->where('ca_preguntas.id_modulo', '=', $module->id)
                ->first();

            if ($test) {
                $module->hasTest = true;
            }
        }

        return $modules;
    }

    public function GetAllTest($id_training)
    {
        $test = $this->select(
            // \DB::raw('COUNT(*) as tiene_preguntas_capacitacion')
            \DB::raw('IF(COUNT(*) = 0, 0, 1) as tiene_preguntas_capacitacion')
        )
            ->FROM('ca_preguntas')
            ->where('id_capacitacion', '=', $id_training)
            ->get();

        return $test;
    }

    public function GetAssessBy($id_training)
    {
        $test = $this->select('evaluara_por')
            ->FROM('ca_capacitaciones')
            ->where('id', '=', $id_training)
            ->get();

        return $test;
    }

    public function GetModuleById($id_module)
    {
        $module = $this->select(
            'ca_modulos.*',
            'ca_capacitaciones.nombre AS capacitacion',
            'ca_capacitaciones.tipo_capacitacion',
            \DB::raw('IF(ca_modulos.nombre IS NULL, "Sin nombre", ca_modulos.nombre) AS nombre'),
            \DB::raw('IF(ca_modulos.descripcion IS NULL, "Sin descripción", ca_modulos.descripcion) AS descripcion'),
            \DB::raw('IF(ca_modulos.estado = 1, "Activo", "Inactivo") as estado_texto'),
            \DB::raw('IF((SELECT COUNT(*) FROM ca_preguntas cps WHERE cps.id_modulo = ca_modulos.id) = 0, 0, 1) as tiene_preguntas'),
            \DB::raw('IF((SELECT COUNT(*) FROM ca_contenido ccs WHERE ccs.id_modulo = ca_modulos.id) = 0, 0, 1) as tiene_contenido'),
            \DB::raw('IF((SELECT COUNT(*) FROM ca_contenido ccs WHERE ccs.id_modulo = ca_modulos.id and tipo_contenido = 3) = 0, 0, 1) as tiene_video')
        )
            ->Join('ca_capacitaciones', 'ca_modulos.id_capacitacion', '=', 'ca_capacitaciones.id')
            ->where('ca_modulos.id', '=', $id_module)
            ->first();

        return $module;
    }

    public function DeleteModule($id_module)
    {
        $delete_answer = CaRespuestas::Join('ca_preguntas', 'ca_respuestas.id_pregunta', '=', 'ca_preguntas.id')
            ->Join('ca_modulos', 'ca_preguntas.id_modulo', '=', 'ca_modulos.id')
            ->where('ca_modulos.id', '=', $id_module)
            ->delete();

        $delete_question = CaPreguntas::Join('ca_modulos', 'ca_preguntas.id_modulo', '=', 'ca_modulos.id')
            ->where('ca_modulos.id', '=', $id_module)
            ->delete();

        $delete_content = CaContenido::Join('ca_modulos', 'ca_contenido.id_modulo', '=', 'ca_modulos.id')
            ->where('ca_modulos.id', '=', $id_module)
            ->delete();

        $delete_module = $this->where('ca_modulos.id', '=', $id_module)->delete();

        return true;
    }

    public static function GetAllModulesExcecutable($id_training)
    {
        $modules = self::select(
            'ca_modulos.*',
            'ca_contenido.ruta_contenido as ruta',
            'ca_contenido.tipo_contenido',
            'ca_contenido.orden',
            'ca_capacitaciones.evaluara_por',
            'usuarios.main_account_id as main_account',
            \DB::raw("(SELECT COUNT(*) FROM ca_capacitaciones_iniciadas ci where ci.id_modulo = ca_modulos.id and id_usuario = " . auth()->user()->id . ") moduloIni"),
            \DB::raw("(SELECT COUNT(*) FROM ca_preguntas p where p.id_modulo = ca_modulos.id) moduloEvaluacion"),
            \DB::raw("(SELECT COUNT(*) FROM ca_evaluacion_iniciada ei where ei.id_capacitacion = $id_training and ei.id_usuario = " . auth()->user()->id . " and ei.last_approved = 1) evaluacionAprovadaCap"),
            \DB::raw("(SELECT COUNT(*) FROM ca_contenido c where c.id_modulo = ca_modulos.id and c.tipo_contenido <> 3) RECURSOS"),
            \DB::raw("(SELECT COUNT(*) FROM ca_preguntas_usuarios pre
                    where pre.id_modulo = ca_modulos.id and pre.id_capacitacion = ca_modulos.id_capacitacion and pre.id_usuario = ".auth()->user()->id.") preguntas"),
            \DB::raw("IF((SELECT COUNT(*) FROM ca_evaluacion_iniciada ceis WHERE ceis.id_modulo = ca_modulos.id AND ceis.certificado = 1 AND ceis.id_usuario = " . auth()->user()->id . ") > 0, 100, 0) PERCENT_MODULE"),
            \DB::raw("
            (CASE
                WHEN (SELECT COUNT(*) FROM ca_evaluacion_iniciada ceis WHERE ceis.id_modulo = ca_modulos.id AND ceis.certificado = 1 AND ceis.id_usuario = " . auth()->user()->id . ") > 0 THEN 'text-success'
                ELSE 'text-warning'
            END ) AS COLOR_PERCENT"),
            'ca_capacitaciones.permitir_certificacion'
        )
            ->Join('ca_capacitaciones', 'ca_modulos.id_capacitacion', '=', 'ca_capacitaciones.id')
            ->Join('usuarios', 'usuarios.id', '=', 'ca_capacitaciones.id_usuario')
            ->leftJoin('ca_contenido', function ($join) {
                $join->on('ca_modulos.id', '=', 'ca_contenido.id_modulo')
                    ->where('ca_contenido.tipo_contenido', '=', 3);
            })
            ->where('ca_capacitaciones.id', '=', $id_training)
            ->get();
        return $modules;
    }

    public static function GetAllModulesExcecutablePublic($id_training, $id_asistente)
    {
        $modules = self::select(
            'ca_modulos.*',
            'ca_contenido.ruta_contenido as ruta',
            'ca_contenido.tipo_contenido',
            'ca_contenido.orden',
            'ca_capacitaciones.evaluara_por',
            'usuarios.main_account_id as main_account',
            \DB::raw("(SELECT COUNT(*) FROM ca_preguntas p where p.id_modulo = ca_modulos.id) moduloEvaluacion"),
            \DB::raw("(SELECT COUNT(*) FROM ca_contenido c where c.id_modulo = ca_modulos.id and c.tipo_contenido <> 3) RECURSOS"),
            'ca_capacitaciones.permitir_certificacion',
            \DB::raw("(SELECT e.id FROM ca_evaluacion_iniciada e where e.id_modulo = ca_modulos.id && id_asistente = $id_asistente && last_approved = 1 && certificado = 1) evaluacionAprobada"),
            \DB::raw("(SELECT e.id FROM ca_evaluacion_iniciada e where e.id_capacitacion = ca_capacitaciones.id && id_asistente = $id_asistente && last_approved = 1 && e.id_modulo is null order by e.id desc limit 1) evaluacionAprobadaCap"),
        )
            ->Join('ca_capacitaciones', 'ca_modulos.id_capacitacion', '=', 'ca_capacitaciones.id')
            ->Join('usuarios', 'usuarios.id', '=', 'ca_capacitaciones.id_usuario')
            ->leftJoin('ca_contenido', function ($join) {
                $join->on('ca_modulos.id', '=', 'ca_contenido.id_modulo')
                    ->where('ca_contenido.tipo_contenido', '=', 3);
            })
            ->where('ca_capacitaciones.id', '=', $id_training)
            ->get();
        return $modules;
    }
}
