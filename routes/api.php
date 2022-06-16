<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\appController;
use App\Http\Controllers\penjualanController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('pick-store/{id}', [appController::class, 'pickStore'])->name('pick.store');
Route::get('penjualanLaravel', [penjualanController::class, 'showProduct']);
Route::get('getPaymentMethods', [penjualanController::class, 'showPaymentMethods']);
Route::get('countSavedCart', [penjualanController::class, 'countSavedCart'])->name('savedCartCount');
Route::get('showSavedCart', [penjualanController::class, 'showSavedCart'])->name('showSavedCart');
Route::get('showSavedCartDetail/{id}', [penjualanController::class, 'showSavedCartDetail'])->name('showSavedCartDetail');
Route::get('getInvoices', [penjualanController::class, 'getInvoices'])->name('getInvoices');
Route::get('getItemSales', [penjualanController::class, 'getItemSales'])->name('getItemSales');
