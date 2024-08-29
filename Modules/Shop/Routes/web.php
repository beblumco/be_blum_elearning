<?php

use Modules\Shop\Http\Controllers\ShopController;
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

Route::prefix('catalogo')->group(function() {
    Route::get('/', [ShopController::class, 'index'])->name('catalogo_index');
    Route::post('/get_data_init', [ShopController::class, 'GetDataInit'])->name('get_data_init');
    Route::get('/get_years', [ShopController::class, 'getAllYears'])->name('get_years');
    Route::get('/detalle/{id_encrypt}', [ShopController::class, 'details'])->name('catalogo_detalle_wh');
    Route::get('/detalle/{id_encript}/{opc}', [ShopController::class, 'details'])->name('catalogo_detalle');
    Route::post('/get_data_product_detail', [ShopController::class, 'GetDataProductDetail'])->name('get_data_product_detail');
    Route::post('/download_doc_product', [ShopController::class, 'DownloadDocProduct'])->name('download_doc_product');
    Route::post('/add_product_to_car', [ShopController::class, 'AddProductToCar'])->name('add_product_to_car');

    Route::get('/carrito', [ShopController::class, 'IndexShoppingCar'])->name('index_shopping_car');
    Route::post('/save_quantity_detail_product', [ShopController::class, 'SaveQuantityDetailProduct'])->name('save_quantity_detail_product');
    Route::post('/delete_detail_product', [ShopController::class, 'DeleteDetailProduct'])->name('delete_detail_product');
    Route::post('/save_obs_pedido', [ShopController::class, 'SaveObsPedido'])->name('save_obs_pedido');
    Route::post('/get_data_init_assign', [ShopController::class, 'GetDataInitAssign'])->name('get_data_init_assign');
    Route::post('/clear_car_order', [ShopController::class, 'ClearCarOrder'])->name('clear_car_order');
    Route::post('/get_data_init_car', [ShopController::class, 'GetDataInitCar'])->name('get_data_init_car');
    Route::post('/save_assign', [ShopController::class, 'SaveAssign'])->name('save_assign');

    Route::get('/historial', [ShopController::class, 'IndexHistorical'])->name('index_historical');
    Route::post('/get_data_init_historical', [ShopController::class, 'GetDataInitHistorical'])->name('get_data_init_historical');
    Route::post('/get_data_detail_order', [ShopController::class, 'GetDetailOrder'])->name('get_data_detail_order');

    Route::get('/reportes', [ShopController::class, 'IndexReports'])->name('index_reports');
    Route::post('/get_data_init_active_products', [ShopController::class, 'GetDataInitActiveProducts'])->name('get_data_init_active_products');
    Route::post('/save_quantity_detail_admin_section', [ShopController::class, 'SaveQuantityDetailAdminSection'])->name('save_quantity_detail_admin_section');
    Route::post('/save_comment_detail', [ShopController::class, 'SaveCommentDetail'])->name('save_comment_detail');
    Route::post('/delete_products_active', [ShopController::class, 'DeleteProductsActive'])->name('delete_products_active');
    Route::post('/to_generate_order_products_active', [ShopController::class, 'ToGenerateOrderProductsActive'])->name('to_generate_order_products_active');
    Route::post('/delete_products_cancel', [ShopController::class, 'DeleteProductsCancel'])->name('delete_products_cancel');

    Route::post('/fill_detail_items', [ShopController::class, 'FillDetailItems'])->name('fill_detail_items');
    Route::post('/save_oc_order', [ShopController::class, 'SaveOcOrder'])->name('save_oc_order');

});
