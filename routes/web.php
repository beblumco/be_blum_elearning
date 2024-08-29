<?php

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\auth\AuthenticationController;
use App\Http\Controllers\WelcomeController;

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

Route::get('/', [AuthenticationController::class, 'LoginView'])->name('login_index');
Route::get('/recuperar-contrasena', [AuthenticationController::class, 'RecoverPasswordView'])->name('recover_password_view');
Route::get('/registrarme', [AuthenticationController::class, 'RegisterView'])->name('register_view');
Route::get('/registrarme-lideres-grupo-empresa', [AuthenticationController::class, 'RegisterLiderGrupoEmpresaView'])->name('register_lideres-grupo_view');
Route::get('/registrarme-colaboradores/{id}', [AuthenticationController::class, 'RegisterColaboradorView'])->name('RegisterColaboradores_view');
Route::post('/login', [AuthenticationController::class, 'Login'])->name('login');
Route::post('/logout', [AuthenticationController::class, 'Logout'])->name('logout');
Route::post('/recuperar_contrasena_enviar', [AuthenticationController::class, 'RecoveryPassword'])->name('send_recovery_password');
Route::get('/nueva_contrasena/{email}', [AuthenticationController::class, 'NewPasswordView'])->name('new_password_view');
Route::post('/change_password', [AuthenticationController::class, 'NewPassword'])->name('new_password');
Route::post('/registro', [AuthenticationController::class, 'registerNewUser'])->name('register');
Route::post('/registro-lideres-grupo-empresa', [AuthenticationController::class, 'registerNewUserLiderGrupoEmpresa'])->name('registro-lideres-grupo-empresa');
Route::get('/puntos-evaluacion-main-account/{main_account}',[AuthenticationController::class, 'getEvaluationPointsByMainAccount']);
Route::get('/sendSectores', [AuthenticationController::class, 'sendSectores'])->name('sendSectores');
Route::get('/registrar-asistencia/{codigo}', [AuthenticationController::class, 'RegisterAsistView'])->name('RegisterAsistView');
Route::post('/crear-asistencia', [AuthenticationController::class, 'RegisterAsist'])->name('RegisterAsist');

//GUIA
Route::get('/get_guia', [Controller::class, 'GetGuia'])->name('GetGuia');
Route::post('/create_guia_visualizada', [Controller::class, 'createGuiaVisualizada'])->name('createGuiaVisualizada');

//PERMISOS
Route::get('/get_all_permisos', [Controller::class, 'GetAllPermisos'])->name('GetAllPermisos');

// RUTAS DE WELCOME TO SAVK
Route::get('/welcome-to-savk', [WelcomeController::class, 'WelcomeIndex'])->name('index_welcome');
Route::get('/auth_by_link/{token}', [AuthenticationController::class, 'AuthByLink'])->name('auth_by_link');
Route::get('/auth_by_link_params', [AuthenticationController::class, 'AuthByLinkParams'])->name('auth_by_link');
