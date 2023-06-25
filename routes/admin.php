<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Panel\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TiposController;

Route::get('', [HomeController::class, 'index']);

Route::group(['prefix' => 'users'], function(){
    Route::get('/usuarios',[App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
    Route::get('/nuevo',[App\Http\Controllers\Admin\UserController::class, 'create'])->name('users.create');
    Route::post('/store',[App\Http\Controllers\Admin\UserController::class, 'store'])->name('users.store');
    Route::get('/edit/{id}',[App\Http\Controllers\Admin\UserController::class, 'edit'])->name('users.edit');
    Route::put('/{id}',[App\Http\Controllers\Admin\UserController::class, 'update'])->name('users.update');
    Route::get('/show/{id}',[App\Http\Controllers\Admin\UserController::class, 'show'])->name('users.show');
    Route::get('/delete/{id}',[App\Http\Controllers\Admin\UserController::class, 'delete'])->name('users.delete');
    Route::get('{id}/destroy',[App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('users.destroy');
});
Route::group(['prefix' => 'datosv'], function(){
    Route::get('/vehiculosnom',[App\Http\Controllers\Admin\DatovController::class, 'index'])->name('datosv.index');
    Route::get('/nuevo',[App\Http\Controllers\Admin\DatovController::class, 'create'])->name('datosv.create');
    Route::post('/store',[App\Http\Controllers\Admin\DatovController::class, 'store'])->name('datosv.store');
    Route::get('/edit/{id}',[App\Http\Controllers\Admin\DatovController::class, 'edit'])->name('datosv.edit');
    Route::put('/{id}',[App\Http\Controllers\Admin\DatovController::class, 'update'])->name('datosv.update');
    Route::get('/delete/{id}',[App\Http\Controllers\Admin\DatovController::class, 'delete'])->name('datosv.delete');
    Route::get('/{id}/destroy',[App\Http\Controllers\Admin\DatovController::class, 'destroy'])->name('datosv.destroy');
});
Route::group(['prefix' => 'tipos'], function(){
    Route::get('/servicios',[App\Http\Controllers\Admin\TiposController::class, 'index'])->name('tipo_servicio.index');
    Route::get('/nuevo',[App\Http\Controllers\Admin\TiposController::class, 'create'])->name('tipo_servicio.create');
    Route::post('/store',[App\Http\Controllers\Admin\TiposController::class, 'store'])->name('tipo_servicio.store');
    Route::get('/edit/{id}',[App\Http\Controllers\Admin\TiposController::class, 'edit'])->name('tipo_servicio.edit');
    Route::put('/{id}',[App\Http\Controllers\Admin\TiposController::class, 'update'])->name('tipo_servicio.update');
    Route::get('/delete/{id}',[App\Http\Controllers\Admin\TiposController::class, 'delete'])->name('tipo_servicio.delete');
    Route::get('/{id}/destroy',[App\Http\Controllers\Admin\TiposController::class, 'destroy'])->name('tipo_servicio.destroy');
});
Route::group(['prefix' => 'tiposvehiculos'], function(){
    Route::get('/tipo',[App\Http\Controllers\Admin\TipoVehiculoController::class, 'index'])->name('tipo_vehiculo.index');
    Route::get('/nuevo',[App\Http\Controllers\Admin\TipoVehiculoController::class, 'create'])->name('tipo_servicio.create');
    Route::post('/store',[App\Http\Controllers\Admin\TipoVehiculoController::class, 'store'])->name('tipo_servicio.store');
    Route::get('/edit/{id}',[App\Http\Controllers\Admin\TipoVehiculoController::class, 'edit'])->name('tipo_servicio.edit');
    Route::put('/{id}',[App\Http\Controllers\Admin\TipoVehiculoController::class, 'update'])->name('tipo_servicio.update');
    Route::get('/delete/{id}',[App\Http\Controllers\Admin\TipoVehiculoController::class, 'delete'])->name('tipo_servicio.delete');
    Route::get('/{id}/destroy',[App\Http\Controllers\Admin\TipoVehiculoController::class, 'destroy'])->name('tipo_servicio.destroy');
});