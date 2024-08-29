<?php

namespace Modules\Trainings\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Trainings\Entities\CaModulos;
use Modules\Trainings\Entities\CaCapacitaciones;

class CaEvaluacionIniciada extends Model
{
    use HasFactory;

    protected $table = 'ca_evaluacion_iniciada';
    protected $fillable = [
        'id',
        'id_capacitacion',
        'id_modulo',
        'id_usuario',
        'id_asistente',
        'fecha_inicio',
        'fecha_terminada',
        'resultado',
        'certificado',
        'certificado_manual',
        'last_approved'
    ];

    public function ValidateExistByIdTrining($id_training)
    {
        $exist = $this->where('id_capacitacion', '=', $id_training)->get();

        return COUNT($exist);
    }

    public function ValidateApproved($data)
    {
        $exist = $this
        ->where('id_capacitacion', '=', $data['id_capacitacion'])
        ->where('id_modulo', '=', $data['id_modulo'])
        ->where('id_usuario', '=', $data['id_usuario'])
        ->where('last_approved', '=', 1)
        ->get();

        return COUNT($exist);
    }

    public function ValidateExistByIdModule($id_module)
    {
        $exist = $this->where('id_modulo', '=', $id_module)->get();

        return COUNT($exist);
    }

    public function GetDataCapTestById($id_test_init)
    {
        $cap_test_inint = $this->whereIn('id', $id_test_init)->get();
        return $cap_test_inint;
    }

    public function CalCulateApprovationByIdTestInit($id_test_init)
    {
         \DB::statement("SET SQL_MODE=''");
        $approved = $this
        ->select(
            \DB::raw('TRUNCATE((SUM(ca_respuestas.ponderado)/COUNT(*)),2) AS RESULTADO'),
            \DB::raw('IF(ca_evaluacion_iniciada.id_modulo IS NOT NULL,
                    (select cms.porcentaje_aprueba from ca_modulos cms where cms.id = ca_evaluacion_iniciada.id_modulo),
                    (select cc.porcentaje_aprobacion from ca_capacitaciones cc where cc.id = ca_evaluacion_iniciada.id_capacitacion)) APRUEBA_CON'),
            // \DB::raw('(select cms.porcentaje_aprueba from ca_modulos cms where cms.id = ca_evaluacion_iniciada.id_modulo) APRUEBA_CON'),
            \DB::raw('IF((SUM(ca_respuestas.ponderado)/COUNT(*)) >= (
                IF(ca_evaluacion_iniciada.id_modulo IS NOT NULL,
                    (select cms.porcentaje_aprueba from ca_modulos cms where cms.id = ca_evaluacion_iniciada.id_modulo),
                    (select cc.porcentaje_aprobacion from ca_capacitaciones cc where cc.id = ca_evaluacion_iniciada.id_capacitacion))
            ), 1,0) APRUEBA')
        )
        ->Join('ca_evaluacion_iniciada_detalle', 'ca_evaluacion_iniciada.id', '=', 'ca_evaluacion_iniciada_detalle.id_evaluacion_iniciada')
        ->Join('ca_respuestas', 'ca_evaluacion_iniciada_detalle.id_respuesta', '=', 'ca_respuestas.id')
        ->where('ca_evaluacion_iniciada.id', '=', $id_test_init)
        ->first();

        return $approved;
    }

    public function ValidateModulesByTraining($id_training, $id_user)
    {


        $modules = CaModulos::where([
            ['id_capacitacion', '=', $id_training],
            ['estado', '=', 1]
        ])
        ->get();

        $total_modules = COUNT($modules);
        $modules_approved = 0;
        foreach ($modules as $key_module => $value_module)
        {
            $count = $this->where([
                ['id_capacitacion', '=', $id_training],
                ['id_modulo', '=', $value_module->id],
                ['id_usuario', '=', $id_user],
                ['resultado', '>=', $value_module->porcentaje_aprueba]
            ])
            ->count();

            if($count != 0)
                $modules_approved++;
        }

        return ($total_modules == $modules_approved ? true : false);
    }

    public function ByIdCertified()
    {
        $certificados = $this
        ->select('ca_evaluacion_iniciada.*','ca.nombre as nom_capacitacion', 'mo.nombre as nom_modulo',
                 'usu.nombre_com as nombre', 'usu.codigo as documento','un.centro_operacion_id')
        ->where([
            ['ca_evaluacion_iniciada.id_usuario', '=', auth()->user()->id],
            ['ca_evaluacion_iniciada.certificado', '=', '1'],
            ['ca_evaluacion_iniciada.last_approved', '=', '1'],
        ])
        ->join('ca_capacitaciones as ca', 'ca_evaluacion_iniciada.id_capacitacion' ,'ca.id')
        ->leftjoin('ca_modulos as mo', 'ca_evaluacion_iniciada.id_modulo' ,'mo.id')
        ->join('usuarios as usu', 'ca_evaluacion_iniciada.id_usuario' ,'usu.id')
        ->join('punto_evaluacion as pt', 'usu.id_punto' ,'pt.id')
        ->join('unidad as un', 'pt.unidad_id' ,'un.id')
        ->get();

        return $certificados;
    }

    public function ByIdCertifiedId($id)
    {
        $certificados = $this
        ->select('ca_evaluacion_iniciada.*','ca.nombre as nom_capacitacion', 'mo.nombre as nom_modulo',
                 'usu.nombre_com as nombre', 'usu.codigo as documento',
                 'un.centro_operacion_id', 'ca.designed_by',
                 \DB::raw('ROUND(ca.tiempo_minutos/60, 1) as tiempo'))
        ->where([
            ['ca_evaluacion_iniciada.certificado', '=', '1'],
            ['ca_evaluacion_iniciada.last_approved', '=', '1'],
            ['ca_evaluacion_iniciada.id', '=', $id],
        ])
        ->join('ca_capacitaciones as ca', 'ca_evaluacion_iniciada.id_capacitacion' ,'ca.id')
        ->leftjoin('ca_modulos as mo', 'ca_evaluacion_iniciada.id_modulo' ,'mo.id')
        ->join('usuarios as usu', 'ca_evaluacion_iniciada.id_usuario' ,'usu.id')
        ->join('punto_evaluacion as pt', 'usu.id_punto' ,'pt.id')
        ->join('unidad as un', 'pt.unidad_id' ,'un.id')
        ->first();

        return $certificados;
    }

    public function ByIdCertifiedIdPublic($id)
    {
        $certificados = $this
        ->select('ca_evaluacion_iniciada.*','ca.nombre as nom_capacitacion', 'mo.nombre as nom_modulo',
                 'usu.nombre as nombre', 'usu.documento as documento',  'ca.designed_by',
                 \DB::raw('ROUND(ca.tiempo_minutos/60, 1) as tiempo'))
        ->where([
            ['ca_evaluacion_iniciada.certificado', '=', '1'],
            ['ca_evaluacion_iniciada.last_approved', '=', '1'],
            ['ca_evaluacion_iniciada.id', '=', $id],
        ])
        ->join('ca_capacitaciones as ca', 'ca_evaluacion_iniciada.id_capacitacion' ,'ca.id')
        ->leftjoin('ca_modulos as mo', 'ca_evaluacion_iniciada.id_modulo' ,'mo.id')
        ->join('ca_asistentes as usu', 'ca_evaluacion_iniciada.id_asistente' ,'usu.id')
        ->first();
        return $certificados;
    }

    public function ByIdCertifiedIdAsistida($id)
    {
        $certificados = $this
        ->select('ca_evaluacion_iniciada.*','ca.nombre as nom_capacitacion', 'mo.nombre as nom_modulo',
                 'usu.nombre_com as nombre', 'usu.codigo as documento',  'ca.designed_by',
                 'un.centro_operacion_id',
                 \DB::raw('ROUND(ca.tiempo_minutos/60, 1) as tiempo'))
        ->where([
            ['ca_evaluacion_iniciada.certificado', '=', '1'],
            ['ca_evaluacion_iniciada.last_approved', '=', '1'],
            ['ca_evaluacion_iniciada.id', '=', $id],
        ])
        ->join('ca_capacitaciones as ca', 'ca_evaluacion_iniciada.id_capacitacion' ,'ca.id')
        ->leftjoin('ca_modulos as mo', 'ca_evaluacion_iniciada.id_modulo' ,'mo.id')
        ->join('usuarios as usu', 'ca_evaluacion_iniciada.id_usuario' ,'usu.id')
        ->join('punto_evaluacion as pt', 'usu.id_punto' ,'pt.id')
        ->join('unidad as un', 'pt.unidad_id' ,'un.id')
        ->first();
        return $certificados;
    }

    public function intentosEvaluacion($id_capacitacion, $usuario, $id_modulo, $tipo = false, $formato = 'privada')
    {
        $capacitacion = CaCapacitaciones::find($id_capacitacion);
        $intentos = $tipo ? []: 0; //Si es true devuelve el id de las evaluaciones, si es false devuelve cantidad
        $intentosGano = 0;

        $asistente = $formato == 'privada' ? 'id_usuario' : 'id_asistente';

        if ($capacitacion->permitir_certificacion == 0 && $capacitacion->evaluara_por == 1) { //NO CERTIFICA & EVALÚA X CAPACITACIÓN
            //OBTENEMOS LA FECHA EN LA QUE SE CERTIFICO POR PRIMERA VEZ
            $fechaAprobado = $this->select('fecha_terminada')
            ->where([
                ['id_capacitacion',$id_capacitacion],
                [$asistente, $usuario],
                ['resultado', '>=', $capacitacion->porcentaje_aprobacion]
            ])
            ->whereNull('id_modulo')
            ->orderBy('fecha_terminada', 'asc')
            ->first();

            $evaluaciones = $this->select('ca_evaluacion_iniciada.*')
                ->where([
                    ['id_capacitacion',$id_capacitacion],
                    [$asistente, $usuario],
                ])
                ->whereNotNull('fecha_terminada')
                ->get();

            foreach ($evaluaciones as $key => $evaluacion) {
                if($evaluacion->last_approved) {
                    $intentosGano+= 1;
                }

                // $usuario == 10009 ? dd($fechaAprobado) : '';
                if ($fechaAprobado && $evaluacion->fecha_terminada <= $fechaAprobado->fecha_terminada) {
                    $tipo ? array_push($intentos, $evaluacion->id) : $intentos+= 1;
                }
            }
        }

        if ($capacitacion->permitir_certificacion == 1 && $capacitacion->evaluara_por == 1) { //CERTIFICA X CAPACITACIÓN & EVALÚA X CAPACITACIÓN
            //OBTENEMOS LA FECHA EN LA QUE SE CERTIFICO POR PRIMERA VEZ
            $fechaAprobado = $this->select('fecha_terminada')
            ->where([
                ['id_capacitacion',$id_capacitacion],
                [$asistente, $usuario],
                ['resultado', '>=', $capacitacion->porcentaje_aprobacion]
            ])
            ->whereNull('id_modulo')
            ->orderBy('fecha_terminada', 'asc')
            ->first();

            $evaluaciones = $this->select('ca_evaluacion_iniciada.*')
                ->where([
                    ['id_capacitacion',$id_capacitacion],
                    [$asistente, $usuario],
                ])
                ->whereNotNull('fecha_terminada')
                ->get();

            foreach ($evaluaciones as $key => $evaluacion) {
                if($evaluacion->last_approved) {
                    $intentosGano+= 1;
                }

                // $usuario == 9280 ? dd($fechaAprobado) : '';
                if ($fechaAprobado && $evaluacion->fecha_terminada <= $fechaAprobado->fecha_terminada) {
                    $tipo ? array_push($intentos, $evaluacion->id) : $intentos+= 1;
                }
            }
        }

        if ($capacitacion->permitir_certificacion == 2 && $capacitacion->evaluara_por == 1) { //CERTIFICA X MÓDULOS & EVALÚA X CAPACITACIÓN
            //OBTENEMOS LA FECHA EN LA QUE SE CERTIFICO POR PRIMERA VEZ
            $fechaAprobado = $this->select('fecha_terminada')
            ->where([
                ['id_capacitacion',$id_capacitacion],
                [$asistente, $usuario],
                ['resultado', '>=', $capacitacion->porcentaje_aprobacion]
            ])
            ->orderBy('fecha_terminada', 'asc')
            ->first();

            $evaluaciones = $this->select('ca_evaluacion_iniciada.*')
                ->where([
                    ['id_capacitacion',$id_capacitacion],
                    [$asistente, $usuario],
                ])
                ->where('id_modulo', function ($query) use ($id_capacitacion) { //SOLO SE REVISA UN MODULO YA QUE LA EVALUACION SE REPITE PARA TODOS
                    $query->select('id')
                        ->from('ca_modulos')
                        ->where('id_capacitacion', $id_capacitacion)
                        ->limit(1);
                })
                ->get();

            foreach ($evaluaciones as $key => $evaluacion) {
                if($evaluacion->last_approved) {
                    $intentosGano+= 1;
                }
                if ($evaluacion->fecha_terminada <= $fechaAprobado->fecha_terminada) {
                    $tipo ? null : $intentos+= 1;
                    if ($tipo) {
                        array_push($intentos, $evaluacion->id);
                    }

                }
            }
            // !$tipo ? $intentos = $intentos/$intentosGano: null ;
        }

        if ($capacitacion->permitir_certificacion == 1 && $capacitacion->evaluara_por == 2) { //CERTIFICA X CAPACITACIÓN & EVALÚA X MODULO
            //OBTENEMOS LA FECHA EN LA QUE SE CERTIFICO POR PRIMERA VEZ
            $fechaAprobado = $this->select('fecha_terminada')
            ->where([
                ['id_capacitacion',$id_capacitacion],
                [$asistente, $usuario]
            ])
            ->whereNull('id_modulo')
            ->orderBy('fecha_terminada', 'asc')
            ->first();

            $modulos = CaModulos::where('id_capacitacion',$id_capacitacion)->get();
            foreach ($modulos as $key => $modulo) {
                $evaluaciones = $this->select('ca_evaluacion_iniciada.*')
                ->where([
                    ['id_modulo',$modulo->id],
                    [$asistente, $usuario],
                ])
                ->get();
                foreach ($evaluaciones as $key => $evaluacion) {
                    if($evaluacion->last_approved) {
                        $intentosGano+= 1;
                    }
                    if ($evaluacion->fecha_terminada <= $fechaAprobado->fecha_terminada) {
                        $tipo ? array_push($intentos, $evaluacion->id) : $intentos+= 1;
                    }
                }
            }
        }

        if ($capacitacion->permitir_certificacion == 2 && $capacitacion->evaluara_por == 2) { //CERTIFICA X MODULO & EVALÚA X MODULO
            $modulo = CaModulos::find($id_modulo);

            if($modulo == null) {
                //SOLO SUCEDE SI CERTIFICO MANUAL
                $data = ['intentos' => 0, 'gano' => 0];
                return $data;
            }

            //OBTENEMOS LA FECHA EN LA QUE SE CERTIFICO POR PRIMERA VEZ
            $fechaAprobado = $this->select('fecha_terminada')
            ->where([
                ['id_modulo',$id_modulo],
                [$asistente, $usuario],
                ['resultado', '>=', $modulo->porcentaje_aprueba]
            ])
            ->orderBy('fecha_terminada', 'asc')
            ->first();


            $evaluaciones = $this->select('ca_evaluacion_iniciada.*')
            ->where([
                ['id_modulo',$id_modulo],
                [$asistente, $usuario],
            ])
            ->get();

            foreach ($evaluaciones as $key => $evaluacion) {
                if($evaluacion->last_approved) {
                    $intentosGano+= 1;
                }
                if ($evaluacion->fecha_terminada <= $fechaAprobado->fecha_terminada) {
                    $tipo ? array_push($intentos, $evaluacion->id) : $intentos+= 1;
                }
            }

        }
        $data = ['intentos' => $intentos, 'gano' => $intentosGano];
        return $data;
    }
}
