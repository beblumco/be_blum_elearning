<?php

use Modules\Reports\Http\Controllers\ReportsController;
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

Route::prefix('informes')->group(function () {
    Route::get('/acompañamiento', [ReportsController::class, 'IndexReportAccompaniment'])->name('report_accompaniment_index');
    Route::get('/entrenamiento', [ReportsController::class, 'IndexReportTraining'])->name('report_training_index');
    Route::post('/descargar-excel-acompañamiento', [ReportsController::class, 'DownloadExcelAccompanimient'])->name('DownloadExcelAccompaniment');
    Route::post('/descargar-excel-entrenamiento', [ReportsController::class, 'DownloadExcelTraining'])->name('DownloadExcelTraining');
    Route::post('/get-report-accompanimient', [ReportsController::class, 'GetReportAccompaniment'])->name('get_report_accompanimient');
    Route::post('/get-report-training', [ReportsController::class, 'GetReportTraining'])->name('get_report_training');
});
