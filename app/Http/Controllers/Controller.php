<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Models\Guia;
use App\Models\SavkPermisosUsuarios;
use App\Models\GuiaVisualizaciones;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;


class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function MessageResponse($entity, $code, $customMessage = "")
    {
        $errorCodes = array(
            //Success
            200 => "$entity ha sido creado",
            201 => "$entity ha sido actualizado",
            202 => "$entity encontrado",
            203 => "$entity ha sido eliminado",
            204 => "success custom",
            205 => "success custom",
            206 => "success custom",

            //Failed
            400 => "Datos invalidos",
            401 => "$entity ya está en uso",
            402 => "no se pudo guardar $entity",
            403 => "formato $entity incorrecto",
            404 => "$entity no se encontró",
            405 => "$entity no está disponible",
            406 => "failed custom",
            407 => "failed custom",
            408 => "failed custom",

            //Error server
            900 => "Error en el servidor, olvidaste enviar el parametro - $entity",
        );

        if ($code === 204 || $code === 205 || $code === 206 || $code === 406 || $code === 407 || $code === 408) {
            $errorCodes[$code] = $customMessage;
        }

        return $errorCodes[$code];
    }

    public function ExitProgram($code, $message, $objectSend = null)
    {
        $success = substr((string) $code, 0, 1) === "2" ? 1 : 0;

        return response()->json([
            'success' => $success,
            'responseCode' => $code,
            'message' => $message,
            'data' => $objectSend
        ]);
    }

    public function PaginationCalculate($paginacion, $rango = 9)
    {
        $hasta = ($paginacion * $rango);
        $desde = ($hasta - $rango);

        return array('desde' => $desde, 'hasta' => $rango);
    }

    public function ValidateParameters($request, $params_required)
    {

        foreach ($params_required as $key => $parameter) {
            if (!in_array($parameter, array_keys($request->all())))
                return $key;
        }

        return null;
    }

    function FunctionResizeImage($file, $first_size, $second_size, $folderName = 'profiles')
    {
        $image = \Image::make($file);
        $image->resize($first_size, $second_size, function ($constraint) {
            $constraint->aspectRatio();
        });

        $uniq = uniqid(auth()->user()->id);
        $fileName = $uniq . '.' . $file->getClientOriginalExtension();
        $path = storage_path('app/public/' . $folderName . '/' . $fileName);
        $pathSave = $folderName . '/' . $fileName;
        $image->save($path);

        return $pathSave;
    }

    function FunctionSaveDocument($file, $folderName = 'profiles')
    {
        $uniq = uniqid(auth()->user()->id);
        $fileName = $uniq . '.' . $file->getClientOriginalExtension();
        $pathSave = $folderName . "/" . $fileName;
        \Storage::disk('local')->putFileAs('public/', $file, $pathSave);

        return $pathSave;
    }

    function getGuia()
    {
        $idUsuario = \Auth::user()->id;
        $data = Guia::whereNotExists(function ($query) use ($idUsuario) {
            $query->select(DB::raw(1))
                ->from('guia_visualizaciones')
                ->whereRaw('guia_visualizaciones.id_guia = guia.id')
                ->where('guia_visualizaciones.id_usuario', $idUsuario);
        })
            ->get();

        return $this->ExitProgram(206, $this->MessageResponse('', 206, 'Datos encontrados'), $data);
    }

    function createGuiaVisualizada(Request $request)
    {
        $idGuia = $request->id_guia;
        GuiaVisualizaciones::create([
            'id_usuario' => \Auth::user()->id,
            'id_guia' => $idGuia,
        ]);
        return $this->ExitProgram(201, 'Se registro visualización');
    }

    function GetAllPermisos1()
    {
        if (auth()->check()) {
            $idUsuario = \Auth::user()->id;
            $permisos =  SavkPermisosUsuarios::select('p.evento')
                ->join('savk_permisos as p', 'p.id', 'id_permiso')
                ->where('id_usuario', $idUsuario)
                ->get();
        } else {
            $permisos = [];
        }

        return $this->ExitProgram(201, 'Permisos', $permisos);
    }

    public function GetAllPermisos()
    {
        if (auth()->check()) {
            $idUsuario = \Auth::user()->id;
            $permisos =  SavkPermisosUsuarios::select('p.evento')
                ->join('savk_permisos as p', 'p.id', 'id_permiso')
                ->where('id_usuario', $idUsuario)
                ->get();
        } else {
            $permisos = [];
        }

        return $permisos;
    }

    public function insertPermisos($usuario)
    {
        $totalPermisos = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 58];
        $liderGrupoEmpresaPermisos = [1 ,45 ,2 ,3 ,4 ,5 ,6 ,7 ,8 ,9, 10, 11, 12, 13 ,15 ,17 ,55 ,56 ,58 ,19 ,20 ,21 ,22 ,23 ,24 ,25 ,26 ,27 ,28 ,29 ,31 ,32 ,34 ,35 ,37 ,38 ,42 ,43 ,47 ,48 ,49 ,50 ,51 ,52 ,53];
        $liderEmpresaPermisos = [45 ,2 ,3 ,4 ,6 ,7 ,8 ,9 ,13 ,15 , 19 ,20 ,21 ,22 ,24 ,25 ,31 ,32 ,34 ,35 ,37 ,38 ,42 ,43 ,50 ,51];
        $liderCentroCostoPermisos = [45 ,2 ,3 ,6 ,7 ,8 ,9 ,13 ,19 ,20 ,21 ,22 ,24 ,25 ,31 ,32 ,34 ,35 ,37 ,38 ,42 ,43 ,50 ,51];
        $liderZona = [45 ,2 ,3 ,6 ,7 ,8 ,9 ,13 ,55 ,56 ,58 ,19 ,20 ,21 ,22 ,24 ,25 ,31 ,32 ,34 ,35 ,37 ,38 ,42 ,43 ,50 ,51, 55, 56];

        $calidadPermisos = [19, 20, 21, 24, 25, 37, 38, 48, 50, 51, 54];
        $comercialPermisos = [19,20,21,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,46,47,48,49,50,51,52,53];

        $colaboradorPermisos = [19, 20, 21, 24, 25, 37, 38, 50, 51];

        $permisos = [];
        switch ($usuario->id_grupo) {
            case 44:
                $permisos = $liderGrupoEmpresaPermisos;
                break;
            case 45:
                $permisos = $liderEmpresaPermisos;
                break;
            case 46:
                $permisos = $liderZona;
                break;
            case 47:
                $permisos = $liderCentroCostoPermisos;
                break;
            case 48:
                $permisos = $colaboradorPermisos;
                break;
            case 20:
                $permisos = $calidadPermisos;
                break;
            case 30:
                $permisos = $comercialPermisos;
                break;
            default:
                $permisos = $colaboradorPermisos; //SINO ES DE NINGUNO DE LOS GRUPOS SAVK POR DEFECTO QUEDA CON PERMISOS MÍNIMOS
                break;
        }

        try {
            //SE VALIDA SI TIENE PERMISOS PARA ELIMINARLOS
            $permisosUsuario = SavkPermisosUsuarios::where('id_usuario', $usuario->id)->get();
            if ($permisosUsuario->count() > 0) {
                $permisosUsuario->each(function ($permisoD) {
                    $permisoD->delete();
                });
            }

            foreach ($permisos as $key => $permiso) {
                SavkPermisosUsuarios::create([
                    'id_usuario' => $usuario->id,
                    'id_permiso' => $permiso,
                ]);
            }
            return true;
        } catch (\Exception  $e) {
            return false;
        }
    }

    public function GetMonthByDay($day)
    {
        $months = [
            1 => "Enero",
            2 => "Febrero",
            3 => "Marzo",
            4 => "Abril",
            5 => "Mayo",
            6 => "Junio",
            7 => "Julio",
            8 => "Agosto",
            9 => "Septiembre",
            10 => "Octubre",
            11 => "Noviembre",
            12 => "Diciembre"
        ];

        return $months[$day];
    }
}
