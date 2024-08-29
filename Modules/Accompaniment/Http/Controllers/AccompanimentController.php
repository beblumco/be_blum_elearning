<?php

namespace Modules\Accompaniment\Http\Controllers;

use App\Exports\AccompanimentExport;
use Illuminate\Pagination\LengthAwarePaginator;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\CentroOperacion;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Modules\Accompaniment\Entities\SavkVisita;
use Modules\Administration\Entities\SavkLideresCentroDeCostos;
use Modules\Administration\Entities\SavkLideresEmpresa;
use Modules\Administration\Entities\SavkLideresGrupoEmpresa;
use Modules\Administration\Entities\SavkLideresZonas;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class AccompanimentController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */

    public function __construct()
    {
        $this->middleware('auth')->except('detalleAuditoria', 'detalleAuditoriaFiltrado', 'generarDocumento', 'descargarCertificacion');
    }

    public function IndexAccompaniment()
    {
        $page_title = 'Acompañamiento';
        $action = 'IndexAccompaniment';
        $permisos = $this->GetAllPermisos();
        return view('accompaniment::index',  compact('page_title', 'action', 'permisos'));
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('accompaniment::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {

        SavkVisita::create([
            'fecha' => $request->fecha['val'],
            'modalidad' => $request->modalidad['val'],
            'id_centro_costo' =>  $request->centro_costo['val'],
            'observacion' => $request->observacion['val'],
            'id_usuario_registro' => Auth::user()->id,
            'interno_externo' => Auth::user()->main_account_id == 1 ? 1 : 2
        ]);

        return response()->json([
            'status' => 201,
            'msg' => 'Se ha creado el acompañamiento.'
        ]);
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('accompaniment::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('accompaniment::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }

    public function getCentrosCostoUsuario(Request $request)
    {
        $data = [];

        if (Auth::user()->main_account_id != 1 && Auth::user()->savk_principal == 1) {
            $data = CentroOperacion::select('punto_evaluacion.id', 'punto_evaluacion.nombre as name')
                ->join('unidad', 'unidad.centro_operacion_id', 'centro_operacion.id')
                ->join('punto_evaluacion', 'punto_evaluacion.unidad_id', 'unidad.id')
                ->where('centro_operacion.main_account_id', Auth::user()->main_account_id)->get();
        } else if (Auth::user()->id_grupo == 30 || \Auth::user()->id_grupo == 39) {
            $data = CentroOperacion::select('punto_evaluacion.id', 'punto_evaluacion.nombre as name')
                ->join('unidad', 'unidad.centro_operacion_id', 'centro_operacion.id')
                ->join('punto_evaluacion', 'punto_evaluacion.unidad_id', 'unidad.id')
                ->where('centro_operacion.asesor_id', Auth::user()->id)->get();
        } else if (Auth::user()->id_grupo == 44) {
            $data = SavkLideresGrupoEmpresa::select('punto_evaluacion.id', 'punto_evaluacion.nombre as name')
                ->join('unidad', 'unidad.centro_operacion_id', 'savk_lideres_grupo_empresas.id_grupo_empresa')
                ->join('punto_evaluacion', 'punto_evaluacion.unidad_id', 'unidad.id')
                ->where('savk_lideres_grupo_empresas.id_usuario', Auth::user()->id)->get();
        } else if (Auth::user()->id_grupo == 45) {
            $data = SavkLideresEmpresa::select('punto_evaluacion.id', 'punto_evaluacion.nombre as name')
                ->join('punto_evaluacion', 'punto_evaluacion.unidad_id', 'savk_lideres_empresas.id_empresa')
                ->where('savk_lideres_empresas.id_usuario', Auth::user()->id)->get();
        } else if (Auth::user()->id_grupo == 47) {
            $data = SavkLideresCentroDeCostos::select('punto_evaluacion.id', 'punto_evaluacion.nombre as name')
                ->join('punto_evaluacion', 'punto_evaluacion.id', 'savk_lideres_centro_de_costos.id_centro_de_costo')
                ->where('savk_lideres_centro_de_costos.id_usuario', Auth::user()->id)->get();
        }

        return $data;
    }

    public function getAcompañamientos(Request $request)
    {
        $cant_pag = 10;

        $where = [];

        if (sizeof($request->get('paginate')) > 0) {
            $cant_pag = $request->paginate['cant'];
        }

        if (strlen($request->get('search')) != 0) {
            array_push($where, ['punto_evaluacion.nombre', 'LIKE', "%$request->search%"]);
        }

        $data = SavkVisita::getAll($where, $cant_pag, $request->filters);

        foreach ($data as $d) {
            $d->tiempo = round(DB::table('auditoria_iniciadas')->select('tiempo')->where('auditoria_iniciadas.visita_id', $d->id)->get()->sum('tiempo') / 60, 2);

            $d->tipo = DB::table('auditoria_iniciadas')
                ->select(
                    DB::raw("
                    (CASE
                        WHEN auditoria_iniciadas.auditoria_id = '65' THEN (
                            SELECT rd.descripcion
                            FROM respuestas_auditoria_iniciadas AS ri
                            JOIN respuesta_detalle rd ON ri.respuesta_id = rd.id
                            WHERE ri.auditoria_iniciada_id = auditoria_iniciadas.id
                        )
                        WHEN auditoria_iniciadas.auditoria_id <> '65' THEN auditorias.nombre
                    END) AS nombre
                    ")
                )
                ->join('auditorias', 'auditorias.id', 'auditoria_iniciadas.auditoria_id')
                ->where('auditoria_iniciadas.visita_id', $d->id)
                ->get()
                ->unique('nombre')
                ->pluck('nombre')
                ->toArray();

            $d->tipo = implode(', ', $d->tipo);
        }

        $tipo = SavkVisita::getTipoAuditorias();

        $usuarios = DB::table('usuarios')->select('id', 'nombre_com as name')->whereIn('id', $data->unique('idUsuario')->pluck('idUsuario')->all())->get();
        $centros_costos = DB::table('punto_evaluacion')->select('id', 'nombre as name')->whereIn('id', $data->unique('idCentroCosto')->pluck('idCentroCosto')->all())->get();


        return response()->json([
            'status' => 200,
            'data' => $data,
            'usuarios' => $usuarios,
            'centros_costos' => $centros_costos,
            'tipo' => $tipo
        ]);
    }

    public function indicadores(Request $request)
    {

        $init_date = $request->init_date == 0 ? 0 : substr($request->init_date, 0, 4) . '-' . substr($request->init_date, 4, 2) . '-' . substr($request->init_date, 6, 2);
        $end_date = $request->end_date == 0 ? 0 : substr($request->end_date, 0, 4) . '-' . substr($request->end_date, 4, 2) . '-' . substr($request->end_date, 6, 2);

        $filtroFecha = true;
        if ($init_date == 0 && $end_date == 0) {
            $filtroFecha = false;
        }


        if (Auth::user()->main_account_id != 1 && Auth::user()->savk_principal == 1) {
            $consulta = CentroOperacion::select(
                'savk_visitas.id',
            )->join('unidad', 'unidad.centro_operacion_id', 'centro_operacion.id')
                ->join('punto_evaluacion', 'punto_evaluacion.unidad_id', 'unidad.id')
                ->join('savk_visitas', 'savk_visitas.id_centro_costo', 'punto_evaluacion.id')
                ->join('auditoria_iniciadas', 'auditoria_iniciadas.visita_id', 'savk_visitas.id')
                ->where('centro_operacion.main_account_id', Auth::user()->main_account_id);

            if ($filtroFecha == true) {
                $consulta->whereBetween('savk_visitas.fecha', [$init_date, $end_date]);
            }

            $numero = $consulta->count();

            $tiempo = DB::table('auditoria_iniciadas')
                ->select('tiempo')
                ->whereIn('visita_id', $consulta->get()->pluck('id'))
                ->sum('tiempo');
        } else if (Auth::user()->id_grupo == 44) {
            $consulta = SavkLideresGrupoEmpresa::select(
                'savk_visitas.id',
            )
                ->join('unidad', 'unidad.centro_operacion_id', 'savk_lideres_grupo_empresas.id_grupo_empresa')
                ->join('punto_evaluacion', 'punto_evaluacion.unidad_id', 'unidad.id')
                ->join('savk_visitas', 'savk_visitas.id_centro_costo', 'punto_evaluacion.id')
                ->join('auditoria_iniciadas', 'auditoria_iniciadas.visita_id', 'savk_visitas.id')
                ->where('savk_lideres_grupo_empresas.id_usuario', Auth::user()->id);


            if ($filtroFecha == true) {
                $consulta->whereBetween('savk_visitas.fecha', [$init_date, $end_date]);
            }

            $numero = $consulta->count();

            $tiempo = DB::table('auditoria_iniciadas')
                ->select('tiempo')
                ->whereIn('visita_id', $consulta->get()->pluck('id'))
                ->sum('tiempo');
        } else if (Auth::user()->id_grupo == 45) {
            $consulta = SavkLideresEmpresa::select(
                'savk_visitas.id',
            )
                ->join('punto_evaluacion', 'punto_evaluacion.unidad_id', 'savk_lideres_empresas.id_empresa')
                ->join('savk_visitas', 'savk_visitas.id_centro_costo', 'punto_evaluacion.id')
                ->join('auditoria_iniciadas', 'auditoria_iniciadas.visita_id', 'savk_visitas.id')
                ->where('savk_lideres_empresas.id_usuario', Auth::user()->id);

            if ($filtroFecha == true) {
                $consulta->whereBetween('savk_visitas.fecha', [$init_date, $end_date]);
            }

            $numero = $consulta->count();

            $tiempo = DB::table('auditoria_iniciadas')
                ->select('tiempo')
                ->whereIn('visita_id', $consulta->get()->pluck('id'))
                ->sum('tiempo');
        } else if (Auth::user()->id_grupo == 46) {
            $consulta = SavkLideresZonas::select(
                'savk_visitas.id',
            )
            ->join('grupos_puntos', 'grupos_puntos.id', 'id_grupos_puntos')
            ->join('grupos_sub_puntos', 'grupos_sub_puntos.grupo_punto_id', 'grupos_puntos.id')
            ->join('savk_visitas', 'savk_visitas.id_centro_costo', 'grupos_sub_puntos.punto_id')
            ->join('auditoria_iniciadas', 'auditoria_iniciadas.visita_id', 'savk_visitas.id')
            ->where('savk_liederes_zonas.id_usuario', Auth::user()->id)
            ->groupBy('savk_visitas.id');

            if ($filtroFecha == true) {
                $consulta->whereBetween('savk_visitas.fecha', [$init_date, $end_date]);
            }

            $numero = count($consulta->get());

            $tiempo = DB::table('auditoria_iniciadas')
                ->select('tiempo')
                ->whereIn('visita_id', $consulta->get()->pluck('id'))
                ->sum('tiempo');
        } else if (Auth::user()->id_grupo == 47) {
            $consulta = SavkLideresCentroDeCostos::select(
                'savk_visitas.id',
            )
            ->join('savk_visitas', 'savk_visitas.id_centro_costo', 'savk_lideres_centro_de_costos.id_centro_de_costo')
            ->join('auditoria_iniciadas', 'auditoria_iniciadas.visita_id', 'savk_visitas.id')
            ->where('savk_lideres_centro_de_costos.id_usuario', Auth::user()->id);

            if ($filtroFecha == true) {
                $consulta->whereBetween('savk_visitas.fecha', [$init_date, $end_date]);
            }

            // $numero = $consulta->groupBy('savk_visitas.id')->count();
            $numero = $consulta->count();

            $tiempo = DB::table('auditoria_iniciadas')
                ->select('tiempo')
                ->whereIn('visita_id', $consulta->get()->pluck('id'))
                ->sum('tiempo');
        }

        $data = [
            'numeroAcompanamiento'   => $numero,
            'horasAcompanamiento'    =>  round($tiempo / 60, 2)
        ];

        return $this->ExitProgram(201, $this->MessageResponse('', 201, 'Se genero tiempo en entrenamiento.'), $data);
    }

    public function DownloadReportVisit($id)
    {
        $estadoAuditorias = SavkVisita::join('auditoria_iniciadas', 'auditoria_iniciadas.visita_id', 'savk_visitas.id')
            ->where('savk_visitas.id', $id)
            ->where('auditoria_iniciadas.estado', 4)
            ->orWhere('auditoria_iniciadas.estado', 5)->exists();

        if ($estadoAuditorias == false) {
            return response()->json([
                'status' => 401,
                'msg' => 'Visita sin ninguna actividad en estado Terminado'
            ], 401);
        }



        $data = SavkVisita::select(
            'punto_evaluacion.nombre as centro_costo',
            DB::raw('DATE_FORMAT(savk_visitas.fecha, "%d-%m-%Y") as fecha'),
            'unidad.nombre as razon_social',
            'centro_operacion.nombre as grupo_empresa',
            'centro_operacion.img_avatar as img_avatar',
            'ciudades.nombre as ciudad',
            DB::raw("
                (CASE
                    WHEN savk_visitas.modalidad = '1' THEN 'Presencial'
                    WHEN savk_visitas.modalidad = '2' THEN 'Virtual'
                END) AS modalidad
            ")
        )
            ->join('punto_evaluacion', 'punto_evaluacion.id', 'savk_visitas.id_centro_costo')
            ->join('unidad', 'unidad.id', 'punto_evaluacion.unidad_id')
            ->join('ciudades', 'ciudades.id', 'punto_evaluacion.ciudad_id')
            ->join('centro_operacion', 'centro_operacion.id', 'unidad.centro_operacion_id')
            ->where('savk_visitas.id', $id)->first();

        $data->actividades = DB::table('auditoria_iniciadas')
            ->select(
                'auditoria_iniciadas.id as id',
                DB::raw("
                (CASE
                    WHEN auditoria_iniciadas.auditoria_id = '65' THEN (
                        SELECT rd.descripcion
                        FROM respuestas_auditoria_iniciadas AS ri
                        JOIN respuesta_detalle rd ON ri.respuesta_id = rd.id
                        WHERE ri.auditoria_iniciada_id = auditoria_iniciadas.id
                    )
                    WHEN auditoria_iniciadas.auditoria_id <> '65' THEN 'Auditoría'
                END) AS actividad
                "),
                'auditoria_iniciadas.auditoria_id as auditoria_id',
                'auditoria_iniciadas.created_at as fecha',
                'auditoria_iniciadas.responsable as responsable',
                'auditoria_iniciadas.tiempo as tiempo',
                'auditorias.nombre as tipo',
                'usuarios.nombre_com as experto',
            )
            ->join('auditorias', 'auditorias.id', 'auditoria_iniciadas.auditoria_id')
            ->join('usuarios', 'usuarios.id', 'auditoria_iniciadas.usuario_id')
            ->where('visita_id', $id)
            ->get();


        foreach ($data->actividades as $actividad) {
            DB::select("SET sql_mode=''");

            if ($actividad->auditoria_id != 65) {

                $actividad->resultadosPorCategoria = DB::select("
            SELECT
                ca.nombre AS CATEGORIA,
                FORMAT(AVG(rd.puntaje),2) AS RESULTADO
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
                rai.auditoria_iniciada_id = " . $actividad->id . "
            GROUP BY
                ca.id
            ORDER BY
                pr.orden asc
            ");

                $array = array();
                $temp =  array();

                for ($i = 0; $i < sizeof($actividad->resultadosPorCategoria); $i++) {

                    $temp[] =  $actividad->resultadosPorCategoria[$i];

                    if (sizeof($temp) == 5 || $i + 1 == sizeof($actividad->resultadosPorCategoria)) {
                        $array[] = $temp;
                        $temp = array();
                    }
                }

                $actividad->resultadosPorCategoria = $array;

                //return response()->json($actividad->resultadosPorCategoria, 500);


                $actividad->resultadoTotal = DB::select("
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
                rai.auditoria_iniciada_id = " . $actividad->id . "
                group by ca.id
            ORDER BY
                pr.orden asc) as R
            ");

                $actividad->hallazgos = DB::table('respuestas_auditoria_iniciadas')
                    ->select(
                        'imagenes_auditoria.imagen as imagen',
                        'preguntas.descripcion as pregunta',
                        'respuesta_detalle.descripcion as respuesta',
                        'respuestas_auditoria_iniciadas.obs_respuesta as observacion'
                    )
                    ->join('respuesta_detalle', 'respuesta_detalle.id', 'respuestas_auditoria_iniciadas.respuesta_id')
                    ->join('preguntas', 'preguntas.id', 'respuestas_auditoria_iniciadas.pregunta_id')
                    ->leftjoin('imagenes_auditoria', function ($join) {
                        $join->on('imagenes_auditoria.auditoria_iniciada_id', 'respuestas_auditoria_iniciadas.auditoria_iniciada_id')
                            ->on('respuestas_auditoria_iniciadas.pregunta_id', '=', 'imagenes_auditoria.pregunta_id');
                    })
                    ->where([
                        ['respuestas_auditoria_iniciadas.auditoria_iniciada_id', $actividad->id],
                        ['respuesta_detalle.puntaje', 0]
                    ])->get();
            } else {
                $tipoRespuesta = DB::table('respuestas_auditoria_iniciadas')
                    ->select('respuesta_id')
                    ->where('auditoria_iniciada_id', $actividad->id)
                    ->pluck('respuesta_id')
                    ->first();

                if ($tipoRespuesta == 4486) {
                    $actividad->respuestaLuminometria = DB::table('respuestas_auditoria_iniciadas as rai')
                        ->select(
                            DB::raw("IFNULL( (SELECT ars.nombre FROM areas ars WHERE ars.id = sai.area_id), (SELECT ars.nombre FROM areas ars WHERE ars.id = sai.area_id)) as area"),
                            DB::raw('(SELECT sus.nombre FROM superficies sus WHERE sus.id = sai.superficie_id) AS superficie'),
                            DB::raw("IFNULL( sai.desde, mai.desde) as antes"),
                            DB::raw("IFNULL( sai.hasta, mai.hasta) as despues"),
                            DB::raw("IFNULL( sai.descripcion, mai.descripcion) as descripcion"),
                            DB::raw("IFNULL( sai.item_producto, mai.item_producto) as item"),
                            DB::raw("IFNULL( sai.nombre_producto, mai.nombre_producto) as producto"),
                            DB::raw("IFNULL( sai.cantidad_concentracion, mai.cantidad_concentracion) as cant_concentracion"),
                            DB::raw("IFNULL((SELECT um.nombre FROM lu_unidad_medida um WHERE um.id = sai.id_unidad_medida_concentracion), (SELECT um.nombre FROM lu_unidad_medida um WHERE um.id = mai.id_unidad_medida_concentracion)) as um_concentracion"),
                            DB::raw("IFNULL( sai.cantidad_cant, mai.cantidad_cant) as cant_cantidad"),
                            DB::raw("IFNULL((SELECT um.nombre FROM lu_unidad_medida um WHERE um.id = sai.id_unidad_medida_cantidad), (SELECT um.nombre FROM lu_unidad_medida um WHERE um.id = mai.id_unidad_medida_cantidad)) as um_cantidad"),
                        )
                        ->join('respuesta_detalle as rd', 'rd.id', 'rai.respuesta_id')
                        ->leftJoin('superficies_aud_iniciadas as sai', 'rai.auditoria_iniciada_id', 'sai.auditoria_iniciada_id')
                        ->leftJoin('manos_aud_iniciadas as mai', 'rai.auditoria_iniciada_id', 'mai.auditoria_iniciada_id')
                        ->where([
                            ['rd.id', 4486],
                            ['rai.auditoria_iniciada_id', $actividad->id]
                        ])
                        ->get();

                    $actividad->respuestaActividadComun = NULL;
                } else {
                    $actividad->respuestaLuminometria = NULL;
                    $actividad->respuestaActividadComun = DB::table('akl_actividad')
                        ->select(
                            'akl_actividad.id',
                            'akl_actividad.descripcion_general',
                        )
                        ->where('akl_actividad.id_auditoria_iniciada', $actividad->id)
                        ->get();

                    foreach ($actividad->respuestaActividadComun as $key => $value) {
                        $value->detalle = DB::table('akl_actividad_detalle')->select(
                            'akl_actividad_detalle.imagen',
                            'akl_actividad_detalle.comentario'
                        )->where('akl_actividad_detalle.id_actividad', $value->id)
                            ->get();
                    }
                }
            }

            $actividad->observacionGeneral = DB::table('observacion_general')->select('observacion')->where([['auditoria_iniciada_id', $actividad->id], ['estado', 1]])->first();
        }


        $pdf = Pdf::loadView('accompaniment::pdf.reportevisita', ['data' => $data]);
        return $pdf->setPaper('A4')->download('reporte.pdf');
    }

    public function downloadExcel(Request $request)
    {
        $cant_pag = NULL;

        $where = [];

        if (strlen($request->get('search')) != 0) {
            array_push($where, ['punto_evaluacion.nombre', 'LIKE', "%$request->search%"]);
        }

        $data = SavkVisita::getAll($where, $cant_pag, $request->filters);

        foreach ($data as $d) {
            $d->tiempo = DB::table('auditoria_iniciadas')->select('tiempo')->where('auditoria_iniciadas.visita_id', $d->id)->get()->sum('tiempo');

            $d->tipo = DB::table('auditoria_iniciadas')
                ->select(
                    DB::raw("
                    (CASE
                        WHEN auditoria_iniciadas.auditoria_id = '65' THEN (
                            SELECT rd.descripcion
                            FROM respuestas_auditoria_iniciadas AS ri
                            JOIN respuesta_detalle rd ON ri.respuesta_id = rd.id
                            WHERE ri.auditoria_iniciada_id = auditoria_iniciadas.id
                        )
                        WHEN auditoria_iniciadas.auditoria_id <> '65' THEN auditorias.nombre
                    END) AS nombre
                    ")
                )
                ->join('auditorias', 'auditorias.id', 'auditoria_iniciadas.auditoria_id')
                ->where('auditoria_iniciadas.visita_id', $d->id)
                ->get()
                ->unique('nombre')
                ->pluck('nombre')
                ->toArray();

            $d->tipo = implode(', ', $d->tipo);
        }

        $data = $data->toArray();

        foreach ($data as &$item) {
            //SE ELIMINA ID USUARIO Y ASISTENTE PARA NO MOSTRARLO EN EXCEL Y SE ELIMINA APROBO SI NO TIENE EVALUACION
            unset($item['idUsuario']);
            unset($item['id']);
            unset($item['idCentroCosto']);
            $item['Fecha'] = $item['fecha'];
            $item['Modalidad'] = $item['modalidad'];
            $item['Tiempo'] = $item['tiempo'];
            $item['Tipo'] = $item['tipo'];
            unset($item['fecha']);
            unset($item['modalidad']);
            unset($item['tiempo']);
            unset($item['tipo']);
            $item['Realizado por'] = $item['nombre_com'];
            $item['Centro de costo'] = $item['nombre'];
            unset($item['nombre_com']);
            unset($item['nombre']);
        }

        $columnas = array_keys($data[0]); //titulos

        // Crear un nuevo objeto Spreadsheet
        $spreadsheet = new Spreadsheet();

        // Obtener la hoja activa del objeto Spreadsheet
        $sheet = $spreadsheet->getActiveSheet();

        $columna = 'A';
        foreach ($columnas as $columnaTitulo) {
            $sheet->setCellValue($columna . '1', $columnaTitulo)->getColumnDimension($columna)->setAutoSize(true);
            $columna++;
        }

        // Llenar los datos en la hoja de cálculo
        $sheet->fromArray($data, null, 'A2');

        // Crear el objeto Writer para escribir en formato XLSX
        $writer = new Xlsx($spreadsheet);

        // Definir el nombre del archivo
        $filename = 'Acompañamientos.xlsx'; // Reemplaza "nombre_del_archivo" con el nombre que desees

        // Descargar el archivo Excel
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    public function getResultadosAcompañamiento(Request $request)
    {

        $data = DB::table('auditoria_iniciadas')
            ->select(
                'auditoria_iniciadas.id',
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
                'observacion_general.observacion',
                'auditoria_iniciadas.tiempo',
                'ciudades.nombre as ciudad',
                'auditoria_iniciadas.lat',
                'auditoria_iniciadas.long',
                'area_punto_evaluacion.nombre as nombreArea',
                'auditoria_iniciadas.signature_path as firma'
            )
            ->leftJoin('area_punto_evaluacion', 'area_punto_evaluacion.id', 'auditoria_iniciadas.area_id')
            ->join('auditorias', 'auditorias.id', 'auditoria_iniciadas.auditoria_id')
            ->join('observacion_general', 'observacion_general.auditoria_iniciada_id', 'auditoria_iniciadas.id')
            ->join('punto_evaluacion', 'punto_evaluacion.id', 'auditoria_iniciadas.punto_id')
            ->leftJoin('ciudades', 'ciudades.id', 'punto_evaluacion.ciudad_id')
            ->join('unidad', 'unidad.id', 'punto_evaluacion.unidad_id')
            ->join('centro_operacion', 'centro_operacion.id', 'unidad.centro_operacion_id')
            ->where('auditoria_iniciadas.visita_id', $request->id)
            ->get();

        foreach ($data as $d) {

            $d->idEncriptado = Crypt::encrypt($d->id);

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

        return response()->json([
            'status' => 200,
            'data' => $data,
        ]);
    }

    public function detalleAuditoria($id)
    {

        $id = Crypt::decrypt($id);

        $page_title = 'Detalle auditoría';
        $action = 'detalleAuditoria';

        $data = DB::table('auditoria_iniciadas')
            ->select(
                'auditoria_iniciadas.id as id',
                'auditoria_iniciadas.fecha_fin_auditoria as fecha',
                'auditoria_iniciadas.responsable',
                'auditoria_iniciadas.auditoria_id',
                DB::raw("
                (CASE
                    WHEN auditoria_iniciadas.auditoria_id = '65' THEN (
                        SELECT rd.descripcion
                        FROM respuestas_auditoria_iniciadas AS ri
                        JOIN respuesta_detalle rd ON ri.respuesta_id = rd.id
                        WHERE ri.auditoria_iniciada_id = auditoria_iniciadas.id
                    )
                    WHEN auditoria_iniciadas.auditoria_id <> '65' THEN 'Auditoría'
                END) AS actividad
                "),
                'usuarios.nombre_com as auditor',
                'observacion_general.observacion',
                'punto_evaluacion.nombre as centro_costo',
                'area_punto_evaluacion.nombre as nombreArea',
                'auditoria_iniciadas.signature_path as firma'
            )
            ->leftJoin('area_punto_evaluacion', 'area_punto_evaluacion.id', 'auditoria_iniciadas.area_id')
            ->join('usuarios', 'usuarios.id', 'auditoria_iniciadas.usuario_id')
            ->join('observacion_general', 'observacion_general.auditoria_iniciada_id', 'auditoria_iniciadas.id')
            ->join('punto_evaluacion', 'punto_evaluacion.id', 'auditoria_iniciadas.punto_id')
            ->where('auditoria_iniciadas.id', $id)
            ->first();

        DB::select("SET sql_mode=''");

        $data->link = CropLink(env('URL') . "/accompaniment/detalle-auditoria/" . $id);


        if ($data->auditoria_id == 65) {
            $data->resultado = "N/A";

            $data->detalle = [];

            $tipoRespuesta = DB::table('respuestas_auditoria_iniciadas')
                ->select('respuesta_id')
                ->where('auditoria_iniciada_id', $data->id)
                ->pluck('respuesta_id')
                ->first();

            if ($tipoRespuesta == 4486) {
                $data->respuestaLuminometria['manos'] = DB::table('manos_aud_iniciadas as mai')
                    ->select(
                        "mai.id AS MANOS_AUDITORIA_ID",
                        "ca.nombre AS CARGO_PERSONA",
                        "mai.nombre AS NOMBRE_RESPONSABLE",
                        "mai.auditoria_iniciada_id AS ID_AUDITORIA_INICIADA",
                        DB::raw("IF(mai.descripcion IS NULL,'',mai.descripcion) AS DESCRIPCION"),
                        DB::raw("IF(mai.desde IS NULL,'',mai.desde) AS ANTES"),
                        DB::raw("IF(ar.nombre IS NULL,'',ar.nombre) AS AREA"),
                        DB::raw("IF(ar.descripcion IS NULL,'',ar.descripcion) AS DESCRIPCION_AREA"),
                        DB::raw("IF(mai.hasta IS NULL,'',mai.hasta) AS DESPUES"),
                        DB::raw("IF(mai.item_producto IS NULL,'Sin item', mai.item_producto) AS ITEM_PRODUCTO_MANOS"),
                        DB::raw("IF(mai.nombre_producto IS NULL,'Sin producto', mai.nombre_producto) AS PRODUCTO_MANOS"),
                        DB::raw("IF(mai.cantidad_concentracion IS NULL,'Sin cantidad', mai.cantidad_concentracion) AS CANTIDAD_CONCENTRACION_MANOS"),
                        DB::raw("IF(mai.id_unidad_medida_concentracion IS NULL,'Sin unidad medida', (SELECT nombre FROM lu_unidad_medida WHERE id = mai.id_unidad_medida_concentracion)) AS UNIDAD_MEDIDA_CONCENTRACION_MANOS"),
                        DB::raw("IF(mai.cantidad_cant IS NULL,'Sin cantidad', mai.cantidad_cant) AS CANT_CANTIDAD_MANOS"),
                        DB::raw("IF(mai.id_unidad_medida_cantidad IS NULL,'Sin unidad medida', (SELECT nombre FROM lu_unidad_medida WHERE id = mai.id_unidad_medida_cantidad)) AS UNIDAD_MEDIDA_CANT_MANOS")
                    )
                    ->join('cargos as ca', 'ca.id', 'mai.cargo_id')
                    ->leftJoin('areas as ar', 'ar.id', 'mai.area_id')
                    ->where('mai.auditoria_iniciada_id', $data->id)
                    ->get();
                $data->respuestaLuminometria['superficies'] = DB::table('superficies_aud_iniciadas as sai')
                    ->select(
                        "sai.id AS SUPERFICIE_AUDITORIA_ID",
                        "su.nombre AS SUPERFICIE",
                        "sai.auditoria_iniciada_id AS ID_AUDITORIA_INICIADA",
                        DB::raw("IF(sai.descripcion IS NULL,'',sai.descripcion) AS DESCRIPCION"),
                        DB::raw("IF(sai.desde IS NULL,'',sai.desde) AS ANTES"),
                        DB::raw("IF(ar.nombre IS NULL,'',ar.nombre) AS AREA"),
                        DB::raw("IF(ar.descripcion IS NULL,'',ar.descripcion) AS DESCRIPCION_AREA"),
                        DB::raw("IF(sai.hasta IS NULL,'',sai.hasta) AS DESPUES"),
                        DB::raw("IF(sai.item_producto IS NULL,'Sin item', sai.item_producto) AS ITEM_PRODUCTO_SUPERFICIES"),
                        DB::raw("IF(sai.nombre_producto IS NULL,'Sin producto', sai.nombre_producto) AS PRODUCTO_SUPERFICIES"),
                        DB::raw("IF(sai.cantidad_concentracion IS NULL,'Sin cantidad', sai.cantidad_concentracion) AS CANTIDAD_CONCENTRACION_SUPERFICIES"),
                        DB::raw("IF(sai.id_unidad_medida_concentracion IS NULL,'Sin unidad medida', (SELECT nombre FROM lu_unidad_medida WHERE id = sai.id_unidad_medida_concentracion)) AS UNIDAD_MEDIDA_CONCENTRACION_SUPERFICIES"),
                        DB::raw("IF(sai.cantidad_cant IS NULL,'Sin cantidad', sai.cantidad_cant) AS CANT_CANTIDAD_SUPERFICIES"),
                        DB::raw("IF(sai.id_unidad_medida_cantidad IS NULL,'Sin unidad medida', (SELECT nombre FROM lu_unidad_medida WHERE id = sai.id_unidad_medida_cantidad)) AS UNIDAD_MEDIDA_CANT_SUPERFICIES")
                    )
                    ->join('superficies as su', 'su.id', 'sai.superficie_id')
                    ->leftJoin('areas as ar', 'ar.id', 'sai.area_id')
                    ->where('sai.auditoria_iniciada_id', $data->id)
                    ->get();

                foreach ($data->respuestaLuminometria['manos'] as $key => $value) {
                    $imagenes =  DB::table('fotos_luminometria')->where([
                        ['auditoria_iniciada_id', $id],
                        ['manos_aud_ini_id', $value->MANOS_AUDITORIA_ID]
                    ])->get()->pluck('imagen');

                    $value->imagenes = [];

                    foreach ($imagenes as $key => $v) {
                        array_push($value->imagenes, explode("/", $v)[3]);
                    }
                }

                foreach ($data->respuestaLuminometria['superficies'] as $key => $value) {
                    $imagenes = DB::table('fotos_luminometria')->where([
                        ['auditoria_iniciada_id', $id],
                        ['superficies_aud_ini_id', $value->SUPERFICIE_AUDITORIA_ID]
                    ])->get()->pluck('imagen');

                    $value->imagenes = [];

                    foreach ($imagenes as $key => $v) {
                        array_push($value->imagenes, explode("/", $v)[3]);
                    }
                }

                $data->respuestaActividadComun = NULL;
            } else if ($tipoRespuesta == 4488) {
                $data->respuestaCapacitacion = DB::table('akl_capacitaciones_iniciadas')
                    ->select(
                        'akl_capacitaciones_iniciadas.id',
                        'akl_capacitaciones.nombre as capacitacion',
                        'akl_capacitaciones_iniciadas.observacion as observacion'
                    )
                    ->join('akl_capacitaciones', 'akl_capacitaciones.id', 'akl_capacitaciones_iniciadas.id_akl_capacitacion')
                    ->where('akl_capacitaciones_iniciadas.id_auditoria_iniciada', $id)
                    ->first();

                $data->respuestaCapacitacion->imagenes =  DB::table('akl_fotos_capacitaciones')->where([
                    ['id_akl_cap_iniciadas', $data->respuestaCapacitacion->id]
                ])->get()->pluck('imagen');

                $data->respuestaCapacitacion->asistentes = DB::table('akl_asistentes_capacitaciones')
                    ->select(
                        'akl_asistentes_capacitaciones.id',
                        'akl_asistentes.nombre',
                        'akl_asistentes.numero_documento'
                    )
                    ->join('akl_asistentes', 'akl_asistentes.id', 'akl_asistentes_capacitaciones.id_akl_asistente')
                    ->join('akl_capacitaciones_iniciadas', 'akl_capacitaciones_iniciadas.id', 'akl_asistentes_capacitaciones.id_akl_capacitacion')
                    ->where('akl_capacitaciones_iniciadas.id_auditoria_iniciada', $id)
                    ->get();
            } else {
                $data->respuestaLuminometria = NULL;
                $data->respuestaActividadComun = DB::table('akl_actividad')
                    ->select(
                        'akl_actividad.id',
                        'akl_actividad.descripcion_general',
                    )
                    ->where('akl_actividad.id_auditoria_iniciada', $data->id)
                    ->get();

                foreach ($data->respuestaActividadComun as $key => $value) {
                    $value->detalle = DB::table('akl_actividad_detalle')->select(
                        'akl_actividad_detalle.imagen',
                        'akl_actividad_detalle.comentario'
                    )->where('akl_actividad_detalle.id_actividad', $value->id)
                        ->get();
                }
            }
        } else {
            $data->resultado =  DB::select("
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
                rai.auditoria_iniciada_id = " . $id . "
                group by ca.id
            ORDER BY
                pr.orden asc) as R
            ")[0]->RESULTADO . "%";

            $data->detalle = DB::table('respuestas_auditoria_iniciadas')
                ->select(
                    'respuestas_auditoria_iniciadas.pregunta_id',
                    'preguntas.descripcion as pregunta',
                    'preguntas.catagoria_id as categoria_id',
                    'categorias.nombre as categoria',
                    'respuesta_detalle.descripcion as respuesta',
                    'respuestas_auditoria_iniciadas.obs_respuesta as observacion'
                )
                ->join('respuesta_detalle', 'respuesta_detalle.id', 'respuestas_auditoria_iniciadas.respuesta_id')
                ->join('preguntas', 'preguntas.id', 'respuestas_auditoria_iniciadas.pregunta_id')
                ->join('categorias', 'preguntas.catagoria_id', 'categorias.id')
                ->where([
                    ['respuestas_auditoria_iniciadas.auditoria_iniciada_id', $id]
                ])->get();
            foreach ($data->detalle as $key => $value) {
                $value->imagenes = DB::table('imagenes_auditoria')
                    ->select(
                        'imagenes_auditoria.imagen as imagen',
                    )
                    ->where([
                        ['imagenes_auditoria.auditoria_iniciada_id', $id],
                        ['imagenes_auditoria.pregunta_id', $value->pregunta_id]
                    ])
                    ->get()
                    ->pluck('imagen');
            }

            $data->detalle = $data->detalle->groupBy('categoria_id');
        }
        $data->resultadosPorCategoria = DB::select("
        SELECT
            ca.nombre AS CATEGORIA,
            FORMAT(AVG(rd.puntaje),2) AS RESULTADO
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
            rai.auditoria_iniciada_id = " . $id . "
        GROUP BY
            ca.id
        ORDER BY
            pr.orden asc
        ");

        return view('accompaniment::detalle_auditoria',  compact('page_title', 'action', 'data'));
    }

    public function detalleAuditoriaFiltrado($id, Request $request)
    {
        $data = DB::table('respuestas_auditoria_iniciadas')
            ->select(
                'respuestas_auditoria_iniciadas.pregunta_id',
                'preguntas.descripcion as pregunta',
                'preguntas.catagoria_id as categoria_id',
                'categorias.nombre as categoria',
                'respuesta_detalle.descripcion as respuesta',
                'respuestas_auditoria_iniciadas.obs_respuesta as observacion'
            )
            ->join('respuesta_detalle', 'respuesta_detalle.id', 'respuestas_auditoria_iniciadas.respuesta_id')
            ->join('preguntas', 'preguntas.id', 'respuestas_auditoria_iniciadas.pregunta_id')
            ->join('categorias', 'preguntas.catagoria_id', 'categorias.id')
            ->where([
                ['respuestas_auditoria_iniciadas.auditoria_iniciada_id', $id]
            ]);

        if (isset($request->filters['respuesta'])) {
            if ($request->filters['respuesta']  == 1) {
                $data->where('respuesta_detalle.descripcion', 'Cumple');
            } else if ($request->filters['respuesta']  == 2) {
                $data->where('respuesta_detalle.descripcion', 'No Cumple');
            } else if ($request->filters['respuesta']  == 3) {
                $data->where('respuesta_detalle.descripcion', '<>', 'No Cumple')->where('respuesta_detalle.descripcion', '<>', 'Cumple');
            }
        }

        $data = $data->get();
        foreach ($data as $key => $value) {
            $value->imagenes = DB::table('imagenes_auditoria')
                ->select(
                    'imagenes_auditoria.imagen as imagen',
                )
                ->where([
                    ['imagenes_auditoria.auditoria_iniciada_id', $id],
                    ['imagenes_auditoria.pregunta_id', $value->pregunta_id]
                ])
                ->get()
                ->pluck('imagen');
        }

        $data = $data->groupBy('categoria_id');

        return response()->json([
            'status' => 200,
            'data' => $data,
        ]);
    }

    public function generarDocumento($id, $tipo)
    {

        $page_title = 'Detalle auditoría';
        $action = 'detalleAuditoria';

        $data = DB::table('auditoria_iniciadas')
            ->select(
                'auditoria_iniciadas.id as id',
                'auditoria_iniciadas.fecha_fin_auditoria as fecha',
                DB::raw("
                (CASE
                    WHEN auditoria_iniciadas.auditoria_id = '65' THEN (
                        SELECT rd.descripcion
                        FROM respuestas_auditoria_iniciadas AS ri
                        JOIN respuesta_detalle rd ON ri.respuesta_id = rd.id
                        WHERE ri.auditoria_iniciada_id = auditoria_iniciadas.id
                    )
                    WHEN auditoria_iniciadas.auditoria_id <> '65' THEN 'Auditoría'
                END) AS actividad
                "),
                'auditoria_iniciadas.responsable',
                'auditoria_iniciadas.auditoria_id',
                'usuarios.nombre_com as auditor',
                'observacion_general.observacion',
                'punto_evaluacion.nombre as centro_costo',
                'auditoria_iniciadas.signature_path as firma',
                'area_punto_evaluacion.nombre as nombreArea'
            )
            ->leftJoin('area_punto_evaluacion', 'area_punto_evaluacion.id', 'auditoria_iniciadas.area_id')
            ->join('usuarios', 'usuarios.id', 'auditoria_iniciadas.usuario_id')
            ->join('observacion_general', 'observacion_general.auditoria_iniciada_id', 'auditoria_iniciadas.id')
            ->join('punto_evaluacion', 'punto_evaluacion.id', 'auditoria_iniciadas.punto_id')
            ->where('auditoria_iniciadas.id', $id)
            ->first();

        DB::select("SET sql_mode=''");

        $data->link = CropLink(env('URL') . "/accompaniment/detalle-auditoria/" . $id);


        if ($data->auditoria_id == 65) {
            $data->resultado = "N/A";

            $data->detalle = [];

            $tipoRespuesta = DB::table('respuestas_auditoria_iniciadas')
                ->select('respuesta_id')
                ->where('auditoria_iniciada_id', $data->id)
                ->pluck('respuesta_id')
                ->first();

            if ($tipoRespuesta == 4486) {
                $data->respuestaLuminometria['manos'] = DB::table('manos_aud_iniciadas as mai')
                    ->select(
                        "mai.id AS MANOS_AUDITORIA_ID",
                        "ca.nombre AS CARGO_PERSONA",
                        "mai.nombre AS NOMBRE_RESPONSABLE",
                        "mai.auditoria_iniciada_id AS ID_AUDITORIA_INICIADA",
                        DB::raw("IF(mai.descripcion IS NULL,'',mai.descripcion) AS DESCRIPCION"),
                        DB::raw("IF(mai.desde IS NULL,'',mai.desde) AS ANTES"),
                        DB::raw("IF(ar.nombre IS NULL,'',ar.nombre) AS AREA"),
                        DB::raw("IF(ar.descripcion IS NULL,'',ar.descripcion) AS DESCRIPCION_AREA"),
                        DB::raw("IF(mai.hasta IS NULL,'',mai.hasta) AS DESPUES"),
                        DB::raw("IF(mai.item_producto IS NULL,'Sin item', mai.item_producto) AS ITEM_PRODUCTO_MANOS"),
                        DB::raw("IF(mai.nombre_producto IS NULL,'Sin producto', mai.nombre_producto) AS PRODUCTO_MANOS"),
                        DB::raw("IF(mai.cantidad_concentracion IS NULL,'Sin cantidad', mai.cantidad_concentracion) AS CANTIDAD_CONCENTRACION_MANOS"),
                        DB::raw("IF(mai.id_unidad_medida_concentracion IS NULL,'Sin unidad medida', (SELECT nombre FROM lu_unidad_medida WHERE id = mai.id_unidad_medida_concentracion)) AS UNIDAD_MEDIDA_CONCENTRACION_MANOS"),
                        DB::raw("IF(mai.cantidad_cant IS NULL,'Sin cantidad', mai.cantidad_cant) AS CANT_CANTIDAD_MANOS"),
                        DB::raw("IF(mai.id_unidad_medida_cantidad IS NULL,'Sin unidad medida', (SELECT nombre FROM lu_unidad_medida WHERE id = mai.id_unidad_medida_cantidad)) AS UNIDAD_MEDIDA_CANT_MANOS")
                    )
                    ->join('cargos as ca', 'ca.id', 'mai.cargo_id')
                    ->leftJoin('areas as ar', 'ar.id', 'mai.area_id')
                    ->where('mai.auditoria_iniciada_id', $data->id)
                    ->get();
                $data->respuestaLuminometria['superficies'] = DB::table('superficies_aud_iniciadas as sai')
                    ->select(
                        "sai.id AS SUPERFICIE_AUDITORIA_ID",
                        "su.nombre AS SUPERFICIE",
                        "sai.auditoria_iniciada_id AS ID_AUDITORIA_INICIADA",
                        DB::raw("IF(sai.descripcion IS NULL,'',sai.descripcion) AS DESCRIPCION"),
                        DB::raw("IF(sai.desde IS NULL,'',sai.desde) AS ANTES"),
                        DB::raw("IF(ar.nombre IS NULL,'',ar.nombre) AS AREA"),
                        DB::raw("IF(ar.descripcion IS NULL,'',ar.descripcion) AS DESCRIPCION_AREA"),
                        DB::raw("IF(sai.hasta IS NULL,'',sai.hasta) AS DESPUES"),
                        DB::raw("IF(sai.item_producto IS NULL,'Sin item', sai.item_producto) AS ITEM_PRODUCTO_SUPERFICIES"),
                        DB::raw("IF(sai.nombre_producto IS NULL,'Sin producto', sai.nombre_producto) AS PRODUCTO_SUPERFICIES"),
                        DB::raw("IF(sai.cantidad_concentracion IS NULL,'Sin cantidad', sai.cantidad_concentracion) AS CANTIDAD_CONCENTRACION_SUPERFICIES"),
                        DB::raw("IF(sai.id_unidad_medida_concentracion IS NULL,'Sin unidad medida', (SELECT nombre FROM lu_unidad_medida WHERE id = sai.id_unidad_medida_concentracion)) AS UNIDAD_MEDIDA_CONCENTRACION_SUPERFICIES"),
                        DB::raw("IF(sai.cantidad_cant IS NULL,'Sin cantidad', sai.cantidad_cant) AS CANT_CANTIDAD_SUPERFICIES"),
                        DB::raw("IF(sai.id_unidad_medida_cantidad IS NULL,'Sin unidad medida', (SELECT nombre FROM lu_unidad_medida WHERE id = sai.id_unidad_medida_cantidad)) AS UNIDAD_MEDIDA_CANT_SUPERFICIES")
                    )
                    ->join('superficies as su', 'su.id', 'sai.superficie_id')
                    ->leftJoin('areas as ar', 'ar.id', 'sai.area_id')
                    ->where('sai.auditoria_iniciada_id', $data->id)
                    ->get();

                foreach ($data->respuestaLuminometria['manos'] as $key => $value) {
                    $imagenes =  DB::table('fotos_luminometria')->where([
                        ['auditoria_iniciada_id', $id],
                        ['manos_aud_ini_id', $value->MANOS_AUDITORIA_ID]
                    ])->get()->pluck('imagen');

                    $value->imagenes = [];

                    foreach ($imagenes as $key => $v) {
                        array_push($value->imagenes, explode("/", $v)[3]);
                    }
                }

                foreach ($data->respuestaLuminometria['superficies'] as $key => $value) {
                    $imagenes = DB::table('fotos_luminometria')->where([
                        ['auditoria_iniciada_id', $id],
                        ['superficies_aud_ini_id', $value->SUPERFICIE_AUDITORIA_ID]
                    ])->get()->pluck('imagen');

                    $value->imagenes = [];

                    foreach ($imagenes as $key => $v) {
                        array_push($value->imagenes, explode("/", $v)[3]);
                    }
                }

                $data->respuestaActividadComun = [];
            } else if ($tipoRespuesta == 4488) {
                $data->respuestaActividadComun = [];
                $data->respuestaLuminometria = [];

                $data->respuestaCapacitacion = DB::table('akl_capacitaciones_iniciadas')
                    ->select(
                        'akl_capacitaciones_iniciadas.id',
                        'akl_capacitaciones.nombre as capacitacion',
                        'akl_capacitaciones_iniciadas.observacion as observacion'
                    )
                    ->join('akl_capacitaciones', 'akl_capacitaciones.id', 'akl_capacitaciones_iniciadas.id_akl_capacitacion')
                    ->where('akl_capacitaciones_iniciadas.id_auditoria_iniciada', $id)
                    ->first();

                $data->respuestaCapacitacion->imagenes =  DB::table('akl_fotos_capacitaciones')->where([
                    ['id_akl_cap_iniciadas', $data->respuestaCapacitacion->id]
                ])->get()->pluck('imagen');

                $data->respuestaCapacitacion->asistentes = DB::table('akl_asistentes_capacitaciones')
                    ->select(
                        'akl_asistentes.nombre',
                        'akl_asistentes.numero_documento'
                    )
                    ->join('akl_asistentes', 'akl_asistentes.id', 'akl_asistentes_capacitaciones.id_akl_asistente')
                    ->join('akl_capacitaciones_iniciadas', 'akl_capacitaciones_iniciadas.id', 'akl_asistentes_capacitaciones.id_akl_capacitacion')
                    ->where('akl_capacitaciones_iniciadas.id_auditoria_iniciada', $id)
                    ->get();
            } else {
                $data->respuestaLuminometria = [];
                $data->respuestaActividadComun = DB::table('akl_actividad')
                    ->select(
                        'akl_actividad.id',
                        'akl_actividad.descripcion_general',
                    )
                    ->where('akl_actividad.id_auditoria_iniciada', $data->id)
                    ->get();

                foreach ($data->respuestaActividadComun as $key => $value) {
                    $value->detalle = DB::table('akl_actividad_detalle')->select(
                        'akl_actividad_detalle.imagen',
                        'akl_actividad_detalle.comentario'
                    )->where('akl_actividad_detalle.id_actividad', $value->id)
                        ->get();
                }
            }
        } else {
            $data->resultado =  DB::select("
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
                rai.auditoria_iniciada_id = " . $id . "
                group by ca.id
            ORDER BY
                pr.orden asc) as R
            ")[0]->RESULTADO . "%";


            $data->detalle = DB::table('respuestas_auditoria_iniciadas')
                ->select(
                    'respuestas_auditoria_iniciadas.pregunta_id',
                    'preguntas.descripcion as pregunta',
                    'preguntas.catagoria_id as categoria_id',
                    'categorias.nombre as categoria',
                    'respuesta_detalle.descripcion as respuesta',
                    'respuestas_auditoria_iniciadas.obs_respuesta as observacion'
                )
                ->join('respuesta_detalle', 'respuesta_detalle.id', 'respuestas_auditoria_iniciadas.respuesta_id')
                ->join('preguntas', 'preguntas.id', 'respuestas_auditoria_iniciadas.pregunta_id')
                ->join('categorias', 'preguntas.catagoria_id', 'categorias.id')
                ->where([
                    ['respuestas_auditoria_iniciadas.auditoria_iniciada_id', $id]
                ])->get();
            foreach ($data->detalle as $key => $value) {
                $value->imagenes = DB::table('imagenes_auditoria')
                    ->select(
                        'imagenes_auditoria.imagen as imagen',
                    )
                    ->where([
                        ['imagenes_auditoria.auditoria_iniciada_id', $id],
                        ['imagenes_auditoria.pregunta_id', $value->pregunta_id]
                    ])
                    ->get()
                    ->pluck('imagen');
            }

            $data->detalle = $data->detalle->groupBy('categoria_id');
        }
        $data->resultadosPorCategoria = DB::select("
        SELECT
            ca.nombre AS CATEGORIA,
            FORMAT(AVG(rd.puntaje),2) AS RESULTADO
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
            rai.auditoria_iniciada_id = " . $id . "
        GROUP BY
            ca.id
        ORDER BY
            pr.orden asc
        ");

        $datos = $data;

        //dd($datos);

        //$pdf = Pdf::loadView('accompaniment::pdf.detalleAuditoria', compact('datos'));
        //return $pdf->setPaper('A4', 'landscape')->stream('reporte.pdf');

        if ($tipo == 1) {
            $pdf = Pdf::loadView('accompaniment::pdf.detalleAuditoria', compact('datos'));
            return $pdf->setPaper('A4', 'landscape')->stream('reporte.pdf');
        } else {
            return Excel::download(new AccompanimentExport($datos), 'Detalle Auditoria.xlsx');
        }
    }

    public function descargarCertificacion($id)
    {
        $certificado = DB::table('akl_asistentes_capacitaciones')
            ->select(
                'auditoria_iniciadas.id',
                'akl_asistentes.nombre',
                'akl_asistentes.numero_documento as documento',
                'akl_capacitaciones.nombre as nom_capacitacion',
                DB::raw('NULL as nom_modulo'),
                DB::raw('TRUNCATE(IF(auditoria_iniciadas.tiempo IS NULL OR auditoria_iniciadas.tiempo = 0,1,(auditoria_iniciadas.tiempo/60)), 2) as tiempo'),
                'auditoria_iniciadas.fecha_fin_auditoria as fecha_terminada',
            )
            ->join('akl_asistentes', 'akl_asistentes.id', 'akl_asistentes_capacitaciones.id_akl_asistente')
            ->join('akl_capacitaciones_iniciadas', 'akl_capacitaciones_iniciadas.id', 'akl_asistentes_capacitaciones.id_akl_capacitacion')
            ->join('akl_capacitaciones', 'akl_capacitaciones.id', 'akl_capacitaciones_iniciadas.id_akl_capacitacion')
            ->join('auditoria_iniciadas', 'auditoria_iniciadas.id', 'akl_capacitaciones_iniciadas.id_auditoria_iniciada')
            ->where('akl_asistentes_capacitaciones.id', $id)
            ->first();

        return Pdf::loadView('trainings::pdf.diploma', ['data' => $certificado])->setPaper('A4', 'landscape')->download($certificado->nombre . '.pdf');
    }
}
