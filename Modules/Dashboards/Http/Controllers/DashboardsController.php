<?php

namespace Modules\Dashboards\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
// use Illuminate\Routing\Controller;
use App\Http\Controllers\Controller;
use Modules\Dashboards\Entities\Dashboard;
use App\Models\PuntoEvaluacion;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Modules\Administration\Entities\SavkLideresGrupoEmpresa;
use Modules\Trainings\Entities\CaEvaluacionIniciada;

class DashboardsController extends Controller
{

    protected $model;
    public function __construct(Dashboard $model){

        $this->middleware('auth');
        $this->model = $model;
    }

    public function DashboardPrincipalIndex()
    {
        $page_title = 'Indicadores LyD';
        $action = 'DashboardPrincipalIndex';
        $permisos = $this->GetAllPermisos();

        return view('dashboards::admin.dashboard_principal', compact('page_title','action','permisos'));
    }

    public function getDataAll()
    {
        $currentProfile = auth()->user()->id_grupo;
        $dataReturn = [];
        switch ($currentProfile)
        {
            case 11: //GRUPO EMPRESARIAL
                $idCompanyGroup = PuntoEvaluacion::select(
                    'centro_operacion.*'
                )
                ->Join('unidad', 'punto_evaluacion.unidad_id', '=', 'unidad.id')
                ->Join('centro_operacion', 'unidad.centro_operacion_id', '=', 'centro_operacion.id')
                ->Join('usuario_punto', 'punto_evaluacion.id', '=', 'usuario_punto.punto_id')
                ->Join('usuarios', 'usuario_punto.usuario_id', '=', 'usuarios.id')
                ->where('usuarios.id', '=', auth()->user()->id)
                ->first();

                if($idCompanyGroup == NULL) //NO TIENE INFORMACIÓN DE GP O NO ESTA ASIGNADO A UN PUNTO DE EVALUACION
                    $dataReturn = [];
                else
                    $dataReturn = $this->model->GetValueProposalGP($idCompanyGroup->id);
                break;

            default:
                $dataReturn = [];
                break;
        }

        return $dataReturn;
    }

    public function PropuestaDetalleView()
    {
        $page_title = 'Detalle propuesta de valor';
        $action = __FUNCTION__;
        $permisos = $this->GetAllPermisos();
        return view('dashboards::admin.detalle_propuesta_valor', compact('page_title','action','permisos'));
    }

    public function DashboardCorporativoIndex()
    {
        $page_title = 'Indicadores corporativos';
        $action = 'DashboardCorporativoIndex';
        $permisos = $this->GetAllPermisos();

        return view('dashboards::admin.dashboard_corporativo', compact('page_title','action','permisos'));
    }

    public function IndicadoresEquipo()
    {
        $page_title = 'Indicadores equipo';
        $action = 'IndicadoresEquipo';

        if (auth()->user()->savk_principal == 1) {
            //USUARIOS ACTIVOS
            $users = User::where([
                ['main_account_id', auth()->user()->main_account_id],
                ['estado', 1],
            ])->get();
        }else{
            $grupoEmpresa = SavkLideresGrupoEmpresa::where('id_usuario',auth()->user()->id)->pluck('id_grupo_empresa');

            $puntosEvaluacion = PuntoEvaluacion::select(
                'punto_evaluacion.id'
            )
            ->Join('unidad', 'punto_evaluacion.unidad_id', '=', 'unidad.id')
            ->Join('centro_operacion', 'unidad.centro_operacion_id', '=', 'centro_operacion.id')
            ->whereIn('centro_operacion.id', $grupoEmpresa)
            ->get();

            $puntosIds = $puntosEvaluacion->pluck('id')->toArray();

            //USUARIOS ACTIVOS
            $users = User::where('estado', 1)
            ->whereIn('id_punto', $puntosIds)
            ->get();
        }
        // $certificadosGenerados = $users->count();

        //TOTAL HORAS Y PUNTOS DE ENTRENAMIENTO
        $minutosTotales = 0;
        $puntosTotales = 0;

        $minutosElearningTotales = 0;
        $puntosElearningTotales = 0;
        $usuariosElearning = 0;
        $certificadosElearnig =0;

        $minutosWebinarsTotales = 0;
        $puntosWebinarsTotales = 0;
        $usuariosWebinars = 0;
        $certificadosWebinars = 0;

        $minutosAsistidasTotales = 0;
        $puntosAsistidasTotales = 0;
        $usuariosAsistidas = 0;
        $certificadosAsistidas = 0;


        //SE RECORRE CADA USUARIO
        foreach ($users as $key => $user) {

            $certificados= CaEvaluacionIniciada::select('c.nombre', 'c.tipo_capacitacion',
                \DB::raw("(SELECT max(ca.duracion) FROM ca_cap_asistidas ca
                inner join  ca_capacitaciones_asistidas_asistentes caa
                on ca.id = caa.id_capacitacion_asistida
                where id_capacitacion = c.id && tipo = 2 && caa.id_usuario = ".$user->id.") as asistida" )
            )
            ->join('ca_capacitaciones as c', 'c.id', 'id_capacitacion')
            ->join('usuarios as u', 'u.id', 'c.id_usuario')
            ->where([
                ['ca_evaluacion_iniciada.id_usuario', $user->id],
                ['certificado', 1],
                ['last_approved', 1],
                ['u.main_account_id', 1]
            ])
            ->get();

            foreach ($certificados as $key => $value) {
                if ($value->tipo_capacitacion != 3 && $value->asistida == null) {
                    $certificadosElearnig+=1;
                }
                if ($value->tipo_capacitacion == 3 && $value->asistida == null) {
                    $certificadosWebinars+=1;
                }
                if ($value->asistida != null) {
                    $certificadosAsistidas+=1;
                    $minutosAsistidasTotales+= $value->asistida;
                }
            }

            // $certificadosElearnig+= CaEvaluacionIniciada::join('ca_capacitaciones as c', 'c.id', 'id_capacitacion')
            // ->where([
            //     ['ca_evaluacion_iniciada.id_usuario', $user->id],
            //     ['certificado', 1],
            //     ['last_approved', 1],
            //     ['c.tipo_capacitacion', '<>','3']
            // ])
            // ->count();

            // $certificadosWebinars+= CaEvaluacionIniciada::join('ca_capacitaciones as c', 'c.id', 'id_capacitacion')
            // ->where([
            //     ['ca_evaluacion_iniciada.id_usuario', $user->id],
            //     ['certificado', 1],
            //     ['last_approved', 1],
            //     ['c.tipo_capacitacion', '=','3']
            // ])
            // ->count();

            //QUERY PRINCIPAL SIN WHERE POR CAPACITACIONES
            $query = "SELECT
            if(
                (select count(*) from ca_modulos m2 where m2.id_capacitacion = c.id) +
                IF((select ce.evaluara_por from ca_capacitaciones ce where ce.id = c.id) = 1, 1, 0)
                =
                (select count(*) from ca_capacitaciones_iniciadas ci2 where ci2.id_capacitacion = c.id and ci2.id_usuario = " .$user->id. ")+
                IF((select ce.evaluara_por from ca_capacitaciones ce where ce.id = c.id) = 1 and
                (select count(*) from ca_evaluacion_iniciada ei where ei.id_capacitacion = c.id and ei.id_usuario = " .$user->id. " and ei.last_approved = 1) >= 1
                , 1, 0),
                c.tiempo_minutos, 0) as minutos,
            if(
                (select count(*) from ca_modulos m2 where m2.id_capacitacion = c.id) +
                IF((select ce.evaluara_por from ca_capacitaciones ce where ce.id = c.id) = 1, 1, 0)
                =
                (select count(*) from ca_capacitaciones_iniciadas ci2 where ci2.id_capacitacion = c.id and ci2.id_usuario = " .$user->id. ")+
                IF((select ce.evaluara_por from ca_capacitaciones ce where ce.id = c.id) = 1 and
                    (select count(*) from ca_evaluacion_iniciada ei where ei.id_capacitacion = c.id and ei.id_usuario = " .$user->id. " and ei.last_approved = 1) >= 1
                    , 1, 0),
                c.puntos, 0) as puntos,
            (SELECT max(ca.id) FROM ca_cap_asistidas ca
                inner join  ca_capacitaciones_asistidas_asistentes caa
                on ca.id = caa.id_capacitacion_asistida
                where id_capacitacion = c.id && tipo = 2 && caa.id_usuario = ".$user->id.") as asistida
            FROM ca_capacitaciones_iniciadas ci
            inner join ca_capacitaciones c on c.id =ci.id_capacitacion
            inner join usuarios u on u.id = c.id_usuario
            where ci.id_usuario = ".$user->id." and u.main_account_id = 1";

            $total = DB::select($query." group by c.id");
            $webinars = DB::select($query." and c.tipo_capacitacion = 3 group by c.id");
            $elearning = DB::select($query." and c.tipo_capacitacion != 3 group by c.id");

            //SE SUMAN LOS MINUTOS Y PUNTOS TOTALES DEL USUARIO(CAPACITACIONES YA REALIZADAS)
            $minutos = 0;
            $puntos = 0;
            foreach ($total as $key => $value) {
                $minutos += $value->minutos;
                $puntos += $value->puntos;
            }

            $minutosAsistidas = 0;
            $puntosAsistidas = 0;
            //SE SUMAN LOS MINUTOS Y PUNTOS POR WEBINARS DEL USUARIO(CAPACITACIONES YA REALIZADAS)
            $minutosWebinars = 0;
            $puntosWebinars = 0;

            $contWebinars = 0;
            $contAsistida = 0;
            foreach ($webinars as $key => $value) {
                if ($value->asistida == null) {
                    $minutosWebinars += $value->minutos;
                    $puntosWebinars += $value->puntos;
                    if($contWebinars == 0){
                        $usuariosWebinars+= 1;
                        $contWebinars = 1;
                    }
                }else{
                    // $minutosAsistidas += $value->minutos;
                    $puntosAsistidas += $value->puntos;
                    if($contAsistida == 0)
                    {
                        $usuariosAsistidas += 1;
                        $contAsistida = 1;
                    }
                }
            }

            //SE SUMAN LOS MINUTOS Y PUNTOS POR E-LEARNING DEL USUARIO(CAPACITACIONES YA REALIZADAS)
            $minutosElearning = 0;
            $puntosElearning = 0;
            //VALIDA SI LA CONSULTA DE E-LEARNING TIENE RESULTADO PARA SUMAR A LA CANTIDAD DE USUARIOS
            $contElearning = 0;
            $contAsistida = 0;
            foreach ($elearning as $key => $value) {
                if ($value->asistida == null) {
                    $minutosElearning += $value->minutos;
                    $puntosElearning += $value->puntos;
                    if($contElearning == 0){
                        $usuariosElearning+= 1;
                        $contElearning = 1;
                    }
                }else{
                    // $minutosAsistidas += $value->minutos;
                    $puntosAsistidas += $value->puntos;
                    if($contAsistida == 0)
                    {
                        $usuariosAsistidas += 1;
                        $contAsistida = 1;
                    }
                }
            }

            //SE SUMA AL TOTAL DE HORAS ENTRENAMIENTO
            $minutosTotales+= $minutos;
            $puntosTotales+= $puntos;
            $minutosWebinarsTotales+= $minutosWebinars;
            $puntosWebinarsTotales+= $puntosWebinars;
            $minutosElearningTotales+= $minutosElearning;
            $puntosElearningTotales+= $puntosElearning;
            $minutosAsistidasTotales+= $minutosAsistidas;
            $puntosAsistidasTotales+= $puntosAsistidas;
        }

        // $minutosTotales = number_format($minutosTotales / 60, 1); //SE CONVIERTE A HORAS
        $minutosTotales = $minutosElearningTotales + $minutosWebinarsTotales + $minutosAsistidasTotales;
        $minutosTotales = number_format($minutosTotales / 60, 1); //SE CONVIERTE A HORAS
        $minutosWebinarsTotales = number_format($minutosWebinarsTotales / 60, 1); //SE CONVIERTE A HORAS
        $minutosElearningTotales = number_format($minutosElearningTotales / 60, 1); //SE CONVIERTE A HORAS
        $minutosAsistidasTotales = number_format($minutosAsistidasTotales / 60, 1); //SE CONVIERTE A HORAS

        // $certificadosGenerados = $usuariosWebinars + $usuariosElearning + $usuariosAsistidas;
        $certificadosGenerados = $certificadosElearnig + $certificadosWebinars + $certificadosAsistidas;


        $permisos = $this->GetAllPermisos();

        return view('dashboards::admin.indicadores_equipo', compact(
            'page_title','action','certificadosGenerados',
            'minutosTotales','puntosTotales',
            'minutosWebinarsTotales','puntosWebinarsTotales', 'certificadosWebinars',
            'minutosElearningTotales','puntosElearningTotales', 'certificadosElearnig',
            'minutosAsistidasTotales','puntosAsistidasTotales', 'certificadosAsistidas',
            'permisos'
        ));
    }

    public function IndicadoresEquipoCorporativos()
    {
        $page_title = 'Indicadores corporativos equipo';
        $action = 'IndicadoresEquipo';

        if (auth()->user()->savk_principal == 1) {
            //USUARIOS ACTIVOS
            $users = User::where([
                ['main_account_id', auth()->user()->main_account_id],
                ['estado', 1],
            ])->get();
        }else{
            $grupoEmpresa = SavkLideresGrupoEmpresa::where('id_usuario',auth()->user()->id)->pluck('id_grupo_empresa')->toArray();

            $puntosEvaluacion = PuntoEvaluacion::select(
                'punto_evaluacion.id'
            )
            ->Join('unidad', 'punto_evaluacion.unidad_id', '=', 'unidad.id')
            ->Join('centro_operacion', 'unidad.centro_operacion_id', '=', 'centro_operacion.id')
            ->whereIn('centro_operacion.id', $grupoEmpresa)
            ->get();

            $puntosIds = $puntosEvaluacion->pluck('id')->toArray();

            //USUARIOS ACTIVOS
            $users = User::where('estado', 1)
            ->whereIn('id_punto', $puntosIds)
            ->get();
        }

        $minutosElearningTotales = 0;
        $puntosElearningTotales = 0;
        $certificadosElearnig =0;

        $minutosAsistidasTotales = 0;
        $puntosAsistidasTotales = 0;
        $usuariosAsistidas = 0;
        $certificadosAsistidas = 0;


        //SE RECORRE CADA USUARIO
        foreach ($users as $key => $user) {

            $certificados= CaEvaluacionIniciada::select('c.nombre', 'c.tipo_capacitacion',
                \DB::raw("(SELECT max(ca.duracion) FROM ca_cap_asistidas ca
                inner join  ca_capacitaciones_asistidas_asistentes caa
                on ca.id = caa.id_capacitacion_asistida
                where id_capacitacion = c.id && tipo = 2 && caa.id_usuario = ".$user->id.") as asistida" )
            )
            ->join('ca_capacitaciones as c', 'c.id', 'id_capacitacion')
            ->join('usuarios as u', 'u.id', 'c.id_usuario')
            ->where([
                ['ca_evaluacion_iniciada.id_usuario', $user->id],
                ['certificado', 1],
                ['last_approved', 1],
                ['u.main_account_id', '<>', 1]
            ])
            ->get();

            foreach ($certificados as $key => $value) {
                if ($value->tipo_capacitacion != 3 && $value->asistida == null) {
                    $certificadosElearnig+=1;
                }
                if ($value->asistida != null) {
                    $certificadosAsistidas+=1;
                    $minutosAsistidasTotales+= $value->asistida;
                }
            }

            //QUERY PRINCIPAL SIN WHERE POR CAPACITACIONES
            $query = "SELECT
            if(
                (select count(*) from ca_modulos m2 where m2.id_capacitacion = c.id) +
                IF((select ce.evaluara_por from ca_capacitaciones ce where ce.id = c.id) = 1, 1, 0)
                =
                (select count(*) from ca_capacitaciones_iniciadas ci2 where ci2.id_capacitacion = c.id and ci2.id_usuario = " .$user->id. ")+
                IF((select ce.evaluara_por from ca_capacitaciones ce where ce.id = c.id) = 1 and
                (select count(*) from ca_evaluacion_iniciada ei where ei.id_capacitacion = c.id and ei.id_usuario = " .$user->id. " and ei.last_approved = 1) >= 1
                , 1, 0),
                c.tiempo_minutos, 0) as minutos,
            if(
                (select count(*) from ca_modulos m2 where m2.id_capacitacion = c.id) +
                IF((select ce.evaluara_por from ca_capacitaciones ce where ce.id = c.id) = 1, 1, 0)
                =
                (select count(*) from ca_capacitaciones_iniciadas ci2 where ci2.id_capacitacion = c.id and ci2.id_usuario = " .$user->id. ")+
                IF((select ce.evaluara_por from ca_capacitaciones ce where ce.id = c.id) = 1 and
                    (select count(*) from ca_evaluacion_iniciada ei where ei.id_capacitacion = c.id and ei.id_usuario = " .$user->id. " and ei.last_approved = 1) >= 1
                    , 1, 0),
                c.puntos, 0) as puntos,
            (SELECT max(ca.id) FROM ca_cap_asistidas ca
                inner join  ca_capacitaciones_asistidas_asistentes caa
                on ca.id = caa.id_capacitacion_asistida
                where id_capacitacion = c.id && tipo = 2 && caa.id_usuario = ".$user->id.") as asistida
            FROM ca_capacitaciones_iniciadas ci
            inner join ca_capacitaciones c on c.id =ci.id_capacitacion
            inner join usuarios u on u.id = c.id_usuario
            where ci.id_usuario = ".$user->id." and u.main_account_id <> 1";

            $elearning = DB::select($query." and c.tipo_capacitacion != 3 group by c.id");



            //SE SUMAN LOS MINUTOS Y PUNTOS POR E-LEARNING DEL USUARIO(CAPACITACIONES YA REALIZADAS)
            $minutosElearning = 0;
            $puntosElearning = 0;

            $minutosAsistidas = 0;
            $puntosAsistidas = 0;

            $contElearning = 0;
            $contAsistida = 0;

            foreach ($elearning as $key => $value) {
                if ($value->asistida == null) {
                    $minutosElearning += $value->minutos;
                    $puntosElearning += $value->puntos;
                    if($contElearning == 0){
                        // $usuariosElearning+= 1;
                        $contElearning = 1;
                    }
                }else{
                    // $minutosAsistidas += $value->minutos;
                    $puntosAsistidas += $value->puntos;
                    if($contAsistida == 0)
                    {
                        $usuariosAsistidas += 1;
                        $contAsistida = 1;
                    }
                }
            }

            //SE SUMA AL TOTAL DE HORAS ENTRENAMIENTO
            $minutosElearningTotales+= $minutosElearning;
            $puntosElearningTotales+= $puntosElearning;
            $minutosAsistidasTotales+= $minutosAsistidas;
            $puntosAsistidasTotales+= $puntosAsistidas;
        }

        $minutosElearningTotales = number_format($minutosElearningTotales / 60, 1); //SE CONVIERTE A HORAS

        $minutosAsistidasTotales = number_format($minutosAsistidasTotales / 60, 1); //SE CONVIERTE A HORAS

        $permisos = $this->GetAllPermisos();

        return view('dashboards::admin.indicadores_equipo_mis_capacitaciones', compact(
            'page_title','action',
            'minutosElearningTotales','puntosElearningTotales', 'certificadosElearnig',
            'minutosAsistidasTotales','puntosAsistidasTotales', 'certificadosAsistidas',
            'permisos'
        ));
    }
}
