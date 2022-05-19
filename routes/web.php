<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\depositController;
use App\Http\Controllers\appController;
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

    Route::get('/', [HomeController::class, 'index'])->name('welcome');
    Route::get('/app', [appController::class, 'index'])->name('app');
    Route::get('pick-store/{id}', [appController::class, 'pickStore'])->name('pick.store');
    Route::resource('deposit', depositController::class);

});
