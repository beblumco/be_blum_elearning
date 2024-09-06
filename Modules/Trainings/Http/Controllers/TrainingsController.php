<?php

namespace Modules\Trainings\Http\Controllers;

use Illuminate\Http\Request;
// use Illuminate\Routing\Controller;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rules\Exists;
use Modules\Accompaniment\Entities\SavkVisita;
use Modules\Administration\Entities\SavkLideresZonas;
use Modules\Administration\Entities\Unidad;
use Modules\Trainings\Entities\CaAsignacionCentroOperacion;
use Modules\Trainings\Entities\CaAsignacionSector;
use Modules\Trainings\Entities\CaPreguntasUsuarios;
use App\Models\CentroOperacion;
use Modules\Trainings\Entities\CaModulos;
use Modules\Trainings\Entities\CaContenido;
use Modules\Trainings\Entities\CaPreguntas;
use Modules\Trainings\Entities\CaEvaluacionIniciada;
use Modules\Trainings\Entities\CaEvaluacionIniciadaDetalle;
use Modules\Trainings\Entities\CaCapacitacionesInscritos;
use Modules\Trainings\Entities\CaCapacitacionesAsistidasAsistentes;
use Modules\Trainings\Entities\CaLinks;
use Modules\Trainings\Entities\CaCapacitacionesIniciadas;
use Modules\Trainings\Entities\CaCapAsistidas;
use Carbon\Carbon;
use Modules\Trainings\Entities\CaAsistentesLinks;
use Modules\Trainings\Entities\CaAsistentes;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Modules\Trainings\Entities\CaCapacitaciones;
use Modules\Trainings\Entities\CaCapAsistidasimg;
use App\Models\PuntoEvaluacion;
use Modules\Trainings\Entities\CaRespuestas;
use ZipArchive;
use App\Models\User;
use App\Models\SavkPermisosUsuarios;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Mail;
use App\Mail\DiplomaMail;
use Modules\Administration\Entities\SavkLideresGrupoEmpresa;
use Modules\Administration\Entities\SavkLideresEmpresa;
use Modules\Administration\Entities\SavkLideresCentroDeCostos;
use App\Mail\PreguntaEstudiante;

use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TrainingsController extends Controller
{
    protected $main_account_id;
    public function __construct()
    {
        $exceptRoutes = [
            'GetTrainingShare', 'RegisterAsistAsistidaView', 'saveAsisAsistida',
            'saveAsisAsistidaPublica', 'getDataTrainingPublic', 'GetDataTest', 'FinishTest',
            'downloadCertificatePublic', 'loginAsistente', 'sendCertificado', 'saveSignature',
            'GenerarQr', 'getCentroCosto'
        ];

        $this->middleware('auth')->except($exceptRoutes);

        $this->middleware(function ($request, $next) {
            if (\Auth::check()) {
                $this->main_account_id = \Auth::user()->main_account_id;
            }
            return $next($request);
        })->except($exceptRoutes);
    }

    public function TrainingsIndex()
    {
        $page_title = 'E-Learning';
        $action = __FUNCTION__;
        $permisos = $this->GetAllPermisos();

        if (Auth::user()->main_account_id == 2) {
            return redirect()->route('trainings_index_menu', ['menu' => 2]);
        }

        return view('trainings::index', compact('page_title', 'action','permisos'));
    }

    public function TrainingsIndexMenu($menu)
    {
        $page_title = 'E-Learning';
        $action = __FUNCTION__;
        $permisos = $this->GetAllPermisos();
        return view('trainings::index', compact('page_title', 'action', 'menu','permisos'));
    }

    public function TrainingsIndexTraining($idTraining)
    {
        $idTraining = Crypt::decryptString($idTraining);
        $page_title = 'E-Learning';
        $action = __FUNCTION__;
        $permisos = $this->GetAllPermisos();
        return view('trainings::index', compact('page_title', 'action', 'idTraining','permisos'));
    }

    public function WebinarsIndex()
    {
        $page_title = 'Webinars';
        $action = __FUNCTION__;
        $permisos = $this->GetAllPermisos();

        return view('trainings::Admin.index_webinars_excecution', compact('page_title', 'action','permisos'));
    }

    public function RegisterAsistAsistidaView($codigo)
    {
        $training_asistida = CaCapAsistidas::select(
            'ca_capacitaciones.nombre as nom_cap',
            'ca_capacitaciones.evaluara_por',
            'ca_capacitaciones.permitir_certificacion',
            'ca_capacitaciones.aplica_certificado',
            'punto_evaluacion.main_account_id',
            'ca_cap_asistidas.id as id_cap_asis',
            'ca_cap_asistidas.id_cliente',
            \DB::raw("(SELECT p.unidad_id FROM punto_evaluacion p where p.id = ca_cap_asistidas.id_cliente) as id_unidad")
        )
            ->where('ca_cap_asistidas.id', Crypt::decryptString($codigo))
            ->join('ca_capacitaciones', 'ca_cap_asistidas.id_capacitacion', 'ca_capacitaciones.id')
            ->leftjoin('punto_evaluacion', 'ca_cap_asistidas.id_cliente', 'punto_evaluacion.id')
            ->first();

        $permisos = $this->GetAllPermisos();

        // dd($training_asistida);
        $action = __FUNCTION__;
        return view('trainings::Admin.register-asist', compact('action', 'training_asistida','permisos'));
    }

    public function saveSignature(Request $request){
        $signature = $request->signature;

        // Obtener información sobre la imagen
        $imageInfo = getimagesizefromstring(base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $signature)));

        // El formato de la imagen está en $imageInfo['mime']
        $imageFormat = explode('/', $imageInfo['mime'])[1]; // Extraer el formato del tipo MIME


        $decodedSignature = base64_decode(str_replace('data:image/'.$imageFormat.';base64,', '', $signature));
        $nombreArchivo = 'firma_' . time() . '_' . uniqid() . '.' . $imageFormat;

        $rutaAlmacenamiento = storage_path('app/public/signatures/' . $nombreArchivo);

        if (!File::exists(storage_path('app/public/signatures'))) {
            File::makeDirectory(storage_path('app/public/signatures'), 0755, true);
        }

        file_put_contents($rutaAlmacenamiento, $decodedSignature);

        $urlImagen = 'storage/signatures/' . $nombreArchivo;
        $asistencia = CaCapacitacionesAsistidasAsistentes::find($request->asistencia);
        $asistencia->signature_path = $urlImagen;
        $asistencia->save();
        return response()->json([
            'status' => 200,
            'msg' => 'Se guardo firma exitosamente.',
            'signature' => $urlImagen
        ]);
    }

    public function puntosUsuario()
    {
        $user = User::find(auth()->user()->id);
        $queryAvatar = DB::select(
            "SELECT c.img_avatar FROM usuarios u
            inner join punto_evaluacion p on p.id = u.id_punto
            inner join unidad un on un.id = p.unidad_id
            inner join centro_operacion c on c.id = un.centro_operacion_id
            where u.id = " . auth()->user()->id
        );

        $avatar = null;
        if (!empty($queryAvatar)) {
            $avatar = $queryAvatar[0]->img_avatar;
        }

        $query = "SELECT
            if(
                (select count(*) from ca_modulos m2 where m2.id_capacitacion = c.id) +
                IF((select ce.evaluara_por from ca_capacitaciones ce where ce.id = c.id) = 1, 1, 0)
                =
                (select count(*) from ca_capacitaciones_iniciadas ci2 where ci2.id_capacitacion = c.id and ci2.id_usuario = " . $user->id . ")+
                IF((select ce.evaluara_por from ca_capacitaciones ce where ce.id = c.id) = 1 and
                (select count(*) from ca_evaluacion_iniciada ei where ei.id_capacitacion = c.id and ei.id_usuario = " . $user->id . " and ei.last_approved = 1) >= 1
                , 1, 0),
                c.puntos, 0) as puntosGanados
            FROM ca_capacitaciones_iniciadas ci
            inner join ca_capacitaciones c on c.id =ci.id_capacitacion
            inner join usuarios u on u.id = c.id_usuario";

        $puntosKlaxen = DB::select(
            $query .
                " where ci.id_usuario = " . $user->id . " and u.main_account_id = 1
             group by c.id;"
        );

        $sumaKlaxen = 0;
        foreach ($puntosKlaxen as $key => $value) {
            $sumaKlaxen += $value->puntosGanados;
        }

        $puntosEmpresa = DB::select(
            $query .
                " where ci.id_usuario = " . $user->id . " and u.main_account_id <> 1
            group by c.id; "
        );

        $sumaEmpresa = 0;
        foreach ($puntosEmpresa as $key => $value) {
            $sumaEmpresa += $value->puntosGanados;
        }

        $data = [
            'puntosKlaxen' => $sumaKlaxen,
            'puntosEmpresa' => $sumaEmpresa,
            'nombre' => $user->nombre_com,
            'avatar' => $avatar
        ];

        return response()->json([
            'status' => 200,
            'data' => $data
        ]);
    }

    public function horasEntrenamiento(Request $request)
    {
        $main_account_id = $this->main_account_id;
        $user = User::find(auth()->user()->id);

        $init_date=$request->init_date == 0 ? 0 : substr($request->init_date, 0, 4) . '-' . substr($request->init_date, 4, 2) . '-' . substr($request->init_date, 6, 2);
        $end_date=$request->end_date == 0 ? 0 : substr($request->end_date, 0, 4) . '-' . substr($request->end_date, 4, 2) . '-' . substr($request->end_date, 6, 2);

        $filtroFecha = true;
        if ($init_date == 0 && $end_date == 0) {
            $filtroFecha = false;
        }

        if ($request->type == 2) {
            $main_account_id_search = 1;
        }else{
            $main_account_id_search = $main_account_id;
        }

        $query = "select sum(y.minutos) minutos from (SELECT
            if(
                (select count(*) from ca_modulos m2 where m2.id_capacitacion = c.id) +
                IF((select ce.evaluara_por from ca_capacitaciones ce where ce.id = c.id) = 1, 1, 0)
                =
                (select count(*) from ca_capacitaciones_iniciadas ci2 where ci2.id_capacitacion = c.id and ci2.id_usuario = " . $user->id . ")+
                IF((select ce.evaluara_por from ca_capacitaciones ce where ce.id = c.id) = 1 and
                    (select count(*) from ca_evaluacion_iniciada ei where ei.id_capacitacion = c.id and ei.id_usuario = " . $user->id . " and ei.last_approved = 1) >= 1
                    , 1, 0),
                c.tiempo_minutos, 0) as minutos
            FROM ca_capacitaciones_iniciadas ci
            inner join ca_capacitaciones c on c.id =ci.id_capacitacion
            inner join usuarios u on u.id = c.id_usuario";

        $trainings_sector = $query . "
            inner join ca_asignacion_sector on ca_asignacion_sector.id_capacitacion = c.id
            inner join sector on ca_asignacion_sector.id_sector = sector.id";

        $training_gp = $query . "
            inner join ca_asignacion_centro_operacion on ca_asignacion_centro_operacion.id_capacitacion = c.id
            inner join centro_operacion on ca_asignacion_centro_operacion.id_centro_operacion = centro_operacion.id";


        $operation_center = CentroOperacion::GetOperationCenterByUser();

        $trainings_sector = $trainings_sector . " where sector.id = " . $operation_center->sector_id;

        if ($filtroFecha) {
            $trainings_sector = $trainings_sector . " && ci.id_usuario = " . $user->id . " and u.main_account_id = ".$main_account_id_search." &&
                ci.fecha_inicio >= '" . $init_date . " 00:00:00' AND ci.fecha_inicio <= '" . $end_date . " 23:59:59' group by c.id) as y";
        } else {
            $trainings_sector = $trainings_sector . " && ci.id_usuario = " . $user->id . " and u.main_account_id = ".$main_account_id_search."
             group by c.id) as y";
        }

        $training_gp = $training_gp . " where centro_operacion.id = " . $operation_center->id;

        if ($filtroFecha) {
            $training_gp = $training_gp . " && ci.id_usuario = " . $user->id . " and u.main_account_id = ".$main_account_id_search." &&
                ci.fecha_inicio >= '" . $init_date . " 00:00:00' AND ci.fecha_inicio <= '" . $end_date . " 23:59:59' group by c.id) as y";
        } else {
            $training_gp = $training_gp . " && ci.id_usuario = " . $user->id . " and u.main_account_id = ".$main_account_id_search."
             group by c.id) as y";
        }

        // $training_gp = $training_gp->where([
        //     ['usuarios.main_account_id', '=', 1],
        // ]);

        $minutos = DB::select($training_gp . ' union ' . $trainings_sector);

        $sumaMinutos = 0;
        foreach ($minutos as $key => $value) {
            $sumaMinutos += $value->minutos;
        }

        $data=[
            'sumaMinutos'           => number_format($sumaMinutos / 60, 1),
            'sumaMinutosTotales'    => $this->horasEntrenamientoTotales($init_date, $end_date, $main_account_id_search),
            'perfil'                => $user->id_grupo
        ];

        return $this->ExitProgram(201, $this->MessageResponse('', 201, 'Se genero tiempo en entrenamiento.'), $data);
    }

    public function horasEntrenamientoTotales($init_date, $end_date, $main_account_id_search)
    {
        $main_account_id = $this->main_account_id;
        $user = User::find(auth()->user()->id);

        $filtroFecha = true;
        if ($init_date == 0 && $end_date == 0) {
            $filtroFecha = false;
        }

        $query = "select sum(y.minutos) minutos from (SELECT
            c.tiempo_minutos as minutos
            FROM ca_capacitaciones c
            inner join usuarios u on u.id = c.id_usuario";

        $trainings_sector = $query . "
            inner join ca_asignacion_sector on ca_asignacion_sector.id_capacitacion = c.id
            inner join sector on ca_asignacion_sector.id_sector = sector.id";

        $training_gp = $query . "
            inner join ca_asignacion_centro_operacion on ca_asignacion_centro_operacion.id_capacitacion = c.id
            inner join centro_operacion on ca_asignacion_centro_operacion.id_centro_operacion = centro_operacion.id";


        $operation_center = CentroOperacion::GetOperationCenterByUser();

        $trainings_sector = $trainings_sector . " where sector.id = " . $operation_center->sector_id;

        // if ($filtroFecha) {
        //     $trainings_sector = $trainings_sector . " and u.main_account_id = ".$main_account_id_search." &&
        //         c.created_at >= '" . $init_date . " 00:00:00' AND c.created_at <= '" . $end_date . " 23:59:59' group by c.id) as y";
        // } else {
        //     $trainings_sector = $trainings_sector . " and u.main_account_id = ".$main_account_id_search."
        //      group by c.id) as y";
        // }
        $trainings_sector = $trainings_sector . " and u.main_account_id = ".$main_account_id_search."
             group by c.id) as y";


        $training_gp = $training_gp . " where centro_operacion.id = " . $operation_center->id;

        // if ($filtroFecha) {
        //     $training_gp = $training_gp . " and u.main_account_id = ".$main_account_id_search." &&
        //         c.created_at >= '" . $init_date . " 00:00:00' AND c.created_at <= '" . $end_date . " 23:59:59' group by c.id) as y";
        // } else {
        //     $training_gp = $training_gp . " and u.main_account_id = ".$main_account_id_search."
        //      group by c.id) as y";
        // }
        $training_gp = $training_gp . " and u.main_account_id = ".$main_account_id_search."
             group by c.id) as y";

        // $training_gp = $training_gp->where([
        //     ['usuarios.main_account_id', '=', 1],
        // ]);
        $minutos = DB::select($training_gp . ' union ' . $trainings_sector);


        $sumaMinutos = 0;
        foreach ($minutos as $key => $value) {
            $sumaMinutos += $value->minutos;
        }

        $sumaMinutos = number_format($sumaMinutos / 60, 1);

        // return $this->ExitProgram(201, $this->MessageResponse('', 201, 'Se genero tiempo en entrenamiento.'), $sumaMinutos);
        return $sumaMinutos;
    }


    public function GetDataInitWebinars(Request $request)
    {
        $main_account_id = $this->main_account_id;
        $currentIdUser = auth()->user()->id;
        $url = env('URL');
        $mode_query = $request->get('mode_query');

        $operation_center = CentroOperacion::GetOperationCenterByUser();

        $trainings_sector = CaCapacitaciones::select(
            'ca_capacitaciones.*',
            'usuarios.nombre_com as nombre_usuario',
            'usuarios.cargo as cargo_usuario',
            \DB::raw('IF(ca_capacitaciones.imagen IS NULL, "' . asset('assets/images/placeholder_capacitaciones.png') . '", CONCAT("' . $url . 'storage/", ca_capacitaciones.imagen)) AS IMAGE_PATH'),
            \DB::raw("(SELECT COUNT(*) FROM ca_evaluacion_iniciada ceis WHERE ceis.id_capacitacion = ca_capacitaciones.id AND ceis.id_usuario = $currentIdUser) AS QUANTITY_MODULE_DONE"),
            \DB::raw("(SELECT COUNT(*) FROM ca_modulos cms WHERE cms.id_capacitacion = ca_capacitaciones.id) AS QUANTITY_TOTAL_TRAININGS"),

            \DB::raw("TRUNCATE(((SELECT COUNT(*) +
                            IF((select ce.evaluara_por from ca_capacitaciones ce where ce.id = ca_capacitaciones.id) = 1 and
                            (select count(*) from ca_evaluacion_iniciada ei where ei.id_capacitacion = ca_capacitaciones.id and ei.id_usuario = $currentIdUser and ei.last_approved = 1) >= 1
                            , 1, 0)
                    FROM ca_capacitaciones_iniciadas cai WHERE cai.id_capacitacion = ca_capacitaciones.id AND cai.id_usuario = $currentIdUser) /
                    (SELECT COUNT(*) +
                        IF((select ce.evaluara_por from ca_capacitaciones ce where ce.id = ca_capacitaciones.id) = 1, 1, 0)
                    FROM ca_modulos cms WHERE cms.id_capacitacion = ca_capacitaciones.id))*100,0) AS TRAINING_PERCENT"),

            \DB::raw("
            (CASE
                WHEN TRUNCATE(((SELECT COUNT(*) FROM ca_evaluacion_iniciada ceis WHERE ceis.id_capacitacion = ca_capacitaciones.id AND ceis.id_usuario = $currentIdUser) / (SELECT COUNT(*) FROM ca_modulos cms WHERE cms.id_capacitacion = ca_capacitaciones.id)),0) > 99 THEN 'text-success'
                WHEN TRUNCATE(((SELECT COUNT(*) FROM ca_evaluacion_iniciada ceis WHERE ceis.id_capacitacion = ca_capacitaciones.id AND ceis.id_usuario = $currentIdUser) / (SELECT COUNT(*) FROM ca_modulos cms WHERE cms.id_capacitacion = ca_capacitaciones.id)),0) = 0 THEN 'text-danger'
                ELSE 'text-warning'
            END ) AS COLOR_PERCENT")
        )
            ->Join('usuarios', 'ca_capacitaciones.id_usuario', '=', 'usuarios.id')
            ->Join('ca_asignacion_sector', 'ca_asignacion_sector.id_capacitacion', '=', 'ca_capacitaciones.id')
            ->Join('sector', 'ca_asignacion_sector.id_sector', '=', 'sector.id')
            ->where([
                ['usuarios.main_account_id', '=', '1'],
                ['ca_capacitaciones.tipo_capacitacion', '=', '3'],
                ['sector.id', '=', $operation_center->sector_id]
            ]);

        switch ($mode_query) {
            case '1': //Agendar
                $trainings_sector = $trainings_sector->where('fecha_realizacion', '>=', now())->get();
                break;
            case '3': //mis webinars
                $trainings_sector = $trainings_sector->where('estado_proceso', '=', 2)->get();
                break;
            default:
                $trainings_sector = [];
                break;
        }


        return $this->ExitProgram(202, $this->MessageResponse('Data', 202), $trainings_sector);
    }

    public function saveAsistentes(Request $request)
    {
        $id_capacitacion = $request->get('id_training');
        $idUser = auth()->user()->id;

        $asistentes = new CaCapacitacionesInscritos();

        $validacion = $asistentes->select(
            'ca_capacitaciones_inscritos.*'
        )
            ->where([
                ['id_capacitacion', '=', $id_capacitacion],
                ['id_usuario', '=', $idUser],
            ])
            ->count();

        if ($validacion == 0) {
            $asistentes->fill(['id_capacitacion' => $id_capacitacion, 'id_usuario' => $idUser]);
            $asistentes->save();

            if ($asistentes) {
                return $this->ExitProgram(200, $this->MessageResponse('Data', 200, 'Se registro usuario con éxito'));
            } else {
                return $this->ExitProgram(204, $this->MessageResponse('Data', 204, 'No fue posible realizar el registro'));
            }
        } else {
            return $this->ExitProgram(203, $this->MessageResponse('Data', 203, 'Ya estas registrado'));
        }
    }

    public function TrainingsAssistedByExpertIndex()
    {
        $page_title = 'Asistida por experto';
        $action = __FUNCTION__;
        $permisos = $this->GetAllPermisos();

        return view('trainings::Admin.index_training_assist', compact('page_title', 'action','permisos'));
    }

    public function GetDataInit(Request $request)
    {
        $main_account_id = $this->main_account_id;
        $currentIdUser = auth()->user()->id;
        $mode_query = $request->get('mode_query');
        $url = env('URL');

        // GRUPO EMPRESA
        $training_gp = CaCapacitaciones::select(
            'ca_capacitaciones.*',
            \DB::raw('IF(ca_capacitaciones.imagen IS NULL, "' . asset('assets/images/placeholder_capacitaciones.png') . '", CONCAT("' . $url . 'storage/", ca_capacitaciones.imagen)) AS IMAGE_PATH'),
            \DB::raw("(SELECT COUNT(*) FROM ca_evaluacion_iniciada ceis WHERE ceis.id_capacitacion = ca_capacitaciones.id AND ceis.id_usuario = $currentIdUser) AS QUANTITY_MODULE_DONE"),
            \DB::raw("(SELECT COUNT(*) FROM ca_modulos cms WHERE cms.id_capacitacion = ca_capacitaciones.id) AS QUANTITY_TOTAL_TRAININGS"),

            \DB::raw("TRUNCATE(((SELECT COUNT(*) +
                            IF((select ce.evaluara_por from ca_capacitaciones ce where ce.id = ca_capacitaciones.id) = 1 and
                            (select count(*) from ca_evaluacion_iniciada ei where ei.id_capacitacion = ca_capacitaciones.id and ei.id_usuario = $currentIdUser and ei.last_approved = 1) >= 1
                            , 1, 0)
                    FROM ca_capacitaciones_iniciadas cai WHERE cai.id_capacitacion = ca_capacitaciones.id AND cai.id_usuario = $currentIdUser) /
                    (SELECT COUNT(*) +
                        IF((select ce.evaluara_por from ca_capacitaciones ce where ce.id = ca_capacitaciones.id) = 1, 1, 0)
                    FROM ca_modulos cms WHERE cms.id_capacitacion = ca_capacitaciones.id))*100,0) AS TRAINING_PERCENT"),

            \DB::raw("
            (CASE
                WHEN TRUNCATE(((SELECT COUNT(*) FROM ca_evaluacion_iniciada ceis WHERE ceis.id_capacitacion = ca_capacitaciones.id AND ceis.id_usuario = $currentIdUser) / (SELECT COUNT(*) FROM ca_modulos cms WHERE cms.id_capacitacion = ca_capacitaciones.id)),0) > 99 THEN 'text-success'
                WHEN TRUNCATE(((SELECT COUNT(*) FROM ca_evaluacion_iniciada ceis WHERE ceis.id_capacitacion = ca_capacitaciones.id AND ceis.id_usuario = $currentIdUser) / (SELECT COUNT(*) FROM ca_modulos cms WHERE cms.id_capacitacion = ca_capacitaciones.id)),0) = 0 THEN 'text-danger'
                ELSE 'text-warning'
            END ) AS COLOR_PERCENT")
        )
            ->Join('usuarios', 'ca_capacitaciones.id_usuario', '=', 'usuarios.id')
            ->Join('ca_asignacion_centro_operacion', 'ca_asignacion_centro_operacion.id_capacitacion', '=', 'ca_capacitaciones.id')
            ->Join('centro_operacion', 'ca_asignacion_centro_operacion.id_centro_operacion', '=', 'centro_operacion.id');

        //SECTORES
        $trainings_sector = CaCapacitaciones::select(
            'ca_capacitaciones.*',
            \DB::raw('IF(ca_capacitaciones.imagen IS NULL, "' . asset('assets/images/placeholder_capacitaciones.png') . '", CONCAT("' . $url . 'storage/", ca_capacitaciones.imagen)) AS IMAGE_PATH'),
            \DB::raw("(SELECT COUNT(*) FROM ca_evaluacion_iniciada ceis WHERE ceis.id_capacitacion = ca_capacitaciones.id AND ceis.id_usuario = $currentIdUser) AS QUANTITY_MODULE_DONE"),
            \DB::raw("(SELECT COUNT(*) FROM ca_modulos cms WHERE cms.id_capacitacion = ca_capacitaciones.id) AS QUANTITY_TOTAL_TRAININGS"),

            \DB::raw("TRUNCATE(((SELECT COUNT(*) +
                            IF((select ce.evaluara_por from ca_capacitaciones ce where ce.id = ca_capacitaciones.id) = 1 and
                            (select count(*) from ca_evaluacion_iniciada ei where ei.id_capacitacion = ca_capacitaciones.id and ei.id_usuario = $currentIdUser and ei.last_approved = 1) >= 1
                            , 1, 0)
                    FROM ca_capacitaciones_iniciadas cai WHERE cai.id_capacitacion = ca_capacitaciones.id AND cai.id_usuario = $currentIdUser) /
                    (SELECT COUNT(*) +
                        IF((select ce.evaluara_por from ca_capacitaciones ce where ce.id = ca_capacitaciones.id) = 1, 1, 0)
                    FROM ca_modulos cms WHERE cms.id_capacitacion = ca_capacitaciones.id))*100,0) AS TRAINING_PERCENT"),

            \DB::raw("
            (CASE
                WHEN TRUNCATE(((SELECT COUNT(*) FROM ca_evaluacion_iniciada ceis WHERE ceis.id_capacitacion = ca_capacitaciones.id AND ceis.id_usuario = $currentIdUser) / (SELECT COUNT(*) FROM ca_modulos cms WHERE cms.id_capacitacion = ca_capacitaciones.id)),0) > 99 THEN 'text-success'
                WHEN TRUNCATE(((SELECT COUNT(*) FROM ca_evaluacion_iniciada ceis WHERE ceis.id_capacitacion = ca_capacitaciones.id AND ceis.id_usuario = $currentIdUser) / (SELECT COUNT(*) FROM ca_modulos cms WHERE cms.id_capacitacion = ca_capacitaciones.id)),0) = 0 THEN 'text-danger'
                ELSE 'text-warning'
            END ) AS COLOR_PERCENT")
        )
            ->Join('usuarios', 'ca_capacitaciones.id_usuario', '=', 'usuarios.id')
            ->Join('ca_asignacion_sector', 'ca_asignacion_sector.id_capacitacion', '=', 'ca_capacitaciones.id')
            ->Join('sector', 'ca_asignacion_sector.id_sector', '=', 'sector.id');

        switch ($mode_query) {
            case 1: //MI PLAN
                $operation_center = CentroOperacion::GetOperationCenterByUser();

                $trainings_sector = $trainings_sector->orWhere(['sector.id' => $operation_center->sector_id]);

                $trainings_sector = $trainings_sector->where([
                    ['usuarios.main_account_id', '=', 1],
                    ['ca_capacitaciones.tipo_capacitacion', '!=', '3']
                ]);

                $training_gp = $training_gp->orWhere(['centro_operacion.id' => $operation_center->id]);

                $training_gp = $training_gp->where([
                    ['usuarios.main_account_id', '=', 1],
                    ['ca_capacitaciones.tipo_capacitacion', '!=', '3']
                ]);

                $trainings_sector->union($training_gp);
                $trainings_sector = $trainings_sector->get();
                break;

            case 2: //MIS CAPACITACIONES
                $operation_center = CentroOperacion::GetOperationCenterByUser();

                $trainings_sector = $trainings_sector->orWhere(['sector.id' => $operation_center->sector_id]);

                $trainings_sector = $trainings_sector->where([
                    ['usuarios.main_account_id', '=', $this->main_account_id],
                    ['ca_capacitaciones.tipo_capacitacion', '!=', '3']
                ]);

                $training_gp = $training_gp->where([
                    ['centro_operacion.id', $operation_center->id],
                    ['usuarios.main_account_id', '=', $main_account_id],
                    ['ca_capacitaciones.tipo_capacitacion', '!=', '3']
                ])
                ->orWhere('ca_capacitaciones.id_usuario', auth()->user()->id)
                ->groupBy('ca_capacitaciones.id');

                $trainings_sector->union($training_gp);
                $trainings_sector = $trainings_sector->get();
                break;

            default:
                // $trainings = $trainings->where([
                //     ['centro_operacion.main_account_id', '=', $main_account_id]
                // ]);
                $trainings_sector = [];
                break;
        }

        foreach ($trainings_sector as $key_training => $value_training) {
            $value_training->id_training_encrypt = Crypt::encryptString($value_training->id);
        }

        return $this->ExitProgram(202, $this->MessageResponse('Data', 202), $trainings_sector);
    }

    public function GetDataModulesById(Request $request)
    {
        $id_training = $request->get('id_training');

        $modules = CaModulos::GetAllModulesExcecutable($id_training);

        return $this->ExitProgram(202, $this->MessageResponse('Data', 202), $modules);
    }

    public function GetResourcesByModule(Request $request)
    {
        $id_module = $request->get('id_module');
        $instance_content = new CaContenido();
        $contenido = $instance_content->GetResourcesByModule($id_module);

        return $this->ExitProgram(202, $this->MessageResponse('Data', 202), $contenido);
    }

    public function GetDataContentModules(Request $request)
    {
        $id_module = $request->get('id_module');
        $instance_content = new CaContenido();

        $content = $instance_content->GetAllContentByIdModule($id_module);

        return $this->ExitProgram(202, $this->MessageResponse('Data', 202), $content);
    }

    public function GetDataTest(Request $request)
    {
        $idModule = $request->get('id_module');
        $idCapacitacion = $request->get('id_capacitacion');
        $publica = $request->has('publica') ? $request->get('publica') : false;
        $idAsistente = $request->has('id_asistente') ? $request->get('id_asistente') : null;

        if($publica){
            $idCapacitacion = $idCapacitacion != 'null' ?  Crypt::decryptString($idCapacitacion): $idCapacitacion;
        }

        $instance_module = new CaModulos();
        $instance_question = new CaPreguntas();

        if ($idCapacitacion != 'null') {
            $idModule = NULL;
            $questions = CaPreguntas::select(
                'ca_preguntas.*'
            )
                ->where([
                    ['ca_preguntas.id_capacitacion', '=', $idCapacitacion],
                    ['ca_preguntas.estado', '=', 1]
                ])
                ->inRandomOrder()
                ->get();
        } else {
            $idCapacitacion = null;
            $info_module = $instance_module->GetModuleById($idModule);
            $idCapacitacion = $info_module->id_capacitacion;
            $questions = CaPreguntas::select(
                'ca_preguntas.*'
            )
                ->where([
                    ['ca_preguntas.id_modulo', '=', $idModule],
                    ['ca_preguntas.estado', '=', 1]
                ])
                ->inRandomOrder()
                ->get();
        }

        if (COUNT($questions) == 0) {
            return $this->ExitProgram(400, $this->MessageResponse('Data', 400));
        }

        foreach ($questions as $key => $question) {
            $questions[$key]['answers'] = $instance_question->FGetAnswersByQuestionId($question->id);
        }


        //INICIAR EXAMEN
        $caCapacitacion = CaCapacitaciones::where('id',$idCapacitacion)->first();


        $idsCaTestInit = [];
        if ($caCapacitacion->permitir_certificacion == 2 && $caCapacitacion->evaluara_por == 1) {
            //CERTIFICA X MODULO Y EVALÚA X CAP
            $modulos = CaModulos::where('id_capacitacion', $caCapacitacion->id)->get();

            foreach ($modulos as $key => $modulo) {
                $caTestInit = new CaEvaluacionIniciada();

                if ($publica) {
                    $array_test_init = [
                        'id_capacitacion' => $caCapacitacion->id,
                        'id_modulo' => $modulo->id,
                        'id_asistente' => $idAsistente,
                    ];
                }else{
                    $array_test_init = [
                        'id_capacitacion' => $caCapacitacion->id,
                        'id_modulo' => $modulo->id,
                        'id_usuario' => auth()->user()->id,
                    ];
                }
                $caTestInit->fill($array_test_init);
                $caTestInit->save();
                $idsCaTestInit[] = $caTestInit->id;
            }
        }else{
            $caTestInit = new CaEvaluacionIniciada();
            if ($publica) {
                $array_test_init = [
                    'id_capacitacion' => $idCapacitacion,
                    'id_modulo' => $idModule,
                    'id_asistente' => $idAsistente,
                ];
            }else{
                $array_test_init = [
                    'id_capacitacion' => $idCapacitacion,
                    'id_modulo' => $idModule,
                    'id_usuario' => auth()->user()->id,
                ];
            }
            $caTestInit->fill($array_test_init);
            $caTestInit->save();
            $idsCaTestInit[] = $caTestInit->id;
        }



        return $this->ExitProgram(
            202,
            $this->MessageResponse('Data', 202),
            [
                'questions' => $questions,
                'id_test_init' => $idsCaTestInit
                // 'id_test_init' => $caTestInit->id
            ]
        );

    }

    public function GetDataTestView(Request $request)
    {
        $idModule = $request->get('id_module');
        $idCapacitacion = $request->get('id_capacitacion');
        $idEvaluacion = $request->get('id_evaluacion');

        $instance_module = new CaModulos();
        $instance_question = new CaPreguntas();

        if ($idCapacitacion != 'null') {
            $idModule = NULL;
            $questions = CaPreguntas::select(
                'ca_preguntas.*'
            )
                ->where([
                    ['ca_preguntas.id_capacitacion', '=', $idCapacitacion],
                    ['ca_preguntas.estado', '=', 1]
                ])
                ->inRandomOrder()
                ->get();
        } else {
            $idCapacitacion = null;
            $info_module = $instance_module->GetModuleById($idModule);
            $idCapacitacion = $info_module->id_capacitacion;
            $questions = CaPreguntas::select(
                'ca_preguntas.*'
            )
                ->where([
                    ['ca_preguntas.id_modulo', '=', $idModule],
                    ['ca_preguntas.estado', '=', 1]
                ])
                ->inRandomOrder()
                ->get();
        }

        if (COUNT($questions) == 0) {
            return $this->ExitProgram(400, $this->MessageResponse('Data', 400));
        }

        foreach ($questions as $key => $question) {
            $questions[$key]['answers'] = $instance_question->FGetAnswersByQuestionIdView($question->id, $idEvaluacion);
            $question->id_evaluacion = $idEvaluacion;
        }

        return $this->ExitProgram(
            202,
            $this->MessageResponse('Data', 202),
            [
                'questions' => $questions,
            ]
        );

    }

    public function FinishTest(Request $request)
    {
        $id_test_init = json_decode($request->get('id_test_init'));
        $publica = $request->get('publica');
        $data_responses = json_decode($request->get('data_responses'));

        $instance_test_init = new CaEvaluacionIniciada();
        $info_test_init = $instance_test_init->GetDataCapTestById($id_test_init);

        $approvedTest = true;

        foreach ($info_test_init as $key => $test_init) {
            foreach ($data_responses as $key_response => $value_response) {
                $array_test_init_det = [
                    'id_evaluacion_iniciada' => $test_init->id,
                    'id_pregunta' => $value_response->id
                ];

                foreach ($value_response->answers as $key_answer => $value_answer) {
                    if ($value_answer->checked)
                        $array_test_init_det['id_respuesta'] = $value_answer->id;
                }


                $caTestInitDet = new CaEvaluacionIniciadaDetalle();
                $caTestInitDet->fill($array_test_init_det);
                $caTestInitDet->save();
            }


            $approved = $instance_test_init->CalCulateApprovationByIdTestInit($test_init->id);

            $now = Carbon::now()->format('Y-m-d H:i:s');
            $array_update = [
                'fecha_terminada' => $now,
                'resultado' => $approved->RESULTADO
            ];

            if ($approved->APRUEBA == 1) //APROBÓ
            {
                //ACTUALIZACIÓN TODAS LAS EVALUACIONES EN CERTIFICADO 0
                if($publica == 'true'){
                    CaEvaluacionIniciada::where([
                        ['id_capacitacion', '=', $test_init->id_capacitacion],
                        ['id_modulo', '=', $test_init->id_modulo],
                        ['id_asistente', '=', $test_init->id_asistente],
                    ])
                        ->update(['certificado' => 0, 'last_approved' => 0]);
                }else{
                    CaEvaluacionIniciada::where([
                        ['id_capacitacion', '=', $test_init->id_capacitacion],
                        ['id_modulo', '=', $test_init->id_modulo],
                        ['id_usuario', '=', $test_init->id_usuario]
                    ])
                        ->update(['certificado' => 0, 'last_approved' => 0]);
                }

                $array_update['last_approved'] = 1;
                //ACTUALIZACIÓN DE LA EVALUACIÓN ACTUAL COMO ÚNICA CERTIFICADA
                CaEvaluacionIniciada::where('id', '=', $test_init->id)->update($array_update);

                //VALIDAR SI SE CERTIFICA O NO
                $capacitacion = CaCapacitaciones::where('id', $test_init->id_capacitacion)->first();
                if (($capacitacion->aplica_certificado == 1 && $capacitacion->permitir_certificacion == 2) ||($capacitacion->aplica_certificado == 1 && $capacitacion->evaluara_por == 1)) {
                    CaEvaluacionIniciada::where('id', '=', $test_init->id)->update(['certificado' => 1]);
                }

                //CERTIFICA GENERAL - EVALÚA POR MÓDULOS
                if ($capacitacion->aplica_certificado == 1 && $capacitacion->permitir_certificacion == 1 && $capacitacion->evaluara_por == 2) {
                    $countModulos = CaModulos::
                    where([
                        ['ca_modulos.id_capacitacion', $capacitacion->id]
                    ])
                    ->where(function ($query) {
                        $query->whereExists(function ($subquery) {
                            $subquery->select(DB::raw(1))
                                ->from('ca_preguntas')
                                ->whereRaw('id_modulo = ca_modulos.id');
                        });
                    })
                    ->count();

                    //Se crea una evaluacion aprobada cuando la evaluacion de los modulos fue aprobada
                    if($publica == 'true'){
                        $countTestInit = CaEvaluacionIniciada::where([
                            ['id_capacitacion', $capacitacion->id],
                            ['id_asistente', $test_init->id_asistente],
                            ['last_approved', 1]
                        ])
                        ->count();

                        if ($countTestInit >= $countModulos) {
                            CaEvaluacionIniciada::where([
                                ['id_capacitacion', '=', $capacitacion->id],
                                ['id_asistente', '=', $test_init->id_asistente]
                            ])
                                ->whereNull('id_modulo')
                                ->update(['certificado' => 0, 'last_approved' => 0]);

                            $now = Carbon::now()->format('Y-m-d H:i:s');
                            $caTestInit = new CaEvaluacionIniciada();
                            $array_test_init = [
                                'id_capacitacion' => $capacitacion->id,
                                'id_asistente' => $test_init->id_asistente,
                                'fecha_inicio' => $now,
                                'fecha_terminada' => $now,
                                'certificado' => '1',
                                'last_approved' => '1',
                            ];
                            $caTestInit->fill($array_test_init);
                            $caTestInit->save();
                            return $this->ExitProgram(
                                206,
                                $this->MessageResponse('', 206, 'Has aprobado la evaluación, ya cuentas con el certificado')
                            );
                        }
                    }else{
                        $countTestInit = CaEvaluacionIniciada::where([
                            ['id_capacitacion', $capacitacion->id],
                            ['id_usuario', $test_init->id_usuario],
                            ['last_approved', 1]
                        ])
                        ->count();

                        if ($countTestInit >= $countModulos) {
                            CaEvaluacionIniciada::where([
                                ['id_capacitacion', '=', $capacitacion->id],
                                ['id_usuario', '=', $test_init->id_usuario]
                            ])
                                ->whereNull('id_modulo')
                                ->update(['certificado' => 0, 'last_approved' => 0]);

                            $now = Carbon::now()->format('Y-m-d H:i:s');
                            $caTestInit = new CaEvaluacionIniciada();
                            $array_test_init = [
                                'id_capacitacion' => $capacitacion->id,
                                'id_usuario' => $test_init->id_usuario,
                                'fecha_inicio' => $now,
                                'fecha_terminada' => $now,
                                'certificado' => '1',
                                'last_approved' => '1',
                            ];
                            $caTestInit->fill($array_test_init);
                            $caTestInit->save();
                            return $this->ExitProgram(
                                206,
                                $this->MessageResponse('', 206, 'Has aprobado la evaluación, ya cuentas con el certificado')
                            );
                        }
                    }
                }

            } else {
                CaEvaluacionIniciada::where('id', '=', $test_init->id)->update($array_update);
                $approvedTest = false;
            }
        }

        if ($approvedTest) {
            if ($capacitacion->aplica_certificado == 1 && $capacitacion->permitir_certificacion == 2) {
                return $this->ExitProgram(
                    206,
                    $this->MessageResponse('', 206, 'Has aprobado la evaluación, ya cuentas con el certificado')
                );
            }else{
                return $this->ExitProgram(
                    200,
                    'Has aprobado la evaluación'
                );
            }

        }else{
            return $this->ExitProgram(
                406,
                $this->MessageResponse('', 406, 'No has aprobado la evaluación, ¡intenta de nuevo!')
            );
        }
    }

    public function GenerateLinkModule(Request $request)
    {
        $id_module = $request->get('id_module');

        $array_data_link = [
            'id_modulo' => $id_module,
            'id_usuario' => auth()->user()->id
        ];

        $caLink = new CaLinks();
        $caLink->fill($array_data_link);
        if ($caLink->save()) {
            $link = env('URL') . "registrar-asistencia/" . CaLinks::GenerateLink($caLink->id);
            $cropLink = CropLink($link); //USO DE HELPER
            return $this->ExitProgram(200, $this->MessageResponse('Data', 200), $cropLink);
        }
    }

    public function ViewLinksByUser(Request $request)
    {
        $id_module = $request->get('id_module');

        $links = CaLinks::GetAllLinksByUserAndModule(auth()->user()->id, $id_module);

        return $this->ExitProgram(202, $this->MessageResponse('', 206, 'Data found'), $links);
    }

    public function ViewAssistantsByLink(Request $request)
    {
        $assistants =  CaAsistentesLinks::GetAllAssistantsByLink($request->id_link);

        return $this->ExitProgram(202, $this->MessageResponse('', 206, 'Data found'), $assistants);
    }

    // public function downloadCertificate($id, $id_link)
    // {
    //     $asistente = CaAsistentesLinks::select(
    //         'ca_asistentes.nombre',
    //         'ca_asistentes.documento',
    //         'ca_modulos.nombre as nom_modulo',
    //         'ca_capacitaciones.nombre as nom_capacitacion'
    //     )
    //         ->join('ca_asistentes', 'ca_asistentes_links.id_asistente', 'ca_asistentes.id')
    //         ->join('ca_links', 'ca_links.id', 'ca_asistentes_links.id_link')
    //         ->leftjoin('ca_capacitaciones', 'ca_links.id_capacitacion', 'ca_capacitaciones.id')
    //         ->leftjoin('ca_modulos', 'ca_links.id_modulo', 'ca_modulos.id')
    //         ->where([
    //             ['ca_asistentes_links.id_link', $id_link],
    //             ['ca_asistentes_links.id_asistente', $id]
    //         ])
    //         ->first();

    //     $pdf = Pdf::loadView('trainings::pdf.diploma', ['data' => $asistente]);
    //     return $pdf->setPaper('A4', 'landscape')->download($asistente->nombre . '.pdf');
    // }
    public function downloadTest(Request $request){
        $data = json_decode($request->data);

        if ($data[0]->id_capacitacion != null) {
            $nomEvaluacion = CaCapacitaciones::find($data[0]->id_capacitacion);
        }else{
            $nomEvaluacion = CaModulos::find($data[0]->id_modulo);
        }

        $evaluacion = CaEvaluacionIniciada::find($data[0]->id_evaluacion);
        if ($evaluacion->id_usuario != null) {
            $usuario = User::find($evaluacion->id_usuario);
        }else{
            $usuario = CaAsistentes::select('nombre as nombre_com', 'documento as codigo')
            ->where('id', $evaluacion->id_asistente)
            ->first();
        }
        //dd($usuario);

        $pdf = Pdf::loadView('trainings::pdf.evaluacion', ['data' => $data, 'evaluacion' => strtoupper($nomEvaluacion->nombre), 'usuario' => $usuario, 'calificacion' => $evaluacion->resultado]);
        return $pdf->setPaper('A4', 'portrait')->download('Evaluacion.pdf');
    }

    public function downloadCertificate($id)
    {
        $evaluacionIniciada = new CaEvaluacionIniciada();
        $certificado = $evaluacionIniciada->ByIdCertifiedId($id);

        $capacitacion = new CaCapacitaciones;
        $capacitacion = $capacitacion->mainAccountTraining($certificado->id_capacitacion);

        if ($capacitacion->main_account_id == 1) {
            if ($certificado->certificado_manual == 1 || $certificado->certificado_manual == 2) { //ASISTENCIA
                $pdf = Pdf::loadView('trainings::pdf.diplomaAsistencia', ['data' => $certificado]);
            }else{//CAPACITACION
                $pdf = Pdf::loadView('trainings::pdf.diploma', ['data' => $certificado]);
            }
        }else{
            if ($certificado->certificado_manual == 1 || $certificado->certificado_manual == 2) { //ASISTENCIA
                $pdf = Pdf::loadView('trainings::pdf.diplomaCapacitacionesPropiasAsistencia', ['data' => $certificado, 'Img' => $capacitacion->imagen, 'img_certificado' => $capacitacion->img_certificado]);
            }else{//CAPACITACION
                $pdf = Pdf::loadView('trainings::pdf.diplomaCapacitacionesPropias', ['data' => $certificado, 'Img' => $capacitacion->imagen, 'img_certificado' => $capacitacion->img_certificado]);
            }
        }

        return $pdf->setPaper('A4', 'landscape')->download($certificado->nombre . '.pdf');
    }

    public function downloadExcelCertificateAll($opcion)
    {
        if ($opcion == 2 && auth()->user()->id_grupo != 48) {

            $where = [
                ['ca_evaluacion_iniciada.certificado', '=', '1'],
                ['ca_evaluacion_iniciada.last_approved', '=', '1'],
                ['ca.tipo_capacitacion', '<>', '2']
            ];
            $whereIn = [];

            if (auth()->user()->savk_principal == 1) {
                array_push($where, ['usu.main_account_id', '=', auth()->user()->main_account_id]);
            }else if (auth()->user()->id_grupo == 44) {
                $grupoEmpresa = SavkLideresGrupoEmpresa::where('id_usuario',auth()->user()->id)->pluck('id_grupo_empresa')->toArray();
                $empresa = Unidad::whereIn('centro_operacion_id', $grupoEmpresa)->pluck('id')->toArray();
                $whereIn = PuntoEvaluacion::whereIn('unidad_id', $empresa)->pluck('id')->toArray();
            } else if (Auth::user()->id_grupo == 45) {
                $whereIn = SavkLideresEmpresa::select(
                    'punto_evaluacion.id'
                )
                ->join('punto_evaluacion', 'punto_evaluacion.unidad_id', 'savk_lideres_empresas.id_empresa')
                ->where('savk_lideres_empresas.id_usuario', Auth::user()->id)
                ->get()
                ->pluck('id');
            } else if (auth()->user()->id_grupo == 46) {
                $whereIn = SavkLideresZonas::join('grupos_sub_puntos', 'grupos_sub_puntos.grupo_punto_id', 'id_grupos_puntos')
                ->where('id_usuario',auth()->user()->id)
                ->pluck('punto_id')->toArray();
            }else if (Auth::user()->id_grupo == 47) {
                $whereIn = SavkLideresCentroDeCostos::select(
                    'savk_lideres_centro_de_costos.id_centro_de_costo'
                )
                ->where('savk_lideres_centro_de_costos.id_usuario', Auth::user()->id)
                ->get()
                ->pluck('id_centro_de_costo');
            }else{
                array_push($where, ['usu.id', '=', null]); //SE DEJA CONDICIONAL PARA QUE NO DEVUELVA NADA
            }

            $data = CaEvaluacionIniciada::select(
                'usu.nombre_com as Nombre',
                'usu.codigo as Identificación',
                \DB::raw("
                    (CASE
                        WHEN usu.estado = '1' THEN 'Activo'
                        WHEN usu.estado = '2' THEN 'Inactivo'
                    END) AS Estado
                "),
                's.nombre as Sección',
                \DB::raw('IF(mo.nombre is null, concat("Capacitación ",ca.nombre), concat("Modulo ",mo.nombre)) as certificado'),
                \DB::raw('ROUND(ca.tiempo_minutos/60, 1) as `tiempo_Hr(s)`'),
                'ca_evaluacion_iniciada.fecha_terminada',
                'ca_evaluacion_iniciada.id_capacitacion',
                'ca_evaluacion_iniciada.id_usuario',
                'ca_evaluacion_iniciada.id_modulo',
                \DB::raw("(SELECT max(ca2.duracion) FROM ca_cap_asistidas ca2
                    inner join  ca_capacitaciones_asistidas_asistentes caa on ca2.id = caa.id_capacitacion_asistida
                    where ca2.id_capacitacion = ca.id && tipo = 2 && caa.id_usuario = ca_evaluacion_iniciada.id_usuario) as asistida"
                )
            )
            ->join('ca_capacitaciones as ca', 'ca_evaluacion_iniciada.id_capacitacion', 'ca.id')
            ->leftjoin('ca_modulos as mo', 'ca_evaluacion_iniciada.id_modulo', 'mo.id')
            ->join('usuarios as usu', 'ca_evaluacion_iniciada.id_usuario', 'usu.id')
            ->leftJoin('savk_secciones as s', 'usu.id_seccion', 's.id')
            ->where($where)
            ->havingRaw('asistida IS NULL');

            if (sizeof($whereIn) > 0) {
                $data = $data->whereIn('usu.id_punto', $whereIn);
            }

            $data = $data->get();

        }else{
            $data = CaEvaluacionIniciada::select(
                \DB::raw('IF(mo.nombre is null, concat("Capacitación ",ca.nombre), concat("Modulo ",mo.nombre)) as certificado'),
                \DB::raw('ROUND(ca.tiempo_minutos/60, 1) as tiempo'),
                'ca_evaluacion_iniciada.fecha_terminada',
                'ca_evaluacion_iniciada.id_capacitacion',
                'ca_evaluacion_iniciada.id_usuario',
                'ca_evaluacion_iniciada.id_modulo',
            )
            ->join('ca_capacitaciones as ca', 'ca_evaluacion_iniciada.id_capacitacion', 'ca.id')
            ->leftjoin('ca_modulos as mo', 'ca_evaluacion_iniciada.id_modulo', 'mo.id')
            ->join('usuarios as usu', 'ca_evaluacion_iniciada.id_usuario', 'usu.id')
            ->where([
                ['usu.id', '=', auth()->user()->id],
                ['ca_evaluacion_iniciada.certificado', '=', '1'],
                ['ca_evaluacion_iniciada.last_approved', '=', '1'],
            ])
            ->get();
        }

        $evaluacionIniciada = new CaEvaluacionIniciada;
        foreach ($data as $key => $d) {
            $capacitacion = CaCapacitaciones::find($d->id_capacitacion);
            $intentos = $evaluacionIniciada->intentosEvaluacion($d->id_capacitacion, $d->id_usuario, $d->id_modulo);

            if ($capacitacion->permitir_certificacion == 1 && $capacitacion->evaluara_por == 2 ) {
                $d->intentos = $intentos['intentos'].' de '. $intentos['gano'];
            }else{
                $d->intentos = $intentos['intentos'];
            }
            unset($d->id_capacitacion);
            unset($d->id_usuario);
            unset($d->id_modulo);
        }

        $columnas = array_keys($data->first()->toArray()); //titulos

        // Crear un nuevo objeto Spreadsheet
        $spreadsheet = new Spreadsheet();

        // Obtener la hoja activa del objeto Spreadsheet
        $sheet = $spreadsheet->getActiveSheet();

        $columna = 'A';
        foreach ($columnas as $columnaTitulo) {
            $sheet->setCellValue($columna . '1', $columnaTitulo);
            $columna++;
        }

        // Llenar los datos en la hoja de cálculo
        $sheet->fromArray($data->toArray(), null, 'A2');

        // Crear el objeto Writer para escribir en formato XLSX
        $writer = new Xlsx($spreadsheet);

        // Definir el nombre del archivo
        $filename = 'Certificados.xlsx'; // Reemplaza "nombre_del_archivo" con el nombre que desees

        // Descargar el archivo Excel
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }
    public function downloadCertificateAll($opcion)
    {
        set_time_limit(600);
        ini_set('max_execution_time', 600);
        $zip = new ZipArchive;

        Storage::deleteDirectory('public/certificados');
        Storage::deleteDirectory('public/pdf');

        Storage::makeDirectory('public/pdf');
        Storage::makeDirectory('public/certificados');

        $nombreArchivoZip = 'storage/certificados/certificados_' . date("Y-m-d-His") . '.zip';

        if ($opcion == 2) {
            $where = [
                ['ca_evaluacion_iniciada.certificado', '=', '1'],
                ['ca_evaluacion_iniciada.last_approved', '=', '1'],
            ];
            $whereIn = [];

            if (auth()->user()->savk_principal == 1) {
                array_push($where, ['usu.main_account_id', '=', auth()->user()->main_account_id]);
            }else if (auth()->user()->id_grupo == 44) {
                $grupoEmpresa = SavkLideresGrupoEmpresa::where('id_usuario',auth()->user()->id)->pluck('id_grupo_empresa')->toArray();
                $empresa = Unidad::whereIn('centro_operacion_id', $grupoEmpresa)->pluck('id')->toArray();
                $whereIn = PuntoEvaluacion::whereIn('unidad_id', $empresa)->pluck('id')->toArray();
            }else if (auth()->user()->id_grupo == 45) {
                $whereIn = SavkLideresEmpresa::select(
                    'punto_evaluacion.id'
                )
                ->join('punto_evaluacion', 'punto_evaluacion.unidad_id', 'savk_lideres_empresas.id_empresa')
                ->where('savk_lideres_empresas.id_usuario', Auth::user()->id)
                ->get()
                ->pluck('id')->toArray();
            }else if (auth()->user()->id_grupo == 46) {
                $whereIn = SavkLideresZonas::join('grupos_sub_puntos', 'grupos_sub_puntos.grupo_punto_id', 'id_grupos_puntos')
                ->where('id_usuario',auth()->user()->id)
                ->pluck('punto_id')->toArray();
            }else if (auth()->user()->id_grupo == 47) {
                $whereIn = SavkLideresCentroDeCostos::select(
                    'savk_lideres_centro_de_costos.id_centro_de_costo'
                )
                ->where('savk_lideres_centro_de_costos.id_usuario', Auth::user()->id)
                ->get()
                ->pluck('id_centro_de_costo')->toArray();
            }else{
                array_push($where, ['usu.id', '=', null]); //SE DEJA CONDICIONAL PARA QUE NO DEVUELVA NADA
            }


            $evaluacion = CaEvaluacionIniciada::select(
                'ca_evaluacion_iniciada.*',
                'ca.nombre as nom_capacitacion',
                'ca.designed_by',
                'mo.nombre as nom_modulo',
                'usu.nombre_com as nombre',
                'usu.codigo as documento',
                'ca.puntos',
                'un.centro_operacion_id',
                \DB::raw('ROUND(ca.tiempo_minutos/60, 1) as tiempo')
            )
            ->join('ca_capacitaciones as ca', 'ca_evaluacion_iniciada.id_capacitacion', 'ca.id')
            ->leftjoin('ca_modulos as mo', 'ca_evaluacion_iniciada.id_modulo', 'mo.id')
            ->join('usuarios as usu', 'ca_evaluacion_iniciada.id_usuario', 'usu.id')
            ->join('punto_evaluacion as pt', 'usu.id_punto' ,'pt.id')
            ->join('unidad as un', 'pt.unidad_id' ,'un.id')
            ->where($where);

            if (sizeof($whereIn) > 0) {
                $evaluacion = $evaluacion->whereIn('usu.id_punto', $whereIn);
            }

            $evaluacion = $evaluacion->get();
        }else{
            $evaluacion = CaEvaluacionIniciada::select(
                'ca_evaluacion_iniciada.*',
                'ca.designed_by',
                'ca.nombre as nom_capacitacion',
                'mo.nombre as nom_modulo',
                'usu.nombre_com as nombre',
                'usu.codigo as documento',
                'ca.puntos',
                'un.centro_operacion_id',
                \DB::raw('ROUND(ca.tiempo_minutos/60, 1) as tiempo')
            )
            ->join('ca_capacitaciones as ca', 'ca_evaluacion_iniciada.id_capacitacion', 'ca.id')
            ->leftjoin('ca_modulos as mo', 'ca_evaluacion_iniciada.id_modulo', 'mo.id')
            ->join('usuarios as usu', 'ca_evaluacion_iniciada.id_usuario', 'usu.id')
            ->join('punto_evaluacion as pt', 'usu.id_punto' ,'pt.id')
            ->join('unidad as un', 'pt.unidad_id' ,'un.id')
            ->where([
                ['usu.id', '=', auth()->user()->id],
                ['ca_evaluacion_iniciada.certificado', '=', '1'],
                ['ca_evaluacion_iniciada.last_approved', '=', '1'],
            ])
            ->get();
        }


        $contador=0;
        foreach ($evaluacion as $index => $asistente ) {
            $capacitacion = new CaCapacitaciones;
            $capacitacion = $capacitacion->mainAccountTraining($asistente->id_capacitacion);

            if ($capacitacion->main_account_id == 1) {
                if ($asistente->certificado_manual == 1 || $asistente->certificado_manual == 2) { //ASISTENCIA
                    $pdf = Pdf::loadView('trainings::pdf.diplomaAsistencia', ['data' => $asistente]);
                }else{//CAPACITACION
                    $pdf = Pdf::loadView('trainings::pdf.diploma', ['data' => $asistente]);
                }
            }else{
                if ($asistente->certificado_manual == 1 || $asistente->certificado_manual == 2) { //ASISTENCIA
                    $pdf = Pdf::loadView('trainings::pdf.diplomaCapacitacionesPropiasAsistencia', ['data' => $asistente, 'Img' => $capacitacion->imagen, 'img_certificado' => $capacitacion->img_certificado]);
                }else{//CAPACITACION
                    $pdf = Pdf::loadView('trainings::pdf.diplomaCapacitacionesPropias', ['data' => $asistente, 'Img' => $capacitacion->imagen, 'img_certificado' => $capacitacion->img_certificado]);
                }
            }
            // $pdf = Pdf::loadView('trainings::pdf.diploma', ['data' => $asistente])
                $pdf->setPaper('A4', 'landscape')
                ->save(public_path('storage/pdf/' . $asistente->nombre ."-".$index.".pdf"));
        }

        if ($zip->open(public_path($nombreArchivoZip), ZipArchive::CREATE) === TRUE) {
            $archivos = File::files(public_path('storage/pdf'));
            foreach ($archivos as $archivo) {
                $zip->addFile($archivo, basename($archivo));
            }

            $zip->close();
        }

        $response = response()->download(public_path($nombreArchivoZip));


        return $response;
    }

    public function downloadCertificatePublic($id)
    {
        $evaluacionIniciada = new CaEvaluacionIniciada();
        $evaluacion = CaEvaluacionIniciada::find($id);

        if ($evaluacion->id_usuario == null) {
            $certificado = $evaluacionIniciada->ByIdCertifiedIdPublic($id);
        }else{
            $certificado = $evaluacionIniciada->ByIdCertifiedId($id);
        }

        $capacitacion = new CaCapacitaciones;
        $capacitacion = $capacitacion->mainAccountTraining($certificado->id_capacitacion);

        if ($capacitacion->main_account_id == 1) {
            if ($certificado->certificado_manual == 1 || $certificado->certificado_manual == 2) { //ASISTENCIA
                $pdf = Pdf::loadView('trainings::pdf.diplomaAsistencia', ['data' => $certificado]);
            }else{//CAPACITACION
                $pdf = Pdf::loadView('trainings::pdf.diploma', ['data' => $certificado]);
            }
        }else{
            if ($certificado->certificado_manual == 1 || $certificado->certificado_manual == 2) { //ASISTENCIA
                $pdf = Pdf::loadView('trainings::pdf.diplomaCapacitacionesPropiasAsistencia', ['data' => $certificado, 'Img' => $capacitacion->imagen, 'img_certificado' => $capacitacion->img_certificado]);
            }else{//CAPACITACION
                $pdf = Pdf::loadView('trainings::pdf.diplomaCapacitacionesPropias', ['data' => $certificado, 'Img' => $capacitacion->imagen, 'img_certificado' => $capacitacion->img_certificado]);
            }
        }

        return $pdf->setPaper('A4', 'landscape')->download($certificado->nombre . '.pdf');
    }

    public function downloadCertificatesPublic($id)
    {
        $idsTest = json_decode(urldecode($id), true);

        $pdfs = [];

        $countIds = count($idsTest);

        foreach ($idsTest as $key => $idData) {
            $evaluacionIniciada = new CaEvaluacionIniciada();
            $certificado = $evaluacionIniciada->ByIdCertifiedIdPublic($idData['id']);

            $capacitacion = new CaCapacitaciones;
            $capacitacion = $capacitacion->mainAccountTraining($certificado->id_capacitacion);

            if ($capacitacion->main_account_id == 1) {
                if ($certificado->certificado_manual == 1 || $certificado->certificado_manual == 2) { //ASISTENCIA
                    $pdf = Pdf::loadView('trainings::pdf.diplomaAsistencia', ['data' => $certificado]);
                }else{//CAPACITACION
                    $pdf = Pdf::loadView('trainings::pdf.diploma', ['data' => $certificado]);
                }
            }else{
                if ($certificado->certificado_manual == 1 || $certificado->certificado_manual == 2) { //ASISTENCIA
                    $pdf = Pdf::loadView('trainings::pdf.diplomaCapacitacionesPropiasAsistencia', ['data' => $certificado, 'Img' => $capacitacion->imagen, 'img_certificado' => $capacitacion->img_certificado]);
                }else{//CAPACITACION
                    $pdf = Pdf::loadView('trainings::pdf.diplomaCapacitacionesPropias', ['data' => $certificado, 'Img' => $capacitacion->imagen, 'img_certificado' => $capacitacion->img_certificado]);
                }
            }

            //SI SOLO TIENE UN CERTIFICADO DESCARGA EL PDF SIN .ZIP
            if ($countIds === 1) {
                return $pdf->setPaper('A4', 'landscape')->download($certificado->nombre . '.pdf');
            }

            $pdfs[] = [
                'name' => $certificado->nombre . '-'.$key.'.pdf',
                'content' => $pdf->setPaper('A4', 'landscape')->output(),
            ];
        }

        // Crear un archivo ZIP para los PDF
        $zip = new \ZipArchive();
        $zipFileName = 'certificados.zip';
        $zip->open($zipFileName, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

        // Agregar los PDF al archivo ZIP
        foreach ($pdfs as $pdf) {
            $zip->addFromString($pdf['name'], $pdf['content']);
        }

        $zip->close();

        // Descargar el archivo ZIP
        return response()->download($zipFileName)->deleteFileAfterSend(true);
    }

    public function shareCertificate(Request $request)
    {
        $id = $request->get('id');

        $evaluacionIniciada = new CaEvaluacionIniciada();
        $certificado = $evaluacionIniciada->ByIdCertifiedId($id);

        $capacitacion = new CaCapacitaciones;
        $capacitacion = $capacitacion->mainAccountTraining($certificado->id_capacitacion);

        if ($capacitacion->main_account_id == 1) {
            if ($certificado->certificado_manual == 1 || $certificado->certificado_manual == 2) { //ASISTENCIA
                $pdf = Pdf::loadView('trainings::pdf.diplomaAsistencia', ['data' => $certificado]);
            }else{//CAPACITACION
                $pdf = Pdf::loadView('trainings::pdf.diploma', ['data' => $certificado]);
            }
        }else{
            if ($certificado->certificado_manual == 1 || $certificado->certificado_manual == 2) { //ASISTENCIA
                $pdf = Pdf::loadView('trainings::pdf.diplomaCapacitacionesPropiasAsistencia', ['data' => $certificado, 'Img' => $capacitacion->imagen, 'img_certificado' => $capacitacion->img_certificado]);
            }else{//CAPACITACION
                $pdf = Pdf::loadView('trainings::pdf.diplomaCapacitacionesPropias', ['data' => $certificado, 'Img' => $capacitacion->imagen, 'img_certificado' => $capacitacion->img_certificado]);
            }
        }
        $fileName = $certificado->nombre . '_' . $certificado->documento . '_' . $certificado->nom_capacitacion . '_' . $certificado->nom_modulo . '.pdf';
        $fileName = preg_replace('/[^a-zA-Z0-9_.\-]/', '', $fileName); //ELIMINA CARACTERES ESPECIALES, GENERAN CONFLICTO

        $folderPath = storage_path('app/public/certificados');

        // Verificar si la carpeta "hola" no existe
        if (!File::exists($folderPath)) {
            // Crear la carpeta "hola" si no existe
            File::makeDirectory($folderPath, 0755, true);
        }

        // Guardar el PDF en la carpeta "storage" con el nombre de archivo deseado
        $pdf->setPaper('A4', 'landscape')->save(storage_path('app/public/certificados/' . $fileName));


        // Retornar la ruta completa del archivo guardado
        $filePath = CropLink(env('APP_URL') . '/storage/certificados/' . $fileName);

        return $this->ExitProgram(200, $this->MessageResponse('Data', 200), $filePath);
    }

    public function shareTraining(Request $request)
    {
        return $this->ExitProgram(202, 'Enlace generado', CropLink(env('APP_URL') . '/capacitaciones/acceder-capacitacion-compartida/' . Crypt::encrypt($request->id) . '/' .  Crypt::encrypt(Auth::user()->main_account_id)));
    }
    public function GetTrainingShare($id_training, $main_account_id)
    {

        $action = __FUNCTION__;
        $id_training = Crypt::decrypt($id_training);

        $main_account_id = Crypt::decrypt($main_account_id);

        $email = DB::table('usuarios')->select('email')->where([
            ['main_account_id', $main_account_id],
            ['savk_principal', 1]
        ])->pluck('email')
        ->first();

        $organizacion = DB::table('centro_operacion')->select('nombre')->where('main_account_id', $main_account_id)->pluck('nombre')->first();

        $permisos = $this->GetAllPermisos();
        if (Auth::check()) {
            return view('trainings::Admin.index_get_training_share', compact('id_training','permisos'));
        } else {
            return view('trainings::Admin.index_login_or_register_training_share', compact('action', 'id_training', 'main_account_id', 'email', 'organizacion','permisos'));
        }
    }

    public function downloadAllCertificateByLink($id_link)
    {
        $zip = new ZipArchive;

        Storage::deleteDirectory('public/certificados');
        Storage::deleteDirectory('public/pdf');

        Storage::makeDirectory('public/pdf');
        Storage::makeDirectory('public/certificados');

        $nombreArchivoZip = 'storage/certificados/certificados_' . date("Y-m-d-His") . '.zip';

        $asistentes = CaAsistentesLinks::select(
            'ca_asistentes.nombre',
            'ca_asistentes.documento',
            'ca_modulos.nombre as nom_modulo',
            'ca_capacitaciones.nombre as nom_capacitacion',
            'ca.designed_by'
        )
            ->join('ca_asistentes', 'ca_asistentes_links.id_asistente', 'ca_asistentes.id')
            ->join('ca_links', 'ca_links.id', 'ca_asistentes_links.id_link')
            ->leftjoin('ca_capacitaciones', 'ca_links.id_capacitacion', 'ca_capacitaciones.id')
            ->leftjoin('ca_modulos', 'ca_links.id_modulo', 'ca_modulos.id')
            ->where([
                ['ca_asistentes_links.id_link', $id_link],
            ])
            ->get();


        foreach ($asistentes as $asistente) {
            $capacitacion = new CaCapacitaciones;
            $capacitacion = $capacitacion->mainAccountTraining($asistente->id_capacitacion);

            if ($capacitacion->main_account_id == 1) {
                if ($asistente->certificado_manual == 1 || $asistente->certificado_manual == 2) { //ASISTENCIA
                    $pdf = Pdf::loadView('trainings::pdf.diplomaAsistencia', ['data' => $asistente]);
                }else{//CAPACITACION
                    $pdf = Pdf::loadView('trainings::pdf.diploma', ['data' => $asistente]);
                }
            }else{
                if ($asistente->certificado_manual == 1 || $asistente->certificado_manual == 2) { //ASISTENCIA
                    $pdf = Pdf::loadView('trainings::pdf.diplomaCapacitacionesPropiasAsistencia', ['data' => $asistente, 'Img' => $capacitacion->imagen, 'img_certificado' => $capacitacion->img_certificado]);
                }else{//CAPACITACION
                    $pdf = Pdf::loadView('trainings::pdf.diplomaCapacitacionesPropias', ['data' => $asistente, 'Img' => $capacitacion->imagen, 'img_certificado' => $capacitacion->img_certificado]);
                }
            }
            $pdf->setPaper('A4', 'landscape')
            ->save(public_path('storage/pdf/' . $asistente->nombre . ".pdf"));
        }

        if ($zip->open(public_path($nombreArchivoZip), ZipArchive::CREATE) === TRUE) {
            $archivos = File::files(public_path('storage/pdf'));
            foreach ($archivos as $archivo) {
                $zip->addFile($archivo, basename($archivo));
            }

            $zip->close();
        }

        $response = response()->download(public_path($nombreArchivoZip));


        return $response;
    }

    public function ChangeStatusTraining(Request $request)
    {

        $training = CaCapacitaciones::where('id', $request->id_training);

        $status = $training->first()->estado;


        $training = $training->update(['estado' => !$status]);

        return $this->ExitProgram(202, $this->MessageResponse('', 206, 'Cambio de estado exitoso'), []);
    }


    public function SaveTrainigInit(Request $request)
    {
        $evaluara_por = $request->get('evaluara_por');

        $dataTrainingInit = [
            'id_capacitacion' => $request->get('id_capacitacion'),
            'id_modulo' => $request->get('id_module'),
            'id_usuario' => auth()->user()->id
        ];

        $training = CaCapacitaciones::where('id',$request->get('id_capacitacion'))->first();
        $trainingInit = new CaCapacitacionesIniciadas();
        $exist = $trainingInit->ValidateExist($dataTrainingInit); //SE VALIDA SI YA INICIO LA CAPACITACIÓN

        if ($exist == 0) {
            if ($evaluara_por == 2) { //EVALUARA POR MÓDULOS
                $test = CaPreguntas::where('id_modulo', $request->get('id_module'))->get(); //VALIDAMOS SI TIENE EVALUACIÓN EL MODULO

                if (count($test) > 0) { //TIENE EVALUACIÓN
                    $caTestInit = new CaEvaluacionIniciada();
                    $moduleApproved = $caTestInit->ValidateApproved($dataTrainingInit); //SE CONSULTA SI LA EVALUACIÓN DEL MODULO ES APROBADA

                    if ($moduleApproved > 0) { //LA EVALUACIÓN DEL MODULO ES APROBADA
                        $trainingInit->fill($dataTrainingInit);
                        $trainingInit->save();
                    }
                } else { //NO TIENE EVALUACIÓN EL MÓDULO
                    $trainingInit->fill($dataTrainingInit);
                    $trainingInit->save();
                }
            } else { //EVALUARA POR CAPACITACIÓN
                $trainingInit->fill($dataTrainingInit);
                $trainingInit->save();

                //SE GUARDA EN EVALUACIÓN INICIADA REGISTRO PARA CERTIFICAR POR ASISTENCIA, SI CAP CERTIFICA Y NO TIENE EVALUACIÓN
                if ($training->aplica_certificado == 1 && $training->aplica_evaluacion == 2) {
                    if ($training->permitir_certificacion == 2) {
                        //CERTIFICA POR MODULO
                        $now = Carbon::now()->format('Y-m-d H:i:s');
                        $caTestInit = new CaEvaluacionIniciada();
                        $array_test_init = [
                            'id_capacitacion' => $training->id,
                            'id_modulo' => $request->get('id_module'),
                            'id_usuario' => auth()->user()->id,
                            'fecha_inicio' => $now,
                            'fecha_terminada' => $now,
                            'certificado' => '1',
                            'certificado_manual' => '2',
                            'last_approved' => '1',
                        ];
                        $caTestInit->fill($array_test_init);
                        $caTestInit->save();
                    }else{
                        //CERTIFICA POR CAP GENERAL
                        $countModules = CaModulos::where('id_capacitacion',$training->id)->count();
                        $countTrainingInit = CaCapacitacionesIniciadas::where([
                            ['id_capacitacion', $training->id],
                            ['id_usuario', auth()->user()->id]
                        ])->count();

                        if ($countModules == $countTrainingInit) {
                            $now = Carbon::now()->format('Y-m-d H:i:s');
                            $caTestInit = new CaEvaluacionIniciada();
                            $array_test_init = [
                                'id_capacitacion' => $training->id,
                                'id_usuario' => auth()->user()->id,
                                'fecha_inicio' => $now,
                                'fecha_terminada' => $now,
                                'certificado' => '1',
                                'certificado_manual' => '2',
                                'last_approved' => '1',
                            ];
                            $caTestInit->fill($array_test_init);
                            $caTestInit->save();
                        }

                    }
                }
            }
        }

        $modules = CaModulos::GetAllModulesExcecutable($dataTrainingInit['id_capacitacion']);
        return $this->ExitProgram(200, 'Training', $modules);
    }


    //CERTIFICADOS

    public function saveQuestionsCliente(Request $request)
    {
        $dataPreguntas = [
            'id_capacitacion' => $request->get('id_capacitacion'),
            'id_modulo' => $request->get('id_modulo'),
            'id_usuario' => auth()->user()->id,
            'pregunta' => $request->get('pregunta'),
        ];

        $user = User::find(auth()->user()->id);
        $grupoEmpresa = CentroOperacion::where('main_account_id',$user->main_account_id)->first();
        $asesor = User::find($grupoEmpresa->asesor_id);
        $capacitacion = CaCapacitaciones::find($request->get('id_capacitacion'));

        $preguntasUsuarios = new CaPreguntasUsuarios();
        $preguntasUsuarios->fill($dataPreguntas);

        if ($preguntasUsuarios->save()) {
            if ($asesor) {
                Mail::to($asesor->email)
                ->cc(['tic@klaxen.com.co', 'administrador@klaxen.com'])
                ->send(new PreguntaEstudiante($user->nombre_com, $capacitacion->nombre));
            }else{
                Mail::to(['tic@klaxen.com.co', 'administrador@klaxen.com'])->send(new PreguntaEstudiante($user->nombre_com, $capacitacion->nombre));
            }
            $preguntas = $preguntasUsuarios->getPreguntasCap(auth()->user()->id, $request->get('id_capacitacion'), $request->get('id_modulo'));
            return $this->ExitProgram(200, 'se ingreso pregunta', $preguntas);
        }
    }

    public function getAllQuestionsUsuarios(Request $request)
    {
        $preguntasUsuarios = new CaPreguntasUsuarios();
        $preguntas = $preguntasUsuarios->getPreguntasCap(auth()->user()->id, $request->get('id_capacitacion'), $request->get('id_modulo'));
        return $this->ExitProgram(202, 'preguntas encontradas', $preguntas);
    }

    public function TrainingsIndexCertificates(Request $request)
    {
        $page_title = 'Certificados';
        $action = __FUNCTION__;
        $permisos = $this->GetAllPermisos();

        return view('trainings::Admin.index_training_certificates', compact('page_title', 'action','permisos'));
    }

    public function dataUser(){
        $data = ['id'=> \Auth::user()->id, 'savk_principal' => \Auth::user()->savk_principal, 'main_account_id' => \Auth::user()->main_account_id];
        return $this->ExitProgram(200, $this->MessageResponse('User', 202), $data);
    }

    public function TrainingsGetCertificatesId(Request $request)
    {
        $evaluacionIniciada = new CaEvaluacionIniciada();
        $certificados = $evaluacionIniciada->ByIdCertified();

        return $this->ExitProgram(202, $this->MessageResponse('Data', 202), $certificados);
    }

    public function getAllCertificatesTeam(Request $request, CaEvaluacionIniciada $evaluacionIniciada)
    {
        $cant_pag = 10;
        $search = '';

        if (sizeof($request->get('paginate')) > 0) {
            $cant_pag = $request->paginate['cant'];
        }

        $whereIn=[];
        $where = [
            ['ca_evaluacion_iniciada.certificado', '=', '1'],
            ['ca_evaluacion_iniciada.last_approved', '=', '1'],
            ['ca.tipo_capacitacion', '<>', '2'], //No traer asistidas por experto
            // ['usu.estado', '=', '1'], //solo usuarios activos
        ];

        if (auth()->user()->savk_principal == 1) {
            array_push($where, ['usu.main_account_id', '=', auth()->user()->main_account_id]);
        }else if (auth()->user()->id_grupo == 44) {
            $grupoEmpresa = SavkLideresGrupoEmpresa::where('id_usuario',auth()->user()->id)->pluck('id_grupo_empresa')->toArray();
            $empresa = Unidad::whereIn('centro_operacion_id', $grupoEmpresa)->pluck('id')->toArray();
            $whereIn = PuntoEvaluacion::whereIn('unidad_id', $empresa)->pluck('id')->toArray();

            if (sizeof($whereIn) == 0) {
                //NO TIENE GRUPO EMPRESA ASIGNADO
                array_push($where, ['usu.id', '=', null]); //SE DEJA CONDICIONAL PARA QUE NO DEVUELVA NADA
            }
        }else if (auth()->user()->id_grupo == 45) {
            $whereIn = SavkLideresEmpresa::select(
                'punto_evaluacion.id'
            )
            ->join('punto_evaluacion', 'punto_evaluacion.unidad_id', 'savk_lideres_empresas.id_empresa')
            ->where('savk_lideres_empresas.id_usuario', Auth::user()->id)
            ->get()
            ->pluck('id');

            if (sizeof($whereIn) == 0) {
                //NO TIENE ZONA ASIGNADO
                array_push($where, ['usu.id', '=', null]); //SE DEJA CONDICIONAL PARA QUE NO DEVUELVA NADA
            }
        }else if (auth()->user()->id_grupo == 46) {
            $whereIn = SavkLideresZonas::join('grupos_sub_puntos', 'grupos_sub_puntos.grupo_punto_id', 'id_grupos_puntos')
            ->where('id_usuario',auth()->user()->id)
            ->pluck('punto_id')->toArray();

            if (sizeof($whereIn) == 0) {
                //NO TIENE ZONA ASIGNADO
                array_push($where, ['usu.id', '=', null]); //SE DEJA CONDICIONAL PARA QUE NO DEVUELVA NADA
            }
        }else if (auth()->user()->id_grupo == 47) {
            $whereIn = SavkLideresCentroDeCostos::select(
                'savk_lideres_centro_de_costos.id_centro_de_costo'
            )
            ->where('savk_lideres_centro_de_costos.id_usuario', Auth::user()->id)
            ->get()
            ->pluck('id_centro_de_costo');

            if (sizeof($whereIn) == 0) {
                //NO TIENE ZONA ASIGNADO
                array_push($where, ['usu.id', '=', null]); //SE DEJA CONDICIONAL PARA QUE NO DEVUELVA NADA
            }
        }else{
            array_push($where, ['usu.id', '=', null]); //SE DEJA CONDICIONAL PARA QUE NO DEVUELVA NADA
        }

        if (strlen($request->get('search')) != 0) {
            $search = $request->search;
        }

        // $data = $users->getAll($where, $cant_pag);
        $data = $evaluacionIniciada
            ->select(
                'ca_evaluacion_iniciada.*',
                'ca.nombre as nom_capacitacion',
                'ca.permitir_certificacion',
                'ca.evaluara_por',
                \DB::raw('ROUND(ca.tiempo_minutos/60, 1) as tiempo'),
                'mo.id as id_modulos',
                'mo.nombre as nom_modulo',
                'usu.nombre_com as nombre',
                'usu.codigo as documento',
                \DB::raw("
                    (CASE
                        WHEN usu.estado = '1' THEN 'Activo'
                        WHEN usu.estado = '2' THEN 'Inactivo'
                    END) AS estado
                "),
                'ca.puntos',
                \DB::raw("(SELECT max(ca2.duracion) FROM ca_cap_asistidas ca2
                    inner join  ca_capacitaciones_asistidas_asistentes caa on ca2.id = caa.id_capacitacion_asistida
                    where ca2.id_capacitacion = ca.id && tipo = 2 && caa.id_usuario = ca_evaluacion_iniciada.id_usuario) as asistida"
                )
            )
            ->join('ca_capacitaciones as ca', 'ca_evaluacion_iniciada.id_capacitacion', 'ca.id')
            ->leftjoin('ca_modulos as mo', 'ca_evaluacion_iniciada.id_modulo', 'mo.id')
            ->join('usuarios as usu', 'ca_evaluacion_iniciada.id_usuario', 'usu.id')
            ->where($where)
            ->where(function ($query) use ($search) {
                $query->where('ca.nombre', 'LIKE', "%$search%")
                    ->orWhere('mo.nombre', 'LIKE', "%$search%")
                    ->orWhere('ca_evaluacion_iniciada.fecha_terminada', 'LIKE', "%$search%")
                    ->orWhere('usu.nombre_com', 'LIKE', "%$search%")
                    ->orWhere('usu.codigo', 'LIKE', "%$search%");
            })
            ->havingRaw('asistida IS NULL');

        if(sizeof($whereIn) > 0){
            $data = $data->whereIn('usu.id_punto', $whereIn);
        }
        $data = $data->orderBy('ca_evaluacion_iniciada.fecha_terminada', 'desc')->paginate($cant_pag);

            foreach ($data as $key => $d) {
                $d->intentos = $evaluacionIniciada->intentosEvaluacion($d->id_capacitacion, $d->id_usuario, $d->id_modulos);
            }

        return response()->json([
            'status' => 200,
            'data' => $data
        ]);
    }

    public function getAllCertificatesClient(Request $request, CaEvaluacionIniciada $evaluacionIniciada)
    {
        $cant_pag = 10;
        $search = '';

        if (sizeof($request->get('paginate')) > 0) {
            $cant_pag = $request->paginate['cant'];
        }

        $whereIn=[];
        $where = [
            ['ca_evaluacion_iniciada.certificado', '=', '1'],
            ['ca_evaluacion_iniciada.last_approved', '=', '1'],
            ['ca.tipo_capacitacion', '<>', '2'], //No traer asistidas por experto
            // ['usu.estado', '=', '1'], //solo usuarios activos
        ];

        $permisos = $this->GetAllPermisos();
        if($permisos->pluck('evento')->contains('ent-ele-certificados_cliente')){
            $grupoEmpresa = CentroOperacion::where('asesor_id',auth()->user()->id)->pluck('id')->toArray();
            $empresa = Unidad::whereIn('centro_operacion_id', $grupoEmpresa)->pluck('id')->toArray();
            $whereIn = PuntoEvaluacion::whereIn('unidad_id', $empresa)->pluck('id')->toArray();

            if (sizeof($whereIn) == 0) {
                //NO TIENE GRUPO EMPRESA ASIGNADO
                array_push($where, ['usu.id', '=', null]); //SE DEJA CONDICIONAL PARA QUE NO DEVUELVA NADA
            }
        }else{
            array_push($where, ['usu.id', '=', null]); //SE DEJA CONDICIONAL PARA QUE NO DEVUELVA NADA
        }

        if (strlen($request->get('search')) != 0) {
            $search = $request->search;
        }

        // $data = $users->getAll($where, $cant_pag);
        $data = $evaluacionIniciada
            ->select(
                'ca_evaluacion_iniciada.*',
                'ca.nombre as nom_capacitacion',
                'ca.permitir_certificacion',
                'ca.evaluara_por',
                \DB::raw('ROUND(ca.tiempo_minutos/60, 1) as tiempo'),
                'mo.id as id_modulos',
                'mo.nombre as nom_modulo',
                'usu.nombre_com as nombre',
                'usu.codigo as documento',
                'co.nombre as grupo_empresa',
                'uni.nombre as empresa',
                \DB::raw("
                    (CASE
                        WHEN usu.estado = '1' THEN 'Activo'
                        WHEN usu.estado = '2' THEN 'Inactivo'
                    END) AS estado
                "),
                'ca.puntos',
                \DB::raw("(SELECT max(ca2.duracion) FROM ca_cap_asistidas ca2
                    inner join  ca_capacitaciones_asistidas_asistentes caa on ca2.id = caa.id_capacitacion_asistida
                    where ca2.id_capacitacion = ca.id && tipo = 2 && caa.id_usuario = ca_evaluacion_iniciada.id_usuario) as asistida"
                )
            )
            ->join('ca_capacitaciones as ca', 'ca_evaluacion_iniciada.id_capacitacion', 'ca.id')
            ->leftjoin('ca_modulos as mo', 'ca_evaluacion_iniciada.id_modulo', 'mo.id')
            ->join('usuarios as usu', 'ca_evaluacion_iniciada.id_usuario', 'usu.id')
            ->leftjoin('punto_evaluacion as pe','pe.id','usu.id_punto')
            ->leftjoin('unidad as uni','uni.id','pe.unidad_id')
            ->leftjoin('centro_operacion as co','co.id','uni.centro_operacion_id')
            ->where($where)
            ->where(function ($query) use ($search) {
                $query->where('ca.nombre', 'LIKE', "%$search%")
                    ->orWhere('mo.nombre', 'LIKE', "%$search%")
                    ->orWhere('ca_evaluacion_iniciada.fecha_terminada', 'LIKE', "%$search%")
                    ->orWhere('usu.nombre_com', 'LIKE', "%$search%")
                    ->orWhere('usu.codigo', 'LIKE', "%$search%");
            })
            ->havingRaw('asistida IS NULL');

        if(sizeof($whereIn) > 0){
            $data = $data->whereIn('usu.id_punto', $whereIn);
        }
        $data = $data->orderBy('ca_evaluacion_iniciada.fecha_terminada', 'desc')->paginate($cant_pag);

            foreach ($data as $key => $d) {
                $d->intentos = $evaluacionIniciada->intentosEvaluacion($d->id_capacitacion, $d->id_usuario, $d->id_modulos);
            }

        return response()->json([
            'status' => 200,
            'data' => $data
        ]);
    }

    public function getInstantesCertificates(Request $request){
        $evaluacion = new CaEvaluacionIniciada;
        $tipo = ($request->tipo == null) ? 'privada' : (($request->tipo == 1) ? 'publica' : 'privada');
        $intentos = $evaluacion->intentosEvaluacion($request->id_capacitacion,$request->id_usuario,$request->id_modulo, true, $tipo);

        $data = CaEvaluacionIniciada::select('m.nombre','fecha_terminada', 'resultado',
            'ca_evaluacion_iniciada.id', 'm.id as id_modulo',
            \DB::raw('if(c.evaluara_por = 1, c.porcentaje_aprobacion, m.porcentaje_aprueba) as aprobacion'))
        ->leftJoin('ca_modulos as m', 'm.id', 'id_modulo')
        ->leftJoin('ca_capacitaciones as c', 'c.id', 'ca_evaluacion_iniciada.id_capacitacion')
        ->whereIn('ca_evaluacion_iniciada.id', $intentos['intentos'])
        ->get();

        return response()->json([
            'status' => 200,
            'data' => $data
        ]);
    }

    public function getAllCertificates(Request $request, CaEvaluacionIniciada $evaluacionIniciada)
    {
        $cant_pag = 10;
        $search = '';

        if (sizeof($request->get('paginate')) > 0) {
            $cant_pag = $request->paginate['cant'];
        }

        $where = [
            ['ca_evaluacion_iniciada.id_usuario', '=', auth()->user()->id],
            ['ca_evaluacion_iniciada.certificado', '=', '1'],
            ['ca_evaluacion_iniciada.last_approved', '=', '1'],
            ['ca.tipo_capacitacion', '<>', '2'], //No traer asistidas por experto
        ];

        if (strlen($request->get('search')) != 0) {
            $search = $request->search;
        }

        // $data = $users->getAll($where, $cant_pag);
        $data = $evaluacionIniciada
            ->select(
                'ca_evaluacion_iniciada.*',
                'ca.nombre as nom_capacitacion',
                'ca.permitir_certificacion',
                'ca.evaluara_por',
                \DB::raw('ROUND(ca.tiempo_minutos/60, 1) as tiempo'),
                'mo.id as id_modulos',
                'mo.nombre as nom_modulo',
                'usu.nombre_com as nombre',
                'usu.codigo as documento',
                'ca.puntos',
                \DB::raw("(SELECT max(ca2.duracion) FROM ca_cap_asistidas ca2
                    inner join  ca_capacitaciones_asistidas_asistentes caa on ca2.id = caa.id_capacitacion_asistida
                    where ca2.id_capacitacion = ca.id && tipo = 2 && caa.id_usuario = ca_evaluacion_iniciada.id_usuario) as asistida"
                )
            )
            ->join('ca_capacitaciones as ca', 'ca_evaluacion_iniciada.id_capacitacion', 'ca.id')
            ->leftjoin('ca_modulos as mo', 'ca_evaluacion_iniciada.id_modulo', 'mo.id')
            ->join('usuarios as usu', 'ca_evaluacion_iniciada.id_usuario', 'usu.id')
            ->where($where)
            ->where(function ($query) use ($search) {
                $query->where('ca.nombre', 'LIKE', "%$search%")
                    ->orWhere('mo.nombre', 'LIKE', "%$search%")
                    ->orWhere('ca_evaluacion_iniciada.fecha_terminada', 'LIKE', "%$search%");
            })
            ->havingRaw('asistida IS NULL')
            ->orderBy('ca_evaluacion_iniciada.fecha_terminada', 'desc')
            ->paginate($cant_pag);

        foreach ($data as $key => $d) {
            $d->intentos = $evaluacionIniciada->intentosEvaluacion($d->id_capacitacion, $d->id_usuario, $d->id_modulos);
        }


        return response()->json([
            'status' => 200,
            'data' => $data
        ]);
    }

    public function GetAllTrainingsAssistByExpert(Request $request)
    {
        $validarLider = true;
        $search = $request->get('search');
        $permisos = $this->GetAllPermisos();

        \DB::statement("SET lc_time_names = 'es_ES'");

        $data = DB::table('ca_cap_asistidas as ci')
            ->select(
                'ci.*',
                'us.nombre_com as asesor',
                'c.nombre as capacitacion',
                'c.permitir_certificacion',
                'c.evaluara_por',
                \DB::raw(
                    "IF(ci.tipo = 1, 'Pública','Privada') as tipoLetras"
                ),
                \DB::raw(
                    "IF(ci.modalidad = 1, 'Virtual','Presencial') as modalidadLetras"
                ),
                \DB::raw(
                    "(select p.unidad_id from punto_evaluacion p where p.id = ci.id_cliente) as id_empresa"
                ),
                \DB::raw(
                    "(select u.centro_operacion_id from unidad u where u.id = id_empresa) as id_grupo_empresa"
                ),
            )
            ->join('usuarios as us', 'us.id', '=', 'ci.id_asesor')
            ->join('ca_capacitaciones as c', 'c.id', '=', 'ci.id_capacitacion')
            ->orderBy('ci.fecha_agendamiento', 'desc');

        if($search != null){
            $data->where(function ($query) use ($search) {
                $query->where('ci.fecha_agendamiento', 'LIKE', "%$search%")
                    ->orWhere('c.nombre', 'LIKE', "%$search%")
                    ->orWhere('us.nombre_com', 'LIKE', "%$search%")
                    ->orWhere(DB::raw("IF(ci.modalidad = 1, 'Virtual','Presencial')"), 'LIKE', "%$search%")
                    ->orWhere(DB::raw("IF(ci.tipo = 1, 'Pública','Privada')"), 'LIKE', "%$search%");
            });
        }

        // if (Auth::user()->savk_principal == 1 && $this->main_account_id != 1) {
        //     $data = $data->join('punto_evaluacion as pe', 'pe.id', '=', 'ci.id_cliente')
        //         ->where('pe.main_account_id', $this->main_account_id)
        //         ->paginate(10);
        // }

        //KLAXEN TRAE ACTUALMENTE TODO
        if (Auth::user()->id == 4301) {
            $data = $data->paginate(10);
        }else if (Auth::user()->savk_principal == 1) {
            $data = $data->join('punto_evaluacion as pe', 'pe.id', '=', 'ci.id_cliente')
                ->where('pe.main_account_id', $this->main_account_id)
                ->paginate(10);
        }

        //LIDER GRUPO EMPRESA
        if (Auth::user()->savk_principal != 1 && Auth::user()->id_grupo == 44) {
            $grupoEmpresa = SavkLideresGrupoEmpresa::where('id_usuario',auth()->user()->id)->pluck('id_grupo_empresa')->toArray();

            if ($grupoEmpresa) {
                $puntosEvaluacion = PuntoEvaluacion::select(
                    'punto_evaluacion.id'
                )
                ->Join('unidad', 'punto_evaluacion.unidad_id', '=', 'unidad.id')
                ->Join('centro_operacion', 'unidad.centro_operacion_id', '=', 'centro_operacion.id')
                ->whereIn('centro_operacion.id', $grupoEmpresa)
                ->get();

                //OBTENEMOS LOS PTOS QUE TIENE A SU CARGO
                $puntosIds = $puntosEvaluacion->pluck('id')->toArray();
                $data = $data->join('punto_evaluacion as pe', 'pe.id', '=', 'ci.id_cliente')
                    ->whereIn('pe.id', $puntosIds)
                    ->paginate(10);
            }else{
                $validarLider = false;
            }

        //LIDER EMPRESA
        }else if(Auth::user()->savk_principal != 1 && Auth::user()->id_grupo == 45) {
            $Empresa = SavkLideresEmpresa::where('id_usuario',auth()->user()->id)->pluck('id_empresa')->toArray();

            if ($Empresa) {
                $puntosEvaluacion = PuntoEvaluacion::select(
                    'punto_evaluacion.id'
                )
                ->Join('unidad', 'punto_evaluacion.unidad_id', '=', 'unidad.id')
                ->whereIn('unidad.id', $Empresa)
                ->get();

                //OBTENEMOS LOS PTOS QUE TIENE A SU CARGO
                $puntosIds = $puntosEvaluacion->pluck('id')->toArray();
                $data = $data->join('punto_evaluacion as pe', 'pe.id', '=', 'ci.id_cliente')
                    ->whereIn('pe.id', $puntosIds)
                    ->paginate(10);
            }else{
                $validarLider = false;
            }

        //LIDER ZONA
        }else if(Auth::user()->savk_principal != 1 && Auth::user()->id_grupo == 46) {
            $centroCosto = SavkLideresZonas::join('grupos_sub_puntos', 'grupos_sub_puntos.grupo_punto_id', 'id_grupos_puntos')
            ->where('id_usuario',auth()->user()->id)
            ->pluck('punto_id')->toArray();

            if ($centroCosto) {
                $data = $data->join('punto_evaluacion as pe', 'pe.id', '=', 'ci.id_cliente')
                    ->whereIn('pe.id', $centroCosto)
                    ->paginate(10);
            }else{
                $validarLider = false;
            }

        //LIDER CENTRO DE COSTO
        }else if(Auth::user()->savk_principal != 1 && Auth::user()->id_grupo == 47) {
            $centroCosto = SavkLideresCentroDeCostos::where('id_usuario',auth()->user()->id)->pluck('id_centro_de_costo')->toArray();

            if ($centroCosto) {
                $data = $data->join('punto_evaluacion as pe', 'pe.id', '=', 'ci.id_cliente')
                    ->whereIn('pe.id', $centroCosto)
                    ->paginate(10);
            }else{
                $validarLider = false;
            }

        //ASESOR
        // }else if(Auth::user()->id_grupo == 30 || Auth::user()->id_grupo == 39) {
        }else if($permisos->pluck('evento')->contains('ent-asi-crear_asistida') && Auth::user()->id != 4301) {
            $data = $data->where('id_asesor', Auth::user()->id)
                    ->paginate(10);
        }

        $evaluacionIniciada = new CaEvaluacionIniciada;

        if((Auth::user()->savk_principal != 1 && Auth::user()->id_grupo != 44 && Auth::user()->id_grupo != 45 && Auth::user()->id_grupo != 46 && Auth::user()->id_grupo != 47 && !$permisos->pluck('evento')->contains('ent-asi-crear_asistida')) || ($validarLider == false)) {
            $msj = null;
            if ($validarLider == false) {
                switch (Auth::user()->id_grupo) {
                    case 44:
                        $msj = 'No tienes Grupo empresa asignado, solo se visualizará las capacitaciones a las que has asistido.';
                        break;
                    case 45:
                        $msj = 'No tienes Empresa asignada, solo se visualizará las capacitaciones a las que has asistido.';
                        break;
                    case 47:
                        $msj = 'No tienes Centro de costo asignado, solo se visualizará las capacitaciones a las que has asistido.';
                        break;
                    default:
                        $msj = null;
                        break;
                }
            }

            $data = $data->join('punto_evaluacion as pe', 'pe.id', '=', 'ci.id_cliente')
                ->leftJoin('ca_capacitaciones_asistidas_asistentes as cas', 'cas.id_capacitacion_asistida', 'ci.id')
                ->where('cas.id_usuario', Auth::user()->id )
                ->paginate(10);

            foreach ($data as $d) {
                $d->asistentes = DB::select("SELECT
                    usu.id AS ID_ASISTENTE,
                    usu.nombre_com AS NOMBRE_ASISTENTE,
                    usu.email AS CORREO_ASISTENTE,
                    usu.codigo AS NUMERO_DOC_ASISTENTE,
                    cap.signature_path,
                    (
                        SELECT id FROM ca_evaluacion_iniciada ei
                        where ei.id_capacitacion = capAsis.id_capacitacion && ei.id_usuario = usu.id && ei.last_approved = 1 && ei.certificado = 1 limit 1
                    ) as id_evaluacion,
                    (
                        SELECT certificado FROM ca_evaluacion_iniciada ei
                        where ei.id_capacitacion = capAsis.id_capacitacion && ei.id_usuario = usu.id && ei.last_approved = 1 limit 1
                    ) as certifica
                    FROM ca_capacitaciones_asistidas_asistentes cap
                    INNER JOIN usuarios usu ON usu.id = cap.id_usuario
                    INNER JOIN ca_cap_asistidas capAsis ON capAsis.id = cap.id_capacitacion_asistida
                    WHERE cap.id_capacitacion_asistida = ".$d->id." and usu.id = ".Auth::user()->id
                );

                foreach ($d->asistentes as $key => $asistente) {
                    $asistente->idsEvaluacion = DB::select(
                        "SELECT id FROM ca_evaluacion_iniciada ei
                        where ei.id_capacitacion = $d->id_capacitacion && ei.id_usuario = $asistente->ID_ASISTENTE && ei.last_approved = 1"
                    );

                    $asistente->intentos = (object) [
                        'intentos' => 0,
                        'gano' => 0
                    ];

                    foreach ($asistente->idsEvaluacion as $key5 => $evaluacion) {
                        $evalIni = CaEvaluacionIniciada::find($evaluacion->id);
                        $asistente->intentos = $evaluacionIniciada->intentosEvaluacion($evalIni->id_capacitacion, $evalIni->id_usuario, $evalIni->id_modulo);
                        $asistente->id_modulo = $evalIni->id_modulo;
                    }
                }
            }

            $msj = $msj == null ? 'Datos encontrados' : $msj;
            return $this->ExitProgram(206, $msj, $data);
        }

        foreach ($data as $d) {
            $d->img = DB::select(
                "SELECT id, SUBSTRING(path, 2) as path FROM ca_cap_asistidasimg
                WHERE id_cap_asistida = $d->id;"
            );

            if ($d->tipoLetras == 'Privada' ){
                $d->asistentes = DB::select("SELECT
                usu.id AS ID_ASISTENTE,
                usu.nombre_com AS NOMBRE_ASISTENTE,
                usu.email AS CORREO_ASISTENTE,
                usu.codigo AS NUMERO_DOC_ASISTENTE,
                cap.signature_path,
                (
                    SELECT id FROM ca_evaluacion_iniciada ei
                    where ei.id_capacitacion = capAsis.id_capacitacion && ei.id_usuario = usu.id && ei.last_approved = 1 && ei.certificado = 1 limit 1
                ) as id_evaluacion,
                (
                    SELECT certificado FROM ca_evaluacion_iniciada ei
                    where ei.id_capacitacion = capAsis.id_capacitacion && ei.id_usuario = usu.id && ei.last_approved = 1 limit 1
                ) as certifica
                FROM ca_capacitaciones_asistidas_asistentes cap
                INNER JOIN usuarios usu ON usu.id = cap.id_usuario
                INNER JOIN ca_cap_asistidas capAsis ON capAsis.id = cap.id_capacitacion_asistida
                WHERE cap.id_capacitacion_asistida = ".$d->id);

                foreach ($d->asistentes as $key => $asistente) {
                    $asistente->idsEvaluacion = DB::select(
                        "SELECT id FROM ca_evaluacion_iniciada ei
                        where ei.id_capacitacion = $d->id_capacitacion && ei.id_usuario = $asistente->ID_ASISTENTE && ei.last_approved = 1"
                    );

                    $asistente->intentos = (object) [
                        'intentos' => 0,
                        'gano' => 0
                    ];

                    foreach ($asistente->idsEvaluacion as $key5 => $evaluacion) {
                        $evalIni = CaEvaluacionIniciada::find($evaluacion->id);
                        $asistente->intentos = $evaluacionIniciada->intentosEvaluacion($evalIni->id_capacitacion, $evalIni->id_usuario, $evalIni->id_modulo);
                        $asistente->id_modulo = $evalIni->id_modulo;
                    }
                }

            }else{
                $d->asistentes = DB::select("SELECT
                usu.id AS ID_ASISTENTE,
                usu.nombre AS NOMBRE_ASISTENTE,
                usu.email AS CORREO_ASISTENTE,
                usu.documento AS NUMERO_DOC_ASISTENTE,
                cap.signature_path,
                (
                    SELECT id FROM ca_evaluacion_iniciada ei
                    where ei.id_capacitacion = capAsis.id_capacitacion && ei.id_asistente = usu.id && ei.last_approved = 1 && ei.certificado = 1 limit 1
                ) as id_evaluacion,
                (
                    SELECT certificado FROM ca_evaluacion_iniciada ei
                    where ei.id_capacitacion = capAsis.id_capacitacion && ei.id_asistente = usu.id && ei.last_approved = 1 limit 1
                ) as certifica
                FROM ca_capacitaciones_asistidas_asistentes cap
                INNER JOIN ca_asistentes usu ON usu.id = cap.id_asistente
                INNER JOIN ca_cap_asistidas capAsis ON capAsis.id = cap.id_capacitacion_asistida
                WHERE cap.id_capacitacion_asistida = ".$d->id);

                foreach ($d->asistentes as $key => $asistente) {
                    $asistente->idsEvaluacion = DB::select(
                        "SELECT id FROM ca_evaluacion_iniciada ei
                        where ei.id_capacitacion = $d->id_capacitacion && ei.id_asistente = $asistente->ID_ASISTENTE && ei.last_approved = 1"
                    );

                    $asistente->intentos = (object) [
                        'intentos' => 0,
                        'gano' => 0
                    ];

                    foreach ($asistente->idsEvaluacion as $key5 => $evaluacion) {
                        $evalIni = CaEvaluacionIniciada::find($evaluacion->id);
                        $asistente->intentos = $evaluacionIniciada->intentosEvaluacion($evalIni->id_capacitacion, $evalIni->id_asistente, $evalIni->id_modulo, false, 'publica');
                        $asistente->id_modulo = $evalIni->id_modulo;
                    }
                }
            }
        }

        // $msj = $msj == null ? 'Datos encontrados' : $msj;
        return $this->ExitProgram(206, 'Datos encontrados', $data);
    }

    public function SaveAsistida(Request $request)
    {
        $url = env('URL');

        if ($request->update['val']) { //MODIFICAR CAPACITACIÓN ASISTIDA
            $new = CaCapAsistidas::where('id', '=', $request->id_asistida['val'])->update([
                    'fecha_agendamiento' => $request->fecha_agendamiento['val'],
                    'id_capacitacion' => $request->id_capacitacion['val'],
                    //'id_asesor' => auth()->user()->id,
                    'modalidad' => $request->modalidad['val'],
                    'tipo' => $request->tipo['val'],
                    'id_cliente' => $request->id_cliente['val'] == 'null' ? null : $request->id_cliente['val'],
                    'duracion' => $request->duracion['val'],
                    'anfitrion_cliente' => $request->anfitrion['val'] == 'null' || '' ? null : $request->anfitrion['val'],
                    'observacion' => $request->observacion['val'] == 'null' || '' ? null : $request->observacion['val']
            ]);

            return response()->json([
                'status' => 201,
                'msg' => 'Se ha modificado la capacitación asistida.',
                'data' => $request->id_asistida['val']
            ]);

        }else{ //CREAR CAPACITACIÓN ASISTIDA
            $new = CaCapAsistidas::create([
                'fecha_agendamiento' => $request->fecha_agendamiento['val'],
                'id_capacitacion' => $request->id_capacitacion['val'],
                'id_asesor' => auth()->user()->id,
                'modalidad' => $request->modalidad['val'],
                'tipo' => $request->tipo['val'],
                'id_cliente' => $request->id_cliente['val'] == 'null' ? null : $request->id_cliente['val'],
                'duracion' => $request->duracion['val'],
                'link' => '',
                'anfitrion_cliente' => $request->anfitrion['val'] == 'null' || '' ? null : $request->anfitrion['val'],
                'observacion' => $request->observacion['val'] == 'null' || '' ? null : $request->observacion['val']
            ]);

            $idTrainig = Crypt::encryptString($new->id);
            $link = CropLink($url . 'capacitaciones/registrar-asistencia/' . $idTrainig);

            $new->link = $link;
            $new->save();

            return response()->json([
                'status' => 201,
                'msg' => 'Se ha creado la capacitación asistida.',
                'data' => $new->id
            ]);
        }


    }

    public function loginAsistente(Request $request){
        // $user = $request->user['value'];
        $pass = $request->password['value'];

        // if ($user == null) {
            $id = $request->id['value'];
            $usuario = User::where('codigo', $id)->first();
            $user = $usuario->email;
        // }

        Auth::logout(); //cerramos cualquier session activa para iniciar la del asistente
        if (
            Auth::attempt(['email' => $user, 'password' => $pass])
        ) {
            return response()->json([
                'status' => 200,
                'msg' => 'Ha iniciado sesión.'
            ]);
        }
        return response()->json([
            'status' => 202,
            'msg' => 'Usuario y/o contraseña incorrectos.'
        ], 202);

    }

    public function sendCertificado(Request $request){
        $email = $request->email['value'];
        $idIniciada = $request->idIniciada['value'];

        $evaluacionIniciada = new CaEvaluacionIniciada();
        $certificado = $evaluacionIniciada->ByIdCertifiedIdPublic($idIniciada);

        $capacitacion = new CaCapacitaciones;
        $capacitacion = $capacitacion->mainAccountTraining($certificado->id_capacitacion);

        if ($capacitacion->main_account_id == 1) {
            if ($certificado->certificado_manual == 1 || $certificado->certificado_manual == 2) { //ASISTENCIA
                $pdf = Pdf::loadView('trainings::pdf.diplomaAsistencia', ['data' => $certificado]);
            }else{//CAPACITACION
                $pdf = Pdf::loadView('trainings::pdf.diploma', ['data' => $certificado]);
            }
        }else{
            if ($certificado->certificado_manual == 1 || $certificado->certificado_manual == 2) { //ASISTENCIA
                $pdf = Pdf::loadView('trainings::pdf.diplomaCapacitacionesPropiasAsistencia', ['data' => $certificado, 'Img' => $capacitacion->imagen, 'img_certificado' => $capacitacion->img_certificado]);
            }else{//CAPACITACION
                $pdf = Pdf::loadView('trainings::pdf.diplomaCapacitacionesPropias', ['data' => $certificado, 'Img' => $capacitacion->imagen, 'img_certificado' => $capacitacion->img_certificado]);
            }
        }

        // Guarda el archivo PDF temporalmente
        $folderPath = storage_path('app/public/temp/');
        if (!File::exists($folderPath)) {
            // Crear la carpeta si no existe
            File::makeDirectory($folderPath, 0755, true);
        }
        $tempFilePath = storage_path('app/public/temp/' . $certificado->nombre . '.pdf');
        $pdf->setPaper('A4', 'landscape')->save($tempFilePath);

        // Envía el correo con el archivo adjunto
        $enviado = Mail::to($email)->send(new DiplomaMail($certificado, $tempFilePath));

        // Elimina el archivo PDF temporal
        unlink($tempFilePath);

        if (empty(Mail::failures())) {
            return response()->json([
                'status' => 200,
                'msg' => 'Se envió el correo exitosamente.'
            ]);
        } else {
            return response()->json([
                'status' => 202,
                'msg' => 'No fue posible enviar el correo.'
            ], 202);
        }

        // return $pdf->setPaper('A4', 'landscape')->download($certificado->nombre . '.pdf');
        // if (
        //     Auth::attempt(['email' => $user, 'password' => $pass])
        // ) {
        //     return response()->json([
        //         'status' => 200,
        //         'msg' => 'Ha iniciado sesión.'
        //     ]);
        // }


    }

    public function CargarAsisAsistidaImg(Request $request){
        $archivos = $request->file('archivos');
        $idAsistida = $request->idAsistida;

        $folderPath = storage_path('app/public/evidencias');

        if (!File::exists($folderPath)) {
            // Crear la carpeta si no existe
            File::makeDirectory($folderPath, 0755, true);
        }

        foreach ($archivos as $key => $archivo) {
            $rutaAlmacenamiento = Storage::disk('public')->put('evidencias', $archivo);

            // Obtener la URL pública del archivo guardado
            $urlImagen = Storage::disk('public')->url($rutaAlmacenamiento);
            $urlImagen = str_replace(url('/'), '', $urlImagen);
            CaCapAsistidasimg::create([
                'path'        => $urlImagen,
                'id_cap_asistida' => $idAsistida
            ]);
        }

        return $this->ExitProgram(201, 'Evidencia guardada, has finalizado la creación.');

    }

    public function EliminarAsisAsistidaImg(Request $request){
        try {
            $id = $request->get('id');

            CaCapAsistidasimg::where('id', $id)
            ->delete();

            return $this->ExitProgram(201, 'Evidencia eliminada.');
        } catch (\Error $e) {
            return $this->ExitProgram(202, 'No se puede eliminadar evidencia.');
        }
    }

    public function GenerarQr(Request $request){
        $link = $request->get('link');

        if (stripos($link, 'capacitaciones') !== false) {
            $link = CropLink($link);
        }

        $folderPath = storage_path('app/public/qr_codes');

        if (!File::exists($folderPath)) {
            File::makeDirectory($folderPath, 0755, true);
        }

        $nombre = 'qr_'.uniqid().'.png';
        QrCode::format('png')->size(700)->generate($link, "../public/storage/qr_codes/$nombre");

        return $this->ExitProgram(200, 'Se genero Qr', $nombre);
    }

    public function CargarAsisAsistidaExcel(Request $request)
    {
        try {


            $archivo = $_FILES['archivo']['tmp_name'];
            $idAsistida = $request->idAsistida;

            // Carga el archivo XLSX utilizando PhpSpreadsheet
            $spreadsheet = IOFactory::load($archivo);
            $worksheet = $spreadsheet->getActiveSheet();

            // Recorre las filas y columnas para obtener los datos
            $data = [];
            foreach ($worksheet->getRowIterator() as $row) {
                $rowData = [];
                foreach ($row->getCellIterator() as $cell) {
                    $value = $cell->getValue();
                    $rowData[] = $value;
                    if (!empty($value)) {
                    }
                }
                if (!empty($rowData) && isset($rowData[0])) {
                    $data[] = $rowData;
                }
            }

            if (sizeof($data) <= 1) {
                return $this->ExitProgram(202, 'El documento no tiene información válida, por favor completar y volver a intentar el cargue.');
            }

            $existeRegistro = 0;
            $capacitacionAsistida = CaCapAsistidas::where('id', $idAsistida)->first();

            foreach ($data as $key => $row) {
                if ($key != 0) { //Omitimos el encabezado
                    $nombreCom = $row[0];
                    $documento = $row[1];
                    $email = isset($row[2]) ? $row[2] : null;

                    if ($capacitacionAsistida->tipo == 2) { //PRIVADA
                        $email = $email != null ? $email : $documento.'notiene@savk.com.co';

                        if ($documento == null) {
                            return $this->ExitProgram(202, 'El documento es obligatorio por favor completar y volver a intentar el cargue.');
                        }
                        if (User::where('codigo', $documento)->exists()) {
                            //USUARIO EXISTENTE
                            $user = User::where('codigo', $documento)->first();
                        } else {
                            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                return $this->ExitProgram(202, 'El correo '.$email.' no es valido por favor cambiarlo y volver a intentar el cargue.');
                            }

                            //USUARIO NO EXISTENTE
                            if (User::where('email', $email)->exists()) {
                                return $this->ExitProgram(202, 'El correo '.$email.' ya se encuentra en uso por otra cuenta, por favor cambiarlo y volver a intentar el cargue.');
                            }

                            $usuario = explode(" ", $nombreCom);
                            $usuario = $usuario[0];

                            $centroCosto = PuntoEvaluacion::where('id', $capacitacionAsistida->id_cliente)->first();

                            $user = User::create([
                                'codigo'        => $documento,
                                'nombre_com'    => $nombreCom,
                                'email'         => $email,
                                'password'      => \Hash::make($documento),
                                'usuario'       => $usuario . $documento,
                                'id_grupo'        => '48',
                                'estado'        => '1',
                                'ultimo_acceso' => now(),
                                'main_account_id' => $centroCosto->main_account_id,
                                'id_punto' => $capacitacionAsistida->id_cliente,
                            ]);
                            $this->insertPermisos($user);
                        }

                        $validarRegistro = CaCapacitacionesAsistidasAsistentes::where([
                            ['id_capacitacion_asistida', $idAsistida],
                            ['id_usuario', $user->id],
                        ])->exists();

                        if ($validarRegistro) {
                            $existeRegistro += 1;
                        } else {
                            $capacitacion = CaCapacitaciones::find($capacitacionAsistida->id_capacitacion);

                            if ($capacitacion->aplica_certificado == 1) { //SE LLENA SOLO SI LA CAP GENERA CERTIFICADO
                                //SE CREA REGISTRO MANUAL 2 PARA DESCARGAR CERTIFICADO POR ASISTENCIA
                                $now = Carbon::now()->format('Y-m-d H:i:s');
                                $caTestInit = new CaEvaluacionIniciada();
                                $array_test_init = [
                                    'id_capacitacion' => $capacitacionAsistida->id_capacitacion,
                                    'id_usuario' => $user->id,
                                    'fecha_inicio' => $now,
                                    'fecha_terminada' => $now,
                                    'certificado' => '1',
                                    'certificado_manual' => '2',
                                    'last_approved' => '1',
                                ];
                                $caTestInit->fill($array_test_init);
                                $caTestInit->save();
                            }

                            CaCapacitacionesAsistidasAsistentes::create([
                                'id_capacitacion_asistida' => $idAsistida,
                                'id_usuario' => $user->id,
                            ]);
                        }
                    }else if ($capacitacionAsistida->tipo == 1){ //PUBLICA
                        $empresa = $row[3];

                        if ($email != null && CaAsistentes::where('email', $email)->exists()) {
                            //Asistente EXISTENTE
                            $user = CaAsistentes::where('email', $email)->first();
                        } else {
                            //Asistente NO EXISTENTE
                            $user = CaAsistentes::create([
                                'documento' => $documento,
                                'nombre'    => $nombreCom,
                                'email'     => $email,
                                'empresa'   => $empresa
                            ]);
                        }

                        $validarRegistro = CaCapacitacionesAsistidasAsistentes::where([
                            ['id_capacitacion_asistida', $idAsistida],
                            ['id_asistente', $user->id],
                        ])->exists();

                        if ($validarRegistro) {
                            $existeRegistro += 1;
                        } else {
                            $capacitacion = CaCapacitaciones::find($capacitacionAsistida->id_capacitacion);

                            if ($capacitacion->aplica_certificado == 1) { //SE LLENA SOLO SI LA CAP GENERA CERTIFICADO
                                //SE CREA REGISTRO MANUAL 2 PARA DESCARGAR CERTIFICADO POR ASISTENCIA
                                $now = Carbon::now()->format('Y-m-d H:i:s');
                                $caTestInit = new CaEvaluacionIniciada();
                                $array_test_init = [
                                    'id_capacitacion' => $capacitacionAsistida->id_capacitacion,
                                    'id_asistente' => $user->id,
                                    'fecha_inicio' => $now,
                                    'fecha_terminada' => $now,
                                    'certificado' => '1',
                                    'certificado_manual' => '2',
                                    'last_approved' => '1',
                                ];
                                $caTestInit->fill($array_test_init);
                                $caTestInit->save();
                            }

                            CaCapacitacionesAsistidasAsistentes::create([
                                'id_capacitacion_asistida' => $idAsistida,
                                'id_asistente' => $user->id,
                            ]);
                        }
                    }
                }
            }

            if ($existeRegistro == 0) {
                return $this->ExitProgram(201, $this->MessageResponse('', 206, 'Se ha cargado asistentes.'));
            } else {
                return $this->ExitProgram(201, $this->MessageResponse('', 206, 'Alguno(s) de los asistentes ya se encontraban en nuestro sistema, los demás fueron agregados con éxito.'));
            }

            // return $this->ExitProgram(206, $this->MessageResponse('', 206, 'El contenido ha sido actualizado correctamente.'), $last_content);
            // return $this->ExitProgram(201, $this->MessageResponse('', 206, 'Se ha cargado asistentes.'));
        } catch (\Exception $ex) {
            return $this->ExitProgram(202, 'El documento no es válido o presenta fallas, por favor cambiarlo y volver a intentar el cargue.');
        }
    }
    public function CargarAsisAsistida(Request $request)
    {

        $existeRegistro = 0;
        foreach ($request->all() as $key => $value) {
            if (is_array($value)) {
                $nombreCom = $value['nombreCom']['val'];
                $documento = $value['documento']['val'];
                $email = $value['email']['val'];
                $idAsistida = $value['idAsistida']['val'];
            }else{
                $nombreCom = $value['nombreCom']['val'];
                $documento = $value['documento']['val'];
                $email = $value['email']['val'];
                $idAsistida = $value['idAsistida']['val'];
            }

            $capacitacionAsistida = CaCapAsistidas::where('id', $idAsistida)->first();

            if ($capacitacionAsistida->tipo == 2) { //PRIVADA
                if (User::where('codigo', $documento)->exists()) {
                    //USUARIO EXISTENTE
                    $user = User::where('codigo', $documento)->first();
                } else {
                    //USUARIO NO EXISTENTE

                    if ($email == '' || $email == null) {
                        $email = $documento.'notiene@savk.com.co';
                    }

                    if (User::where('email', $email)->exists()) {
                        return response()->json([
                            'status' => 202,
                            'msg' => 'El correo '.$email.' ya se encuentra en uso por otra cuenta, por favor cambiarlo y volver a intentar el cargue.'
                        ]);
                    }

                    $usuario = explode(" ", $nombreCom);
                    $usuario = $usuario[0];

                    $capacitacionAsistida = CaCapAsistidas::where('id', $idAsistida)->first();
                    $centroCosto = PuntoEvaluacion::where('id', $capacitacionAsistida->id_cliente)->first();

                    $user = User::create([
                        'codigo'        => $documento,
                        'nombre_com'    => $nombreCom,
                        'email'         => $email,
                        'password'      => \Hash::make($documento),
                        'usuario'       => $usuario . $documento,
                        'id_grupo'        => '48',
                        'estado'        => '1',
                        'ultimo_acceso' => now(),
                        'main_account_id' => $centroCosto->main_account_id,
                        'id_punto' => $capacitacionAsistida->id_cliente,
                    ]);
                    $this->insertPermisos($user);
                }

                $validarRegistro = CaCapacitacionesAsistidasAsistentes::where([
                    ['id_capacitacion_asistida', $idAsistida],
                    ['id_usuario', $user->id],
                ])->exists();

                if ($validarRegistro) {
                    $existeRegistro += 1;
                } else {
                    $capacitacion = CaCapacitaciones::find($capacitacionAsistida->id_capacitacion);

                    if ($capacitacion->aplica_certificado == 1) { //SE LLENA SOLO SI LA CAP GENERA CERTIFICADO
                        //SE CREA REGISTRO MANUAL 2 PARA DESCARGAR CERTIFICADO POR ASISTENCIA SINO TIENE CERTIFICADO EN ESA CAPACITACION
                        $cap =CaEvaluacionIniciada::where([
                            ['id_capacitacion', $capacitacionAsistida->id_capacitacion],
                            ['id_usuario', $user->id],
                            ['certificado','1'],
                            ['last_approved' , '1']
                        ])->exists();

                        if ($cap == false) {
                            $now = Carbon::now()->format('Y-m-d H:i:s');
                            $caTestInit = new CaEvaluacionIniciada();
                            $array_test_init = [
                                'id_capacitacion' => $capacitacionAsistida->id_capacitacion,
                                'id_usuario' => $user->id,
                                'fecha_inicio' => $now,
                                'fecha_terminada' => $now,
                                'certificado' => '1',
                                'certificado_manual' => '2',
                                'last_approved' => '1',
                            ];
                            $caTestInit->fill($array_test_init);
                            $caTestInit->save();
                        }
                    }

                    CaCapacitacionesAsistidasAsistentes::create([
                        'id_capacitacion_asistida' => $idAsistida,
                        'id_usuario' => $user->id,
                    ]);
                }
            }else if ($capacitacionAsistida->tipo == 1) { //publica
                $empresa = $value['empresa']['val'];
                if (CaAsistentes::where('email', $email)->exists()) {
                    //Asistente EXISTENTE
                    $user = CaAsistentes::where('email', $email)->first();
                } else {
                    //Asistente NO EXISTENTE
                    $user = CaAsistentes::create([
                        'documento' => $documento,
                        'nombre'    => $nombreCom,
                        'email'     => $email,
                        'empresa'   => $empresa
                    ]);
                }

                $validarRegistro = CaCapacitacionesAsistidasAsistentes::where([
                    ['id_capacitacion_asistida', $idAsistida],
                    ['id_asistente', $user->id],
                ])->exists();

                if ($validarRegistro) {
                    $existeRegistro += 1;
                } else {

                    $capacitacion = CaCapacitaciones::find($capacitacionAsistida->id_capacitacion);

                    if ($capacitacion->aplica_certificado == 1) { //SE LLENA SOLO SI LA CAP GENERA CERTIFICADO

                        //SE CREA REGISTRO MANUAL 2 PARA DESCARGAR CERTIFICADO POR ASISTENCIA
                        $now = Carbon::now()->format('Y-m-d H:i:s');
                        $caTestInit = new CaEvaluacionIniciada();
                        $array_test_init = [
                            'id_capacitacion' => $capacitacionAsistida->id_capacitacion,
                            'id_asistente' => $user->id,
                            'fecha_inicio' => $now,
                            'fecha_terminada' => $now,
                            'certificado' => '1',
                            'certificado_manual' => '2',
                            'last_approved' => '1',
                        ];
                        $caTestInit->fill($array_test_init);
                        $caTestInit->save();
                    }
                    CaCapacitacionesAsistidasAsistentes::create([
                        'id_capacitacion_asistida' => $idAsistida,
                        'id_asistente' => $user->id,
                    ]);
                }
            }
        }

        if ($existeRegistro == 0) {
            return response()->json([
                'status' => 201,
                'msg' => 'Se ha cargado asistentes.'
            ]);
        } else {
            return response()->json([
                'status' => 201,
                'msg' => 'Alguno(s) de los asistentes ya se encontraban en nuestro sistema, los demás fueron agregados con éxito.'
            ]);
        }
    }

    public function saveAsisAsistida(Request $request)
    {
        try {
            $existeRegistro = 0;

            $nombreCom = $request->nombreCom['value'];
            $documento = $request->documento['value'];
            $email = $request->email['value'];
            $idAsistida = $request->idAsistida['value'];
            $cc = $request->cc['value'];

            $capacitacionAsistida = CaCapAsistidas::where('id', $idAsistida)->first();

            if (User::where('codigo', $documento)->exists()) {
                //USUARIO EXISTENTE
                $user = User::where('codigo', $documento)->first();
            } else {
                //USUARIO NO EXISTENTE
                $usuario = explode(" ", $nombreCom);
                $usuario = $usuario[0];

                if ($email == null) {
                    //PROCEDEMOS A CREAR UN CORREO ALEATORIO CUANDO NO ES ENVIADO UNO
                    $email = $documento.'notiene@savk.com.co';
                }

                if (User::where('email', $email)->exists()) {
                    return response()->json([
                        'status' => 202,
                        'msg' => 'El correo suministrado ya se encuentra en uso por otra cuenta.',
                    ]);
                }

                $centroCosto = PuntoEvaluacion::where('id', $cc)->first();

                $user = User::create([
                    'codigo'        => $documento,
                    'nombre_com'    => $nombreCom,
                    'email'         => $email,
                    'password'      => \Hash::make($documento),
                    'usuario'       => $usuario . $documento,
                    'estado'        => '1',
                    'id_grupo'        => '48',
                    'ultimo_acceso' => now(),
                    'main_account_id' => $centroCosto->main_account_id,
                    'id_punto' => $cc,
                ]);
                $this->insertPermisos($user);
            }

            $validarRegistro = CaCapacitacionesAsistidasAsistentes::where([
                ['id_capacitacion_asistida', $idAsistida],
                ['id_usuario', $user->id],
            ])->exists();

            if ($validarRegistro) {
                $existeRegistro += 1;
                $asistencia = CaCapacitacionesAsistidasAsistentes::where([
                                ['id_capacitacion_asistida', $idAsistida],
                                ['id_usuario', $user->id],
                            ])->first();
            } else {
                $asistencia = CaCapacitacionesAsistidasAsistentes::create([
                    'id_capacitacion_asistida' => $idAsistida,
                    'id_usuario' => $user->id,
                ]);
            }

            //-----------
            $capacitacion = CaCapacitaciones::where('id', $capacitacionAsistida->id_capacitacion)->first();

            $preguntas = CaPreguntas::where('id_capacitacion', $capacitacionAsistida->id_capacitacion)->exists(); //Validar hay evaluacion en general

            $caPreguntas =  new CaPreguntas;
            $preguntasModulos = $caPreguntas->existPreguntasByTrainig($capacitacionAsistida->id_capacitacion);

            $evaluacion = true;
            $id_evaluacion_iniciada = null;

            if ($capacitacion->aplica_evaluacion == 2  || ($capacitacion->evaluara_por == 1 && $preguntas == false) || ($capacitacion->evaluara_por == 2 && $preguntasModulos == 0)) {

                $validacionExisteEvaluacion = CaEvaluacionIniciada::where([
                    ['id_capacitacion', $capacitacionAsistida->id_capacitacion],
                    ['id_usuario', $user->id],
                ])->exists();

                if (!$validacionExisteEvaluacion) {
                    if ($capacitacion->aplica_certificado == 1) {
                        if ($capacitacion->permitir_certificacion == 1) {
                            //CERTIFICA POR CAPACITACION
                            $now = Carbon::now()->format('Y-m-d H:i:s');
                            $caTestInit = new CaEvaluacionIniciada();
                            $array_test_init = [
                                'id_capacitacion' => $capacitacionAsistida->id_capacitacion,
                                'id_usuario' => $user->id,
                                'fecha_inicio' => $now,
                                'fecha_terminada' => $now,
                                'certificado' => '1',
                                'certificado_manual' => '1',
                                'last_approved' => '1',

                            ];
                            $caTestInit->fill($array_test_init);
                            $caTestInit->save();
                            $id_evaluacion_iniciada = $caTestInit->id;
                        }else{
                            //CERTIFICA POR MODULOS
                            $arregloIniciadas = [];
                            $modulos = CaModulos::where('id_capacitacion', $capacitacionAsistida->id_capacitacion)->get();
                            foreach ($modulos as $key => $modulo) {
                                $now = Carbon::now()->format('Y-m-d H:i:s');
                                $caTestInit = new CaEvaluacionIniciada();
                                $array_test_init = [
                                    'id_capacitacion' => $capacitacionAsistida->id_capacitacion,
                                    'id_modulo' => $modulo->id,
                                    'id_usuario' => $user->id,
                                    'fecha_inicio' => $now,
                                    'fecha_terminada' => $now,
                                    'certificado' => '1',
                                    'certificado_manual' => '1',
                                    'last_approved' => '1',
                                ];
                                $caTestInit->fill($array_test_init);
                                $caTestInit->save();
                                $arregloIniciadas[] = $caTestInit->id;
                            }
                            $id_evaluacion_iniciada = $arregloIniciadas;
                        }

                    }else{
                        //NO CERTIFICA
                        $id_evaluacion_iniciada = 0;
                    }

                }else{
                    if ($capacitacion->permitir_certificacion == 1) {
                        $caTestInit = CaEvaluacionIniciada::where([
                            ['id_capacitacion', $capacitacionAsistida->id_capacitacion],
                            ['id_usuario', $user->id],
                        ])->first();
                        $id_evaluacion_iniciada = $caTestInit->id;
                    }else{
                        $arregloIniciadas = [];
                        $caTestInit = CaEvaluacionIniciada::where([
                            ['id_capacitacion', $capacitacionAsistida->id_capacitacion],
                            ['id_usuario', $user->id],
                        ])->get();

                        foreach ($caTestInit as $key => $init) {
                            $arregloIniciadas[] = $init->id;
                        }
                        $id_evaluacion_iniciada = $arregloIniciadas;
                    }


                }
                $evaluacion = false;
            }

            //-----------
            $login = false;
            // if (Auth::check()) {
            //     $login = true;
            // }

            if ($existeRegistro == 0) {
                return response()->json([
                    'status' => 201,
                    'msg' => 'Se ha registrado asistente correctamente.',
                    'data' => Crypt::encryptString($capacitacionAsistida->id_capacitacion),
                    'evaluacion' => $evaluacion,
                    'asistente' => $user->id,
                    'iniciada' => $id_evaluacion_iniciada,
                    'login' => $login,
                    'asistencia' => $asistencia->id,
                    'signature' => $asistencia->signature_path
                ]);
            } else {
                return response()->json([
                    'status' => 201,
                    'msg' => 'El asistente ya se encuentra registrado.',
                    'data' => Crypt::encryptString($capacitacionAsistida->id_capacitacion),
                    'evaluacion' => $evaluacion,
                    'asistente' => $user->id,
                    'iniciada' => $id_evaluacion_iniciada,
                    'login' => $login,
                    'asistencia' => $asistencia->id,
                    'signature' => $asistencia->signature_path
                ]);
            }
        } catch (\Exception $ex) {
            return response()->json([
                'status' => 500,
                'msg' => 'Algo salio mal.'
            ]);
        }
    }

    public function saveAsisAsistidaPublica(Request $request)
    {
        try {
            $existeRegistro = 0;

            $nombreCom = $request->nombreCom['value'];
            $documento = $request->documento['value'] == 'null' ? null : $request->documento['value'];
            $email = $request->email['value'];
            $empresa = $request->empresa['value'] == 'null' ? null : $request->empresa['value'];
            $idAsistida = $request->idAsistida['value'];

            $capacitacionAsistida = CaCapAsistidas::where('id', $idAsistida)->first();

            $ca_asistentes = CaAsistentes::where('email', $email)->first();

            if ($ca_asistentes == null) {
                $asistente = new CaAsistentes();
                $asistente->fill([
                    'documento' => $documento,
                    'nombre' => $nombreCom,
                    'email' => $email,
                    'empresa' => $empresa
                ]);

                $asistente->save();
                $ca_asistentes = CaAsistentes::where('email', $email)->first();
            }

            $validarRegistro = CaCapacitacionesAsistidasAsistentes::where([
                ['id_capacitacion_asistida', $idAsistida],
                ['id_asistente', $ca_asistentes->id],
            ])->exists();

            if ($validarRegistro) {
                $existeRegistro += 1;
                $asistencia = CaCapacitacionesAsistidasAsistentes::where([
                    ['id_capacitacion_asistida', $idAsistida],
                    ['id_asistente', $ca_asistentes->id],
                ])->first();
            } else {
                $asistencia = CaCapacitacionesAsistidasAsistentes::create([
                    'id_capacitacion_asistida' => $idAsistida,
                    'id_asistente' => $ca_asistentes->id,
                ]);
            }

            $capacitacion = CaCapacitaciones::where('id', $capacitacionAsistida->id_capacitacion)->first();

            $preguntas = CaPreguntas::where('id_capacitacion', $capacitacionAsistida->id_capacitacion)->exists(); //Validar hay evaluacion en general

            $caPreguntas =  new CaPreguntas;
            $preguntasModulos = $caPreguntas->existPreguntasByTrainig($capacitacionAsistida->id_capacitacion);

            $evaluacion = true;
            $id_evaluacion_iniciada = null;

            if ($capacitacion->aplica_evaluacion == 2  || ($capacitacion->evaluara_por == 1 && $preguntas == false) || ($capacitacion->evaluara_por == 2 && $preguntasModulos == 0)) {

                $validacionExisteEvaluacion = CaEvaluacionIniciada::where([
                    ['id_capacitacion', $capacitacionAsistida->id_capacitacion],
                    ['id_asistente', $ca_asistentes->id],
                ])->exists();

                if (!$validacionExisteEvaluacion) {
                    if ($capacitacion->aplica_certificado == 1) {
                        if ($capacitacion->permitir_certificacion == 1) {
                            //CERTIFICA POR CAPACITACION
                            $now = Carbon::now()->format('Y-m-d H:i:s');
                            $caTestInit = new CaEvaluacionIniciada();
                            $array_test_init = [
                                'id_capacitacion' => $capacitacionAsistida->id_capacitacion,
                                'id_asistente' => $ca_asistentes->id,
                                'fecha_inicio' => $now,
                                'fecha_terminada' => $now,
                                'certificado' => '1',
                                'certificado_manual' => '1',
                                'last_approved' => '1',

                            ];
                            $caTestInit->fill($array_test_init);
                            $caTestInit->save();
                            $id_evaluacion_iniciada = $caTestInit->id;
                        }else{
                            //CERTIFICA POR MODULOS
                            $arregloIniciadas = [];
                            $modulos = CaModulos::where('id_capacitacion', $capacitacionAsistida->id_capacitacion)->get();
                            foreach ($modulos as $key => $modulo) {
                                $now = Carbon::now()->format('Y-m-d H:i:s');
                                $caTestInit = new CaEvaluacionIniciada();
                                $array_test_init = [
                                    'id_capacitacion' => $capacitacionAsistida->id_capacitacion,
                                    'id_modulo' => $modulo->id,
                                    'id_asistente' => $ca_asistentes->id,
                                    'fecha_inicio' => $now,
                                    'fecha_terminada' => $now,
                                    'certificado' => '1',
                                    'certificado_manual' => '1',
                                    'last_approved' => '1',
                                ];
                                $caTestInit->fill($array_test_init);
                                $caTestInit->save();
                                $arregloIniciadas[] = $caTestInit->id;
                            }
                            $id_evaluacion_iniciada = $arregloIniciadas;
                        }

                    }else{
                        //NO CERTIFICA
                        $id_evaluacion_iniciada = 0;
                    }

                }else{
                    if ($capacitacion->permitir_certificacion == 1) {
                        $caTestInit = CaEvaluacionIniciada::where([
                            ['id_capacitacion', $capacitacionAsistida->id_capacitacion],
                            ['id_asistente', $ca_asistentes->id],
                        ])->first();
                        $id_evaluacion_iniciada = $caTestInit->id;
                    }else{
                        $arregloIniciadas = [];
                        $caTestInit = CaEvaluacionIniciada::where([
                            ['id_capacitacion', $capacitacionAsistida->id_capacitacion],
                            ['id_asistente', $ca_asistentes->id],
                        ])->get();

                        foreach ($caTestInit as $key => $init) {
                            $arregloIniciadas[] = $init->id;
                        }
                        $id_evaluacion_iniciada = $arregloIniciadas;
                    }


                }
                $evaluacion = false;
            }

            if ($existeRegistro == 0) {
                return response()->json([
                    'status' => 201,
                    'msg' => 'Se ha registrado asistente correctamente.',
                    'data' => Crypt::encryptString($capacitacionAsistida->id_capacitacion),
                    'evaluacion' => $evaluacion,
                    'asistente' => $ca_asistentes->id,
                    'iniciada' => $id_evaluacion_iniciada,
                    'asistencia' => $asistencia->id,
                    'signature' => $asistencia->signature_path
                ]);
            } else {
                return response()->json([
                    'status' => 201,
                    'msg' => 'El asistente ya se encuentra registrado.',
                    'data' => Crypt::encryptString($capacitacionAsistida->id_capacitacion),
                    'evaluacion' => $evaluacion,
                    'asistente' => $ca_asistentes->id,
                    'iniciada' => $id_evaluacion_iniciada,
                    'asistencia' => $asistencia->id,
                    'signature' => $asistencia->signature_path
                ]);
            }
        } catch (\Exception $ex) {
            return response()->json([
                'status' => 500,
                'msg' => 'Algo salio mal.'
            ]);
        }

    }

    public function getTraining()
    {
        return DB::table('ca_capacitaciones')
        ->select('ca_capacitaciones.id', 'nombre as name')
        ->join('usuarios as u', 'u.id', 'id_usuario')
        ->where([
            ['ca_capacitaciones.estado', 1],
            ['u.main_account_id', Auth::user()->main_account_id],
            ['tipo_capacitacion', '<>', 3]
        ])
        ->orderBy('nombre','asc')
        ->whereNotNull('nombre')
        ->get();
    }

    public function getCentroCosto($id_empresa)
    {
        return DB::table('punto_evaluacion')->select('id', 'nombre as name')->where('estado', 1)->where('unidad_id', $id_empresa)->get();
    }

    public function getGrupoEmpresa()
    {
        $data = DB::table('centro_operacion')
        ->select('id', 'nombre as name')
        ->where('estado', 1)
        ->when(Auth::user()->main_account_id == 2, function($query){
            return $query->where('main_account_id', Auth::user()->main_account_id);
        })
        ->get();
        return $data;
    }

    public function getEmpresa($id_grupo_emp)
    {
        return DB::table('unidad')->select('id', 'nombre as name')->where('estado', 1)->where('centro_operacion_id', $id_grupo_emp)->get();
    }

    public function getDataTrainingPublic($id_training, $id_asistente)
    {
        $id_training = Crypt::decryptString($id_training);

        $training = CaCapacitaciones::where('id', $id_training)->first();
        if ($training->evaluara_por == 1 && $training->permitir_certificacion == 1) { //EVALUA Y CERTIFICA POR CAP
            $data = CaEvaluacionIniciada::SELECT('ca_evaluacion_iniciada.*',
            DB::raw('3 as evaluacionAprobadaCap')) //3 AS evaluacionAprobadaCap, aprobo pero es general
            ->where([
                ['id_capacitacion',$id_training],
                ['id_asistente',$id_asistente],
                ['last_approved','1'],
                ['certificado','1'],
            ])->get();

            $data = empty($data) ? [] : $data;
        }else{
            $data = CaModulos::GetAllModulesExcecutablePublic($id_training, $id_asistente);
        }

        return $data;
        // return $this->ExitProgram(202, $this->MessageResponse('Data', 202), $modules);
        // dd($id_training);
        // return DB::table('unidad')->select('id', 'nombre as name')->where('estado', 1)->where('centro_operacion_id', $id_grupo_emp)->get();
    }

    public function downloadCertificateByAssistExpert($id, $id_asistente)
    {
        $idsTest = json_decode(urldecode($id), true);

        $pdfs = [];
        $countIds = count($idsTest);

        foreach ($idsTest as $key => $idData) {
            $evaluacionIniciada = new CaEvaluacionIniciada();
            $certificado = $evaluacionIniciada->ByIdCertifiedIdAsistida($idData['id']);

            $capacitacion = new CaCapacitaciones;
            $capacitacion = $capacitacion->mainAccountTraining($certificado->id_capacitacion);

            if ($capacitacion->main_account_id == 1) {
                if ($certificado->certificado_manual == 1 || $certificado->certificado_manual == 2) { //ASISTENCIA
                    $pdf = Pdf::loadView('trainings::pdf.diplomaAsistencia', ['data' => $certificado]);
                }else{//CAPACITACION
                    $pdf = Pdf::loadView('trainings::pdf.diploma', ['data' => $certificado]);
                }
            }else{
                if ($certificado->certificado_manual == 1 || $certificado->certificado_manual == 2) { //ASISTENCIA
                    $pdf = Pdf::loadView('trainings::pdf.diplomaCapacitacionesPropiasAsistencia', ['data' => $certificado, 'Img' => $capacitacion->imagen, 'img_certificado' => $capacitacion->img_certificado]);
                }else{//CAPACITACION
                    $pdf = Pdf::loadView('trainings::pdf.diplomaCapacitacionesPropias', ['data' => $certificado, 'Img' => $capacitacion->imagen, 'img_certificado' => $capacitacion->img_certificado]);
                }
            }

            //SI SOLO TIENE UN CERTIFICADO DESCARGA EL PDF SIN .ZIP
            if ($countIds === 1) {
                return $pdf->setPaper('A4', 'landscape')->download($certificado->nombre . '.pdf');
            }

            $pdfs[] = [
                'name' => $certificado->nombre . '-'.$key.'.pdf',
                'content' => $pdf->setPaper('A4', 'landscape')->output(),
            ];
        }

        // Crear un archivo ZIP para los PDF
        $zip = new \ZipArchive();
        $zipFileName = 'certificados.zip';
        $zip->open($zipFileName, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

        // Agregar los PDF al archivo ZIP
        foreach ($pdfs as $pdf) {
            $zip->addFromString($pdf['name'], $pdf['content']);
        }

        $zip->close();

        // Descargar el archivo ZIP
        return response()->download($zipFileName)->deleteFileAfterSend(true);

    }

    public function downloadAllCertificateByAssistExpert($id)
    {
        $zip = new ZipArchive;

        Storage::deleteDirectory('public/certificados');
        Storage::deleteDirectory('public/pdf');

        Storage::makeDirectory('public/pdf');
        Storage::makeDirectory('public/certificados');

        $nombreArchivoZip = 'storage/certificados/certificados_' . date("Y-m-d-His") . '.zip';

        $capAsistida = CaCapAsistidas::where('id',$id)->first();

        $asistentes = CaCapacitacionesAsistidasAsistentes::where('id_capacitacion_asistida',$id)->get();

        foreach ($asistentes as $key => $asistente) {
            if($capAsistida->tipo == 1){
                //PUBLICA
                $testInit = CaEvaluacionIniciada::where([
                    ['id_asistente', $asistente->id_asistente],
                    ['id_capacitacion', $capAsistida->id_capacitacion],
                    ['certificado', '1'],
                    ['last_approved', '1'],
                ])
                ->get();
            }else{
                //PRIVADA
                $testInit = CaEvaluacionIniciada::where([
                    ['id_usuario', $asistente->id_usuario],
                    ['id_capacitacion', $capAsistida->id_capacitacion],
                    ['certificado', '1'],
                    ['last_approved', '1'],
                ])
                ->get();
            }

            foreach ($testInit as $key => $test) {
                $evaluacionIniciada = new CaEvaluacionIniciada();

                if($capAsistida->tipo == 1){
                    $data = $evaluacionIniciada->ByIdCertifiedIdPublic($test->id);
                }else{
                    $data = $evaluacionIniciada->ByIdCertifiedIdAsistida($test->id);
                }

                $capacitacion = new CaCapacitaciones;
                $capacitacion = $capacitacion->mainAccountTraining($data->id_capacitacion);

                if ($capacitacion->main_account_id == 1) {
                    if ($test->certificado_manual == 1 || $test->certificado_manual == 2) { //ASISTENCIA
                        $pdf = Pdf::loadView('trainings::pdf.diplomaAsistencia', ['data' => $data]);
                    }else{//CAPACITACION
                        $pdf = Pdf::loadView('trainings::pdf.diploma', ['data' => $data]);
                    }
                }else{
                    if ($test->certificado_manual == 1 || $test->certificado_manual == 2) { //ASISTENCIA
                        $pdf = Pdf::loadView('trainings::pdf.diplomaCapacitacionesPropiasAsistencia', ['data' => $data, 'Img' => $capacitacion->imagen, 'img_certificado' => $capacitacion->img_certificado]);
                    }else{//CAPACITACION
                        $pdf = Pdf::loadView('trainings::pdf.diplomaCapacitacionesPropias', ['data' => $data, 'Img' => $capacitacion->imagen, 'img_certificado' => $capacitacion->img_certificado]);
                    }
                }
                $pdf->setPaper('A4', 'landscape')
                ->save(public_path('storage/pdf/' . $data->nombre . "-".$key.".pdf"));
            }

        }

        if ($zip->open(public_path($nombreArchivoZip), ZipArchive::CREATE) === TRUE) {
            $archivos = File::files(public_path('storage/pdf'));
            foreach ($archivos as $archivo) {
                $zip->addFile($archivo, basename($archivo));
            }

            $zip->close();
        }

        $response = response()->download(public_path($nombreArchivoZip));


        return $response;
    }
    public function downloadReportVissit($id)
    {
        // dd($id);
        $data = CaCapAsistidas::select(
            'anfitrion_cliente',
            'ca_cap_asistidas.*','pe.nombre as centro_costo', 'co.nombre as grupo_empresa', 'ciu.nombre as ciudad',
            'us.id as asesor_id', 'us.nombre_com as asesor', 'us.main_account_id as main_account_asesor',
            'cap.nombre as capacitacion', 'uni.nombre as empresa',
            'ca_cap_asistidas.duracion', 'co.img_avatar', 'observacion'
            )
        ->leftjoin('punto_evaluacion as pe','pe.id','id_cliente')
        ->leftjoin('unidad as uni','uni.id','pe.unidad_id')
        ->leftjoin('centro_operacion as co','co.id','uni.centro_operacion_id')
        ->leftjoin('ca_capacitaciones as cap','cap.id','ca_cap_asistidas.id_capacitacion')
        ->leftjoin('ciudades as ciu','ciu.id','pe.ciudad_id')
        ->join('usuarios as us','us.id','ca_cap_asistidas.id_asesor')
        ->where('ca_cap_asistidas.id',$id)
        ->first();

        $img = CaCapAsistidasimg::where('id_cap_asistida',$id)->get();
        $avatarExperto = User::join('punto_evaluacion as p', 'usuarios.id_punto', 'p.id')
        ->join('unidad as u', 'p.unidad_id', 'u.id')
        ->join('centro_operacion as c', 'u.centro_operacion_id', 'c.id')
        ->where('usuarios.id', $data->asesor_id)
        ->first()->img_avatar;

        $pdf = Pdf::loadView('trainings::pdf.reporteVisita', ['data' => $data, 'img' => $img, 'avatarExperto'=>$avatarExperto]);
        return $pdf->setPaper('A4')->download('reporte.pdf');
    }

    public function downloadAsistentes($id, $formato = 'pdf')
    {

        $caAsistdida = CaCapAsistidas::where('ca_cap_asistidas.id', $id)
        ->join('ca_capacitaciones as ca','ca.id','id_capacitacion')
        ->first();
        $evaluacion = false;

        switch ($caAsistdida->tipo) {
            case 1:
                $data = CaCapacitacionesAsistidasAsistentes::select('nombre', 'documento', 'email', 'empresa','ca_capacitaciones_asistidas_asistentes.id_asistente')
                ->when($formato == 'pdf', function ($query) {
                    return $query->addSelect('signature_path as firma');
                })
                ->where('id_capacitacion_asistida', $id)
                ->join('ca_asistentes', 'ca_capacitaciones_asistidas_asistentes.id_asistente', 'ca_asistentes.id')
                ->get();

                if ($caAsistdida->aplica_evaluacion == 1 && $caAsistdida->evaluara_por == 1) {  //SOLO SE MUESTRA LA EVALUACION SI EVALUA POR CAP GENERAL
                    $evaluacion = true;
                    foreach ($data as $key => $asistente) {
                        $consulta = DB::select(
                            "SELECT id FROM ca_evaluacion_iniciada ei
                            where ei.id_capacitacion = $caAsistdida->id_capacitacion && ei.id_asistente = $asistente->id_asistente && ei.last_approved = 1"
                        );
                        count($consulta) > 0 ? $asistente->aprobo = 'Aprobo' : $asistente->aprobo =  'No aprobo';
                    }
                }
                break;

            case 2:
                $data = CaCapacitacionesAsistidasAsistentes::select('nombre_com as nombre', 'codigo as documento','email','ca_capacitaciones_asistidas_asistentes.id_usuario')
                ->when($formato === 'pdf', function ($query) {
                    return $query->addSelect('signature_path as firma');
                })
                ->where('id_capacitacion_asistida', $id)
                ->join('usuarios', 'ca_capacitaciones_asistidas_asistentes.id_usuario', 'usuarios.id')
                ->get();

                if ($caAsistdida->aplica_evaluacion == 1 && $caAsistdida->evaluara_por == 1) {  //SOLO SE MUESTRA LA EVALUACION SI EVALUA POR CAP GENERAL
                    $evaluacion = true;
                    foreach ($data as $key => $asistente) {
                        $consulta = DB::select(
                            "SELECT id FROM ca_evaluacion_iniciada ei
                            where ei.id_capacitacion = $caAsistdida->id_capacitacion && ei.id_usuario = $asistente->id_usuario && ei.last_approved = 1"
                        );
                        count($consulta) > 0 ? $asistente->aprobo = 'Aprobo' : $asistente->aprobo =  'No aprobo';
                    }
                }
                break;
            default:
                $data = [];
                break;
        }

        if ($formato == 'pdf') {
            $pdf = Pdf::loadView('trainings::pdf.capacitacionAsistidaAsistentes', ['data' => $data, "tipo" => $caAsistdida->tipo, "capacitacion" => $caAsistdida->nombre, "fecha" => $caAsistdida->fecha_agendamiento, "evaluacion" => $evaluacion]);
            return $pdf->setPaper('A4', 'portrait')->download('asistentes.pdf');
        }else{
            $data = $data->toArray();

            foreach ($data as &$item) {
                //SE ELIMINA ID USUARIO Y ASISTENTE PARA NO MOSTRARLO EN EXCEL Y SE ELIMINA APROBO SI NO TIENE EVALUACION
                unset($item['id_usuario']);
                unset($item['id_asistente']);

                if (!$evaluacion) {
                    unset($item['aprobo']);
                }
            }

            $columnas = array_keys($data[0]); //titulos

            // Crear un nuevo objeto Spreadsheet
            $spreadsheet = new Spreadsheet();

            // Obtener la hoja activa del objeto Spreadsheet
            $sheet = $spreadsheet->getActiveSheet();

            $columna = 'A';
            foreach ($columnas as $columnaTitulo) {
                $sheet->setCellValue($columna . '1', $columnaTitulo);
                $columna++;
            }

            // Llenar los datos en la hoja de cálculo
            $sheet->fromArray($data, null, 'A2');

            // Crear el objeto Writer para escribir en formato XLSX
            $writer = new Xlsx($spreadsheet);

            // Definir el nombre del archivo
            $filename = 'Asistentes.xlsx'; // Reemplaza "nombre_del_archivo" con el nombre que desees

            // Descargar el archivo Excel
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');

            $writer->save('php://output');
            exit;
        }
    }

    public function migrarIndex()
    {
        $page_title = 'E-Learning';
        $action = __FUNCTION__;
        $permisos = $this->GetAllPermisos();

        $centro = CentroOperacion::where('main_account_id', $this->main_account_id)->first();

        return view('trainings::Admin.indexMigrar', compact('page_title', 'action','permisos','centro'));
    }

    public function migrarExcel(Request $request)
    {
        $file = $request->file('excel_file');

        // Validar el archivo, si es necesario

        // Cargar el archivo Excel
        $spreadsheet = IOFactory::load($file);

        // Obtener la hoja activa del archivo
        $sheet = $spreadsheet->getActiveSheet();

        // Leer los datos de la hoja activa
        $data = $sheet->toArray();

        set_time_limit(360);
        foreach ($data as $key => $user) {

            $permisos = false;
            if ($user[3] != '' && $key != 0) { //que el correo no este vacio y omite el titulo
                if (!User::where('codigo',$user[2])->exists() || empty($user[2])) {
                    //echo $user[2].' - '.$user[3].'<br>';

                    $usuario = User::create([
                        'codigo'        => $user[2],
                        'nombre_com'    => $user[0],
                        'email'         => $user[3],
                        'password'      => \Hash::make($user[2]),
                        'usuario'       => $user[3],
                        'id_grupo'        => $user[7] == '' ? '48' : $user[7],
                        'estado'        => $user[4] == 'Activo' ? '1' : '2',
                        'ultimo_acceso' => now(),
                        'main_account_id' => $user[5],
                        'id_punto' => $user[6]
                    ]);
                    //echo $usuario->id_grupo." + ". $usuario->codigo."<br>";
                    $this->insertPermisos($usuario);
                }else{
                    //echo $user[2].' - '.$user[3].'<br>';
                    $usuario = User::where('codigo',$user[2])->first();
                    $usuario->main_account_id = $user[5];
                    $usuario->id_punto = $user[6];

                    if ($user[5] != 1) {
                        $usuario->id_grupo = $user[7] == '' ? '48' : $user[7];
                    }


                    $usuario->save();
                    //echo $usuario->id_grupo." + ". $usuario->codigo."<br>";
                    if (!SavkPermisosUsuarios::where('id_usuario', $usuario->id)->exists()) {
                        $this->insertPermisos($usuario);
                    }
                    //$permisos = SavkPermisosUsuarios::where('id_usuario',$usuario->id)->exists();
                }

                // if ($permisos == false) {
                //     //PERMISOS PARA CADA USUARIO REGISTRADO
                //     $arrayPermisos = [19,20,21,24,25,37,38]; //colaborador

                //     foreach ($arrayPermisos as $key => $permiso) {
                //         SavkPermisosUsuarios::create([
                //             'id_usuario' => $usuario->id,
                //             'id_permiso' => $permiso,
                //         ]);
                //     }
                // }
            }
        }
        dd("finalizo!!!");
        $page_title = 'E-Learning';
        $action = __FUNCTION__;
        // return view('trainings::Admin.indexMigrar', compact('page_title', 'action'));
    }

    public function migrarExcelCertificados(Request $request)
    {
        $file = $request->file('excel_file');

        // Validar el archivo, si es necesario

        // Cargar el archivo Excel
        $spreadsheet = IOFactory::load($file);

        // Obtener la hoja activa del archivo
        $sheet = $spreadsheet->getActiveSheet();

        // Leer los datos de la hoja activa
        $data = $sheet->toArray();
        set_time_limit(360);
        foreach ($data as $key => $certificado) {
            if ($key != 0) {
                $capacitacion = CaCapacitaciones::where([
                    ['nombre',$certificado[2]],
                    ['id_usuario',4301] //validamos que sea de klaxen
                ])->first();
                $user = User::where('codigo',$certificado[0])->first();

                if(!$user){
                    continue; // Salta al próximo registro en el ciclo
                    echo 'no existe usuario '.$certificado[0].'<br>';
                }

                if ($capacitacion) {
                    //continue;
                    //echo 'Capacitacion: '.$certificado[2].'<br>';
                    $modulos = CaModulos::where('id_capacitacion', $capacitacion->id)->get();

                    //SE RECORRE TODOS LOS MODULOS DE LA CAPACITACION
                    foreach ($modulos as $keyM => $modulo) {
                        // echo 'Modulo'.$modulo->nombre.'<br>';

                        //SE VALIDA QUE EL MODULO TENGA EVALUACION
                        if (CaPreguntas::where('id_modulo',$modulo->id)->exists()) {
                            //SE REGISTRA QUE GANO LA EVALUACION POR CADA MODULO
                            if (CaEvaluacionIniciada::where([['id_modulo',$modulo->id],['id_usuario',$user->id]])->exists() == false) {
                                $evaInit = CaEvaluacionIniciada::create([
                                    'id_capacitacion' => $capacitacion->id,
                                    'id_modulo' => $modulo->id,
                                    'id_usuario' => $user->id ,
                                    'fecha_inicio' => now(),
                                    'fecha_terminada' => now(),
                                    'resultado' => 100,
                                    'certificado' => 0,
                                    'certificado_manual' => 0,
                                    'last_approved' => 1,
                                ]);

                                $preguntas = CaPreguntas::where('id_modulo', $modulo->id)->get();

                                foreach ($preguntas as $keyP => $pregunta) {
                                    $respuesta = CaRespuestas::where([
                                        ['id_pregunta', $pregunta->id],
                                        ['ponderado', 100.00]
                                    ])
                                    ->first();

                                    CaEvaluacionIniciadaDetalle::create([
                                        'id_evaluacion_iniciada' => $evaInit->id,
                                        'id_pregunta' => $pregunta->id,
                                        'id_respuesta' => $respuesta->id,
                                    ]);
                                }

                            }
                        }

                        if (CaCapacitacionesIniciadas::where([['id_modulo',$modulo->id],['id_usuario',$user->id]])->exists() == false) {
                            CaCapacitacionesIniciadas::create([
                                'id_capacitacion' => $capacitacion->id,
                                'id_modulo' => $modulo->id,
                                'id_usuario' => $user->id,
                                'fecha_inicio' => now()
                            ]);
                        }
                    }

                    if (CaEvaluacionIniciada::where([['id_capacitacion',$capacitacion->id],['id_usuario',$user->id],['certificado',1]])->exists() == false) {
                        //AGREGAMOS EL CERTIFICADO DE LA CAPACITACION
                        CaEvaluacionIniciada::create([
                            'id_capacitacion' => $capacitacion->id,
                            'id_usuario' => $user->id ,
                            'fecha_inicio' => now(),
                            'fecha_terminada' => now(),
                            'resultado' => 100,
                            'certificado' => 1,
                            'certificado_manual' => 0,
                            'last_approved' => 1,
                        ]);
                    }
                }else{
                    echo "No existe capacitacion: ".$certificado[2].'<br>';
                }
            }
            //echo '____________________________________________________________________________________<br><br>';
        }
        dd("finalizo!!!");
        $page_title = 'E-Learning';
        $action = __FUNCTION__;
        // return view('trainings::Admin.indexMigrar', compact('page_title', 'action'));
    }

    public function migrarExcelAcompañamiento(Request $request)
    {
        $file = $request->file('excel_file');
        // Validar el archivo, si es necesario

        // Cargar el archivo Excel
        $spreadsheet = IOFactory::load($file);

        // Obtener la hoja activa del archivo
        $sheet = $spreadsheet->getActiveSheet();

        // Leer los datos de la hoja activa
        $data = $sheet->toArray();

        set_time_limit(360);
        foreach ($data as $key => $auditoria) {
            if ($key != 0) {
                $fechaCarbon = Carbon::createFromFormat('Y-m-d H:i:s', $auditoria[0]);
                // $fechaCarbon = Carbon::createFromFormat('n/j/Y H:i', $auditoria[0]); // se cambio
                // Formatea la fecha en el formato deseado (YYYY-MM-DD HH:mm)
                $fechaFormateada = $fechaCarbon->format('Y-m-d H:i');
                $visita = SavkVisita::create([
                    'fecha' => $fechaFormateada,
                    'modalidad' => $auditoria[1],
                    'id_usuario_registro' => $auditoria[2],
                    'id_centro_costo' => $auditoria[3],
                    'estado' => $auditoria[7] == '4' ? 2 : 1,
                    'interno_externo' => $auditoria[6],
                ]);

                DB::table('auditoria_iniciadas')
                ->where('id',  $auditoria[8])
                ->update(['visita_id' => $visita->id]);
            }
        }
        dd("finalizo!!!");
        $page_title = 'E-Learning';
        $action = __FUNCTION__;
        // return view('trainings::Admin.indexMigrar', compact('page_title', 'action'));
    }

    public function migrarCapacitacionesAsistidas(Request $request)
    {
        $idCentro = $request->get('id_centro');
        $fecha = $request->get('fecha');

        $data = DB::select(
            "
                SELECT
                    ai.id as id_auditoria
                    ,cap.id as id_cap_akl
                    ,ai.created_at as fecha
                    ,cap.nombre as capacitacion
                    ,(select ca_capacitaciones.id from ca_capacitaciones where ca_capacitaciones.nombre = cap.nombre limit 1) as id_capacitacion
                    ,ai.usuario_id as id_asesor
                    ,ai.responsable as anfitrion
                    ,'2' as modalidad
                    ,'2' as tipo
                    ,ai.punto_id as cliente
                    ,60 as duracion
                    ,'Sin link' as link
                    ,max(ci.observacion) as observacion
                FROM auditoria_iniciadas as ai
                    inner join punto_evaluacion pt on pt.id = ai.punto_id
                    inner join unidad u on u.id = pt.unidad_id
                    inner join centro_operacion c on c.id = u.centro_operacion_id
                    inner join respuestas_auditoria_iniciadas AS ri on ai.id = ri.auditoria_iniciada_id
                    inner join respuesta_detalle rd ON ri.respuesta_id = rd.id
                    inner join akl_capacitaciones_iniciadas ci on ci.id_auditoria_iniciada = ai.id
                    inner join akl_capacitaciones cap on ci.id_akl_capacitacion = cap.id
                where
                    c.id = $idCentro
                    and ai.estado = 4
                    and ai.auditoria_id = '65'
                    and rd.descripcion = 'Capacitación presencial'
                    and ai.created_at > '$fecha'
                group by ai.id, ci.id_akl_capacitacion
                order by ai.id desc
            "
        );

        foreach ($data as $key => $value) {
            if ($value->id_capacitacion == null) {
                $capExiste = CaCapacitaciones::where('nombre', $value->capacitacion)->first();

                if ($capExiste) {
                    $value->id_capacitacion = $capExiste->id;
                }else{
                    //CREACION DE CAPACITACIONES NO EXISTENTES
                    $cap = CaCapacitaciones::create([
                        'nombre' => $value->capacitacion,
                        'descripcion' => 'Asistidas migradas',
                        'tiempo_minutos' => $value->duracion,
                        'permitir_certificacion' => 1,
                        'id_usuario' => 4301,
                        'aplica_evaluacion' => 2,
                        'aplica_certificado' => 1,
                        'tipo_capacitacion' => 2,
                        'puntos' => 100,
                        'porcentaje_aprobacion' => 80
                    ]);
                    $mod =  CaModulos::create([
                        'nombre' => $value->capacitacion,
                        'orden' => 1,
                        'id_capacitacion' => $cap->id
                    ]);
                    $value->id_capacitacion = $cap->id;
                    //END CREACION DE CAPACITACIONES NO EXISTENTES
                }
            }

            //CREANDO CAPACITACION ASISTIDA
            $asistida = CaCapAsistidas::create([
                'fecha_agendamiento' => $value->fecha,
                'id_capacitacion' => $value->id_capacitacion,
                'id_asesor' => $value->id_asesor,
                'modalidad' => $value->modalidad,
                'tipo' => $value->tipo,
                'id_cliente' => $value->cliente,
                'duracion' => $value->duracion,
                'link' => $value->link,
                'anfitrion_cliente' => $value->anfitrion,
                'observacion' => $value->observacion
            ]);
            //GUARDANDO IMAGENES DE ASISTIDAS
            $dataImg = DB::select(
                "
                    SELECT fc.* FROM akl_fotos_capacitaciones as fc
                    inner join akl_capacitaciones_iniciadas as ci on ci.id = fc.id_akl_cap_iniciadas
                    where ci.id_auditoria_iniciada = $value->id_auditoria and ci.id_akl_capacitacion = $value->id_cap_akl;
                "
            );

            foreach ($dataImg as $key => $img) {
                $rutaImagenExterna = "https://klaxen.co/storage/Capacitaciones/$img->imagen";
                // Obtener el contenido de la imagen desde la URL
                $contenidoImagen = file_get_contents($rutaImagenExterna);

                // Verificar si se obtuvo el contenido
                if ($contenidoImagen !== false) {
                    $rutaAlmacenamiento = 'public/evidencias/' . $img->imagen;
                    Storage::put($rutaAlmacenamiento, $contenidoImagen);

                    CaCapAsistidasimg::create([
                        'path'        => '/storage/evidencias/'.$img->imagen,
                        'id_cap_asistida' => $asistida->id
                    ]);
                }
            }
            //END CREANDO CAPACITACION ASISTIDA

            //CARGAR ASISTENTES
            $dataAsis = DB::select(
                "
                    SELECT ac.id, a.nombre, a.numero_documento, a.correo
                    FROM akl_asistentes_capacitaciones as ac
                    inner join akl_asistentes as a on a.id = ac.id_akl_asistente
                    inner join akl_capacitaciones_iniciadas as ci on ci.id = ac.id_akl_capacitacion
                    where ci.id_auditoria_iniciada = $value->id_auditoria and ci.id_akl_capacitacion = $value->id_cap_akl;
                "
            );

            set_time_limit(360);
            foreach ($dataAsis as $key => $asistente) {
                if (User::where('codigo', $asistente->numero_documento)->exists()) {
                    //USUARIO EXISTENTE
                    $user = User::where('codigo', $asistente->numero_documento)->first();

                    $centroCosto = PuntoEvaluacion::where('id', $value->cliente)->first();

                    $user->main_account_id = $user->main_account_id != null ? $user->main_account_id : $centroCosto->main_account_id;
                    $user->id_punto = $user->id_punto != null ? $user->id_punto : $value->cliente;

                    $user->save();

                    if (!SavkPermisosUsuarios::where('id_usuario', $user->id)->exists()) {
                        $this->insertPermisos($user);
                    }
                } else {
                    if($asistente->numero_documento == ''){
                        continue; //SI NO HAY DOCUMENTO NO SE GUARDA ASISTENTE Y PROSIGUE CON EL SIGUIENTE
                    }
                    //USUARIO NO EXISTENTE
                    $email = $asistente->correo == null || $asistente->correo == '' ? $asistente->numero_documento.'notiene@savk.com.co' : $asistente->correo;

                    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $email = $asistente->numero_documento.'notiene@savk.com.co';
                    }

                    if (User::where('email', $email)->exists()) {
                        $email = $asistente->numero_documento.'notiene@savk.com.co';
                    }

                    $centroCosto = PuntoEvaluacion::where('id', $value->cliente)->first();

                    $usuario = explode(" ", $asistente->nombre);
                    $usuario = $usuario[0];

                    $user = User::create([
                        'codigo'        => $asistente->numero_documento,
                        'nombre_com'    => $asistente->nombre,
                        'email'         => $email,
                        'password'      => \Hash::make($asistente->numero_documento),
                        'usuario'       => $usuario . $asistente->numero_documento,
                        'id_grupo'        => '48',
                        'estado'        => '1',
                        'ultimo_acceso' => now(),
                        'main_account_id' => $centroCosto->main_account_id,
                        'id_punto' => $value->cliente,
                    ]);
                    $this->insertPermisos($user);
                }

                //SE CREA REGISTRO MANUAL 2 PARA DESCARGAR CERTIFICADO POR ASISTENCIA
                //ACTUALIZACIÓN DE LA EVALUACIÓN ACTUAL COMO ÚNICA CERTIFICADA
                CaEvaluacionIniciada::where([
                    ['id_capacitacion', '=', $value->id_capacitacion],
                    ['id_usuario', '=', $user->id]
                ])
                ->whereNull('id_modulo')
                ->update(['certificado' => 0, 'last_approved' => 0]);

                $now = Carbon::now()->format('Y-m-d H:i:s');
                $caTestInit = new CaEvaluacionIniciada();
                $array_test_init = [
                    'id_capacitacion' => $value->id_capacitacion,
                    'id_usuario' => $user->id,
                    'fecha_inicio' => $now,
                    'fecha_terminada' => $now,
                    'certificado' => '1',
                    'certificado_manual' => '2',
                    'last_approved' => '1',
                ];
                $caTestInit->fill($array_test_init);
                $caTestInit->save();

                CaCapacitacionesAsistidasAsistentes::create([
                    'id_capacitacion_asistida' => $asistida->id,
                    'id_usuario' => $user->id,
                ]);
            }
            //END CARGAR ASISTENTES
        }
        dd("finalizo!!!");
    }
}
