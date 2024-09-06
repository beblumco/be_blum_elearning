<?php

namespace Modules\Trainings\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Administration\Entities\SavkLideresGrupoEmpresa;
use Modules\Trainings\Entities\CaCapacitaciones;
use Modules\Trainings\Entities\CaEvaluacionIniciada;
use Modules\Trainings\Entities\CaModulos;
use Modules\Trainings\Entities\CaPreguntas;
use Modules\Trainings\Entities\CaRespuestas;
use Modules\Trainings\Entities\CaContenido;
use Modules\Trainings\Entities\CaAsignacionCentroOperacion;
use Modules\Trainings\Entities\CaAsignacionSector;
use App\Models\CentroOperacion;
use App\Models\Sector;
use App\Models\PuntoEvaluacion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Modules\Trainings\Entities\CaCapacitacionesIniciadas;

class TrainingsAdministrationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            return $next($request);
        });
    }


    public function TrainingsAdminIndex()
    {
        $page_title = 'Capacitaciones';
        $action = __FUNCTION__;
        $permisos = $this->GetAllPermisos();

        return view('trainings::Admin.index_training_excecution', compact('page_title', 'action','permisos'));
    }

    public function GetDataInit(Request $request)
    {
        $current_page = $request->get('current_page');
        $filters = $request->get('filters');
        $quantity_rows = 10;
        $limit_result = $this->PaginationCalculate($current_page, $quantity_rows);

        $instanceCaCapacitaciones = new CaCapacitaciones();
        $data_training = $instanceCaCapacitaciones->GetAllTrainings(auth()->user()->main_account_id, $current_page, $quantity_rows, $limit_result, $filters);

        return $this->ExitProgram(202, $this->MessageResponse('Data', 202), [
            'trainings' => $data_training->trainings,
            'per_page' => $data_training->per_page,
            'main_account' => auth()->user()->main_account_id
        ]);
    }

    public function NewTraining(Request $request)
    {
        $dataTraining = [
            'id_usuario' => auth()->user()->id
        ];

        if (auth()->user()->id_grupo == 27) //CLIENTE
            $dataTraining['assign'] = 2;

        $training = new CaCapacitaciones();
        $training->fill($dataTraining);

        if ($training->save())
            return $this->ExitProgram(200, $this->MessageResponse('Data', 200), Crypt::encryptString($training->id));
    }

    public function DeleteTraining(Request $request)
    {
        $id_training = $request->get('id_training');

        //VALIDAR QUE NO EXISTA EN EJECUCIÓN LA CAPACITACIÓN
        $instance_eva_init = new CaEvaluacionIniciada();
        $count = $instance_eva_init->ValidateExistByIdTrining($id_training);
        $count_capacitacion_iniciada = CaCapacitacionesIniciadas::where('id_capacitacion', $id_training)->count();

        if ($count != 0 || $count_capacitacion_iniciada != 0)
            return $this->ExitProgram(406, $this->MessageResponse('Data', 406, 'La capacitación tiene trazabilidad'));

        $instace_capacitaciones = new CaCapacitaciones();
        $instace_capacitaciones->DeleteTraining($id_training);

        return $this->ExitProgram(206, $this->MessageResponse('Data', 206, 'La capacitación ha sido eliminada correctamente'));
    }

    public function ChangeStatusTraining(Request $request)
    {
        $id_training = $request->get('id_training');
        $status_change = ($request->get('current_status') == 1 ? 0 : 1);
        $instace_capacitaciones = new CaCapacitaciones();

        $instace_capacitaciones->where('id', '=', $id_training)->update(['estado' => $status_change]);
        $current_training = $instace_capacitaciones->GetTrainingById($id_training);

        return $this->ExitProgram(206, $this->MessageResponse('Data', 206, 'La capacitación ha sido actualizada'), $current_training);
    }

    public function SaveTrainingSection(Request $request)
    {
        $training_name = $request->get('training_name');
        $description = $request->get('description');
        $designedBy = $request->get('designedBy') == 'null' ? null : $request->get('designedBy');
        $principal_image = $request->file('principal_image');
        $time = $request->get('time');
        $id_training = $request->get('id_training');
        $assign = $request->get('assign') == 'null' ? null : $request->get('assign');
        $points = $request->get('points');
        $certified = ($request->get('certified') == 'undefined' || $request->get('certified') == 'null') ? null : $request->get('certified') ;
        $ids_selected = explode(',', $request->get('id_selected'));
        $assessBy = ($request->get('assessBy') == 'undefined' || $request->get('assessBy') == 'null') ? null : $request->get('assessBy');
        $assess = $request->get('assess');
        $certify = $request->get('certify');
        $type_training = $request->get('type_training');
        $price = ($request->get('price') == 'undefined' || $request->get('price') == 'null') ? null : $request->get('price');
        $date_webinars = ($request->get('date_webinars') == 'undefined' || $request->get('date_webinars') == 'null') ? null : $request->get('date_webinars');
        $estado_proceso = ($request->get('estado') == 'undefined' || $request->get('estado') == 'null') ? null : $request->get('estado');


        $instace_capacitaciones = new CaCapacitaciones();

        $array_update = [
            'nombre' => $training_name,
            'descripcion' => $description,
            'designed_by' => $designedBy,
            'tiempo_minutos' => $time,
            'assign' => $assign,
            'permitir_certificacion' => $certified,
            'evaluara_por' => $assessBy,
            'aplica_evaluacion' => $assess,
            'aplica_certificado' => $certify,
            'tipo_capacitacion' => $type_training,
            'puntos' => $points,
            'precio' => $price,
            'fecha_realizacion' => $date_webinars,
            'estado_proceso' => $estado_proceso == 2 ? 1 : $estado_proceso, //si es estado 2 solo se guardara al guardar video
        ];

        if ($assess == 2 || $assessBy == 2) {
            $array_update['porcentaje_aprobacion'] = null;
        }

        if ($principal_image != null) {
            $current_training = $instace_capacitaciones->GetTrainingById($id_training);
            if (isset($current_training->imagen)) {
                $pathVerified = storage_path('app/public/') . $current_training->imagen;
                if (\File::exists($pathVerified))
                    \File::delete($pathVerified);
            }

            $path = $this->FunctionResizeImage($principal_image, 800, 800, 'trainings_images');
            $array_update['imagen'] = $path;
        }

        //ELIMINAR ASIGNACION DE CENTRO DE OPERACIONES
        CaAsignacionCentroOperacion::DeleteByIdTraining($id_training);

        //ELIMINAR ASIGNACION DE SECTOR
        CaAsignacionSector::DeleteByIdTraining($id_training);
        if ($assign == 1) //SECTOR
        {
            //AGREGAR ASIGNACIONES NUEVAS
            foreach ($ids_selected as $key_selected => $value_selected) {
                if ($value_selected != '') {
                    $assign_sector = new CaAsignacionSector();
                    $assign_sector->fill([
                        'id_sector' => $value_selected,
                        'id_capacitacion' => $id_training
                    ]);

                    $assign_sector->save();
                }
            }
        } else if ($assign == 2) //CAPACITACIONES
        {
            //AGREGAR ASIGNACIONES NUEVAS
            foreach ($ids_selected as $key_selected => $value_selected) {
                if ($value_selected != '') {
                    $assign_centro_operacion = new CaAsignacionCentroOperacion();
                    $assign_centro_operacion->fill([
                        'id_centro_operacion' => $value_selected,
                        'id_capacitacion' => $id_training
                    ]);

                    $assign_centro_operacion->save();
                }
            }
        }

        $updated = $instace_capacitaciones->where('id', '=', $id_training)->update($array_update);

        return $this->ExitProgram(206, $this->MessageResponse('', 206, 'La capacitación ha sido actualizada correctamente'));
    }

    // SECTION CREATE
    public function TrainingsAdminIndexNew($id_training)
    {
        $id_training_decrypt = Crypt::decryptString($id_training);
        $page_title = 'Nueva capacitación';
        $action = __FUNCTION__;
        $permisos = $this->GetAllPermisos();
        return view('trainings::Admin.index_create_training', compact('page_title', 'action', 'id_training_decrypt','permisos'));
    }

    public function GetDataInitGeneral(Request $request)
    {
        $tab_selected = $request->get('tab_selected');
        $id_training = $request->get('id_training');
        switch ($tab_selected) {
            case 1: //TRAININGS
                $instace_capacitaciones = new CaCapacitaciones();
                $current_training = $instace_capacitaciones->GetTrainingById($id_training);
                if (auth()->user()->main_account_id != 1)
                    if (auth()->user()->savk_principal == 1) {
                        //SAVK PRINCIPAL 1
                        $operation_center = CentroOperacion::GetAllOperationCenterByMainAccount(auth()->user()->main_account_id);
                    }else{
                        //LIDER GRUPO EMPRESA
                        $grupoEmpresa = SavkLideresGrupoEmpresa::where('id_usuario',auth()->user()->id)->pluck('id_grupo_empresa');
                        $operation_center = \DB::table('centro_operacion')
                        ->select('centro_operacion.*')
                        ->where('estado', 1)
                        ->whereIn('id', $grupoEmpresa)
                        ->get();
                    }

                else
                    //MAIN ACCOUNT 1
                    $operation_center = CentroOperacion::GetAllOperationCenter();
                $sectors = Sector::GetAllSectors();

                return $this->ExitProgram(
                    202,
                     $this->MessageResponse('Data', 202),
                    ['current_training' => $current_training, 'operation_center' => $operation_center, 'sectors' => $sectors, 'main_account_id' => Auth::user()->main_account_id]
                );
                break;

            case 2: //MODULES
                $instace_modules = new CaModulos();
                $modules = $instace_modules->GetAllModules($id_training);
                $hasTestTraining = $instace_modules->GetAllTest($id_training);
                $assessBy = $instace_modules->GetAssessBy($id_training);

                return $this->ExitProgram(202, $this->MessageResponse('Data', 202), [
                    'modules' => $modules,
                    'id_training' => $id_training,
                    'hasTestTraining' => $hasTestTraining,
                    'assessBy' => $assessBy
                ]);
                break;
            case 3: //EVALUACION POR CAPACITACION GENERAL
                $instace_modules = new CaModulos();
                $modules = $instace_modules->GetAllModules($id_training);
                $hasTestTraining = $instace_modules->GetAllTest($id_training);
                $assessBy = $instace_modules->GetAssessBy($id_training);

                return $this->ExitProgram(202, $this->MessageResponse('Data', 202), [
                    'modules' => $modules,
                    'id_training' => $id_training,
                    'hasTestTraining' => $hasTestTraining,
                    'assessBy' => $assessBy
                ]);
                break;

            default:
                break;
        }
    }

    public function SaveModule(Request $request)
    {
        $module_name = $request->get('module_name');
        $id_training = $request->get('id_training');

        $order = null;
        $data_module = CaModulos::where('id_capacitacion', '=', $id_training)->orderBy('orden', 'DESC')->first();
        if ($data_module != null)
            $order = $data_module->orden;
        else
            $order = 1;

        $dataModule = [
            'nombre' => $module_name,
            'orden' => $order,
            'id_capacitacion' => $id_training
        ];

        $module = new CaModulos();
        $module->fill($dataModule);

        if ($module->save()) {
            $instace_modules = new CaModulos();
            $current_module = $instace_modules->GetModuleById($module->id);

            return $this->ExitProgram(206, $this->MessageResponse('', 206, 'El módulo ha sido agregado correctamente'), $current_module);
        }
    }

    public function UpdateModule(Request $request)
    {
        $id_module = $request->get('id_module');
        $name = $request->get('name');
        $description = $request->get('description');
        $percentage = $request->get('percentage');
        $image = $request->file('image');
        $instace_modules = new CaModulos();

        $dataModule = [
            'nombre' => $name,
            'descripcion' => $description,
            'porcentaje_aprueba' => $percentage,
        ];

        if ($image != null) {
            $current_module = $instace_modules->GetModuleById($id_module);
            if (isset($current_module->imagen)) {
                $pathVerified = storage_path('app/public/') . $current_module->imagen;
                if (\File::exists($pathVerified))
                    \File::delete($pathVerified);
            }

            $path = $this->FunctionResizeImage($image, 300, 300, 'modules_images');
            $dataModule['imagen'] = $path;
        }

        $update = CaModulos::where('id', '=', $id_module)->update($dataModule);

        if ($update) {
            $current_module = $instace_modules->GetModuleById($id_module);
            return $this->ExitProgram(206, $this->MessageResponse('', 206, 'El módulo ha sido actualizado correctamente'), $current_module);
        }
    }

    public function DeleteModule(Request $request)
    {
        $id_module = $request->get('id_module');

        //VALIDAR QUE NO EXISTA EN EJECUCIÓN EL MÓDULO
        $instance_eva_init = new CaEvaluacionIniciada();
        $count = $instance_eva_init->ValidateExistByIdModule($id_module);
        if ($count != 0)
            return $this->ExitProgram(406, $this->MessageResponse('Data', 406, 'El módulo tiene trazabilidad, no puede ser eliminado'));

        CaCapacitacionesIniciadas::
            where('id_modulo', '=', $id_module)
            ->delete();

        $instace_module = new CaModulos();
        $instace_module->DeleteModule($id_module);

        return $this->ExitProgram(206, $this->MessageResponse('Data', 206, 'El módulo ha sido eliminada correctamente'));
    }

    public function guardarEstado(Request $request)
    {
        $id_capacitacion = $request->get('id_training');

        $has_video = CaContenido::select('ca_modulos.*')
        ->join('ca_modulos', 'ca_modulos.id', 'ca_contenido.id_modulo')
        ->where([
            ['ca_modulos.id_capacitacion', '=', $id_capacitacion],
            ['tipo_contenido', '=', 3],
        ])->get();

        if ($has_video->count() > 0) {
            //Tiene video
            $instace_capacitaciones = new CaCapacitaciones();
            $instace_capacitaciones->where('id', '=', $id_capacitacion)->update(['estado_proceso' => 2]);
            return $this->ExitProgram(200, $this->MessageResponse('Data', 200, 'la capacitacion se cambio de estado'));
        }else{
            return $this->ExitProgram(204, $this->MessageResponse('Data', 204, 'En tipo capacitación Webinar debes crear un modulo y guardar un video'));
        }
    }

    public function AddTestModule(Request $request)
    {
        $id_module = $request->get('id_module');
        $id_capacitacion = $request->get('id_capacitacion');
        $has_question = $request->get('has_question');
        $data_test = json_decode($request->get('data_test'));
        $porcentajeAprobacion = $request->get('porcentajeAprobacion');

        $instace_capacitaciones = new CaCapacitaciones();
        if ($id_module) { //Se valida si evaluación es por modulo o capacitacion
            // $instace_capacitaciones->where('id', '=', $id_capacitacion)->update(['porcentaje_aprobacion' => null]);
            CaModulos::where('id', $id_module)->update(['porcentaje_aprueba' => $porcentajeAprobacion]);
        } else {
            $instace_capacitaciones->where('id', '=', $id_capacitacion)->update(['porcentaje_aprobacion' => $porcentajeAprobacion]);
        }

        if ($has_question == 1) //TIENE YA PREGUNTAS CREADAS
        {
            //VALIDAR ANTES DE ELIMINAR SI TIENE TRAZABILIDAD
            if ($id_module) {
                $trazz = CaEvaluacionIniciada::where('id_modulo', '=', $id_module)->whereNotNull('fecha_terminada')->count();
            } else {
                $trazz = CaEvaluacionIniciada::where('id_capacitacion', '=', $id_capacitacion)->whereNotNull('fecha_terminada')->count();
            }
            if ($trazz != 0)
                return $this->ExitProgram(406, $this->MessageResponse('', 406, 'No puedes hacer cambios en las preguntas porque ya han sido ejecutadas.'));

            // ELIMINACIÓN DE PREGUNTAS - RESPUESTAS
            $instace_answers = new CaRespuestas();
            if ($id_module) {
                $instace_answers->DeleteAnswersByIdModule($id_module);
            } else {
                $instace_answers->DeleteAnswersByIdTraining($id_capacitacion);
            }


            $instace_question = new CaPreguntas();
            if ($id_module) {
                $instace_question->DeleteQuestionsByIdModule($id_module);
            } else {
                $instace_question->DeleteQuestionsByIdTraining($id_capacitacion);
            }
        }

        try {
            foreach ($data_test as $key_question => $value_question) {
                $array_question = [
                    'pregunta' => $value_question->pregunta,
                    'orden' => $value_question->orden,
                    'id_modulo' => $id_module,
                    'id_capacitacion' => $id_capacitacion
                ];

                $question = new CaPreguntas();
                $question->fill($array_question);
                if ($question->save()) {
                    foreach ($value_question->respuestas as $key_answers => $value_answers) {
                        $array_answer = [
                            'respuesta' => $value_answers->respuesta,
                            'ponderado' => ($value_answers->checked ? 100 : 0),
                            'id_tipo_respuesta' => 1,
                            'id_pregunta' => $question->id,
                        ];
                        $answer = new CaRespuestas();
                        $answer->fill($array_answer);

                        $answer->save();
                    }
                }
            }

            return $this->ExitProgram(206, $this->MessageResponse('', 206, 'La/las preguntas han sido creadas correctamente'));
        } catch (\Throwable $th) {
            return $this->ExitProgram(406, $this->MessageResponse('', 406, 'Hubo un inconveniente al crear las preguntas, comunícate con el administrador.'));
        }
    }

    public function GetTestModule(Request $request)
    {
        $id_module = $request->get('id_module');
        $modulo = CaModulos::find($id_module);
        $instance_questions = new CaPreguntas();

        $questions = $instance_questions->GetQuestionsByIdModule($id_module);

        return $this->ExitProgram(202, $this->MessageResponse('', 202), ['questions' => $questions, 'porcentajeAprobacion' => $modulo->porcentaje_aprueba ?? 75]);
    }

    public function GetTestTraining(Request $request)
    {
        $id_training = $request->get('id_training');
        $instance_questions = new CaPreguntas();

        $porcentajeAprobacion = CaCapacitaciones::where('id', $id_training)->pluck('porcentaje_aprobacion')->first();
        $questions = $instance_questions->GetQuestionsByIdTraining($id_training);

        $responseData = [
            'questions' => $questions,
            'porcentajeAprobacion' => $porcentajeAprobacion,
        ];

        return $this->ExitProgram(202, $this->MessageResponse('', 202), $responseData);
    }

    public function SaveContent(Request $request)
    {
        $id_training = $request->get('id_training');
        $id_module = $request->get('id_module');
        $content_type = $request->get('content_type');

        $instace_content = new CaContenido();
        switch ($content_type) {
            case 1: //IMAGE
                $image = $request->file('image');
                $mode = $request->get('mode');
                $instace_content = new CaContenido();

                if ($image != null) {
                    if ($mode == 1) //ADD NEW
                    {
                        $last_content = CaContenido::orderBy('orden', 'DESC')->where('id_modulo', '=', $id_module)->first();
                        $order = 0;
                        if ($last_content != NULL)
                            $order = $last_content->orden + 1;
                        else
                            $order = $order + 1;

                        $content = new CaContenido();
                        $array_content = [
                            'nombre' => $image->getClientOriginalName(),
                            'tipo_contenido' => $content_type,
                            'orden' => $order,
                            'id_modulo' => $id_module
                        ];

                        $path = $this->FunctionResizeImage($image, 720, 720, 'content_images');
                        $array_content['ruta_contenido'] = $path;

                        $content->fill($array_content);
                        if ($content->save()) {
                            $last_content = $instace_content->GetAllContentByIdModule($id_module);
                            return $this->ExitProgram(206, $this->MessageResponse('', 206, 'El contenido ha sido agregado correctamente.'), $last_content);
                        }
                    } else //UPDATE EXIST
                    {
                        $id_content = $request->get('id_content');
                        $current_content = $instace_content->GetContentById($id_content);

                        $pathVerified = storage_path('app/public/') . $current_content->ruta_contenido;
                        if (\File::exists($pathVerified))
                            \File::delete($pathVerified);

                        $path = $this->FunctionResizeImage($image, 720, 720, 'content_images');
                        CaContenido::where('id', '=', $id_content)->update(['ruta_contenido' => $path]);

                        $last_content = $instace_content->GetAllContentByIdModule($id_module);
                        return $this->ExitProgram(206, $this->MessageResponse('', 206, 'El contenido ha sido actualizado correctamente.'), $last_content);
                    }
                }
                break;

            case 2: //PDF
                $pdf = $request->file('pdf');
                $mode = $request->get('mode');

                if ($pdf != null) {
                    if ($mode == 1) //ADD NEW
                    {
                        $last_content = CaContenido::orderBy('orden', 'DESC')->where('id_modulo', '=', $id_module)->first();
                        $order = 0;
                        if ($last_content != NULL)
                            $order = $last_content->orden + 1;
                        else
                            $order = $order + 1;

                        $content = new CaContenido();
                        $array_content = [
                            'nombre' => $pdf->getClientOriginalName(),
                            'tipo_contenido' => $content_type,
                            'orden' => $order,
                            'id_modulo' => $id_module
                        ];

                        $path = $this->FunctionSaveDocument($pdf, 'content_pdfs');
                        $array_content['ruta_contenido'] = $path;

                        $content->fill($array_content);
                        if ($content->save()) {
                            $last_content = $instace_content->GetAllContentByIdModule($id_module);
                            return $this->ExitProgram(206, $this->MessageResponse('', 206, 'El contenido ha sido agregado correctamente.'), $last_content);
                        }
                    } else //UPDATE EXIST
                    {
                        $id_content = $request->get('id_content');
                        $current_content = $instace_content->GetContentById($id_content);

                        $pathVerified = storage_path('app/public/') . $current_content->ruta_contenido;
                        if (\File::exists($pathVerified))
                            \File::delete($pathVerified);

                        $path = $this->FunctionSaveDocument($pdf, 'content_pdfs');
                        CaContenido::where('id', '=', $id_content)->update(['ruta_contenido' => $path]);

                        $last_content = $instace_content->GetAllContentByIdModule($id_module);
                        return $this->ExitProgram(206, $this->MessageResponse('', 206, 'El contenido ha sido actualizado correctamente.'), $last_content);
                    }
                }
                break;

            case 3: //VIDEO
                $video = $request->get('video');
                $mode = $request->get('mode');


                if ($video != null) {
                    if ($mode == 1) //ADD NEW
                    {
                        $last_content = CaContenido::orderBy('orden', 'DESC')->where('id_modulo', '=', $id_module)->first();
                        $order = 0;
                        if ($last_content != NULL)
                            $order = $last_content->orden + 1;
                        else
                            $order = $order + 1;

                        $content = new CaContenido();

                        $video = $content->GetUrlVideo($video);

                        $array_content = [
                            'nombre' => "Nombre contenido (video)",
                            'tipo_contenido' => $content_type,
                            'orden' => $order,
                            'id_modulo' => $id_module,
                            //'ruta_contenido' => '<iframe src="'.$video.'" width="640" height="369" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>'}'ruta_contenido' => '<iframe src="'.$video.'" width="640" height="369" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>'
                            'ruta_contenido' => $video
                        ];

                        $content->fill($array_content);
                        if ($content->save()) {
                            $last_content = $instace_content->GetAllContentByIdModule($id_module);
                            return $this->ExitProgram(206, $this->MessageResponse('', 206, 'El contenido ha sido agregado correctamente.'), $last_content);
                        }
                    } else //UPDATE EXIST
                    {
                        $id_content = $request->get('id_content');
                        $current_content = $instace_content->GetContentById($id_content);

                        $content = new CaContenido();
                        $video = $content->GetUrlVideo($video);
                        CaContenido::where('id', '=', $id_content)->update(['ruta_contenido' => $video]);

                        $last_content = $instace_content->GetAllContentByIdModule($id_module);
                        return $this->ExitProgram(206, $this->MessageResponse('', 206, 'El contenido ha sido actualizado correctamente.'), $last_content);
                    }
                }

                break;
            default:
                break;
        }
    }

    public function GetDataContents(Request $request)
    {
        $id_module = $request->get('id_module');
        $instace_content = new CaContenido();
        $contents = $instace_content->GetAllContentByIdModule($id_module);

        return $this->ExitProgram(202, $this->MessageResponse('Contenido encontrado', 202), $contents);
    }

    public function DeleteContent(Request $request)
    {
        $type_content = $request->get('type_content');
        $id_content = $request->get('id_content');
        $instace_content = new CaContenido();

        $current_content = $instace_content->GetContentById($id_content);

        $pathVerified = storage_path('app/public/') . $current_content->ruta_contenido;
        if (\File::exists($pathVerified))
            \File::delete($pathVerified);

        $instace_content->DeleteContent($id_content);

        $instace_content->SortContent($current_content->id_modulo);

        $last_content = $instace_content->GetAllContentByIdModule($current_content->id_modulo);

        return $this->ExitProgram(206, $this->MessageResponse('', 206, 'El contenido ha sido eliminado correctamente.'), $last_content);
    }

    public function UpdateOrderContent(Request $request)
    {
        $order = $request->get('order');
        $id_content = $request->get('id_content');
        $id_module = $request->get('id_module');

        $instance_content = new CaContenido();

        $current_content = $instance_content->GetContentById($id_content);
        $current_order = $current_content->orden;

        $content_order = $instance_content->GetContentByOrder($order, $id_module);

        //ACTUALIZAR EL CONTENIDO ACTUAL CON EL NÚMERO PEDIDO
        CaContenido::where('id', '=', $id_content)->update(['orden' => $order]);

        //ACTUALIZAR EL QUE TENÍA ESTE ORDEN CON EL ORDEN DEL CONTENIDO ACTUAL
        CaContenido::where('id', '=', $content_order->id)->update(['orden' => $current_content->orden]);

        $contents = $instance_content->GetAllContentByIdModule($id_module);

        return $this->ExitProgram(206, $this->MessageResponse('', 206, 'Orden cambiado correctamente'), $contents);
    }


}
