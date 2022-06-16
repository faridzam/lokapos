<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\depositController;
use App\Http\Controllers\appController;
use App\Http\Controllers\penjualanController;
use App\Http\Controllers\printController;
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

Route::middleware(['guest'])->group(function () {

    Auth::routes();
    Route::get('login', [LoginController::class, 'index'])->name('login');
    Route::post('login', [LoginController::class, 'authenticate'])->name('login.authenticate');

});

Route::middleware(['auth:cashier'])->group(function () {

    Route::get('/{vue_capture?}', function () {
        return view('index');
    })->where('vue_capture', '[\/\w\.-]*');

    Route::get('/', [HomeController::class, 'index'])->name('welcome');
    Route::post('/logout', [loginController::class, 'logout'])->name('logout');
    //Route::get('/app', [appController::class, 'index'])->name('app');
    //Route::get('pick-store/{id}', [appController::class, 'pickStore'])->name('pick.store');

    Route::resource('depositLaravel', depositController::class);
    Route::post('/storeOrder', [penjualanController::class, 'storeOrder'])->name('storeOrder');
    Route::post('/storeSavedOrder', [penjualanController::class, 'storeSavedOrder'])->name('storeSavedOrder');
    Route::post('/saveCart', [penjualanController::class, 'saveCart'])->name('saveCart');
    Route::post('/saveCartPrint', [penjualanController::class, 'saveCartPrint'])->name('saveCartPrint');
    Route::post('/printBillAll/{id}', [penjualanController::class, 'printBillAll'])->name('printBillAll');
    Route::post('/printBillRemain/{id}', [penjualanController::class, 'printBillRemain'])->name('printBillRemain');
    Route::post('/openCashDrawer', [printController::class, 'openCashDrawer'])->name('openCashDrawer');
    Route::post('/closeOrder', [penjualanController::class, 'closeOrder'])->name('closeOrder');
    Route::post('/printInvoice2', [printController::class, 'printInvoice2'])->name('printInvoice2');

    //Route::get('penjualanLaravel', [penjualanController::class, 'showProduct']);

});
