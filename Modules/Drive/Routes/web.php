<?php
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

Route::middleware(['auth'])->prefix('drive')->group(function() {
    Route::get('/', [DriveController::class , 'DriveIndexV'])->name('drive_indexV');
    Route::get('/grupo-empresa', [DriveController::class, 'getCompanyGroup']);
    Route::get('/empresas', [DriveController::class, 'getCompanies']);
    Route::get('/puntos', [DriveController::class, 'getEvaluationPoints']);
    Route::post('/nueva-carpeta', [DriveController::class, 'newFolder']);
    Route::post('/subir-archivos', [DriveController::class, 'uploadFiles']);
    Route::post('/data', [DriveController::class, 'getData']);
    Route::get('/almacenamiento', [DriveController::class, 'getStorageSpace']);
    Route::post('/renombrar', [DriveController::class, 'rename']);
    Route::post('/eliminar', [DriveController::class, 'delete']);
    Route::get('/obtener-permisos/{folder_id}', [DriveController::class, 'getPermissionsFolder']);
    Route::get('/obtener-permisos-carpeta/{folder_id}', [DriveController::class, 'getFolderPermissions']);
    Route::get('/cantidad-subcarpetas/{folder_id}', [DriveController::class, 'getSubFoldersCount']);
    Route::get('/descargar-archivo/{file_id}', [DriveController::class, 'downloadFile']);
    Route::get('/get-etiquetas', [DriveController::class, 'getEtiquetas']);
    Route::post('/get-productos', [DriveController::class, 'getProductos']);
    Route::post('/eliminar-documentacion', [DriveController::class, 'EliminarDocumentacionEtiqueta']);
    Route::post('/cargar-documentacion-tecnica', [DriveController::class, 'CargarDocumentacionEtiqueta']);
});
