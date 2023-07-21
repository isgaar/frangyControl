<?php

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
        if(\Auth::check()){
                return redirect('/home');
            }else{
                return redirect()-> route('landing.pages.welcome');
            }
            return view('landing.pages.index');
        });

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/creditos', [App\Http\Controllers\HomeController::class, 'about'])->name('acerca');

Route::group(['prefix' => 'landing'], function () {
        Route::get('/welcome', [App\Http\Controllers\LandingController::class, 'welcome']) -> name('landing.pages.welcome');
    });


Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::post('verificar_nombre_usuario', 'App\Http\Controllers\Admin\OrdenController@verificarNombreUsuario')->name('verificar_nombre_usuario');

