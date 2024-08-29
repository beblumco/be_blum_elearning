<?php

use Illuminate\Http\Request;
use Modules\Shop\Http\Controllers\ShopController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/shop', function (Request $request) {
    return $request->user();
});

Route::prefix('cg1_programming')->group(function () {
    Route::post('/update_state_botty_bd', [ShopController::class, 'UpdateStateBottyBD']);
    Route::post('/get_order_names', [ShopController::class, 'GetOrderNames']);
    Route::post('/update_order_status', [ShopController::class, 'UpdateOrderStatus']);
    Route::post('/activate_botty_by_id_order', [ShopController::class, 'ActivateBottyByIdOrder']);
});