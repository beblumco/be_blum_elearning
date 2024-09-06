<?php

namespace Modules\Reports\Http\Controllers;

use App\Exports\ReportAccopanimentExport;
use App\Exports\ReportTrainingAsistidasExport;
use App\Exports\ReportTrainingExport;
use App\Http\Controllers\Controller;
use App\Models\CentroOperacion;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Administration\Entities\SavkLideresCentroDeCostos;
use Modules\Administration\Entities\SavkLideresEmpresa;
use Modules\Administration\Entities\SavkLideresGrupoEmpresa;
use Modules\Administration\Entities\SavkLideresZonas;

class ReportsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function IndexReportAccompaniment()
    {
        $page_title = 'Informe de acompañamientos';
        $action = __FUNCTION__;
        $permisos = $this->GetAllPermisos();
        return view('reports::index_accompaniment', compact('page_title', 'action', 'permisos'));
    }

    public function IndexReportTraining()
    {
        $page_title = 'Informe de entrenamientos';
        $action = __FUNCTION__;
        $permisos = $this->GetAllPermisos();
        return view('reports::index_training', compact('page_title', 'action', 'permisos'));
    }

    public function IndexReportTrainingAsistidas()
    {
        $page_title = 'Informe de asistidas por experto';
        $action = __FUNCTION__;
        $permisos = $this->GetAllPermisos();
        return view('reports::index_training_asistidas', compact('page_title', 'action', 'permisos'));
    }

    public function GetReportAccompaniment(Request $request)
    {
        $cant_pag = 10;

        $where = [];

        if (sizeof($request->get('paginate')) > 0) {
            $cant_pag = $request->paginate['cant'];
        }

        if (strlen($request->get('search')) != 0) {
            array_push($where, ['punto_evaluacion.nombre', 'LIKE', "%$request->search%"]);
        }

        $data = $this->GetDataReportAccompaniment($cant_pag, $where);

        return response()->json([
            'status' => 200,
            'data' => $data,
        ]);
    }

    public function GetDataReportAccompaniment($cant_pag = 10, $where = [])
    {
        $data = DB::table('auditoria_iniciadas')
            ->select(
                'auditoria_iniciadas.id',
                DB::raw('DATE_FORMAT(savk_visitas.fecha, "%Y-%m-%d") as fecha'),
                'auditoria_iniciadas.auditoria_id',
                DB::raw("
        (CASE
            WHEN auditoria_iniciadas.auditoria_id = '65' THEN (
                SELECT rd.descripcion
                FROM respuestas_auditoria_iniciadas AS ri
                JOIN respuesta_detalle rd ON ri.respuesta_id = rd.id
                WHERE ri.auditoria_iniciada_id = auditoria_iniciadas.id
            )
            WHEN auditoria_iniciadas.auditoria_id <> '65' THEN auditorias.nombre
        END) AS tipo
        "),
                'centro_operacion.nombre as operacion',
                'punto_evaluacion.nombre as centro_costo',
                'auditoria_iniciadas.tiempo',
                'ciudades.nombre as ciudad',
                'observacion_general.observacion'
            )
            ->join('savk_visitas', 'savk_visitas.id', 'auditoria_iniciadas.visita_id')
            ->join('auditorias', 'auditorias.id', 'auditoria_iniciadas.auditoria_id')
            ->join('observacion_general', 'observacion_general.auditoria_iniciada_id', 'auditoria_iniciadas.id')
            ->join('punto_evaluacion', 'punto_evaluacion.id', 'auditoria_iniciadas.punto_id')
            ->join('ciudades', 'ciudades.id', 'punto_evaluacion.ciudad_id')
            ->join('unidad', 'unidad.id', 'punto_evaluacion.unidad_id')
            ->join('centro_operacion', 'centro_operacion.id', 'unidad.centro_operacion_id');

        if (Auth::user()->main_account_id != 1 && Auth::user()->savk_principal == 1) {
            $ids = CentroOperacion::select(
                'punto_evaluacion.id'
            )
                ->join('unidad', 'unidad.centro_operacion_id', 'centro_operacion.id')
                ->join('punto_evaluacion', 'punto_evaluacion.unidad_id', 'unidad.id')
                ->where('centro_operacion.main_account_id', Auth::user()->main_account_id)->get()
                ->pluck('id');

            $data->whereIn('savk_visitas.id_centro_costo', $ids);
        } else if (Auth::user()->id_grupo == 44) {
            $ids = SavkLideresGrupoEmpresa::select(
                'punto_evaluacion.id'
            )
            ->join('unidad', 'unidad.centro_operacion_id', 'savk_lideres_grupo_empresas.id_grupo_empresa')
            ->join('punto_evaluacion', 'punto_evaluacion.unidad_id', 'unidad.id')
            ->where('savk_lideres_grupo_empresas.id_usuario', Auth::user()->id)
            ->get()
            ->pluck('id');

            $data->whereIn('savk_visitas.id_centro_costo', $ids);
        } else if (Auth::user()->id_grupo == 45) {
            $ids = SavkLideresEmpresa::select(
                'punto_evaluacion.id'
            )
            ->join('punto_evaluacion', 'punto_evaluacion.unidad_id', 'savk_lideres_empresas.id_empresa')
            ->where('savk_lideres_empresas.id_usuario', Auth::user()->id)
            ->get()
            ->pluck('id');

            $data->whereIn('savk_visitas.id_centro_costo', $ids);
        } else if (Auth::user()->id_grupo == 46) {
            $ids = SavkLideresZonas::join('grupos_sub_puntos', 'grupos_sub_puntos.grupo_punto_id', 'id_grupos_puntos')
            ->where('id_usuario',auth()->user()->id)
            ->pluck('punto_id')->toArray();

            $data->whereIn('savk_visitas.id_centro_costo', $ids);
        } else if (Auth::user()->id_grupo == 47) {
            $ids = SavkLideresCentroDeCostos::select(
                'savk_lideres_centro_de_costos.id_centro_de_costo'
            )
            ->where('savk_lideres_centro_de_costos.id_usuario', Auth::user()->id)
            ->get()
            ->pluck('id_centro_de_costo');

            $data->whereIn('savk_visitas.id_centro_costo', $ids);
        }

        if ($cant_pag == null) {
            $data = $data->orderBy('savk_visitas.fecha', 'DESC')->get();
        } else {
            $data = $data->orderBy('savk_visitas.fecha', 'DESC')->paginate($cant_pag);
        }

        foreach ($data as $d) {
            DB::select("SET sql_mode=''");

            if ($d->auditoria_id == 65) {
                $d->resultado = "N/A";
            } else {
                $d->resultado =  DB::select("
        select truncate (avg(RESULTADO),2) AS RESULTADO  from(SELECT
        ca.nombre AS CATEGORIA,
       truncate (AVG(rd.puntaje),2) AS RESULTADO
    FROM
        respuestas_auditoria_iniciadas rai
    INNER JOIN
        preguntas pr ON rai.pregunta_id = pr.id
    INNER JOIN
        categorias ca ON pr.catagoria_id = ca.id
    INNER JOIN
        respuesta_detalle rd ON rai.respuesta_id = rd.id
    INNER JOIN
        respuestas re ON rd.respuesta_id = re.id
    WHERE
        rai.auditoria_iniciada_id = " . $d->id . "
        group by ca.id
    ORDER BY
        pr.orden asc) as R
    ")[0]->RESULTADO;
            }
        }

        return $data;
    }

    public function GetReportTraining(Request $request)
    {
        $cant_pag = 10;

        $where = [];

        if (sizeof($request->get('paginate')) > 0) {
            $cant_pag = $request->paginate['cant'];
        }

        if (strlen($request->get('search')) != 0) {
            array_push($where, ['punto_evaluacion.nombre', 'LIKE', "%$request->search%"]);
        }

        $data = $this->GetDataReportTraining($cant_pag, $where);

        return response()->json([
            'status' => 200,
            'data' => $data,
        ]);
    }

    public function GetReportTrainingAsistidas(Request $request)
    {
        $cant_pag = 10;

        $where = [];

        if (sizeof($request->get('paginate')) > 0) {
            $cant_pag = $request->paginate['cant'];
        }

        // if (strlen($request->get('search')) != 0) {
        //     array_push($where, ['punto_evaluacion.nombre', 'LIKE', "%$request->search%"]);
        // }

        $data = $this->GetDataReportTrainingAsistidas($cant_pag, $where);

        return response()->json([
            'status' => 200,
            'data' => $data,
        ]);
    }

    public function GetDataReportTraining($cant_pag = 10, $where = [])
    {
        if (Auth::user()->savk_principal == 1) {
            $ids = CentroOperacion::select(
                'punto_evaluacion.id'
            )
            ->join('unidad', 'unidad.centro_operacion_id', 'centro_operacion.id')
            ->join('punto_evaluacion', 'punto_evaluacion.unidad_id', 'unidad.id')
            ->where('centro_operacion.main_account_id', Auth::user()->main_account_id)->get()
            ->pluck('id');
        } else if (Auth::user()->id_grupo == 44) {
            $ids = SavkLideresGrupoEmpresa::select(
                'punto_evaluacion.id'
            )
            ->join('unidad', 'unidad.centro_operacion_id', 'savk_lideres_grupo_empresas.id_grupo_empresa')
            ->join('punto_evaluacion', 'punto_evaluacion.unidad_id', 'unidad.id')
            ->where('savk_lideres_grupo_empresas.id_usuario', Auth::user()->id)
            ->get()
            ->pluck('id');
        } else if (Auth::user()->id_grupo == 45) {
            $ids = SavkLideresEmpresa::select(
                'punto_evaluacion.id'
            )
            ->join('punto_evaluacion', 'punto_evaluacion.unidad_id', 'savk_lideres_empresas.id_empresa')
            ->where('savk_lideres_empresas.id_usuario', Auth::user()->id)
            ->get()
            ->pluck('id');
        } else if (Auth::user()->id_grupo == 46) {
            $ids = SavkLideresZonas::join('grupos_sub_puntos', 'grupos_sub_puntos.grupo_punto_id', 'id_grupos_puntos')
            ->where('id_usuario',auth()->user()->id)
            ->pluck('punto_id');
        } else if (Auth::user()->id_grupo == 47) {
            $ids = SavkLideresCentroDeCostos::select(
                'savk_lideres_centro_de_costos.id_centro_de_costo'
            )
            ->where('savk_lideres_centro_de_costos.id_usuario', Auth::user()->id)
            ->get()
            ->pluck('id_centro_de_costo');
        }

        // $centro_operacion = DB::table('centro_operacion')->select('id')->where('main_account_id', auth()->user()->main_account_id)->pluck('id')->first();
        $centro_operacion = DB::table('centro_operacion')
        ->leftJoin('unidad AS u', 'u.centro_operacion_id', 'centro_operacion.id')
        ->leftJoin('punto_evaluacion AS pe', 'pe.unidad_id', 'u.id')
        ->whereIn('pe.id', $ids)
        ->groupBy('centro_operacion.id')
        ->pluck('centro_operacion.id')
        ->toArray();

        $dataCapCentroOperacion = DB::table('ca_capacitaciones')->select(
            'ca_capacitaciones.created_at as fecha',
            'ca_capacitaciones.nombre as capacitacion',
            DB::raw("
                (CASE
                    WHEN ca_capacitaciones.assign = 1 THEN 'A sector'
                    WHEN ca_capacitaciones.assign = 2 THEN 'A centro operación'
                END) as asignacion
            "),
            DB::raw("
                (CASE
                    WHEN ca_capacitaciones.tipo_capacitacion = 1 THEN 'E-Learning'
                    WHEN ca_capacitaciones.tipo_capacitacion = 3 THEN 'Webinar'
                END) as categoria
            "),
            DB::raw('TRUNCATE((ca_capacitaciones.tiempo_minutos/60), 1) as duracion'),
            'usuarios.nombre_com as asesor',
            DB::raw("(
                SELECT count(DISTINCT id_usuario)
                FROM ca_capacitaciones_iniciadas
                INNER JOIN usuarios as u2 ON u2.id = ca_capacitaciones_iniciadas.id_usuario
                INNER JOIN punto_evaluacion AS pe ON pe.id = u2.id_punto
                WHERE id_capacitacion = ca_capacitaciones.id
                    AND pe.unidad_id = u.id
                    AND u2.id_punto in (".implode(',', $ids->toArray()).")
            ) as asistentes"),
            DB::raw("(
                SELECT
                    count(DISTINCT us.id)
                FROM ca_capacitaciones_iniciadas ci
                INNER JOIN ca_capacitaciones c2 ON c2.id = ci.id_capacitacion
                INNER JOIN usuarios us ON us.id = ci.id_usuario
                INNER JOIN punto_evaluacion AS pe ON pe.id = us.id_punto
                WHERE
                    pe.unidad_id = u.id
                    AND us.id_punto in (".implode(',', $ids->toArray()).")
                    AND c2.id = ca_capacitaciones.id
                    AND us.main_account_id = ".Auth::user()->main_account_id."
                    AND IF(
                        (SELECT COUNT(*) FROM ca_modulos m2 WHERE m2.id_capacitacion = c2.id) +
                        IF((SELECT ce.evaluara_por FROM ca_capacitaciones ce WHERE ce.id = c2.id) = 1, 1, 0)
                        =
                        (SELECT COUNT(*) FROM ca_capacitaciones_iniciadas ci2 WHERE ci2.id_capacitacion = c2.id AND ci2.id_usuario = us.id) +
                        IF((SELECT ce.evaluara_por FROM ca_capacitaciones ce WHERE ce.id = c2.id) = 1 AND
                        (SELECT COUNT(*) FROM ca_evaluacion_iniciada ei WHERE ei.id_capacitacion = c2.id AND ei.id_usuario = us.id AND ei.last_approved = 1) >= 1,
                        1, 0),
                        1, 0
                    ) > 0
            ) as certificados"),
            'usuarios.id_punto as punto_id',
            'co.nombre as grupo_empresa',
            'u.nombre as empresa'
        )->join('usuarios', 'usuarios.id', '=', 'ca_capacitaciones.id_usuario')
        ->join('ca_asignacion_centro_operacion', 'ca_asignacion_centro_operacion.id_capacitacion', '=', 'ca_capacitaciones.id')
        ->join('centro_operacion as co', 'co.id', 'ca_asignacion_centro_operacion.id_centro_operacion')
        ->join('unidad AS u', 'u.centro_operacion_id', 'co.id')
        ->join('punto_evaluacion AS pue', 'pue.unidad_id', 'u.id')
        // ->where('ca_asignacion_centro_operacion.id_centro_operacion', $centro_operacion)
        ->whereIn('ca_asignacion_centro_operacion.id_centro_operacion', $centro_operacion)
        ->whereIn('pue.id', $ids)
        ->where(function (Builder $query) {
            $query->where('ca_capacitaciones.tipo_capacitacion', 1)
                ->orWhere('ca_capacitaciones.tipo_capacitacion', 3);
        });


        $sector = DB::table('centro_operacion')->select('sector_id')
        ->where('main_account_id', auth()->user()->main_account_id)
        ->pluck('sector_id')
        ->toArray();

        $dataCapSector = DB::table('ca_capacitaciones')->select(
            'ca_capacitaciones.created_at as fecha',
            'ca_capacitaciones.nombre as capacitacion',
            DB::raw("
                (CASE
                    WHEN ca_capacitaciones.assign = 1 THEN 'A sector'
                    WHEN ca_capacitaciones.assign = 2 THEN 'A centro operación'
                END) as asignacion
            "),
            DB::raw("
                (CASE
                    WHEN ca_capacitaciones.tipo_capacitacion = 1 THEN 'E-Learning'
                    WHEN ca_capacitaciones.tipo_capacitacion = 3 THEN 'Webinar'
                END) as categoria
            "),
            DB::raw('TRUNCATE((ca_capacitaciones.tiempo_minutos/60), 1) as duracion'),
            'usuarios.nombre_com as asesor',
            DB::raw("(
                    SELECT count(DISTINCT id_usuario)
                    FROM ca_capacitaciones_iniciadas
                    INNER JOIN usuarios as u2 ON u2.id = ca_capacitaciones_iniciadas.id_usuario
                    INNER JOIN punto_evaluacion AS pe ON pe.id = u2.id_punto
                    WHERE id_capacitacion = ca_capacitaciones.id
                        AND pe.unidad_id = u.id
                        AND u2.id_punto in (".implode(',', $ids->toArray()).")
                ) as asistentes"),
            DB::raw("(
                SELECT
                    count(DISTINCT us.id)
                FROM ca_capacitaciones_iniciadas ci
                INNER JOIN ca_capacitaciones c2 ON c2.id = ci.id_capacitacion
                INNER JOIN usuarios us ON us.id = ci.id_usuario
                INNER JOIN punto_evaluacion AS pe ON pe.id = us.id_punto
                WHERE
                    pe.unidad_id = u.id
                    AND us.id_punto in (".implode(',', $ids->toArray()).")
                    AND c2.id = ca_capacitaciones.id
                    AND us.main_account_id = ".Auth::user()->main_account_id."
                    AND IF(
                        (SELECT COUNT(*) FROM ca_modulos m2 WHERE m2.id_capacitacion = c2.id) +
                        IF((SELECT ce.evaluara_por FROM ca_capacitaciones ce WHERE ce.id = c2.id) = 1, 1, 0)
                        =
                        (SELECT COUNT(*) FROM ca_capacitaciones_iniciadas ci2 WHERE ci2.id_capacitacion = c2.id AND ci2.id_usuario = us.id) +
                        IF((SELECT ce.evaluara_por FROM ca_capacitaciones ce WHERE ce.id = c2.id) = 1 AND
                        (SELECT COUNT(*) FROM ca_evaluacion_iniciada ei WHERE ei.id_capacitacion = c2.id AND ei.id_usuario = us.id AND ei.last_approved = 1) >= 1,
                        1, 0),
                        1, 0
                    ) > 0
            ) as certificados"),
            'usuarios.id_punto as punto_id',
            'co.nombre as grupo_empresa',
            'u.nombre as empresa'
        )->join('usuarios', 'usuarios.id', '=', 'ca_capacitaciones.id_usuario')
        ->join('ca_asignacion_sector', 'ca_asignacion_sector.id_capacitacion', 'ca_capacitaciones.id')
        ->join('sector', 'ca_asignacion_sector.id_sector', '=', 'sector.id')
        ->join('centro_operacion as co', 'co.sector_id', 'sector.id')
        ->join('unidad AS u', 'u.centro_operacion_id', 'co.id')
        ->join('punto_evaluacion AS pue', 'pue.unidad_id', 'u.id')
        ->whereIn('sector.id', $sector)
        ->whereIn('pue.id', $ids)
        ->where('co.main_account_id', auth()->user()->main_account_id)
        ->where(function (Builder $query) {
            $query->where('ca_capacitaciones.tipo_capacitacion', 1)
                ->orWhere('ca_capacitaciones.tipo_capacitacion', 3);
        });

        $dataCapSector->union($dataCapCentroOperacion);

        $data = DB::table(
            $dataCapSector,
            'data'
        )->select(
            'data.fecha',
            'data.categoria',
            'data.capacitacion',
            'data.duracion',
            'data.certificados',
            'data.asesor',
            'data.asistentes',
            'data.asignacion',
            'grupo_empresa',
            'empresa'
        )->orderBy('fecha', 'DESC');

        if ($cant_pag == null) {
            $data = $data->get();
        } else {
            //dd($data->toSql());
            $data = $data->paginate($cant_pag);
        }

        return $data;
    }

    public function GetDataReportTrainingAsistidas($cant_pag = 10, $where = [])
    {
        if (Auth::user()->savk_principal == 1) {
            $ids = CentroOperacion::select(
                'punto_evaluacion.id'
            )
            ->join('unidad', 'unidad.centro_operacion_id', 'centro_operacion.id')
            ->join('punto_evaluacion', 'punto_evaluacion.unidad_id', 'unidad.id')
            ->where('centro_operacion.main_account_id', Auth::user()->main_account_id)->get()
            ->pluck('id');
        } else if (Auth::user()->id_grupo == 44) {
            $ids = SavkLideresGrupoEmpresa::select(
                'punto_evaluacion.id'
            )
            ->join('unidad', 'unidad.centro_operacion_id', 'savk_lideres_grupo_empresas.id_grupo_empresa')
            ->join('punto_evaluacion', 'punto_evaluacion.unidad_id', 'unidad.id')
            ->where('savk_lideres_grupo_empresas.id_usuario', Auth::user()->id)
            ->get()
            ->pluck('id');
        } else if (Auth::user()->id_grupo == 45) {
            $ids = SavkLideresEmpresa::select(
                'punto_evaluacion.id'
            )
            ->join('punto_evaluacion', 'punto_evaluacion.unidad_id', 'savk_lideres_empresas.id_empresa')
            ->where('savk_lideres_empresas.id_usuario', Auth::user()->id)
            ->get()
            ->pluck('id');
        } else if (Auth::user()->id_grupo == 46) {
            $ids = SavkLideresZonas::join('grupos_sub_puntos', 'grupos_sub_puntos.grupo_punto_id', 'id_grupos_puntos')
            ->where('id_usuario',auth()->user()->id)
            ->pluck('punto_id');
        } else if (Auth::user()->id_grupo == 47) {
            $ids = SavkLideresCentroDeCostos::select(
                'savk_lideres_centro_de_costos.id_centro_de_costo'
            )
            ->where('savk_lideres_centro_de_costos.id_usuario', Auth::user()->id)
            ->get()
            ->pluck('id_centro_de_costo');
        }

        $dataAsistidas = DB::table('ca_cap_asistidas')
            ->select(
                'ca_cap_asistidas.fecha_agendamiento as fecha',
                'ca_capacitaciones.nombre as capacitacion',
                DB::raw('"Asistida por experto" as categoria'),
                DB::raw('TRUNCATE((ca_cap_asistidas.duracion/60), 1) as duracion'),
                'usuarios.nombre_com as asesor',
                DB::raw("(
                    SELECT count(*)
                    FROM ca_capacitaciones_asistidas_asistentes
                    WHERE id_capacitacion_asistida = ca_cap_asistidas.id
                ) as asistentes"),
                DB::raw("(
                    SELECT
                        count(DISTINCT us.id)
                    FROM ca_capacitaciones_iniciadas ci
                    INNER JOIN ca_capacitaciones c2 ON c2.id = ci.id_capacitacion
                    INNER JOIN usuarios us ON us.id = ci.id_usuario
                    INNER JOIN punto_evaluacion AS pe ON pe.id = us.id_punto
                    WHERE
                        pe.unidad_id = u.id
                        AND us.id_punto in (".implode(',', $ids->toArray()).")
                        AND us.id in (
                                SELECT ca_capacitaciones_asistidas_asistentes.id_usuario
                                FROM ca_capacitaciones_asistidas_asistentes
                                WHERE id_capacitacion_asistida = ca_cap_asistidas.id
                            )
                        AND c2.id = ca_capacitaciones.id
                        AND us.main_account_id = ".Auth::user()->main_account_id."
                        AND IF(
                            (SELECT COUNT(*) FROM ca_modulos m2 WHERE m2.id_capacitacion = c2.id) +
                            IF((SELECT ce.evaluara_por FROM ca_capacitaciones ce WHERE ce.id = c2.id) = 1, 1, 0)
                            =
                            (SELECT COUNT(*) FROM ca_capacitaciones_iniciadas ci2 WHERE ci2.id_capacitacion = c2.id AND ci2.id_usuario = us.id) +
                            IF((SELECT ce.evaluara_por FROM ca_capacitaciones ce WHERE ce.id = c2.id) = 1 AND
                            (SELECT COUNT(*) FROM ca_evaluacion_iniciada ei WHERE ei.id_capacitacion = c2.id AND ei.id_usuario = us.id AND ei.last_approved = 1) >= 1,
                            1, 0),
                            1, 0
                        ) > 0
                ) as certificados"),
                DB::raw("
                (CASE
                    WHEN ca_cap_asistidas.modalidad = 1 THEN 'Virtual'
                    ELSE 'Presencial'
                END)
                as modalidad"),
                DB::raw("
                (CASE
                    WHEN ca_cap_asistidas.tipo = 1 THEN 'Pública'
                    ELSE 'Privada'
                END)
                as tipo"),
                'id_cliente as punto_id',
                'co.nombre AS grupo_empresa',
                'u.nombre AS empresa',
                'pe.nombre AS punto'
            )
            ->join('usuarios', 'usuarios.id', '=', 'ca_cap_asistidas.id_asesor')
            ->join('ca_capacitaciones', 'ca_capacitaciones.id', '=', 'ca_cap_asistidas.id_capacitacion')
            ->leftJoin('punto_evaluacion AS pe', 'pe.id', 'id_cliente')
            ->leftJoin('unidad AS u', 'u.id', 'pe.unidad_id')
            ->leftJoin('centro_operacion AS co', 'co.id', 'u.centro_operacion_id');

        if (Auth::user()->savk_principal == 1) {
            $dataAsistidas->where(function ($query) use ($ids) {
                $query->whereIn('id_cliente', $ids)
                      ->orWhereNull('id_cliente');
            })->when(Auth::user()->main_account_id == 1 || Auth::user()->main_account_id == 2, function($query){
                return $query->where('usuarios.main_account_id', Auth::user()->main_account_id);
            });
        } else if (Auth::user()->id_grupo == 44) {
            $dataAsistidas->whereIn('id_cliente', $ids);
        } else if (Auth::user()->id_grupo == 45) {
            $dataAsistidas->whereIn('id_cliente', $ids);
        } else if (Auth::user()->id_grupo == 46) {
            $dataAsistidas->whereIn('id_cliente', $ids);
        } else if (Auth::user()->id_grupo == 47) {
            $dataAsistidas->whereIn('id_cliente', $ids);
        }else{
            $dataAsistidas->where('ca_cap_asistidas.id', 0); //no trae nada
        }

        if ($cant_pag == null) {
            $data = $dataAsistidas->orderBy('fecha', 'DESC')->get();
        } else {
            //dd($data->toSql());
            $data = $dataAsistidas->orderBy('fecha', 'DESC')->paginate($cant_pag);
        }

        return $data;
    }

    public function DownloadExcelAccompanimient(Request $request)
    {
        $cant_pag = null;

        $where = [];

        if (strlen($request->get('search')) != 0) {
            array_push($where, ['punto_evaluacion.nombre', 'LIKE', "%$request->search%"]);
        }

        $data = $this->GetDataReportAccompaniment($cant_pag, $where);

        foreach ($data as $key => $value) {
            $temp = $value->observacion;
            unset($value->id);
            unset($value->auditoria_id);
            unset($value->observacion);
            $value->observacion = $temp;
        }

        return Excel::download(new ReportAccopanimentExport($data), 'informe_acompañamiento.xlsx');
    }

    public function DownloadExcelTraining(Request $request)
    {
        $cant_pag = null;

        $where = [];

        if (strlen($request->get('search')) != 0) {
            array_push($where, ['punto_evaluacion.nombre', 'LIKE', "%$request->search%"]);
        }

        $data = $this->GetDataReportTraining($cant_pag, $where);

        foreach ($data as $key => $value) {
            unset($value->punto_id);
            unset($value->fecha);
            unset($value->asistentes);
            $value->certificados = strval($value->certificados);
            $value->duracion = strval($value->duracion * $value->certificados);
        }
        return Excel::download(new ReportTrainingExport($data), 'informe_entrenamiento.xlsx');
    }

    public function DownloadExcelTrainingAsistidas(Request $request)
    {
        $cant_pag = null;

        $where = [];

        if (strlen($request->get('search')) != 0) {
            array_push($where, ['punto_evaluacion.nombre', 'LIKE', "%$request->search%"]);
        }

        $data = $this->GetDataReportTrainingAsistidas($cant_pag, $where);

        foreach ($data as $key => $value) {
            unset($value->punto_id);
            unset($value->duracion);
            unset($value->categoria);
            $value->certificados = strval($value->certificados);
            $value->asistentes = strval($value->asistentes);
        }

        return Excel::download(new ReportTrainingAsistidasExport($data), 'informe_entrenamiento_asistidas.xlsx');
    }
}
