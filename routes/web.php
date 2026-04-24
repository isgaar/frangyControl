<?php

use App\Http\Controllers\Admin\OrdenController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LandingController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    if (Auth::check()) {
        return redirect('/home');
    }

    return redirect()->route('landing.pages.welcome');
});

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::get('/creditos', [HomeController::class, 'about'])->name('acerca');

Route::group(['prefix' => 'landing'], function () {
    Route::get('/welcome', [LandingController::class, 'welcome'])->name('landing.pages.welcome');
});

Route::post('verificar_nombre_usuario', [OrdenController::class, 'verificarNombreUsuario'])->name('verificar_nombre_usuario');
