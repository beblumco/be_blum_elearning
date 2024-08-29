<?php
use Modules\Administration\Http\Controllers\MyOrganizationController;


Route::middleware(['auth'])->prefix('administration')->group(function () {
    Route::get('/', [MyOrganizationController::class, 'index'])->name('my_organization_index');

    Route::get('/mis_clientes', [MyOrganizationController::class, 'MyClientsIndex'])->name('my_clients');

    Route::get('/grupo-empresas',[MyOrganizationController::class, 'getCompaniesGroup']);
    Route::get('/capacitaciones_virtuales', [MyOrganizationController::class, 'IndexAdminTrainings'])->name('index_admin_traninigs');
    Route::post('/grupo-empresa/crear',[MyOrganizationController::class, 'createCompanyGroup']);
    Route::post('/grupo-empresa/all',[MyOrganizationController::class, 'getAllCompanysGroup']);
    Route::post('/grupo-empresa/eliminar',[MyOrganizationController::class, 'deleteCompanyGroup']);
    Route::post('/grupo-empresa/responder_mensajes',[MyOrganizationController::class, 'mensajesCompanyGroup']);

    //Zonas
    Route::post('/zona/all',[MyOrganizationController::class, 'getAllZona']);
    Route::post('/zona/crear',[MyOrganizationController::class, 'createZona']);
    Route::post('/zona/eliminar',[MyOrganizationController::class, 'deleteZona']);

    //Empresa
    Route::post('/empresa/crear',[MyOrganizationController::class, 'createCompany']);
    Route::post('/empresa/all',[MyOrganizationController::class, 'getAllCompanies']);
    Route::post('/empresa/eliminar',[MyOrganizationController::class, 'deleteCompany']);
    Route::get('/empresas',[MyOrganizationController::class, 'getCompaniesAll']);

    //Punto Evaluación
    Route::post('/punto/crear',[MyOrganizationController::class, 'createEvaluationPoint']);
    Route::post('/punto/all',[MyOrganizationController::class, 'getAllEvaluationPoints']);
    Route::post('/punto/eliminar',[MyOrganizationController::class, 'deleteEvaluationPoint']);
    Route::get('/puntos',[MyOrganizationController::class, 'getCompaniesAll']);
    Route::get('/puntos-evaluacion',[MyOrganizationController::class, 'getEvaluationPointsAll']);

    Route::post( '/seccion/crear', [MyOrganizationController::class, 'createSeccion']);
    Route::post( '/seccion/eliminar', [MyOrganizationController::class, 'deleteSeccion']);
    Route::post( '/seccion/editar', [MyOrganizationController::class, 'updateSeccion']);
    Route::get('/secciones',[MyOrganizationController::class, 'getSeccionesAll']);

    //USUARIOS
    Route::post(
        '/usuarios/all',
        [MyOrganizationController::class, 'getAllUsers']
    );
    Route::post(
        '/usuarios/crear',
        [MyOrganizationController::class, 'createUser']
    );
    Route::post(
        '/usuarios/actualizar-avatar',
        [MyOrganizationController::class, 'updateAvatar']
    );
    Route::get(
        '/usuarios/puntos/{user_id}',
        [MyOrganizationController::class, 'getPointsUser']
    );
    Route::get(
        '/usuario_puntos/{user_id}',
        [MyOrganizationController::class, 'getUserPoints']
    );
    Route::post(
        '/usuarios/eliminar',
        [MyOrganizationController::class, 'deleteUser']
    );
    Route::get(
        '/usuarios/mi-perfil',
        [MyOrganizationController::class, 'getDataMiPerfil']
    );
    Route::get(
        '/usuarios/link-invitacion',
        [MyOrganizationController::class, 'GenerarLinkInvitacion']
    );
    //FIN USUARIOS


    //Pais, Departamentos, Sectores y ciudades
    Route::get('/sectores', [MyOrganizationController::class, 'getSectors']);
    Route::get('/paises', [MyOrganizationController::class, 'getCountries']);
    Route::get('/zonas', [MyOrganizationController::class, 'getZonas']);
    Route::get('/departamentos/{country_id}', [MyOrganizationController::class, 'getDepartaments']);
    Route::get('/ciudades/{departament_id}', [MyOrganizationController::class, 'getCities']);
    Route::get('/asesores', [MyOrganizationController::class, 'getAsesores']);
    Route::get('/perfiles', [MyOrganizationController::class, 'getProfiles']);
    Route::get('/lideres-grupo-empresa', [MyOrganizationController::class, 'getLideresGrupoEmpresa']);
    Route::get('/lideres-empresa', [MyOrganizationController::class, 'getLideresEmpresa']);
    Route::get('/lideres-centro-de-costos', [MyOrganizationController::class, 'getLideresCentroDeCosto']);
    Route::get('/lideres-zonas', [MyOrganizationController::class, 'getLideresZonas']);
});
