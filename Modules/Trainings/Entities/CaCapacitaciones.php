<?php

namespace Modules\Trainings\Entities;

use App\Models\CentroOperacion;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Trainings\Entities\CaRespuestas;
use Modules\Trainings\Entities\CaPreguntas;
use Modules\Trainings\Entities\CaModulos;
use Modules\Trainings\Entities\CaContenido;
use Modules\Trainings\Entities\CaAsignacionSector;
use Modules\Trainings\Entities\CaAsignacionCentroOperacion;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;

class CaCapacitaciones extends Model
{
    use HasFactory;

    protected $table = 'ca_capacitaciones';
    protected $fillable = [
        'id',
        'nombre',
        'descripcion',
        'designed_by',
        'imagen',
        'estado',
        'tiempo_minutos',
        'id_usuario',
        'assign',
        'permitir_certificacion',
        'tipo_capacitacion',
        'aplica_certificado',
        'aplica_evaluacion',
        'evaluara_por',
        'porcentaje_aprobacion',
        'puntos',
        'fecha_realizacion',
        'precio',
        'estado_proceso'
    ];


    public function mainAccountTraining($certificadoId){
        $capacitacion = CaCapacitaciones::find($certificadoId);
        $userCap = User::find($capacitacion->id_usuario);
        $grupoE = CentroOperacion::GetOperationCenterByPointer($userCap->id_punto);

        $data = $this->select(
            'u.main_account_id',
            \DB::raw("(select img_avatar from centro_operacion where centro_operacion.id = $grupoE->id) as imagen"),
            \DB::raw("(select img_certificado from centro_operacion where centro_operacion.id = $grupoE->id) as img_certificado")
        )
        ->where('ca_capacitaciones.id', $certificadoId)
        ->join('usuarios as u', 'u.id','id_usuario')
        ->first();
        return $data;
    }

    public function GetAllTrainings($main_account, $current_page = 1, $quantity_rows = 10, $limit_result = null, $filters = null)
    {
        $trainings = $this->select(
            'ca_capacitaciones.*',
            'usuarios.nombre_com',
            \DB::raw('IF(ca_capacitaciones.nombre IS NULL, "Sin nombre", ca_capacitaciones.nombre) AS nombre'),
            \DB::raw('IF(ca_capacitaciones.descripcion IS NULL, "Sin descripción", ca_capacitaciones.descripcion) AS descripcion'),
            \DB::raw('IF(ca_capacitaciones.estado = 1, "Activo", "Inactivo") as estado_texto')
        )
            ->Join('usuarios', 'ca_capacitaciones.id_usuario', '=', 'usuarios.id')
            ->orderBy('created_at', 'desc');

        if (\Auth::user()->savk_principal == 1){
            $trainings = $trainings->where('usuarios.main_account_id', '=', $main_account);
        }else{
            $trainings = $trainings->where('ca_capacitaciones.id_usuario', '=', \Auth::user()->id);
        }

        if(!empty($filters)){
            $trainings->where(function ($query) use ($filters) {
                $query->where('ca_capacitaciones.nombre', 'LIKE', "%$filters%")
                    ->orWhere('usuarios.nombre_com', 'LIKE', "%$filters%")
                    ->orWhere(function ($query) use ($filters) {
                        // Array de mapeo de valores
                        $tipoMapping = [
                            1 => 'K-Learning',
                            2 => 'Asistida por experto',
                            3 => 'Webinar',
                            // Agrega más valores según sea necesario
                        ];

                        // Buscar el valor numérico correspondiente al filtro
                        $filteredValues = array_filter($tipoMapping, function ($value) use ($filters) {
                            return stripos($value, $filters) !== false;
                        });

                        // Obtener las claves del array resultante
                        $tipoFilter = empty($filteredValues) ? false : key($filteredValues);

                        if ($tipoFilter !== false) {
                            // Si se encuentra un valor, busca en base al campo 'tipo'
                            $query->orWhere('ca_capacitaciones.tipo_capacitacion', $tipoFilter);
                        }
                    });
            });
        }

        $from = $limit_result['desde'];
        $until = $limit_result['hasta'];

        $per_page = $trainings->paginate($quantity_rows)->lastPage();
        // $trainings = $trainings->skip($from)->take($until)->get();
        $totalResults = $trainings->count();

        // Verificar si hay suficientes resultados para aplicar skip y take
        if ($totalResults >= $from) {
            // Aplicar skip y take solo si hay suficientes resultados
            $trainings = $trainings->skip($from)->take($until)->get();
        } else {
            // Si no hay suficientes resultados, obtener todos los resultados
            $trainings = $trainings->get();
        }

        foreach ($trainings as $key_training => $value_training) {
            $value_training->id_training_encrypt = Crypt::encryptString($value_training->id);
        }

        return (object) [
            'trainings' => $trainings,
            'per_page' => $per_page
        ];
    }

    public function GetTrainingById($id_training)
    {
        $instance_sector = new CaAsignacionSector();
        $instance_operation_center = new CaAsignacionCentroOperacion();
        $training = $this->select(
            'ca_capacitaciones.*',
            'usuarios.nombre_com',
            \DB::raw('IF(ca_capacitaciones.nombre IS NULL, "Sin nombre", ca_capacitaciones.nombre) AS nombre'),
            \DB::raw('IF(ca_capacitaciones.descripcion IS NULL, "Sin descripción", ca_capacitaciones.descripcion) AS descripcion'),
            \DB::raw('IF(ca_capacitaciones.estado = 1, "Activo", "Inactivo") as estado_texto')
        )
            ->Join('usuarios', 'ca_capacitaciones.id_usuario', '=', 'usuarios.id')
            ->where('ca_capacitaciones.id', '=', $id_training)
            ->first();

        $training->id_training_encrypt = Crypt::encryptString($training->id);
        if ($training->assign == 1) //SECTOR
            $training->sectors = $instance_sector->GetSectorsByIdTraining($training->id);
        else
            $training->operation_center = $instance_operation_center->GetCentroOperacionByIdTraining($training->id);

        return $training;
    }

    public function DeleteTraining($id_training)
    {

        $files_content = CaContenido::select('ca_contenido.ruta_contenido')
            ->Join('ca_modulos', 'ca_contenido.id_modulo', '=', 'ca_modulos.id')
            ->where('ca_modulos.id_capacitacion', '=', $id_training)
            ->whereIn('ca_contenido.tipo_contenido', [1, 2])
            ->get();

        foreach ($files_content as $file) {
            Storage::delete('public/'.$file->ruta_contenido);
        }

        $image_modules = CaModulos::select('imagen')
            ->where('ca_modulos.id_capacitacion', '=', $id_training)
            ->get();

        foreach ($image_modules as $file) {
            Storage::delete('public/'.$file->imagen);
        }

        $image_trainign = $this->where('id', '=', $id_training)->first();
        Storage::delete('public/'.$image_trainign->imagen);

        //dd([$image_modules, $image_trainign, $files_content]);

        $delete_answer = CaRespuestas::Join('ca_preguntas', 'ca_respuestas.id_pregunta', '=', 'ca_preguntas.id')
            ->Join('ca_modulos', 'ca_preguntas.id_modulo', '=', 'ca_modulos.id')
            ->where('ca_modulos.id_capacitacion', '=', $id_training)
            ->delete();

        $delete_question = CaPreguntas::Join('ca_modulos', 'ca_preguntas.id_modulo', '=', 'ca_modulos.id')
            ->where('ca_modulos.id_capacitacion', '=', $id_training)
            ->delete();

        $delete_content = CaContenido::Join('ca_modulos', 'ca_contenido.id_modulo', '=', 'ca_modulos.id')
            ->where('ca_modulos.id_capacitacion', '=', $id_training)
            ->delete();

        $delete_module = CaModulos::where('ca_modulos.id_capacitacion', '=', $id_training)->delete();

        $delete_assign_training = CaAsignacionCentroOperacion::where('id_capacitacion', '=', $id_training)->delete();

        $delete_assign_sector = CaAsignacionSector::where('id_capacitacion', '=', $id_training)->delete();

        $delete_training = $this->where('id', '=', $id_training)->delete();



        return true;
    }
}
