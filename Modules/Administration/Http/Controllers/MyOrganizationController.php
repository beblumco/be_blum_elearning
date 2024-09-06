<?php

namespace Modules\Administration\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\GuiaVisualizaciones;
use App\Models\SavkSecciones;
use App\Models\SavkPermisosUsuarios;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Administration\Entities\CentroOperacion;
use Modules\Administration\Entities\GruposPuntos;
use Modules\Administration\Entities\GruposSubPuntos;
use Modules\Administration\Entities\Lider;
use Modules\Administration\Entities\SavkLideresZonas;
use Modules\Administration\Entities\Unidad;
use Modules\Administration\Entities\PuntoEvaluacion;
use Modules\Administration\Entities\SavkLideresCentroDeCostos;
use Modules\Administration\Entities\SavkLideresEmpresa;
use Modules\Administration\Entities\SavkLideresGrupoEmpresa;
use Modules\Administration\Entities\User;
use Modules\Trainings\Entities\CaCapacitacionesIniciadas;
use Modules\Trainings\Entities\CaEvaluacionIniciada;
use Modules\Trainings\Entities\CaPreguntasUsuarios;
use Illuminate\Support\Facades\Mail;
use App\Mail\RespuestaAsesor;

class MyOrganizationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $page_title = 'Mi organización';
        $action = 'IndexMyOrganization';
        $permisos = $this->GetAllPermisos();
        return view('administration::index', compact('page_title', 'action', 'permisos'));
    }

    public function MyClientsIndex()
    {
        $page_title = 'Mis clientes';
        $action = 'MyClientsIndex';
        $permisos = $this->GetAllPermisos();
        return view('administration::my_clients_index', compact('page_title', 'action', 'permisos'));
    }

    public function IndexAdminTrainings()
    {
        $page_title = 'Capacitaciones virtuales';
        $action = 'IndexAdminTrainings';
        $permisos = $this->GetAllPermisos();
        return view('administration::virtual_trainings', compact('page_title', 'action', 'permisos'));
    }

    public function getCountries()
    {
        return \DB::table('paises')->select('id', 'nombre as name')->where('estado', 1)->get();
    }

    public function getZonas()
    {
        return \DB::table('grupos_puntos')->select('id', 'nombre as name')
        ->where('main_account_id', \Auth::user()->main_account_id)
        ->get();
    }

    public function getDepartaments(string $country_id)
    {
        return \DB::table('departamenos')->select('id', 'nombre as name')->where([
            ['estado', 1],
            ['paises_id', $country_id]
        ])->get();
    }

    public function getCities(string $departament_id)
    {
        return \DB::table('ciudades')->select('id', 'nombre as name')->where([
            ['estado', 1],
            ['departamentos_id', $departament_id]
        ])->get();
    }

    public function getSectors()
    {
        return \DB::table('sector')->select('id', 'nombre as name')->get();
    }

    public function getAsesores()
    {
        $data = \DB::table('usuarios')
            ->select('id', 'nombre_com as name')
            ->where('estado', 1)
            ->where('id_grupo', 30)
            ->when(Auth::user()->main_account_id == 2, function($query){
                return $query->where('main_account_id', 2);
            })
            ->orderBy('nombre_com', 'ASC')
            ->get();

        return $data;
    }

    //GRUPO EMPRESA
    public function createCompanyGroup(Request $request)
    {
        try {
            switch ($request->get('mode')) {
                case 'create':
                    $file = $request->file('logo');
                    $path = null;
                    if($file){
                        $path = $file->store('avatars', 'public');
                    }

                    $company_group = CentroOperacion::where('identificacion', $request->nit)->exists();

                    if ($company_group) {
                        return response()->json([
                            'status' => 202,
                            'msg' => 'Ya existe el NIT'
                        ]);
                    }

                    $new_company_group = CentroOperacion::create([
                        'nombre'          => $request->nombre,
                        'identificacion'  => $request->nit,
                        'estado'          => $request->estado,
                        'ciudad_id'       => $request->ciudad,
                        'main_account_id' => \Auth::user()->main_account_id,
                        'sector_id'       => $request->sector,
                        'usuario_id'      => \Auth::user()->id,
                        'asesor_id'       => $request->asesor == 'null' || $request->asesor == 'undefined' ? null : $request->asesor,
                        'img_avatar'      => $path
                    ]);

                    if($request->lideres){
                        foreach ($request->lideres as $lider) {
                            SavkLideresGrupoEmpresa::create([
                                'id_usuario' => $lider,
                                'id_grupo_empresa' => $new_company_group->id,
                            ]);
                        }
                    }

                    return response()->json([
                        'status' => 201,
                        'msg' => 'Se ha creado el Grupo Empresa.'
                    ]);
                    break;

                case 'edit':
                    $company_group = CentroOperacion::find($request->id);

                    if (is_null($company_group)) {
                        return response()->json([
                            'status' => 202,
                            'msg' => 'No se encontró el grupo empresa a editar.'
                        ]);
                    }

                    $company_group_exist = CentroOperacion::where([
                        ['identificacion', $request->nit],
                        ['id', '<>', $request->id]
                    ])->exists();

                    if ($company_group_exist) {
                        return response()->json([
                            'status' => 202,
                            'msg' => 'Ya existe el NIT'
                        ]);
                    }

                    $file = $request->file('logo');
                    if($file){
                        // Verificar y eliminar el archivo existente si es necesario
                        if ($company_group->img_avatar) {
                            $existingFilePath = storage_path('app/public/' . $company_group->img_avatar);
                            if (\File::exists($existingFilePath)) {
                                \File::delete($existingFilePath);
                            }
                        }

                        $path = $file->store('avatars', 'public');
                        $company_group->img_avatar      = $path;
                    }

                    $fileCertificado = $request->file('logoCertificado');
                    if($fileCertificado){
                        // Verificar y eliminar el archivo existente si es necesario
                        if ($company_group->img_certificado) {
                            $existingFilePath = storage_path('app/public/' . $company_group->img_certificado);
                            if (\File::exists($existingFilePath)) {
                                \File::delete($existingFilePath);
                            }
                        }

                        $path = $fileCertificado->store('avatars', 'public');
                        $company_group->img_certificado      = $path;
                    }

                    $company_group->nombre         = $request->nombre;
                    $company_group->identificacion = $request->nit;
                    $company_group->estado         = $request->estado;
                    $company_group->sector_id      = $request->sector;
                    $company_group->ciudad_id      = $request->ciudad;
                    $company_group->asesor_id      = $request->asesor == 'null' ? null : $request->asesor;

                    $company_group->save();

                    SavkLideresGrupoEmpresa::where('id_grupo_empresa', $request->id)->delete();

                    if($request->lideres){
                        foreach ($request->lideres as $lider) {
                            SavkLideresGrupoEmpresa::create([
                                'id_usuario' => $lider,
                                'id_grupo_empresa' => $company_group->id,
                            ]);
                        }
                    }


                    return response()->json([
                        'status' => 201,
                        'msg' => 'Información actualizada'
                    ]);

                    break;
            }
        } catch (\Exception $ex) {
            return response()->json([
                'status' => 500,
                'msg' => $ex->getMessage()
            ]);
        }
    }

    public function mensajesCompanyGroup(Request $request){
        $user = User::find($request->id_usuario);
        try {
            $pregunta = CaPreguntasUsuarios::find($request->id);
            $pregunta->respuesta = $request->respuesta;
            $pregunta->estado = 1;
            $pregunta->save();

            Mail::to($user->email)->send(new RespuestaAsesor($request));

            return response()->json([
                'status' => 200,
                'msg' => 'La respuesta se ha registrado con éxito. Se ha enviado una notificación al correo del usuario.'
            ]);
        } catch (\Exception $ex) {
            return response()->json([
                'status' => 500,
                'msg' => $ex->getMessage()
            ]);
        }
    }

    public function getAllCompanysGroup(Request $request, CentroOperacion $centro_operacion)
    {
        $cant_pag = 10;
        $whereIn = [];
        $where = [];
        $puntos = [];
        if (\Auth::user()->savk_principal == 1) { //SAVK PRINCIPAL
            $where = [
                ['centro_operacion.main_account_id', \Auth::user()->main_account_id]
            ];
        }else if(auth()->user()->id_grupo == 30 || auth()->user()->id_grupo == 39){// ASESOR
            $where = [
                ['asesor_id', \Auth::user()->id]
            ];
        }else if(auth()->user()->id_grupo == 44){// LIDER GRUPO EMPRESA
            $lider = SavkLideresGrupoEmpresa::where('id_usuario', \Auth::user()->id)->pluck('id_grupo_empresa')->toArray();
            $whereIn =  $lider;
            if (sizeof($whereIn) == 0) {
                //NO TIENE GRUPO EMPRESA ASIGNADO
                $where = [
                    ['centro_operacion.id', null] //Se deja null para que no devuelva ningún valor
                ];
            }
        }else{ //NO TIENE ACCESO
            $where = [
                ['centro_operacion.id', null] //Se deja null para que no devuelva ningún valor
            ];
        }

        if (strlen($request->get('search')) != 0) {
            array_push($where, ['centro_operacion.nombre', 'LIKE', "%$request->search%"]);
        }

        if (sizeof($request->get('paginate')) > 0) {
            $cant_pag = $request->paginate['cant'];
        }

        $data = $centro_operacion->getAll($where, $cant_pag, $whereIn);


        foreach ($data as $d) {
            $d->lideres = SavkLideresGrupoEmpresa::select('id_usuario as id')->where('id_grupo_empresa', $d->id)->pluck('savk_lideres_grupo_empresa.id');

            if(auth()->user()->id_grupo == 30 || auth()->user()->id_grupo == 39){
                $puntos = PuntoEvaluacion::join('unidad as u','u.id', 'unidad_id' )
                ->where('punto_evaluacion.estado', 1)
                ->where('u.centro_operacion_id',$d->id)
                ->pluck('punto_evaluacion.id')->toArray();

                $d->mensajes = CaPreguntasUsuarios::select('ca_preguntas_usuarios.*','u.nombre_com', 'c.nombre as capacitacion',
                \DB::raw('date_format(ca_preguntas_usuarios.created_at, "%d-%m-%Y") as fecha_formateada'),
                \DB::raw("
                (CASE
                    WHEN ca_preguntas_usuarios.estado = '0' THEN 'Por contestar'
                    WHEN ca_preguntas_usuarios.estado = '1' THEN 'Contestada'
                END) AS estado_pregunta")
                )
                ->join('usuarios as u','u.id','id_usuario')
                ->join('ca_capacitaciones as c','c.id','id_capacitacion')
                ->whereIn('u.id_punto', $puntos)
                ->get();
            }
        }

        return response()->json([
            'status' => 200,
            'data' => $data,
            'admin' =>  auth()->user()->id_grupo == 44 ? 1 : 0
        ]);
    }

    public function getAllZona(Request $request, GruposPuntos $gruposPuntos)
    {
        $cant_pag = 10;
        $whereIn = [];
        $where = [];
        $sinAsignar = [];

        if (\Auth::user()->savk_principal == 1 || auth()->user()->id_grupo == 44) {
            $sinAsignar = PuntoEvaluacion::select('id', 'nombre')
            ->where('main_account_id', auth()->user()->main_account_id)
            ->whereNotIn('id', function ($query) {
                $query->select('punto_id')
                        ->from('grupos_sub_puntos');
            })
            ->get();
        }

        if (\Auth::user()->savk_principal == 1) { //SAVK PRINCIPAL
            $where = [
                ['grupos_puntos.main_account_id', \Auth::user()->main_account_id]
            ];
        }else if(auth()->user()->id_grupo == 30 || auth()->user()->id_grupo == 39){// ASESOR
            $where = [
                ['asesor_id', \Auth::user()->id]
            ];
        }else if(auth()->user()->id_grupo == 44 || auth()->user()->id_grupo == 45 ||  auth()->user()->id_grupo == 47){
            $ge = SavkLideresGrupoEmpresa::where('id_usuario', auth()->user()->id)->exists();
            $e = SavkLideresEmpresa::where('id_usuario', auth()->user()->id)->exists();
            $cc = SavkLideresCentroDeCostos::where('id_usuario', auth()->user()->id)->exists();

            if($ge || $e || $cc){
                $where = [
                    ['grupos_puntos.main_account_id', \Auth::user()->main_account_id]
                ];
            }else{
                $sinAsignar = [];
                $where = [
                    ['grupos_puntos.id', null] //Se deja null para que no devuelva ningún valor
                ];
            }

        }else if(auth()->user()->id_grupo == 46){
            $whereIn = SavkLideresZonas::where('id_usuario',auth()->user()->id)
            ->pluck('id_grupos_puntos')->toArray();

            if(sizeof($whereIn) == 0){
                $where = [
                    ['grupos_puntos.id', null] //Se deja null para que no devuelva ningún valor
                ];
            }
        }else{ //NO TIENE ACCESO
            $where = [
                ['grupos_puntos.id', null] //Se deja null para que no devuelva ningún valor
            ];
        }

        if (strlen($request->get('search')) != 0) {
            array_push($where, ['grupos_puntos.nombre', 'LIKE', "%$request->search%"]);
        }

        if (sizeof($request->get('paginate')) > 0) {
            $cant_pag = $request->paginate['cant'];
        }

        $data = $gruposPuntos->getAll($where, $cant_pag, $whereIn);

        foreach ($data as $d) {
            $d->lideres = SavkLideresZonas::select('id_usuario as id')->where('id_grupos_puntos', $d->id)->pluck('id');
            $d->centros = GruposSubPuntos::select('punto_id', 'p.nombre')
            ->join('punto_evaluacion as p', 'p.id', 'punto_id')
            ->where([
                ['grupo_punto_id', $d->id],
                ['p.main_account_id', auth()->user()->main_account_id]
            ])
            ->get();
        }

        return response()->json([
            'status' => 200,
            'data' => $data,
            'main_account_id' => Auth::user()->main_account_id,
            'sinAsignar' => $sinAsignar
        ]);
    }

    public function createZona(Request $request)
    {
        try {
            switch ($request->get('mode')) {
                case 'create':
                    $new_zona = GruposPuntos::insertGetId([
                        'nombre'          => $request->nombre['val'],
                        'main_account_id' => \Auth::user()->main_account_id,
                    ]);

                    foreach ($request->lideres['val'] as $lider) {
                        SavkLideresZonas::create([
                            'id_usuario' => $lider,
                            'id_grupos_puntos' => $new_zona,
                        ]);
                    }

                    return response()->json([
                        'status' => 201,
                        'msg' => 'Se ha creado la zona'
                    ]);
                    break;

                case 'edit':
                    $zona_edit = GruposPuntos::find($request->id['val']);

                    if (is_null($zona_edit)) {
                        return response()->json([
                            'status' => 202,
                            'msg' => 'No se encontró la zona a editar.'
                        ]);
                    }

                    $zona_edit->nombre         = $request->nombre['val'];
                    $zona_edit->save();

                    SavkLideresZonas::where('id_grupos_puntos', $request->id['val'])->delete();

                    foreach ($request->lideres['val'] as $lider) {
                        SavkLideresZonas::create([
                            'id_usuario' => $lider,
                            'id_grupos_puntos' => $request->id['val'],
                        ]);
                    }

                    return response()->json([
                        'status' => 201,
                        'msg' => 'Información actualizada'
                    ]);

                    break;
            }
        } catch (\Exception $ex) {
            return response()->json([
                'status' => 500,
                'msg' => $ex->getMessage()
            ]);
        }
    }

    public function deleteZona(Request $request)
    {
        $zonaDelete = GruposPuntos::find($request->id);

        if (is_null($zonaDelete)) {
            return response()->json([
                'status' => 202,
                'msg' => 'No se encontró el grupo empresa'
            ]);
        }

        //Validación de trazabilidad
        $empresas = GruposSubPuntos::where('grupo_punto_id', $request->id)->exists();

        if ($empresas) {
            return response()->json([
                'status' => 202,
                'msg' => 'No se puede eliminar la zona porque tiene centros de costo asociados.'
            ]);
        }

        SavkLideresZonas::where('id_grupos_puntos', $request->id)->delete();
        $zonaDelete->delete();

        return response()->json([
            'status' => 200,
            'msg' => 'Se ha eliminado la zona.'
        ]);
    }

    public function deleteCompanyGroup(Request $request)
    {
        $centro_operacion = CentroOperacion::find($request->id);

        if (is_null($centro_operacion)) {
            return response()->json([
                'status' => 202,
                'msg' => 'No se encontró el grupo empresa'
            ]);
        }

        //Validación de trazabilidad
        $empresas = Unidad::where('centro_operacion_id', $request->id)->exists();

        if ($empresas) {
            return response()->json([
                'status' => 202,
                'msg' => 'No se puede eliminar el Grupo Empresa porque tiene empresas asociadas.'
            ]);
        }

        SavkLideresGrupoEmpresa::where('id_grupo_empresa', $centro_operacion->id)->delete();

        $centro_operacion->delete();

        return response()->json([
            'status' => 200,
            'msg' => 'Se ha eliminado el grupo empresa.'
        ]);
    }

    public function getCompaniesGroup()
    {
        $data = CentroOperacion::select('centro_operacion.id', 'centro_operacion.nombre as name')
        ->join('unidad as u', 'u.centro_operacion_id', 'centro_operacion.id')
        ->join('punto_evaluacion as pe', 'pe.unidad_id', 'u.id')
        ->where([
            ['centro_operacion.estado', 1],
            ['centro_operacion.main_account_id', \Auth::user()->main_account_id]
        ])
        ->groupBy('centro_operacion.id');

        if(auth()->user()->savk_principal == 1){// PRINCIPAL
            //NO TIENE RESTRICCIONES
        }else if(auth()->user()->id_grupo == 30 || auth()->user()->id_grupo == 39){// ASESOR
            $data->where('centro_operacion.asesor_id', \Auth::user()->id);
        }else if(auth()->user()->id_grupo == 44){// LIDER GRUPO EMPRESA
            $lider = SavkLideresGrupoEmpresa::where('id_usuario', \Auth::user()->id)->pluck('id_grupo_empresa')->toArray();
            $data->whereIn('centro_operacion.id', $lider);
        }else if(auth()->user()->id_grupo == 45){// LIDER EMPRESA
            $lider = SavkLideresEmpresa::where('id_usuario', \Auth::user()->id)->pluck('id_empresa')->toArray();
            $data->whereIn('u.id', $lider);
        }else if(auth()->user()->id_grupo == 46){// LIDER ZONA
            $lider = SavkLideresZonas::join('grupos_sub_puntos', 'grupos_sub_puntos.grupo_punto_id', 'id_grupos_puntos')
            ->where('id_usuario',auth()->user()->id)
            ->pluck('punto_id')->toArray();

            $data->whereIn('pe.id', $lider);
        }else if(auth()->user()->id_grupo == 47){// LIDER CENTRO DE COSTO
            $lider = SavkLideresCentroDeCostos::select('savk_lideres_centro_de_costos.id_centro_de_costo')
            ->where('id_usuario', Auth::user()->id)
            ->pluck('id_centro_de_costo')->toArray();

            $data->whereIn('pe.id', $lider);
        }else{
            $data->where('pe.id', 0);
        }

        return response()->json([
            'status' => 200,
            'data' => $data->get()
        ]);

        // return response()->json([
        //     'status' => 200,
        //     'data' => CentroOperacion::select('id', 'nombre as name')->where([
        //         ['estado', 1],
        //         ['centro_operacion.main_account_id', \Auth::user()->main_account_id]
        //     ])->get()
        // ]);
    }

    //FIN GRUPO EMPRESA

    //EMPRESA
    public function createCompany(Request $request)
    {
        try {
            switch ($request->get('mode')) {
                case 'create':
                    $file = $request->file('logo');
                    $path = null;
                    if($file){
                        $path = $file->store('avatars', 'public');
                    }

                    $company_group = Unidad::where('nit', $request->nit)->exists();

                    if ($company_group) {
                        return response()->json([
                            'status' => 202,
                            'msg' => 'Ya existe el NIT'
                        ]);
                    }

                    $new_unidad = Unidad::create([
                        'nombre' => $request->nombre,
                        'nit' => $request->nit,
                        'direccion' => $request->dir,
                        'telefono' => $request->tel,
                        'email' => $request->email == 'null' ? null : $request->email,
                        'pais_id' => $request->pais,
                        'ciudad_id' => $request->ciudad,
                        'departamentos_id' => $request->departamento,
                        'estado' => $request->estado,
                        'centro_operacion_id' => $request->centro_operacion,
                        'main_account_id' => \Auth::user()->main_account_id,
                        'img_avatar'      => $path
                    ]);

                    if($request->lideres){
                        foreach ($request->lideres as $lider) {
                            SavkLideresEmpresa::create([
                                'id_usuario' => $lider,
                                'id_empresa' => $new_unidad->id,
                            ]);
                        }
                    }

                    return response()->json([
                        'status' => 201,
                        'msg' => 'Se ha creado la Empresa.'
                    ]);
                    break;

                case 'edit':
                    $unidad = Unidad::find($request->id);

                    if (is_null($unidad)) {
                        return response()->json([
                            'status' => 202,
                            'msg' => 'No se encontró la empresa a editar.'
                        ]);
                    }

                    $unidad_exist = Unidad::where([
                        ['nit', $request->nit],
                        ['id', '<>', $request->id]
                    ])->exists();

                    if ($unidad_exist) {
                        return response()->json([
                            'status' => 202,
                            'msg' => 'Ya existe el NIT'
                        ]);
                    }

                    $file = $request->file('logo');
                    if($file){
                        // Verificar y eliminar el archivo existente si es necesario
                        if ($unidad->img_avatar) {
                            $existingFilePath = storage_path('app/public/' . $unidad->img_avatar);
                            if (\File::exists($existingFilePath)) {
                                \File::delete($existingFilePath);
                            }
                        }

                        $path = $file->store('avatars', 'public');
                        $unidad->img_avatar      = $path;
                    }

                    $unidad->nombre = $request->nombre;
                    $unidad->direccion = $request->dir;
                    $unidad->telefono = $request->tel;
                    $unidad->email = $request->email == 'null' ? null : $request->email;
                    $unidad->nit = $request->nit;
                    $unidad->estado = $request->estado;
                    $unidad->centro_operacion_id = $request->centro_operacion;
                    $unidad->ciudad_id = $request->ciudad;
                    $unidad->pais_id = $request->pais;
                    $unidad->departamentos_id = $request->departamento;

                    $unidad->save();

                    SavkLideresEmpresa::where('id_empresa', $request->id)->delete();

                    if($request->lideres){
                        foreach ($request->lideres as $lider) {
                            SavkLideresEmpresa::create([
                                'id_usuario' => $lider,
                                'id_empresa' => $unidad->id,
                            ]);
                        }
                    }

                    return response()->json([
                        'status' => 201,
                        'msg' => 'Información actualizada'
                    ]);

                    break;
            }
        } catch (\Exception $ex) {
            return response()->json([
                'status' => 500,
                'msg' => $ex->getMessage()
            ]);
        }
    }

    public function getAllCompanies(Request $request, Unidad $unidad)
    {
        $cant_pag = 10;

        $whereIn = [];
        $where = [];
        if (\Auth::user()->savk_principal == 1) { //SAVK PRINCIPAL
            $where = [
                ['unidad.main_account_id', \Auth::user()->main_account_id]
            ];
        }else if(auth()->user()->id_grupo == 30 || auth()->user()->id_grupo == 39){// ASESOR
            $where = [
                ['co.asesor_id', \Auth::user()->id]
            ];
        }else if(auth()->user()->id_grupo == 44){// LIDER GRUPO EMPRESA
            $lider = SavkLideresGrupoEmpresa::where('id_usuario', \Auth::user()->id)->pluck('id_grupo_empresa')->toArray();
            $whereIn =  $lider;

            if (sizeof($whereIn) == 0) {
                //NO TIENE GRUPO EMPRESA ASIGNADO
                $where = [
                    ['unidad.id', null] //Se deja null para que no devuelva ningún valor
                ];
            }
        }else if(auth()->user()->id_grupo == 45){// LIDER EMPRESA
            $lider = SavkLideresEmpresa::where('id_usuario', \Auth::user()->id)->pluck('id_empresa')->toArray();
            $whereIn =  $lider;

            if (sizeof($whereIn) == 0) {
                //NO TIENE GRUPO EMPRESA ASIGNADO
                $where = [
                    ['unidad.id', null] //Se deja null para que no devuelva ningún valor
                ];
            }
        }else{ //NO TIENE ACCESO
            $where = [
                ['unidad.id', null] //Se deja null para que no devuelva ningún valor
            ];
        }

        if (sizeof($request->get('paginate')) > 0) {
            $cant_pag = $request->paginate['cant'];
        }

        if (strlen($request->get('search')) != 0) {
            array_push($where, ['unidad.nombre', 'LIKE', "%$request->search%"]);
        }

        $data = $unidad->getAll($where, $cant_pag, $whereIn);

        foreach ($data as $d) {
            $d->lideres = SavkLideresEmpresa::select('id_usuario as id')->where('id_empresa', $d->id)->pluck('savk_lideres_empresa.id');
        }


        return response()->json([
            'status' => 200,
            'data' => $data
        ]);
    }

    public function deleteCompany(Request $request)
    {
        $centro_operacion = Unidad::find($request->id);

        if (is_null($centro_operacion)) {
            return response()->json([
                'status' => 202,
                'msg' => 'No se encontró la empresa'
            ]);
        }

        //Validación de trazabilidad
        $puntos = PuntoEvaluacion::where('unidad_id', $request->id)->exists();

        if ($puntos) {
            return response()->json([
                'status' => 202,
                'msg' => 'No se puede eliminar la Empresa porque tiene puntos de evaluación asociadas.'
            ]);
        }

        $centro_operacion->delete();

        return response()->json([
            'status' => 200,
            'msg' => 'Se ha eliminado la empresa.'
        ]);
    }

    public function getCompaniesAll()
    {
        $data = Unidad::select('unidad.id', 'unidad.nombre as name')
        ->join('centro_operacion as co', 'co.id', 'unidad.centro_operacion_id')
        ->join('punto_evaluacion as pe', 'pe.unidad_id', 'unidad.id')
        ->where([
            ['unidad.estado', 1],
            ['unidad.main_account_id', \Auth::user()->main_account_id]
        ])
        ->groupBy('unidad.id');;

        if(auth()->user()->savk_principal == 1){// PRINCIPAL
            //NO TIENE RESTRICCIONES
        }else if(auth()->user()->id_grupo == 30 || auth()->user()->id_grupo == 39){// ASESOR
            $data->where('co.asesor_id', \Auth::user()->id);
        }else if(auth()->user()->id_grupo == 44){// LIDER GRUPO EMPRESA
            $lider = SavkLideresGrupoEmpresa::where('id_usuario', \Auth::user()->id)->pluck('id_grupo_empresa')->toArray();
            $data->whereIn('co.id', $lider);
        }else if(auth()->user()->id_grupo == 45){// LIDER EMPRESA
            $lider = SavkLideresEmpresa::where('id_usuario', \Auth::user()->id)->pluck('id_empresa')->toArray();
            $data->whereIn('u.id', $lider);
        }else if(auth()->user()->id_grupo == 46){// LIDER ZONA
            $lider = SavkLideresZonas::join('grupos_sub_puntos', 'grupos_sub_puntos.grupo_punto_id', 'id_grupos_puntos')
            ->where('id_usuario',auth()->user()->id)
            ->pluck('punto_id')->toArray();

            $data->whereIn('pe.id', $lider);
        }else if(auth()->user()->id_grupo == 47){// LIDER CENTRO DE COSTO
            $lider = SavkLideresCentroDeCostos::select('savk_lideres_centro_de_costos.id_centro_de_costo')
            ->where('id_usuario', Auth::user()->id)
            ->pluck('id_centro_de_costo')->toArray();

            $data->whereIn('pe.id', $lider);
        }else{
            $data->where('pe.id', 0);
        }

        return $data->get();
    }
    //FIN EMPRESA

    //PUNTO EVALUACIÓN
    public function createEvaluationPoint(Request $request)
    {
        try {
            switch ($request->get('mode')) {
                case 'create':
                    $codigo = Str::random(10);
                    $new_unidad = PuntoEvaluacion::create([
                        'nombre' => $request->nombre['val'],
                        'direccion' => $request->dir['val'],
                        'telefono' => $request->tel['val'],
                        'correo' => $request->email['val'],
                        'main_account_id' => \Auth::user()->main_account_id,
                        'pais_id' => $request->pais['val'],
                        'ciudad_id' => $request->ciudad['val'],
                        'departamentos_id' => $request->departamento['val'],
                        'estado' => $request->estado['val'],
                        'unidad_id' => $request->empresa['val'],
                        'codigo' => "savk_$codigo"
                    ]);

                    foreach ($request->lideres['val'] as $lider) {
                        SavkLideresCentroDeCostos::create([
                            'id_usuario' => $lider,
                            'id_centro_de_costo' => $new_unidad->id,
                        ]);
                    }

                    if(isset($request->zona['val']) && $request->zona['val'] != '' && $request->zona['val'] != 'null'){
                        $zona = GruposSubPuntos::create([
                            'grupo_punto_id' => $request->zona['val'],
                            'punto_id' => $new_unidad->id,
                        ]);
                    }

                    if ($new_unidad) {
                        //KLARENGP INIT
                        $ciudad = \DB::connection('mysql')->table('ciudades')->where('id', $request->ciudad['val'])->first()->nombre;
                        $empresa = Unidad::where('id', $request->empresa['val'])->first()->nit;
                        $empresa_klgp  = \DB::connection('ka_mysql')->table('tda_empresa')->where('nit', $empresa)->first();
                        $empresa_klgp = $empresa_klgp->id ?? null;

                        if ($empresa_klgp) {
                            \DB::connection('ka_mysql')->table('tda_departamento')
                            ->insert([
                                'nombre' => $request->nombre['val'],
                                'direccion' => $request->dir['val'],
                                'telefono' => $request->tel['val'],
                                'correo' => $request->email['val'],
                                'ciudad' => $ciudad,
                                'estado' => $request->estado['val'],
                                'codigo' => "savk_$codigo",
                                'id_empresa' => $empresa_klgp
                            ]);
                        }
                        //KLARENGP END
                    }

                    return response()->json([
                        'status' => 201,
                        'msg' => 'Se ha creado el Punto de Evaluación.'
                    ]);
                    break;

                case 'edit':
                    $punto = PuntoEvaluacion::find($request->id['val']);

                    if (is_null($punto)) {
                        return response()->json([
                            'status' => 202,
                            'msg' => 'No se encontró el punto de evaluación a editar.'
                        ]);
                    }

                    $punto->nombre = $request->nombre['val'];
                    $punto->direccion = $request->dir['val'];
                    $punto->telefono = $request->tel['val'];
                    $punto->correo = $request->email['val'];
                    $punto->estado = $request->estado['val'];
                    $punto->unidad_id = $request->empresa['val'];
                    $punto->ciudad_id = $request->ciudad['val'];
                    $punto->pais_id = $request->pais['val'];
                    $punto->departamentos_id = $request->departamento['val'];

                    $punto->save();

                    SavkLideresCentroDeCostos::where('id_centro_de_costo', $request->id['val'])->delete();

                    foreach ($request->lideres['val'] as $lider) {
                        SavkLideresCentroDeCostos::create([
                            'id_usuario' => $lider,
                            'id_centro_de_costo' => $punto->id,
                        ]);
                    }

                    GruposSubPuntos::where('punto_id', $punto->id)->delete();
                    if(isset($request->zona['val']) && $request->zona['val'] != '' && $request->zona['val'] != 'null'){
                        $zona = GruposSubPuntos::create([
                            'grupo_punto_id' => $request->zona['val'],
                            'punto_id' => $punto->id,
                        ]);
                    }

                    //KLARENGP INIT
                    $ciudad = \DB::connection('mysql')->table('ciudades')->where('id', $request->ciudad['val'])->first()->nombre;
                    $empresa = Unidad::where('id', $request->empresa['val'])->first()->nit;
                    $empresa_klgp  = \DB::connection('ka_mysql')->table('tda_empresa')->where('nit', $empresa)->first();
                    $empresa_klgp = $empresa_klgp->id ?? null;
                    if ($empresa_klgp) {
                        \DB::connection('ka_mysql')->table('tda_departamento')
                        ->where('codigo', $punto->codigo)
                        ->update([
                            'nombre' => $request->nombre['val'],
                            'direccion' => $request->dir['val'],
                            'telefono' => $request->tel['val'],
                            'correo' => $request->email['val'],
                            'ciudad' => $ciudad,
                            'estado' => $request->estado['val'],
                            'id_empresa' => $empresa_klgp
                        ]);
                    }
                    //KLARENGP END

                    return response()->json([
                        'status' => 201,
                        'msg' => 'Información actualizada'
                    ]);

                    break;
            }
        } catch (\Exception $ex) {
            return response()->json([
                'status' => 500,
                'msg' => $ex->getMessage()
            ]);
        }
    }

    public function getAllEvaluationPoints(Request $request, PuntoEvaluacion $puntos)
    {
        $cant_pag = 10;

        $whereIn = [];
        $where = [];
        if (\Auth::user()->savk_principal == 1) { //SAVK PRINCIPAL
            $where = [
                ['punto_evaluacion.main_account_id', \Auth::user()->main_account_id]
            ];
        }else if(auth()->user()->id_grupo == 30 || auth()->user()->id_grupo == 39){// ASESOR
            $where = [
                ['co.asesor_id', \Auth::user()->id]
            ];
        }else if(auth()->user()->id_grupo == 44){// LIDER GRUPO EMPRESA
            $lider = SavkLideresGrupoEmpresa::where('id_usuario', \Auth::user()->id)->pluck('id_grupo_empresa')->toArray();
            $empresas = Unidad::whereIn('centro_operacion_id', $lider)->pluck('id')->toArray();
            $whereIn =  $empresas;
        }else if (Auth::user()->id_grupo == 45) {
            $whereIn = SavkLideresEmpresa::select(
                'punto_evaluacion.id'
            )
            ->join('punto_evaluacion', 'punto_evaluacion.unidad_id', 'savk_lideres_empresas.id_empresa')
            ->where('savk_lideres_empresas.id_usuario', Auth::user()->id)
            ->get()
            ->pluck('id')->toArray();
        } else if (Auth::user()->id_grupo == 46) {
            $whereIn = SavkLideresZonas::join('grupos_sub_puntos', 'grupos_sub_puntos.grupo_punto_id', 'id_grupos_puntos')
            ->where('id_usuario',auth()->user()->id)
            ->pluck('punto_id')->toArray();
        } else if (Auth::user()->id_grupo == 47) {
            $whereIn = SavkLideresCentroDeCostos::select(
                'savk_lideres_centro_de_costos.id_centro_de_costo'
            )
                ->where('savk_lideres_centro_de_costos.id_usuario', Auth::user()->id)
                ->get()
                ->pluck('id_centro_de_costo')->toArray();
        }else{ //NO TIENE ACCESO
            $where = [
                ['punto_evaluacion.id', null] //Se deja null para que no devuelva ningún valor
            ];
        }

        if (sizeof($whereIn) == 0 && sizeof($where) == 0) {
            //NO TIENE GRUPO EMPRESA ASIGNADO
            $where = [
                ['punto_evaluacion.id', null] //Se deja null para que no devuelva ningún valor
            ];
        }

        if (sizeof($request->get('paginate')) > 0) {
            $cant_pag = $request->paginate['cant'];
        }

        if (strlen($request->get('search')) != 0) {
            array_push($where, ['punto_evaluacion.nombre', 'LIKE', "%$request->search%"]);
        }

        $data = $puntos->getAll($where, $cant_pag, $whereIn);

        foreach ($data as $d) {
            $d->lideres = SavkLideresCentroDeCostos::select('id_usuario as id')->where('id_centro_de_costo', $d->id)->pluck('savk_lideres_centro_de_costos.id');
        }

        return response()->json([
            'status' => 200,
            'data' => $data,
            'main_account_id' => Auth::user()->main_account_id,
        ]);
    }

    public function deleteEvaluationPoint(Request $request)
    {
        try {
            $centro_operacion = PuntoEvaluacion::find($request->id);

            if (is_null($centro_operacion)) {
                return response()->json([
                    'status' => 202,
                    'msg' => 'No se encontró el grupo empresa'
                ]);
            }
            $centro_operacion->delete();

            return response()->json([
                'status' => 200,
                'msg' => 'Se ha eliminado el punto de evaluación.'
            ]);
        } catch (\Exception $ex) {
            return response()->json([
                'status' => 202,
                'msg' => 'No se puede eliminar el centro de costo por que tiene trazabilidad.'
            ]);
        }


        //Validación de trazabilidad
        // $empresas = Unidad::where('centro_operacion_id', $request->id)->exists();
    }

    public function getEvaluationPointsAll()
    {
        if (\Auth::user()->savk_principal == 1) {
            $data = PuntoEvaluacion::select('nombre as name', 'id')
            ->where([
                ['estado', 1],
                ['main_account_id', \Auth::user()->main_account_id]
            ])->get();
        }else if (Auth::user()->id_grupo == 44){
            $grupoEmpresa = SavkLideresGrupoEmpresa::where('id_usuario', \Auth::user()->id)->pluck('id_grupo_empresa')->toArray(); //LÍDER GRUPO EMPRESA
            $empresas = Unidad::whereIn('centro_operacion_id', $grupoEmpresa)->pluck('id')->toArray();

            $data = PuntoEvaluacion::select('nombre as name', 'id')
            ->where('estado', 1)
            ->whereIn('unidad_id', $empresas)
            ->get();
        }else{
            switch (Auth::user()->id_grupo) {
                case 45:
                    $ids = SavkLideresEmpresa::select(
                        'punto_evaluacion.id'
                    )
                    ->join('punto_evaluacion', 'punto_evaluacion.unidad_id', 'savk_lideres_empresas.id_empresa')
                    ->where('savk_lideres_empresas.id_usuario', Auth::user()->id)
                    ->get()
                    ->pluck('id');
                    break;
                case 46:
                    $ids = SavkLideresZonas::join('grupos_sub_puntos', 'grupos_sub_puntos.grupo_punto_id', 'id_grupos_puntos')
                    ->where('id_usuario',auth()->user()->id)
                    ->pluck('punto_id')->toArray();
                    break;
                case 47:
                    $ids = SavkLideresCentroDeCostos::select(
                        'savk_lideres_centro_de_costos.id_centro_de_costo'
                    )
                    ->where('savk_lideres_centro_de_costos.id_usuario', Auth::user()->id)
                    ->get()
                    ->pluck('id_centro_de_costo');
                    break;
                default:
                    $ids = [];
                    break;
            }

            $data = PuntoEvaluacion::select('nombre as name', 'id')
            ->where('estado', 1)
            ->whereIn('id', $ids)
            ->get();
        }

        return $data;
    }

    public function getSeccionesAll()
    {
        return SavkSecciones::select(
            'nombre as name',
            'id'
        )
            ->where([
                ['main_account_id', \Auth::user()->main_account_id]
            ])->get();
    }
    //FIN PUNTO EVALUACIÓN

    //USUARIOS
    public function getAllUsers(Request $request, User $users)
    {
        $cant_pag = 10;

        $whereIn = [];
        $where = [];
        if (\Auth::user()->savk_principal == 1) { //SAVK PRINCIPAL
            $where = [
                ['usuarios.main_account_id', \Auth::user()->main_account_id]
            ];
        }else if(auth()->user()->id_grupo == 30 || auth()->user()->id_grupo == 44 || auth()->user()->id_grupo == 39){// ASESOR Y LÍDER GRUPO EMPRESA
            if (auth()->user()->id_grupo == 30 || auth()->user()->id_grupo == 39) {
                $grupoEmpresa = CentroOperacion::where('asesor_id', \Auth::user()->id)->pluck('id')->toArray(); //ASESOR
            }else if (auth()->user()->id_grupo == 44){
                $grupoEmpresa = SavkLideresGrupoEmpresa::where('id_usuario', \Auth::user()->id)->pluck('id_grupo_empresa')->toArray(); //LÍDER
            }

            $empresas = Unidad::whereIn('centro_operacion_id', $grupoEmpresa)->pluck('id')->toArray();
            $puntos = PuntoEvaluacion::whereIn('unidad_id', $empresas)->pluck('id')->toArray();
            $whereIn =  $puntos;
            if (sizeof($whereIn) == 0) {
                $where = [
                    ['usuarios.id', null] //Se deja null para que no devuelva ningún valor
                ];
            }
        }else if (auth()->user()->id_grupo == 45){
            $ids = SavkLideresEmpresa::select(
                'punto_evaluacion.id'
            )
            ->join('punto_evaluacion', 'punto_evaluacion.unidad_id', 'savk_lideres_empresas.id_empresa')
            ->where('savk_lideres_empresas.id_usuario', Auth::user()->id)
            ->get()
            ->pluck('id');
            $whereIn =  $ids;
            if (sizeof($whereIn) == 0) {
                $where = [
                    ['usuarios.id', null] //Se deja null para que no devuelva ningún valor
                ];
            }
        }else if (auth()->user()->id_grupo == 46){
            $ids = SavkLideresZonas::join('grupos_sub_puntos', 'grupos_sub_puntos.grupo_punto_id', 'id_grupos_puntos')
            ->where('id_usuario',auth()->user()->id)
            ->pluck('punto_id')->toArray();
            $whereIn =  $ids;
            if (sizeof($whereIn) == 0) {
                $where = [
                    ['usuarios.id', null] //Se deja null para que no devuelva ningún valor
                ];
            }
        }else if (auth()->user()->id_grupo == 47){
            $ids = SavkLideresCentroDeCostos::select(
                'savk_lideres_centro_de_costos.id_centro_de_costo'
            )
            ->where('savk_lideres_centro_de_costos.id_usuario', Auth::user()->id)
            ->get()
            ->pluck('id_centro_de_costo');
            $whereIn =  $ids;
            if (sizeof($whereIn) == 0) {
                $where = [
                    ['usuarios.id', null] //Se deja null para que no devuelva ningún valor
                ];
            }
        }else{ //NO TIENE ACCESO
            $where = [
                ['usuarios.id', null] //Se deja null para que no devuelva ningún valor
            ];
        }

        if (sizeof($request->get('paginate')) > 0) {
            $cant_pag = $request->paginate['cant'];
        }

        // if (strlen($request->get('search')) != 0) {
        //     array_push($where, ['usuarios.nombre_com', 'LIKE', "%$request->search%"]);
        // }

        // $data = $users->getAll($where, $cant_pag);
        $data = $users->with(['perfil', 'empresasLider'])
            ->select(
                'usuarios.id',
                'usuarios.codigo',
                'usuarios.nombre_com AS nombre',
                'usuarios.email',
                'usuarios.telefono',
                'usuarios.estado AS estado_id',
                'usuarios.id_seccion',
                'usuarios.can_to_approve',
                \DB::raw("
                    (CASE
                        WHEN usuarios.estado = '1' THEN 'Activo'
                        WHEN usuarios.estado = '2' THEN 'Inactivo'
                    END) AS estado
                "),
                'usuarios.cargo_id',
                'ca.nombre AS cargo',
                'pa.nombre AS pais',
                'usuarios.pais_id',
                'de.nombre AS departamento',
                'usuarios.departamento_id',
                'c.nombre AS ciudad',
                'usuarios.ciudad_id',
                'usuarios.created_at',
                'savk_perfil_id',
                'id_grupo',
                'g.nombre as nombre_perfil',
                'usuarios.id_punto',
                'pe.nombre as punto',
                'u.nombre as empresa',
                'co.nombre as grupo_empresa'
            )
            ->leftJoin('paises AS pa', 'pa.id', 'usuarios.pais_id')
            ->leftJoin('departamenos AS de', 'de.id', 'usuarios.departamento_id')
            ->leftJoin('ciudades AS c', 'c.id', 'usuarios.ciudad_id')
            ->leftJoin('cargos AS ca', 'ca.id', 'usuarios.cargo_id')
            ->leftJoin('grupo AS g', 'g.id', 'usuarios.id_grupo')
            ->leftJoin('punto_evaluacion AS pe', 'pe.id', 'usuarios.id_punto')
            ->leftJoin('unidad AS u', 'u.id', 'pe.unidad_id')
            ->leftJoin('centro_operacion AS co', 'co.id', 'u.centro_operacion_id');

            if (sizeof($whereIn) > 0) {
                $data->where($where);
                $data->whereIn('usuarios.id_punto', $whereIn);
            } else {
                $data->where($where);
            }

            if (strlen($request->get('search')) != 0) {
                $search = $request->search;
                $data->where(function ($query) use ($search) {
                    $query->where('usuarios.nombre_com', 'LIKE', "%$search%")
                        ->orWhere('usuarios.codigo', 'LIKE', "%$search%")
                        ->orWhere('g.nombre', 'LIKE', "%$search%")
                        ->orWhere('usuarios.email', 'LIKE', "%$search%");
                });
            }

            $filters = $request->get('filters');
            $data->when(isset($filters['estado']['id']) && $filters['estado']['id'] != '0', function($query) use ($filters){
                return $query->where('usuarios.estado', $filters['estado']['id']);
            });

            $data->when(isset($filters['perfil']['id']) && $filters['perfil']['id'] != '0', function($query) use ($filters){
                return $query->where('usuarios.id_grupo', $filters['perfil']['id']);
            });

            $data->when(isset($filters['grupo_empresa']['id']) && $filters['grupo_empresa']['id'] != '0', function($query) use ($filters){
                return $query->where('co.id', $filters['grupo_empresa']['id']);
            });

            $data->when(isset($filters['empresa']['id']) && $filters['empresa']['id'] != '0', function($query) use ($filters){
                return $query->where('u.id', $filters['empresa']['id']);
            });

            $data->when(isset($filters['centro_costo']['id']) && $filters['centro_costo']['id'] != '0', function($query) use ($filters){
                return $query->where('pe.id', $filters['centro_costo']['id']);
            });

            $data = $data->orderBy('usuarios.nombre_com')->paginate($cant_pag);

        return response()->json([
            'status' => 200,
            'data' => $data,
            'perfil' => auth()->user()->id_grupo,
            'user_id' => auth()->user()->id,
            'main_account_id' => auth()->user()->main_account_id,
        ]);
    }

    public function getProfiles()
    {
        if (\Auth::user()->main_account_id == 1) {
            return DB::table('grupo')->select('id', 'nombre as name')->where('estado', 1)->get();
        }else if(\Auth::user()->main_account_id == 2 && \Auth::user()->savk_principal == 1){
            return DB::table('grupo')->select('id', 'nombre as name')
            ->where([
                ['estado', 1],
                ['tipo_perfil', 1],
                ['id','<>', 46]
            ])
            ->orWhere('id', 30)
            ->get();
        }else{
            $perfiles = DB::table('grupo')->select('id', 'nombre as name')->where('estado', 1)->where('tipo_perfil', 1);
            if (\Auth::user()->id_grupo == 44) {
                return $perfiles->get();
            }else if(\Auth::user()->id_grupo == 45 || \Auth::user()->id_grupo == 46){
                return $perfiles->whereIn('id', [47, 48])->get();
            }else{
                return $perfiles->where('id', 48)->get();
            }
        }
    }

    public function createSeccion(Request $request){
        $nombre = $request->nombre['val'];

        try {
            SavkSecciones::create([
                'nombre' => $nombre,
                'main_account_id' => \Auth::user()->main_account_id
            ]);

            return response()->json([
                'status' => 201,
                'msg' => 'Sección creado.'
            ]);
        } catch (\Exception $ex) {
            return response()->json([
                'status' => 500,
                'msg' => $ex->getMessage(),
                'ex' => $ex->getLine()
            ]);
        }
    }

    public function createUser(Request $request)
    {
        try {
            switch ($request->get('mode')) {
                case 'create':
                    if($request->codigo['val'] != '' && User::where('codigo', $request->codigo['val'])->exists())
                        return response()->json([
                            'status' => 202,
                            'msg' => 'Identificación ya se encuentra registrada.'
                        ]);

                    if(User::where('email', $request->email['val'])->exists())
                        return response()->json([
                            'status' => 202,
                            'msg' => 'Correo ya se encuentra registrado.'
                        ]);

                    $new_user = User::create([
                        'nombre_com'       => $request->nombre['val'],
                        'telefono'         => $request->tel['val'],
                        'codigo'           => $request->codigo['val'],
                        'email'            => $request->email['val'],
                        'usuario'          => $request->email['val'],
                        'password'         => \Hash::make($request->password['val']),
                        'main_account_id'  => \Auth::user()->main_account_id,
                        'pais_id'          => $request->pais['val'],
                        'ciudad_id'        => $request->ciudad['val'],
                        'departamentos_id' => $request->departamento['val'],
                        'estado'           => $request->estado['val'],
                        'savk_perfil_id'   => null,
                        'id_grupo'         => $request->profile['val'],
                        'id_punto'         => $request->punto['val'],
                        'id_seccion'       => isset($request->seccion['val']) && !empty($request->seccion['val']) ? $request->seccion['val'] : null,
                        'can_to_approve'   => $request->can_to_approve['val'],
                    ]);

                    $this->insertPermisos($new_user);
                    //Registro los puntos de venta
                    /* foreach ($request->punto['val'] as $punto_id) {
                        \DB::table('usuario_punto')->insert([
                            'usuario_id'  => $new_user->id,
                            'punto_id'    => $punto_id,
                            'responsable' => 0,
                            'created_at'  => now(),
                            'updated_at'  => now()
                        ]);
                    }*/

                    //Registro los puntos de los que es Lider
                    // if ((int) $request->profile['val'] === 2 || (int) $request->profile['val'] === 3) {
                    //     foreach ($request->lider_empresas['val'] as $el) {
                    //         $data = [
                    //             'usuario_id' => $new_user->id
                    //         ];
                    //         if ((int) $request->profile['val'] === 2) {
                    //             $data['empresa_id'] = $el;
                    //         } else { // 3
                    //             $data['punto_evaluacion_id'] = $el;
                    //         }
                    //         Lider::create($data);
                    //     }
                    // }

                    return response()->json([
                        'status' => 201,
                        'msg' => 'Se ha creado el usuario.'
                    ]);
                    break;

                case 'edit':
                    $user = User::find($request->id['val']);
                    $grupoActual = $user->id_grupo;

                    if (is_null($user)) {
                        return response()->json([
                            'status' => 202,
                            'msg' => 'No se encontró el usuario a editar.'
                        ]);
                    }

                    if($request->codigo['val'] != '' && User::where([['codigo', $request->codigo['val']],['id', '!=' , $user->id]])->exists())
                        return response()->json([
                            'status' => 202,
                            'msg' => 'Identificación ya se encuentra registrada.'
                        ]);

                    if(User::where([['email', $request->email['val']] ,['id', '!=' , $user->id]])->exists())
                        return response()->json([
                            'status' => 202,
                            'msg' => 'Correo ya se encuentra registrado.'
                        ]);

                    $user->nombre_com = $request->nombre['val'];
                    $user->codigo = $request->codigo['val'];
                    $user->telefono = isset($request->tel['val']) ? $request->tel['val'] :  $user->telefono;
                    $user->email = $request->email['val'];
                    $user->estado = $request->estado['val'];
                    $user->ciudad_id = isset($request->ciudad['val'])  ? $request->ciudad['val'] :  $user->ciudad_id;
                    $user->pais_id = isset($request->pais['val']) ? $request->pais['val'] :  $user->pais_id;
                    $user->departamento_id = isset($request->departamento['val']) ? $request->departamento['val'] :  $user->departamento_id;
                    $user->savk_perfil_id = null;
                    $user->id_grupo = isset($request->profile['val']) ? $request->profile['val'] :  $user->id_grupo;
                    $user->id_punto = $request->punto['val'];
                    $user->id_seccion = isset($request->seccion['val']) && !empty($request->seccion['val']) ? $request->seccion['val'] : null;
                    if (isset($request->can_to_approve['val'])) {
                        $user->can_to_approve = $request->can_to_approve['val'];
                    }


                    if (strlen($request->password['val']) > 0)
                        $user->password = \Hash::make($request->password['val']);

                    $user->save();

                    $this->insertPermisos($user);
                    if ($grupoActual != $user->id_grupo) {
                        switch ($user->id_grupo) {
                            case 44:
                                SavkLideresGrupoEmpresa::where('id_usuario', $user->id)->delete();
                                break;
                            case 45:
                                SavkLideresEmpresa::where('id_usuario', $user->id)->delete();
                                break;
                            case 46:
                                SavkLideresZonas::where('id_usuario', $user->id)->delete();
                                break;
                            case 47:
                                SavkLideresCentroDeCostos::where('id_usuario', $user->id)->delete();
                                break;
                            default:
                        }
                    }


                    //Reasigno los puntos de evaluación
                    //$this->reallocatePointsUser($user->id, $request->punto['val']);

                    // Asigno los puntos o empresas del cual es lider
                    // Lider::where('usuario_id', $user->id)->delete();
                    // if (
                    //     (int) $request->profile['val'] === 2 ||
                    //     (int) $request->profile['val'] === 3
                    // ) {
                    //     foreach ($request->lider_empresas['val'] as $el) {
                    //         $data = [
                    //             'usuario_id' => $user->id
                    //         ];
                    //         if ((int) $request->profile['val'] === 2) {
                    //             $data['empresa_id'] = $el;
                    //         } else { // 3
                    //             $data['punto_evaluacion_id'] = $el;
                    //         }
                    //         Lider::create($data);
                    //     }
                    // }

                    return response()->json([
                        'status' => 201,
                        'msg' => 'Información actualizada'
                    ]);

                    break;
            }
        } catch (\Exception $ex) {
            return response()->json([
                'status' => 500,
                'msg' => $ex->getMessage(),
                'ex' => $ex->getLine()
            ]);
        }
    }

    /**
     * Función para reasignar puntos de evaluación al usuario
     */
    private function reallocatePointsUser(int $user_id, array $points)
    {
        //Borro los actuales puntos
        $deleted = \DB::table('usuario_punto')->where('usuario_id', $user_id)->delete();

        foreach ($points as $point_id) {
            \DB::table('usuario_punto')->insert([
                'usuario_id' => $user_id,
                'punto_id' => $point_id,
                'responsable' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }

    public function getPointsUser($user_id, User $users)
    {
        return response()->json([
            'status' => 200,
            'data' => $users->getPointsUser((int) $user_id)
        ]);
    }

    public function getUserPoints($user_id)
    {
        $user = User::find($user_id);

        $puntoAsignado = User::selectRaw("'0' as responsable, pe.id, pe.nombre")
            ->join('punto_evaluacion as pe', 'pe.id', 'id_punto')
            ->where('usuarios.id', $user_id);

        $label = null;

        switch ($user->id_grupo) {
            case 44:
                $responsable = SavkLideresGrupoEmpresa::selectRaw("'1' as responsable, l.id, l.nombre")
                    ->join('centro_operacion as l', 'l.id', 'id_grupo_empresa')
                    ->where('id_usuario', $user_id);
                $label = 'los grupos empresas:';
                break;
            case 45:
                $responsable = SavkLideresEmpresa::selectRaw("'1' as responsable, l.id, l.nombre")
                    ->join('unidad as l', 'l.id', 'id_empresa')
                    ->where('id_usuario', $user_id);
                $label = 'las empresas:';
                break;
            case 47:
                $responsable = SavkLideresCentroDeCostos::selectRaw("'1' as responsable, pe.id, pe.nombre")
                    ->join('punto_evaluacion as pe', 'pe.id', 'id_centro_de_costo')
                    ->where('id_usuario', $user_id);
                $label = 'de los centros de costo:';
                break;

            default:
                break;
        }

        if (!empty($responsable)) {
            $data = $puntoAsignado->union($responsable)->get();
        } else {
            $data = $puntoAsignado->get();
        }

        return ['data' => $data, 'label' => $label];
    }

    public function deleteSeccion(Request $request){
        $seccion= SavkSecciones::find($request->id);

        //se valida trazabilidad
        $usuarios = User::where('id_seccion', $request->id)->exists();

        if ($usuarios) {
            return response()->json([
                'status' => 202,
                'msg' => 'No se puede eliminar por que ya existe trazabilidad.'
            ]);
        }

        $seccion->delete();

        return response()->json([
            'status' => 200,
            'msg' => 'Se ha eliminado la sección.'
        ]);
    }

    public function updateSeccion(Request $request){
        try {
            $seccion= SavkSecciones::find($request->id);

            $seccion->nombre = $request->valueSeccion;
            $seccion->save();

            return response()->json([
                'status' => 200,
                'msg' => 'Se actualizo la sección.'
            ]);
        } catch (\Exception $ex) {
            return response()->json([
                'status' => 500,
                'msg' => 'Algo salio mal.'
            ]);
        }
    }

    public function deleteUser(Request $request)
    {
        $user = User::find($request->id);

        if (is_null($user)) {
            return response()->json([
                'status' => 202,
                'msg' => 'No se encontró el usuario'
            ]);
        }

        //Validación de trazabilidad
        $evaluacionesIniciadas =  CaEvaluacionIniciada::where('id_usuario', $request->id)->exists();

        if ($evaluacionesIniciadas) {
            return response()->json([
                'status' => 202,
                'msg' => 'No se puede eliminar el usuario porque tiene evaluaciones iniciadas.'
            ]);
        }

        $capacitacionesIniciadas =  CaCapacitacionesIniciadas::where('id_usuario', $request->id)->exists();

        if ($capacitacionesIniciadas) {
            return response()->json([
                'status' => 202,
                'msg' => 'No se puede eliminar el usuario porque tiene capacitaciones iniciadas.'
            ]);
        }

        $activosFijos = DB::table('iac_activos_fijos')->where('user_id', $request->id)->exists();

        if ($activosFijos) {
            return response()->json([
                'status' => 202,
                'msg' => 'No se puede eliminar el usuario porque tiene activos fijos asociados.'
            ]);
        }

        // $empresas = Unidad::where('user_id', $request->id)->exists();

        // if ($empresas) {
        //     return response()->json([
        //         'status' => 202,
        //         'msg' => 'No se puede eliminar el Grupo Empresa porque tiene empresas asociadas.'
        //     ]);
        // }

        GuiaVisualizaciones::where('id_usuario', $request->id)->delete();
        SavkPermisosUsuarios::where('id_usuario', $request->id)->delete();

        $user->delete();

        return response()->json([
            'status' => 200,
            'msg' => 'Se ha eliminado el usuario.'
        ]);
    }

    //FIN USUARIOS

    public function getLideresGrupoEmpresa()
    {
        return \DB::table('usuarios')->select('id', 'nombre_com as text')->where([
            ['main_account_id', Auth::user()->main_account_id],
            ['savk_principal', '!=', 1],
            ['estado', 1],
            ['id_grupo', 44]
        ])->get();
    }
    public function getLideresEmpresa()
    {
        return \DB::table('usuarios')->select('id', 'nombre_com as text')->where([
            ['main_account_id', Auth::user()->main_account_id],
            ['savk_principal', '!=', 1],
            ['estado', 1],
            ['id_grupo', 45]
        ])->get();
    }

    public function getLideresCentroDeCosto()
    {
        return \DB::table('usuarios')->select('id', 'nombre_com as text')->where([
            ['main_account_id', Auth::user()->main_account_id],
            ['savk_principal', '!=', 1],
            ['estado', 1],
            ['id_grupo', 47]
        ])->get();
    }

    public function getLideresZonas()
    {
        return \DB::table('usuarios')->select('id', 'nombre_com as text')->where([
            ['main_account_id', Auth::user()->main_account_id],
            ['savk_principal', '!=', 1],
            ['estado', 1],
            ['id_grupo', 46]
        ])->get();
    }

    public function getDataMiPerfil()
    {
        $perfil =   \DB::table('usuarios')->select(
            'id',
            'codigo',
            'img_avatar',
            'nombre_com',
            'email',
            'id_punto'
        )->where(
            'id',
            Auth::user()->id
        )->first();

        return response()->json([
            'status' => 202,
            'msg' => 'Data encontrada.',
            'data' => $perfil
        ]);
    }

    public function updateAvatar(Request $request)
    {
        $path_before =  DB::table('usuarios')->select('img_avatar')->where('id', Auth::user()->id)->first();

        if (isset($path_before->img_avatar)) {
            $pathVerified = storage_path('app/public/') . $path_before->img_avatar;
            if (\File::exists($pathVerified))
                \File::delete($pathVerified);
        }

        $path = $this->FunctionResizeImage($request->avatar, 800, 800, 'avatars');

        DB::table('usuarios')->where('id', Auth::user()->id)->update([
            'img_avatar' => $path
        ]);

        return response()->json([
            'status' => 202,
            'msg' => 'Avatar actualizado.',
            'data' => $path
        ]);
    }
    public function GenerarLinkInvitacion()
    {

        $id = DB::table('usuarios')->select('id')->where([
            ['main_account_id', Auth::user()->main_account_id],
            ['savk_principal', 1]
        ])->first();
        $id = Crypt::encryptString($id->id);


        $link = CropLink(env('APP_URL') . '/registrarme-colaboradores/' . $id);

        return response()->json([
            'status' => 202,
            'msg' => 'Link generado.',
            'data' => $link
        ]);
    }
}
