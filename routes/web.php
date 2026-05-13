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
        return redirect()->route('panel.index');
    }

    return redirect()->route('landing.inicio');
});

Auth::routes();

Route::get('/panel', [HomeController::class, 'index'])->name('panel.index');
Route::get('/panel/pdf', [HomeController::class, 'exportPdf'])->name('panel.pdf');
Route::redirect('/home', '/panel');

Route::get('/acerca', [HomeController::class, 'about'])->name('acerca.index');
Route::redirect('/creditos', '/acerca');

Route::get('/inicio', [LandingController::class, 'welcome'])->name('landing.inicio');
Route::redirect('/landing/welcome', '/inicio');

Route::post('/clientes/verificar-nombre', [OrdenController::class, 'verificarNombreUsuario'])->name('clientes.verificar_nombre');
