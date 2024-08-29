<?php
use Modules\Certificates\Http\Controllers\CertificatesController;

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
Route::prefix('certificados')->group(function() {
    Route::get('/', [CertificatesController::class, 'CertificatesIndex'])->name('certificate_index');
});