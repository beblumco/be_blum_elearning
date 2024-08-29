<?php

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

use Modules\Accompaniment\Http\Controllers\AccompanimentController;

Route::prefix('accompaniment')->group(function () {
    Route::get('/descargar-certificacion/{id}', [AccompanimentController::class, 'descargarCertificacion'])->name('descargarCertificacion');
    Route::get('/descargar-documento/{id}/{tipo}', [AccompanimentController::class, 'generarDocumento'])->name('generarDocumento');
    Route::get('/', [AccompanimentController::class, 'IndexAccompaniment'])->name('accompaniment_index');
    Route::get('/detalle-auditoria/{id}', [AccompanimentController::class, 'detalleAuditoria'])->name('detalleAuditoria');
    Route::post('/detalle-auditoria-filtro/{id}', [AccompanimentController::class, 'detalleAuditoriaFiltrado'])->name('detalleAuditoriaFiltrado');
    Route::get('/download-report/{id}', [AccompanimentController::class, 'DownloadReportVisit'])->name('downloadreport');
    Route::get('/download-excel', [AccompanimentController::class, 'downloadExcel'])->name('downloadexcel');
    Route::post('/get-all-accompaniments', [AccompanimentController::class, 'getAcompañamientos']);
    Route::post('/crear', [AccompanimentController::class, 'store']);
    Route::post('/indicadores', [AccompanimentController::class, 'indicadores']);
    Route::post('/get-result', [AccompanimentController::class, 'getResultadosAcompañamiento']);

    Route::get('/centros-de-costo-usuario', [AccompanimentController::class, 'getCentrosCostoUsuario']);
});
