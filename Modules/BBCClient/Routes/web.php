<?php

use Modules\BBCClient\Http\Controllers\BBCClientController;
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

Route::prefix('bbc')->group(function() {
    Route::get('/', [BBCClientController::class, 'IndexBBC'])->name('index_bbc');
    Route::get('/certificados', [BBCClientController::class, 'IndexDownloadBBC'])->name('index_bbc_download');
    Route::get('/matriz_insumo', [BBCClientController::class, 'IndexDownloadMatrizInsumoBBC'])->name('index_matriz_insumo');
    Route::get('/formulario', [BBCClientController::class, 'IndexFormBBC'])->name('index_form');
    Route::post('/send_email', [BBCClientController::class, 'SendEmailForm'])->name('send_email');
});
