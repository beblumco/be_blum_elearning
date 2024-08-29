<?php

use Modules\Trainings\Http\Controllers\TrainingsController;
use Modules\Trainings\Http\Controllers\TrainingsAdministrationController;
use Modules\Drive\Http\Controllers\DriveController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::prefix('capacitaciones')->group(function () {
    Route::post('/dataUser', [TrainingsController::class, 'dataUser'])->name('dataUser');

    //CERTIFICADOS
    Route::post('/get_data_test_view', [TrainingsController::class, 'GetDataTestView'])->name('get_data_test_view');
    Route::post('/certificados/get-instantes', [TrainingsController::class, 'getInstantesCertificates'])->name('get_instantes_certificates');
    Route::get('/certificados', [TrainingsController::class, 'TrainingsIndexCertificates'])->name('trainings_index_certificates');
    Route::post('/certificados/all', [TrainingsController::class, 'getAllCertificates'])->name('getAllCertificates');
    Route::post('/certificados-team/all', [TrainingsController::class, 'getAllCertificatesTeam'])->name('getAllCertificatesTeam');
    Route::get('/download-certificate/{data}', [TrainingsController::class, 'downloadCertificate'])->name('download-certificate');
    Route::get('/download-certificateAll/{data}', [TrainingsController::class, 'downloadCertificateAll'])->name('download-certificate-all');
    Route::get('/download-excel-certificateAll/{data}', [TrainingsController::class, 'downloadExcelCertificateAll'])->name('download-excel-certificate-all');
    Route::get('/download-certificate-public/{data}', [TrainingsController::class, 'downloadCertificatePublic'])->name('downloadCertificatePublic');
    Route::get('/download-certificates-public/{data}', [TrainingsController::class, 'downloadCertificatesPublic'])->name('downloadCertificatePublic');//data es arreglo
    Route::get('/download-certificate-by-assist-expert/{id}/{id_asistente}', [TrainingsController::class, 'downloadCertificateByAssistExpert'])->name('download-certificate-by-assist-expert');
    Route::post('/share-certificate', [TrainingsController::class, 'shareCertificate'])->name('shareCertificate');
    Route::post('/share-training', [TrainingsController::class, 'shareTraining'])->name('shareTraining');
    Route::get('/acceder-capacitacion-compartida/{id_training}/{main_account_id}', [TrainingsController::class, 'GetTrainingShare'])->name('acceder-capacitacion-compartida');
    Route::post('/save_questions_cliente', [TrainingsController::class, 'saveQuestionsCliente'])->name('saveQuestionsCliente');
    Route::post('/get_all_questions_users', [TrainingsController::class, 'getAllQuestionsUsuarios'])->name('getAllQuestionsUsuarios');
    Route::post('/download-test', [TrainingsController::class, 'downloadTest'])->name('downloadTest');
    // Route::get('/share-certificate/{data}', [TrainingsController::class, 'shareCertificate'])->name('shareCertificate');
    //REPORTES
    Route::get('/download-report-vissit/{data}', [TrainingsController::class, 'downloadReportVissit'])->name('download-report-vissit');

    // EJECUCIÓN
    Route::get('/', [TrainingsController::class, 'TrainingsIndex'])->name('trainings_index');
    Route::get('/init/{idTraining}', [TrainingsController::class, 'TrainingsIndexTraining'])->name('TrainingsIndexTraining');
    Route::get('/menu/{menu}', [TrainingsController::class, 'TrainingsIndexMenu'])->name('trainings_index_menu');
    Route::post('/get_data_init', [TrainingsController::class, 'GetDataInit'])->name('get_data_init');
    Route::post('/get_data_resources_module', [TrainingsController::class, 'GetResourcesByModule'])->name('get_data_resources_module');
    Route::post('/save_training_init', [TrainingsController::class, 'SaveTrainigInit'])->name('save_training_init');
    Route::post('/get_data_modules_by_id', [TrainingsController::class, 'GetDataModulesById'])->name('get_data_modules_by_id');
    Route::post('/generate_link_module', [TrainingsController::class, 'GenerateLinkModule'])->name('generate_link_module');
    Route::post('/view_links_by_user', [TrainingsController::class, 'ViewLinksByUser'])->name('view_links_by_user');
    Route::post('/view_assistants_by_link', [TrainingsController::class, 'ViewAssistantsByLink'])->name('view_assistants_by_link');
    Route::post('/get_data_content_modules', [TrainingsController::class, 'GetDataContentModules'])->name('get_data_content_modules');
    Route::post('/get_data_test', [TrainingsController::class, 'GetDataTest'])->name('get_data_test');
    Route::post('/finish_test', [TrainingsController::class, 'FinishTest'])->name('finish_test');
    Route::post('/change_status_training', [TrainingsController::class, 'ChangeStatusTraining'])->name('change_status_training');

    //ASISTIDO POR UN EXPERTO
    Route::get('/asistido-por-experto', [TrainingsController::class, 'TrainingsAssistedByExpertIndex'])->name('assisted_by_expert');
    Route::post('/get-data-all-assist-by-expert', [TrainingsController::class, 'GetAllTrainingsAssistByExpert'])->name('get_data_all_assist_by_expert');
    Route::post('/download-pdf-by-training', [TrainingsController::class, 'DownloadPDFByTraining'])->name('download_pdf_by_training');
    Route::post('/asistida/crear', [TrainingsController::class, 'SaveAsistida'])->name('SaveAsistida');
    Route::post('/asistida/cargar', [TrainingsController::class, 'CargarAsisAsistida'])->name('CargarAsisAsistida');
    Route::post('/asistida/guardarAsistente', [TrainingsController::class, 'saveAsisAsistida'])->name('saveAsisAsistida');
    Route::post('/asistida/guardarAsistentePublica', [TrainingsController::class, 'saveAsisAsistidaPublica'])->name('saveAsisAsistidaPublica');
    Route::post('/asistida/cargarExcel', [TrainingsController::class, 'CargarAsisAsistidaExcel'])->name('CargarAsisAsistidaExcel');
    Route::post('/asistida/cargarImg', [TrainingsController::class, 'CargarAsisAsistidaImg'])->name('CargarAsisAsistidaImg');
    Route::post('/asistida/deleteImg', [TrainingsController::class, 'EliminarAsisAsistidaImg']);
    Route::post('/asistida/generarQr', [TrainingsController::class, 'GenerarQr']);
    Route::post('/asistida/loginAsistente', [TrainingsController::class, 'loginAsistente'])->name('loginAsistente');
    Route::post('/asistida/sendCertificado', [TrainingsController::class, 'sendCertificado'])->name('sendCertificado');
    Route::get('/asistida/downloadAsistentes/{id}/{formato}', [TrainingsController::class, 'downloadAsistentes'])->name('downloadAsistentes');
    Route::get('/get_training', [TrainingsController::class, 'getTraining']);
    Route::get('/get_centro_costo/{id_empresa}', [TrainingsController::class, 'getCentroCosto']);
    Route::get('/get_grupo_empresa', [TrainingsController::class, 'getGrupoEmpresa']);
    Route::get('/get_empresa/{id_grupo_emp}', [TrainingsController::class, 'getEmpresa']);
    Route::get('/registrar-asistencia/{codigo}', [TrainingsController::class, 'RegisterAsistAsistidaView'])->name('RegisterAsistAsistidaView');
    Route::post('/registrar-asistencia/saveSignature', [TrainingsController::class, 'saveSignature'])->name('saveSignature');
    Route::get('/get_data_training_public/{id_training}/{id_asistente}', [TrainingsController::class, 'getDataTrainingPublic']);


    // ADMINISTRACIÓN
    Route::get('/administracion', [TrainingsAdministrationController::class, 'TrainingsAdminIndex'])->name('trainings_admin_index');
    Route::post('/administracion/get_data_init', [TrainingsAdministrationController::class, 'GetDataInit'])->name('get_data_init_administration');
    Route::post('/administracion/new_training_administration', [TrainingsAdministrationController::class, 'NewTraining'])->name('new_training_administration');
    Route::post('/administracion/delete_training_administration', [TrainingsAdministrationController::class, 'DeleteTraining'])->name('delete_training_administration');
    Route::post('/administracion/change_training_administration', [TrainingsAdministrationController::class, 'ChangeStatusTraining'])->name('change_training_administration');

    Route::get('/administracion/{id_training}', [TrainingsAdministrationController::class, 'TrainingsAdminIndexNew'])->name('trainings_admin_index_new');
    Route::post('/administracion/save_training_section', [TrainingsAdministrationController::class, 'SaveTrainingSection'])->name('save_training_section');
    Route::post('/administracion/get_data_init_general', [TrainingsAdministrationController::class, 'GetDataInitGeneral'])->name('get_data_init_general');

    Route::post('/administracion/save_module', [TrainingsAdministrationController::class, 'SaveModule'])->name('save_module');
    Route::post('/administracion/update_module', [TrainingsAdministrationController::class, 'UpdateModule'])->name('update_module');
    Route::post('/administracion/delete_module', [TrainingsAdministrationController::class, 'DeleteModule'])->name('delete_module');
    Route::post('/administracion/add_test_module', [TrainingsAdministrationController::class, 'AddTestModule'])->name('add_test_module');
    Route::post('/administracion/get_test_module', [TrainingsAdministrationController::class, 'GetTestModule'])->name('get_test_module');
    Route::post('/administracion/get_test_training', [TrainingsAdministrationController::class, 'GetTestTraining'])->name('get_test_training');
    Route::post('/administracion/guardar_estado', [TrainingsAdministrationController::class, 'guardarEstado'])->name('guardarEstado');

    Route::post('/administracion/save_content', [TrainingsAdministrationController::class, 'SaveContent'])->name('save_content');
    Route::post('/administracion/get_data_contents', [TrainingsAdministrationController::class, 'GetDataContents'])->name('get_data_contents');

    Route::post('/administracion/delete_content', [TrainingsAdministrationController::class, 'DeleteContent'])->name('delete_content');
    Route::post('/administracion/update_order_content', [TrainingsAdministrationController::class, 'UpdateOrderContent'])->name('update_order_content');
    // Route::get('/download-certificate/{id}/{id_link}', [TrainingsController::class, 'downloadCertificate'])->name('download-certificate');
    Route::get('/download-all-certificates-by-link/{id_link}', [TrainingsController::class, 'downloadAllCertificateByLink'])->name('download-all-certificates-by-link');
    Route::get('/download-all-certificates-by-assist-expert/{id}', [TrainingsController::class, 'downloadAllCertificateByAssistExpert'])->name('download-all-certificates-by-assist-expert');

    //WEBINARS
    Route::get('/webinars', [TrainingsController::class, 'WebinarsIndex'])->name('webinars_index');
    Route::post('/webinars/get_data_init', [TrainingsController::class, 'GetDataInitWebinars'])->name('get_data_init_webinars');
    Route::post('/webinars/save_asistentes', [TrainingsController::class, 'saveAsistentes'])->name('saveAsistentes');

    //PUNTOS USUARIO
    Route::get('/puntos-usuario', [TrainingsController::class, 'puntosUsuario'])->name('puntosUsuario');

    //DASBOARD INDICADOR ENTRENAMIENTO
    Route::post('/horas_entrenamiento', [TrainingsController::class, 'horasEntrenamiento'])->name('horasEntrenamiento');

    //PROVISIONAL PARA MIGRAR LA CURUÑA
    Route::get('/migrar', [TrainingsController::class, 'migrarIndex'])->name('migrar');
    Route::post('/migrarExcel', [TrainingsController::class, 'migrarExcel'])->name('migrarExcel');
    Route::post('/migrarExcelCertificados', [TrainingsController::class, 'migrarExcelCertificados'])->name('migrarExcelCertificados');
    Route::post('/migrarExcelAcompañamiento', [TrainingsController::class, 'migrarExcelAcompañamiento'])->name('migrarExcelAcompañamiento');
    Route::post('/migrarCapacitacionesAsistidas', [TrainingsController::class, 'migrarCapacitacionesAsistidas'])->name('migrarCapacitacionesAsistidas');

    Route::get('/entorno-aprendizaje', [DriveController::class, 'DriveIndexEntornoAprendizaje'])->name('drive_index_entorno_aprendizaje');
});
