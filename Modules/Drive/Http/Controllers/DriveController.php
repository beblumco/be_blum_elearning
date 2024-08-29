<?php

namespace Modules\Drive\Http\Controllers;


use App\Models\CentroOperacion;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
// use Illuminate\Routing\Controller;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Modules\Administration\Entities\PuntoEvaluacion;
use Modules\Administration\Entities\Unidad;
use Modules\Drive\Entities\DriveAlmacenamiento;
use Modules\Drive\Entities\DriveCarpetas;
use Modules\Drive\Entities\DrivePermisos;
use Modules\Drive\Entities\DriveCompartir;
use Modules\Drive\Entities\DriveArchivos;
use Modules\Drive\Entities\DriveLogs;
use Modules\Drive\Entities\User;
use Modules\Administration\Entities\SavkLideresGrupoEmpresa;

class DriveController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function DriveIndex()
    {
        $page_title = 'Drive';
        $action = __FUNCTION__;
        $permisos = $this->GetAllPermisos();
        return view('drive::index', compact('page_title', 'action', 'permisos'));
    }

    public function DriveIndexV()
    {
        $page_title = 'Drive';
        $action = __FUNCTION__;
        $permisos = $this->GetAllPermisos();
        return view('drive::drive', compact('page_title', 'action', 'permisos'));
    }

    public function DriveIndexEntornoAprendizaje()
    {
        $page_title = 'Entornos de aprendizaje';
        $action = __FUNCTION__;
        $permisos = $this->GetAllPermisos();
        return view('drive::entorno_aprendizaje', compact('page_title', 'action', 'permisos'));
    }

    public function newFolder(Request $request)
    {
        try {
            if (is_null($request->get('id'))) {
                $data_folder = [
                    'nombre' => $request->get('name'),
                    'propietario_id' => \Auth::user()->id,
                    'cant_archivos' => 0,
                    'tamano' => 0,
                    'propietario_nombre' => \Auth::user()->nombre_com,
                    'main_account_id' => \Auth::user()->main_account_id,
                    'tipo' => $request->get('type'),
                    'permanente' => $request->get('status')
                ];

                if (strlen($request->get('parent')) > 0)
                    $data_folder['padre_id'] = $request->get('parent');

                $new_folder = DriveCarpetas::create($data_folder);

                if (is_null($new_folder)) {
                    return response()->json([
                        'status' => 202,
                        'msg' => 'No se creó la carpeta.'
                    ], 202);
                }
            }

            //Elimino los permisos que tenga la carpeta ya que se van a actualizar
            if (!is_null($request->get('id'))) {
                \DB::table('savk_drive_compartir')
                    ->where('carpeta_id', $request->get('id'))->delete();
                DrivePermisos::where('carpeta_id', $request->get('id'))
                    ->delete();
            }

            //Asigno los permisos
            $data_permissions = [
                'carpeta_id' => is_null($request->get('id')) ?
                    $new_folder->id : $request->get('id'),
            ];

            if (!in_array('create', $request->get('permissions')))
                $data_permissions['escritura'] = 0;
            if (!in_array('update', $request->get('permissions')))
                $data_permissions['editar'] = 0;
            if (!in_array('read', $request->get('permissions')))
                $data_permissions['lectura'] = 0;
            if (!in_array('share', $request->get('permissions')))
                $data_permissions['compartir'] = 0;
            if (!in_array('delete', $request->get('permissions')))
                $data_permissions['eliminar'] = 0;

            $new_permissions = DrivePermisos::create($data_permissions);

            //Comparto la carpeta a Grupo Empresa
            if ($request->get('company_group') != "") {
                $this->shareGroupCompany(
                    $request->get('company_group'),
                    is_null($request->get('id')) ?
                        $new_folder->id : $request->get('id'),
                    0
                );
            }

            //Comparto la carpeta a Empresa
            if ($request->get('company') != "") {
                $this->shareCompany(
                    $request->get('company'),
                    is_null($request->get('id')) ?
                        $new_folder->id : $request->get('id'),
                    0
                );
            }
            //Comparto la carpeta a Punto de Evaluación
            if ($request->get('evaluation_point') != "") {
                $this->shareEvaluationPoint(
                    $request->get('evaluation_point'),
                    is_null($request->get('id')) ?
                        $new_folder->id : $request->get('id'),
                    0
                );
            }

            if (is_null($request->get('id'))) {
                //Registro el log
                DriveLogs::saveLog(
                    is_null($request->get('id')) ?
                        $new_folder->id : $request->get('id'),
                    0,
                    'Creación de carpeta - ' . $request->get('name'),
                    \Auth::user()->id,
                    \Auth::user()->nombre_com
                );
            } else {
                DriveLogs::saveLog(
                    $request->get('id'),
                    0,
                    'Cambio de permisos a carpeta - ' . $request->get('name'),
                    \Auth::user()->id,
                    \Auth::user()->nombre_com
                );
            }

            return response()->json([
                'status' => 200,
                'msg' => is_null($request->get('id')) ? 'Nueva Carpeta.' : 'Permisos actualizados',
                'folder' => is_null($request->get('id')) ?
                    $new_folder : null
            ]);
        } catch (\Exception $ex) {
            return response()->json([
                'status' => 500,
                'error' => $ex->getMessage()
            ]);
        }
    }

    private function shareGroupCompany(
        array $companies_group,
        int $folder_id = 0,
        int $file_id = 0
    ) {

        $data = [];
        if ($folder_id != 0) {
            $data['carpeta_id'] = $folder_id;
        } else {
            $data['archivo_id'] = $file_id;
        }

        foreach ($companies_group as $company_group_id) {
            $data['centro_operacion_id'] = $company_group_id;
            $data['responsable_id'] = \Auth::user()->id;

            $new_share = DriveCompartir::create($data);
        }
    }

    private function shareCompany(
        array $companies,
        int $folder_id = 0,
        int $file_id = 0
    ) {

        $data = [];
        if ($folder_id != 0) {
            $data['carpeta_id'] = $folder_id;
        } else {
            $data['archivo_id'] = $file_id;
        }

        foreach ($companies as $company_id) {
            $data['unidad_id'] = $company_id;
            $data['responsable_id'] = \Auth::user()->id;

            $new_share = DriveCompartir::create($data);
        }
    }

    private function shareEvaluationPoint(
        array $points,
        int $folder_id = 0,
        int $file_id = 0
    ) {

        $data = [];
        if ($folder_id != 0) {
            $data['carpeta_id'] = $folder_id;
        } else {
            $data['archivo_id'] = $file_id;
        }

        foreach ($points as $point_id) {
            $data['punto_evaluacion_id'] = $point_id;
            $data['responsable_id'] = \Auth::user()->id;

            $new_share = DriveCompartir::create($data);
        }
    }
    public function getCompanyGroup()
    {
        if (\Auth::user()->savk_principal == 1) { //PRINCIPAL
            $data = \DB::table('centro_operacion')
                ->select('id', 'nombre')
                ->where([
                    ['estado', 1],
                    ['main_account_id', \Auth::user()->main_account_id]
                ])->get();
        } else if (\Auth::user()->id_grupo == 44) { //LIDER GRUPO EMPRESA
            $grupoEmpresa = SavkLideresGrupoEmpresa::where('id_usuario', auth()->user()->id)->pluck('id_grupo_empresa')->toArray();
            $data = \DB::table('centro_operacion')
                ->select('id', 'nombre')
                ->where('estado', 1)
                ->whereIn('id', $grupoEmpresa)
                ->get();
        } else if (\Auth::user()->id_grupo == 30 || \Auth::user()->id_grupo == 39) { // COMERCIAL
            $data = \DB::table('centro_operacion')
                ->select('id', 'nombre')
                ->where([
                    ['estado', 1],
                    ['asesor_id', auth()->user()->id]
                ])->get();
        } else if (\Auth::user()->id_grupo == 21) { // CALIDAD
            $data = \DB::table('centro_operacion')
                ->select('id', 'nombre')
                ->where([
                    ['estado', 1],
                    ['main_account_id', 1]
                ])->get();
        } else {
            $data = [];
        }

        return response()->json(['data' => $data], 200);
    }

    public function getCompanies()
    {
        if (\Auth::user()->savk_principal == 1) { //PRINCIPAL
            $data = \DB::table('unidad')
                ->select('id', 'nombre')
                ->where([
                    ['estado', 1],
                    ['main_account_id', \Auth::user()->main_account_id]
                ])->get();
        } else if (\Auth::user()->id_grupo == 44) { //LIDER GRUPO EMPRESA
            $grupoEmpresa = SavkLideresGrupoEmpresa::where('id_usuario', auth()->user()->id)->pluck('id_grupo_empresa')->toArray();
            $data = \DB::table('unidad')
                ->select('id', 'nombre')
                ->where('estado', 1)
                ->whereIn('centro_operacion_id', $grupoEmpresa)
                ->get();
        } else if (\Auth::user()->id_grupo == 30 || \Auth::user()->id_grupo == 39) { // COMERCIAL
            $grupoEmpresa = CentroOperacion::where('asesor_id', auth()->user()->id)->pluck('id');
            $data = \DB::table('unidad')
                ->select('id', 'nombre')
                ->where('estado', 1)
                ->whereIn('centro_operacion_id', $grupoEmpresa)
                ->get();
        } else if (\Auth::user()->id_grupo == 21) { // CALIDAD
            $grupoEmpresa = CentroOperacion::where('main_account_id', 1)->pluck('id');
            $data = \DB::table('unidad')
                ->select('id', 'nombre')
                ->where('estado', 1)
                ->whereIn('centro_operacion_id', $grupoEmpresa)
                ->get();
        }

        return response()->json(['data' => $data], 200);
    }

    public function getEvaluationPoints()
    {
        if (\Auth::user()->savk_principal == 1) { //PRINCIPAL
            $data = \DB::table('punto_evaluacion')
                ->select('id', 'nombre')
                ->where([
                    ['estado', 1],
                    ['main_account_id', \Auth::user()->main_account_id]
                ])->get();
        } else if (\Auth::user()->id_grupo == 44) { //LIDER GRUPO EMPRESA
            $grupoEmpresa = SavkLideresGrupoEmpresa::where('id_usuario', auth()->user()->id)->pluck('id_grupo_empresa')->toArray();
            $data = \DB::table('punto_evaluacion as p')
                ->select('p.id', 'p.nombre')
                ->join('unidad as u', 'u.id', 'p.unidad_id')
                ->where('p.estado', 1)
                ->whereIn('u.centro_operacion_id', $grupoEmpresa)
                ->get();
        } else if (\Auth::user()->id_grupo == 30 || \Auth::user()->id_grupo == 39) { // COMERCIAL
            $grupoEmpresa = CentroOperacion::where('asesor_id', auth()->user()->id)->pluck('id');
            $data = \DB::table('punto_evaluacion as p')
                ->select('p.id', 'p.nombre')
                ->join('unidad as u', 'u.id', 'p.unidad_id')
                ->where('p.estado', 1)
                ->whereIn('u.centro_operacion_id', $grupoEmpresa)
                ->get();
        } else if (\Auth::user()->id_grupo == 21) { // COMERCIAL
            $grupoEmpresa = CentroOperacion::where('main_account_id', 1)->pluck('id');
            $data = \DB::table('punto_evaluacion as p')
                ->select('p.id', 'p.nombre')
                ->join('unidad as u', 'u.id', 'p.unidad_id')
                ->where('p.estado', 1)
                ->whereIn('u.centro_operacion_id', $grupoEmpresa)
                ->get();
        }

        return response()->json(['data' => $data], 200);
    }

    /**
     * Obtener espacio de almacenamiento por main_account
     * @return JsonResponse
     */
    public function getStorageSpace()
    {
        $storage = DriveAlmacenamiento::where('main_account_id', \Auth::user()->main_account_id)
            ->first();

        $percentage = ($storage->tamano_usado / $storage->tamano_total) * 100;
        return response()->json([
            'status' => 200,
            'total_size' => $storage->tamano_total,
            'current_size' => $storage->tamano_usado,
            'percentage' => round($percentage, 2)
        ]);
    }

    /**
     * Función para convertir tamaños en KB, MB y GB
     * @param float $size
     * @param string $unit
     * @return string
     */
    private function convertSize(float $size, string $unit)
    {
        if ($unit == "KB") {
            return $fileSize = round($size / 1024, 4) . 'KB';
        }
        if ($unit == "MB") {
            return $fileSize = round($size / 1024 / 1024, 4) . 'MB';
        }
        if ($unit == "GB") {
            return $fileSize = round($size / 1024 / 1024 / 1024, 4) . 'GB';
        }
    }

    public function uploadFiles(Request $request)
    {
        try {
            $files = $request->file('files');
            $folder_id = $request->get('folder_id') === 'null' ? null : $request->get('folder_id');
            $new_files = [];

            $count_files = sizeof($files);
            $total_size = 0;
            if ($count_files === 0) {
                return response()->json([
                    'status' => 200,
                    'msg' => 'No hay archivos para cargar.'
                ]);
            }

            foreach ($files as $k => $file) {
                $files_obj = json_decode($request->get('files_obj')[$k]);

                $name = $file->getClientOriginalName();
                $type = $file->getClientMimeType();
                $ext = $type === 'application/octet-stream' ? null : $file->extension();
                $size = $file->getSize() !== false ? $file->getSize() : $files_obj->{'size'};

                //valido que no tenga el mismo nombre
                $last_file = DriveArchivos::where([
                    ['nombre', 'LIKE', "%$name%"],
                    ['carpeta_id', $folder_id]
                ])->count();

                if ($last_file > 0) {
                    $name = $name . ' - copia-' . ($last_file + 1);
                }

                //Guardo el archivo
                $path = $file->store('drive', 'public');
                // $path = Storage::putFileAs(
                //     'public/drive',
                //     $file,
                //     $name
                // );

                $new_file = DriveArchivos::create([
                    'nombre' => $name,
                    'tipo' => $type,
                    'ext' => $ext,
                    'tamano' => $size,
                    'carpeta_id' => $folder_id,
                    'propietario_id' => \Auth::user()->id,
                    'ruta' => $path,
                    'propietario_nombre' => \Auth::user()->nombre_com,
                    'main_account_id' => \Auth::user()->main_account_id,
                    'tipo_drive' => $request->get('type')
                ]);

                $new_files[] = $new_file;
                $total_size = $total_size + $size;

                DriveLogs::saveLog(
                    0,
                    $new_file->id,
                    'Nuevo archivo - ' . $name,
                    \Auth::user()->id,
                    \Auth::user()->nombre_com
                );
            }

            //Actualizo la carpeta donde se encuentren los archivos
            if (!is_null($folder_id)) {
                $folder = DriveCarpetas::find($folder_id);

                $folder->cant_archivos = $folder->cant_archivos + $count_files;
                $folder->tamano = (float) $folder->tamano + $total_size;
                $folder->save();

                //Actualizo el tamaño de la carpeta padre
                if (!is_null($folder->padre_id)) {
                    $folder_padre = DriveCarpetas::find($folder->padre_id);
                    $folder_padre->tamano = (float) $folder_padre->tamano + $total_size;
                    $folder_padre->save();
                }
            }

            //Actualizo el espacio total del main_account
            $account = DriveAlmacenamiento::where('main_account_id', \Auth::user()->main_account_id)->first();

            $account->tamano_usado = (float) $account->tamano_usado + $total_size;
            $account->save();

            return response()->json([
                'status' => 200,
                'files' => $new_files
            ]);
        } catch (\Exception $ex) {
            return response()->json([
                'status' => 500,
                'error' => $ex->getMessage()
            ], 500);
        }
    }

    public function getData(Request $request)
    {
        $current_folder = $request->get('current_folder');
        $grupoEmpresaAsignado = false; //Se usa para validar si el tiene ge asignado
        $main_accounts = [];
        $where_share = [];

        if (\Auth::user()->id_grupo == 44) {
            //Si es lider grupo empresa buscamos todas las empresas y puntos asignados a ese grupo
            $lider = SavkLideresGrupoEmpresa::where('id_usuario', \Auth::user()->id)->get();
            $grupoEmpresaAsignado = $lider->count() > 0 ? true : false;

            foreach ($lider as $k => $val) {
                $ge = CentroOperacion::find($val->id_grupo_empresa);
                $where_share['gempresa'][$val->id_grupo_empresa] = $ge->nombre;

                $empresas = Unidad::where('centro_operacion_id', $val->id_grupo_empresa)->get();

                foreach ($empresas as $keyE => $empresa) {
                    $where_share['empresa'][] = $empresa->id;
                    $puntos = PuntoEvaluacion::where('unidad_id', $empresa->id)->get();

                    foreach ($puntos as $keyP => $punto) {
                        $where_share['punto'][] = $punto->id;
                    }
                }
            }
        } else if (\Auth::user()->id_grupo == 30 || \Auth::user()->id_grupo == 39) {
            //Si es asesor buscamos todas las empresas y puntos asignados a el
            $asesor = CentroOperacion::where('asesor_id', \Auth::user()->id)->get();

            foreach ($asesor as $k => $val) {
                $where_share['gempresa'][$val->id] = $val->nombre;
                $main_accounts[] = $val->main_account_id;

                $empresas = Unidad::where('centro_operacion_id', $val->id)->get();

                foreach ($empresas as $keyE => $empresa) {
                    $where_share['empresa'][] = $empresa->id;
                    $puntos = PuntoEvaluacion::where('unidad_id', $empresa->id)->get();

                    foreach ($puntos as $keyP => $punto) {
                        $where_share['punto'][] = $punto->id;
                    }
                }
            }
        }

        if ((\Auth::user()->id_grupo != 44) || !$grupoEmpresaAsignado) {
            $user_points = \DB::table('usuarios AS up')
                ->join('punto_evaluacion AS pe', 'pe.id', 'up.id_punto')
                ->join('unidad AS u', 'u.id', 'pe.unidad_id')
                ->select(
                    'u.centro_operacion_id AS gempresa',
                    'u.id AS empresa',
                    'pe.id AS punto'
                )
                ->where('up.id', \Auth::user()->id)
                ->groupBy(
                    'u.centro_operacion_id',
                    'u.id',
                    'pe.id'
                )->get()->toArray();

            foreach ($user_points as $k => $val) {
                $ge = CentroOperacion::find($val->{'gempresa'});
                $where_share['gempresa'][$val->{'gempresa'}] = $ge->nombre;
                $where_share['empresa'][] = $val->{'empresa'};
                $where_share['punto'][] = $val->{'punto'};
            }
        }

        if (count($main_accounts) == 0) {
            $main_accounts[] = \Auth::user()->main_account_id;
        }
        $main_accounts[] = 1; //todos tendran resultado de Klaxen para ver lo que el asesor publica

        $where_folder = [];
        if (!is_null($current_folder)) {
            array_push($where_folder, ['padre_id', $current_folder]);
        } else {
            array_push($where_folder, ['padre_id', null]);
        }

        if (\Auth::user()->main_account_id != 1) {
            array_push($where_folder, ['permanente', 2]);
        }

        // dd($where_share);
        $data_folders = DriveCarpetas::with(['files', 'share', 'permissions'])
            ->where($where_folder)
            ->whereIn('main_account_id', $main_accounts)
            ->get()->toArray();

        foreach ($data_folders as $key => &$folder) {
            foreach ($folder['share'] as $key2 => $share) {
                if ($share['punto_evaluacion_id'] != null) {
                    $ge = CentroOperacion::GetOperationCenterByPointer($share['punto_evaluacion_id']);
                    $folder['grupoEmpresa'][] = $ge->id;
                } else if ($share['unidad_id'] != null) {
                    $ge = Unidad::find($share['unidad_id']);
                    $folder['grupoEmpresa'][] = $ge->centro_operacion_id;
                } else if ($share['centro_operacion_id'] != null) {
                    $folder['grupoEmpresa'][] = $share['centro_operacion_id'];
                }
            }
        }

        $where_files = [];
        if (!is_null($current_folder)) {
            array_push($where_files, ['carpeta_id', $current_folder]);
        } else {
            array_push($where_files, ['carpeta_id', null]);
        }

        $data_files = DriveArchivos::where($where_files)
            ->whereIn('main_account_id', $main_accounts)
            ->paginate(25)->toArray();

        //Uno los arrays

        $resul = array_merge($data_folders, $data_files['data']);
        // dd($data_files);
        //Remplazo el data de folders por el data mergeado
        // $data_folders['data'] = $resul;

        if ($current_folder != null) {
            $isParentFolderPermanent = DriveCarpetas::getParentFolder($current_folder);
        } else {
            $isParentFolderPermanent = null;
        }

        return response()->json([
            'status'            => 200,
            'data'              => $resul,
            'share'             => $where_share,
            'savk_principal'    => \Auth::user()->savk_principal,
            'grupo'             => \Auth::user()->id_grupo,
            'paginate'          => [
                'current_page'  => $data_files['current_page'],
                'last_page'     => $data_files['last_page'],
                'next_page_url' => $data_files['next_page_url']
            ],
            'isParentFolderPermanent' => $isParentFolderPermanent
        ]);
    }

    public function rename(Request $request)
    {
        switch ($request->get('type')) {
            case 'folder':
                $folder = DriveCarpetas::find($request->get('id'));
                if (is_null($folder)) {
                    return response()->json([
                        'status' => 202,
                        'msg' => 'No se encontró la carpeta renombrar.'
                    ]);
                }
                $folder->nombre = $request->get('name');
                $folder->save();

                return response()->json([
                    'status' => 200,
                    'msg' => 'Carpeta renombrada!'
                ]);
                break;

            case 'file':
                $file = DriveArchivos::find($request->get('id'));
                if (is_null($file)) {
                    return response()->json([
                        'status' => 202,
                        'msg' => 'No se encontró el archivo a renombrar.'
                    ]);
                }
                $file->nombre = $request->get('name');
                $file->save();
                return response()->json([
                    'status' => 200,
                    'msg' => 'Archivo renombrado!'
                ]);
                break;
        }
    }
    public function delete(Request $request)
    {
        switch ($request->get('type')) {
            case 'folder':
                try {
                    $folder = DriveCarpetas::with(['files'])->where('id', $request->id)
                        ->first();
                    $name_folder = $folder->nombre;

                    $subfolders = DriveCarpetas::with(['files'])->where('padre_id', $folder->id)
                        ->get();

                    //Elimino los archivos de las subcarpetas
                    $total_size_del = 0;
                    foreach ($subfolders as $subfolder) {
                        $subfolder_name = $subfolder->nombre;
                        foreach ($subfolder->files as $file) {
                            try {
                                Storage::disk('public')->delete($file->ruta);
                                $total_size_del = $total_size_del + $file->tamano;

                                //Registro el log
                                DriveLogs::saveLog(
                                    0,
                                    $file->id,
                                    'Se eliminó el archivo ' . $file->nombre,
                                    \Auth::user()->id,
                                    \Auth::user()->nombre_com
                                );
                                //Elimino de la BD
                                $file->delete();
                            } catch (\Exception $ex) {
                                return response()->json([
                                    'status' => 500,
                                    'msg' => $ex->getMessage()
                                ]);
                            }
                        }
                        //Elimino el subfolder
                        $subfolder->delete();

                        //Registro el log
                        DriveLogs::saveLog(
                            $request->get('id'),
                            0,
                            'Se eliminó la carpeta ' . $subfolder_name,
                            \Auth::user()->id,
                            \Auth::user()->nombre_com
                        );
                    }

                    //Elimino los archivos del folder principal
                    foreach ($folder->files as $file) {
                        try {
                            Storage::disk('public')->delete($file->ruta);
                            $total_size_del = $total_size_del + $file->tamano;

                            //Registro el log
                            DriveLogs::saveLog(
                                0,
                                $file->id,
                                'Se eliminó el archivo ' . $file->nombre,
                                \Auth::user()->id,
                                \Auth::user()->nombre_com
                            );
                            //Elimino de la BD
                            $file->delete();
                        } catch (\Exception $ex) {
                            return response()->json([
                                'status' => 500,
                                'msg' => $ex->getMessage()
                            ]);
                        }
                    }
                    //Actualizo información para la carpeta padre
                    if (!is_null($folder->padre_id)) {
                        $parent_folder = DriveCarpetas::find($folder->padre_id);
                        $parent_folder->tamano = $parent_folder->tamano - $total_size_del;
                        $parent_folder->save();
                    }

                    $folder->delete();

                    //Actualizo el almacenamiento
                    $storage = DriveAlmacenamiento::where(
                        'main_account_id',
                        \Auth::user()->main_account_id
                    )
                        ->first();
                    $storage->tamano_usado = $storage->tamano_usado - $total_size_del;
                    $storage->save();

                    //Registro el log
                    DriveLogs::saveLog(
                        $request->get('id'),
                        0,
                        'Se eliminó la carpeta ' . $name_folder,
                        \Auth::user()->id,
                        \Auth::user()->nombre_com
                    );

                    return response()->json([
                        'status' => 200,
                        'msg' => 'Carpeta eliminada'
                    ]);
                } catch (\Exception $ex) {
                    return response()->json([
                        'status' => 500,
                        'msg' => $ex->getMessage()
                    ]);
                }
                break;

            case 'file':
                $file = DriveArchivos::find($request->get('id'));
                if (is_null($file)) {
                    return response()->json([
                        'status' => 202,
                        'msg' => 'No se encontró el archivo que desea eliminar.'
                    ]);
                }

                DB::table('documentacion_producto')->where([
                    ['id_audeed_drive_archivo', $request->get('id')]
                ])->delete();

                //Actualizo el almacenamiento
                $storage = DriveAlmacenamiento::where(
                    'main_account_id',
                    \Auth::user()->main_account_id
                )
                    ->first();
                $storage->tamano_usado = $storage->tamano_usado - $file->tamano;
                $storage->save();

                //Elimino el archivo
                Storage::disk('public')->delete($file->ruta);

                //Actualizo el tamaño y cantidad de archivos de la carpeta
                $folder = DriveCarpetas::find($file->carpeta_id);
                if ($folder) {
                    $folder->tamano = $folder->tamano - $file->tamano;
                    $folder->cant_archivos = $folder->cant_archivos - 1;
                    $folder->save();

                    //Actualizo el tamaño de la carpeta padre en caso de que exista
                    if (!is_null($folder->padre_id)) {
                        $parent_folder = DriveCarpetas::find($folder->padre_id);
                        $parent_folder->tamano = $parent_folder->tamano - $file->tamano;
                        $parent_folder->save();
                    }
                }

                //Registro el log
                DriveLogs::saveLog(
                    0,
                    $file->id,
                    'Se eliminó el archivo ' . $file->nombre,
                    \Auth::user()->id,
                    \Auth::user()->nombre_com
                );
                $file->delete();

                return response()->json([
                    'status' => 200,
                    'msg' => 'Archivo eliminado'
                ]);
                break;
        }
    }

    //Obtengo los permisos de compartir para la carpeta
    public function getPermissionsFolder($folder_id)
    {
        $folder = DriveCarpetas::with(['permissions', 'share'])->find($folder_id);

        if (is_null($folder)) {
            return response()->json([
                'status' => 202,
                'msg' => 'No se encontró la carpeta.'
            ]);
        }

        //Permisos
        $permissions = [];
        if ($folder->permissions[0]->lectura != 0)
            $permissions[] = 'read';
        if ($folder->permissions[0]->escritura != 0)
            $permissions[] = 'create';
        if ($folder->permissions[0]->eliminar != 0)
            $permissions[] = 'delete';
        if ($folder->permissions[0]->compartir != 0)
            $permissions[] = 'share';
        if ($folder->permissions[0]->editar != 0)
            $permissions[] = 'update';

        $company_group = [];
        $companies = [];
        $points = [];

        foreach ($folder->share as $el) {
            if (!is_null($el->centro_operacion_id))
                $company_group[] = $el->centro_operacion_id;
            if (!is_null($el->unidad_id))
                $companies[] = $el->unidad_id;
            if (!is_null($el->punto_evaluacion_id))
                $points[] = $el->punto_evaluacion_id;
        }


        return response()->json([
            'company_group' => $company_group,
            'companies' => $companies,
            'points' => $points,
            'permissions' => $permissions
        ]);
    }

    public function getSubFoldersCount($folder_id)
    {
        $data = DriveCarpetas::where([
            ['main_account_id', \Auth::user()->main_account_id],
            ['padre_id', $folder_id]
        ])->count();

        return response()->json([
            'status' => 200,
            'data' => $data
        ]);
    }

    public function downloadFile($file_id)
    {
        try {
            $file = DriveArchivos::find($file_id);
            return Storage::download('public/' . $file->ruta, $file->nombre);
        } catch (\Exception $ex) {
            return response("Error interno", 500);
        }
    }

    //Obtengo los permisos de acceso de la carpeta
    public function getFolderPermissions($folder_id)
    {
        return DriveCarpetas::with(['permissions', 'share'])->find($folder_id);
    }

    public function getEtiquetas()
    {
        return DB::table('etiqueta_documentacion')->select(
            'id',
            'nombre as name'
        )->get();
    }

    public function getProductos(Request $request)
    {
        $productos = DB::table('productos')->select(
            'id',
            'nombre as name'
        )
            ->where('id_etiqueta', $request->id_etiqueta)
            ->get();

        $documentos = DB::table('documentacion_producto')->select(
            'documentacion_producto.id',
            'documentacion_tecnica.nombre',
            'documentacion_producto.id_documentacion'
        )->where('id_etiqueta', $request->id_etiqueta)
            ->join('documentacion_tecnica', 'documentacion_tecnica.id', 'documentacion_producto.id_documentacion')
            ->get()
            ->unique('id_documentacion');

        return [
            'productos' => $productos,
            'documentos' => $documentos
        ];
    }
    public function EliminarDocumentacionEtiqueta(Request $request)
    {
        $id = DB::table('documentacion_producto')->where([
            ['id_etiqueta', $request->id_etiqueta],
            ['id_documentacion', $request->id_doc]
        ])->first();

        DB::table('documentacion_producto')->where([
            ['id_etiqueta', $request->id_etiqueta],
            ['id_documentacion', $request->id_doc]
        ])->delete();

        $file = DriveArchivos::find($id->id_drive_archivo);
        if (is_null($file)) {
            return response()->json([
                'status' => 202,
                'msg' => 'No se encontró el archivo que desea eliminar.'
            ]);
        }

        //Actualizo el almacenamiento
        $storage = DriveAlmacenamiento::where(
            'main_account_id',
            \Auth::user()->main_account_id
        )
            ->first();
        $storage->tamano_usado = $storage->tamano_usado - $file->tamano;
        $storage->save();

        //Elimino el archivo
        Storage::disk('public')->delete($file->ruta);

        //Actualizo el tamaño y cantidad de archivos de la carpeta
        $folder = DriveCarpetas::find($file->carpeta_id);
        if ($folder) {
            $folder->tamano = $folder->tamano - $file->tamano;
            $folder->cant_archivos = $folder->cant_archivos - 1;
            $folder->save();

            //Actualizo el tamaño de la carpeta padre en caso de que exista
            if (!is_null($folder->padre_id)) {
                $parent_folder = DriveCarpetas::find($folder->padre_id);
                $parent_folder->tamano = $parent_folder->tamano - $file->tamano;
                $parent_folder->save();
            }
        }

        //Registro el log
        DriveLogs::saveLog(
            0,
            $file->id,
            'Se eliminó el archivo ' . $file->nombre,
            \Auth::user()->id,
            \Auth::user()->nombre_com
        );
        $file->delete();


        return response()->json([
            'status' => 200,
            'msg' => 'Documento eliminado'
        ]);
    }

    public function CargarDocumentacionEtiqueta(Request $request)
    {
        $files = $request->file('files');
        $folder_id = $request->get('folder_id') === 'null' ? null : $request->get('folder_id');

        $count_files = sizeof($files);
        $total_size = 0;

        foreach ($request->file('files') as $k => $file) {

            $name = $file->getClientOriginalName();
            $type = $file->getClientMimeType();
            $ext = $type === 'application/octet-stream' ? null : $file->extension();
            $size = $file->getSize();

            $path = $file->store('drive/documentacion_tecnica', 'public');

            DB::table('documentacion_tecnica')->insert([
                'nombre' => $file->getClientOriginalName(),
                'ruta_imagen' => $path
            ]);

            $id =  DB::table('documentacion_tecnica')->latest('id')->first();

            $productos = DB::table('productos')->select(
                'id',
                'nombre as name'
            )
                ->where('id_etiqueta', $request->id_etiqueta)
                ->get();



            $new_file = DriveArchivos::create([
                'nombre' => $name,
                'tipo' => $type,
                'ext' => $ext,
                'tamano' => $size,
                'carpeta_id' => $folder_id,
                'propietario_id' => \Auth::user()->id,
                'ruta' => $path,
                'propietario_nombre' => \Auth::user()->nombre_com,
                'main_account_id' => \Auth::user()->main_account_id,
                'tipo_drive' => $request->get('type')
            ]);

            $new_files[] = $new_file;
            $total_size = $total_size + $size;

            DriveLogs::saveLog(
                0,
                $new_file->id,
                'Nuevo archivo - ' . $name,
                \Auth::user()->id,
                \Auth::user()->nombre_com
            );

            foreach ($productos as $key => $value) {
                DB::table('documentacion_producto')->insert([
                    'id_documentacion' => $id->id,
                    'id_producto' => $value->id,
                    'id_etiqueta' => $request->id_etiqueta,
                    'ruta_documento' => $path,
                    'id_drive_archivo' => $new_file->id
                ]);
            }
        }

        //Actualizo la carpeta donde se encuentren los archivos
        if (!is_null($folder_id)) {
            $folder = DriveCarpetas::find($folder_id);

            $folder->cant_archivos = $folder->cant_archivos + $count_files;
            $folder->tamano = (float) $folder->tamano + $total_size;
            $folder->save();

            //Actualizo el tamaño de la carpeta padre
            if (!is_null($folder->padre_id)) {
                $folder_padre = DriveCarpetas::find($folder->padre_id);
                $folder_padre->tamano = (float) $folder_padre->tamano + $total_size;
                $folder_padre->save();
            }
        }

        //Actualizo el espacio total del main_account
        $account = DriveAlmacenamiento::where('main_account_id', \Auth::user()->main_account_id)->first();

        $account->tamano_usado = (float) $account->tamano_usado + $total_size;
        $account->save();
    }
}
