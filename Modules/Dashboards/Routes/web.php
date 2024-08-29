<?php

use Modules\Dashboards\Http\Controllers\DashboardsController;
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

Route::prefix('dashboard')->group(function() {
    Route::get('/', [DashboardsController::class, 'DashboardPrincipalIndex'])->name('principal_index');
    Route::get('/data', [DashboardsController::class, 'getDataAll']);
    Route::get('/propuesta_detalle', [DashboardsController::class, 'PropuestaDetalleView'])->name('propuesta_detalle');

    Route::get('/dashboard_corporativo', [DashboardsController::class, 'DashboardCorporativoIndex'])->name('dashboard_corporativo');

    Route::get('/indicadores_equipo', [DashboardsController::class, 'IndicadoresEquipo'])->name('indicadores_equipo');
    Route::get('/indicadores_equipo_corporativos', [DashboardsController::class, 'IndicadoresEquipoCorporativos'])->name('indicadores_equipo_corporativos');
});


