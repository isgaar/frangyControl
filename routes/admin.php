<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Panel\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TiposController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\OrdenController;

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
    Route::get('/nuevounique',[App\Http\Controllers\Admin\DatovController::class, 'createunique'])->name('datosv.createunique');
    Route::post('/storeunique',[App\Http\Controllers\Admin\DatovController::class, 'storeunique'])->name('datosv.storeunique');
    Route::post('/store',[App\Http\Controllers\Admin\DatovController::class, 'store'])->name('datosv.store');
    Route::get('/edit/{id}',[App\Http\Controllers\Admin\DatovController::class, 'edit'])->name('datosv.edit');
    Route::put('/{id}',[App\Http\Controllers\Admin\DatovController::class, 'update'])->name('datosv.update');
    Route::get('/delete/{id}',[App\Http\Controllers\Admin\DatovController::class, 'delete'])->name('datosv.delete');
    Route::get('/{id}/destroy',[App\Http\Controllers\Admin\DatovController::class, 'destroy'])->name('datosv.destroy');
});
Route::group(['prefix' => 'tiposvehiculos'], function(){
    Route::get('/tipounique',[App\Http\Controllers\Admin\TipoVehiculoController::class, 'index'])->name('tipo_vehiculo.index');
    Route::get('/nuevo',[App\Http\Controllers\Admin\TipoVehiculoController::class, 'create'])->name('tipo_vehiculo.create');
    Route::post('/store',[App\Http\Controllers\Admin\TipoVehiculoController::class, 'store'])->name('tipo_vehiculo.store');
    Route::get('/edit/{id}',[App\Http\Controllers\Admin\TipoVehiculoController::class, 'edit'])->name('tipo_vehiculo.edit');
    Route::put('/{id}',[App\Http\Controllers\Admin\TipoVehiculoController::class, 'update'])->name('tipo_vehiculo.update');
    Route::get('/delete/{id}',[App\Http\Controllers\Admin\TipoVehiculoController::class, 'delete'])->name('tipo_vehiculo.delete');
    Route::get('/{id}/destroy',[App\Http\Controllers\Admin\TipoVehiculoController::class, 'destroy'])->name('tipo_vehiculo.destroy');
});
Route::group(['prefix' => 'tipos'], function(){
    Route::get('/servicios',[App\Http\Controllers\Admin\TiposController::class, 'index'])->name('tipo_servicio.index');
    Route::get('/nuevounique',[App\Http\Controllers\Admin\TiposController::class, 'create'])->name('tipo_servicio.create');
    Route::post('/store',[App\Http\Controllers\Admin\TiposController::class, 'store'])->name('tipo_servicio.store');
    Route::get('/edit/{id}',[App\Http\Controllers\Admin\TiposController::class, 'edit'])->name('tipo_servicio.edit');
    Route::put('/{id}',[App\Http\Controllers\Admin\TiposController::class, 'update'])->name('tipo_servicio.update');
    Route::get('/delete/{id}',[App\Http\Controllers\Admin\TiposController::class, 'delete'])->name('tipo_servicio.delete');
    Route::get('/{id}/destroy',[App\Http\Controllers\Admin\TiposController::class, 'destroy'])->name('tipo_servicio.destroy');
});
Route::group(['prefix' => 'cliente'], function(){
    Route::get('/nuevos',[App\Http\Controllers\Admin\ClienteController::class, 'index'])->name('clientes.index');
    Route::get('/nuevo',[App\Http\Controllers\Admin\ClienteController::class, 'create'])->name('clientes.create');
    Route::post('/store',[App\Http\Controllers\Admin\ClienteController::class, 'store'])->name('clientes.store');
    Route::get('/edit/{id}',[App\Http\Controllers\Admin\ClienteController::class, 'edit'])->name('clientes.edit');
    Route::put('/{id}',[App\Http\Controllers\Admin\ClienteController::class, 'update'])->name('clientes.update');
    Route::get('/show/{id}',[App\Http\Controllers\Admin\ClienteController::class, 'show'])->name('clientes.show');
    Route::get('/delete/{id}',[App\Http\Controllers\Admin\ClienteController::class, 'delete'])->name('clientes.delete');
    Route::get('{id}/destroy',[App\Http\Controllers\Admin\ClienteController::class, 'destroy'])->name('clientes.destroy');
});

Route::group(['prefix' => 'orden'], function(){
    Route::get('/listado',[App\Http\Controllers\Admin\OrdenController::class, 'index'])->name('ordenes.index');
    Route::get('/nuevo',[App\Http\Controllers\Admin\OrdenController::class, 'create'])->name('ordenes.create');
    Route::get('/registro',[App\Http\Controllers\Admin\OrdenController::class, 'registro'])->name('ordenes.registro');
    Route::get('/asignacion',[App\Http\Controllers\Admin\OrdenController::class, 'asigne'])->name('ordenes.asigne');
    Route::post('/store2',[App\Http\Controllers\Admin\OrdenController::class, 'store2'])->name('ordenes.store2');
    Route::post('/store',[App\Http\Controllers\Admin\OrdenController::class, 'store'])->name('ordenes.store');
    Route::get('/cliente',[App\Http\Controllers\Admin\OrdenController::class, 'clienteList'])->name('cliente.list');
    Route::get('/marca',[App\Http\Controllers\Admin\OrdenController::class, 'marcaList'])->name('marca.list');
    Route::get('/tipov',[App\Http\Controllers\Admin\OrdenController::class, 'tipovList'])->name('tipov.list');
    Route::get('/tipos',[App\Http\Controllers\Admin\OrdenController::class, 'tiposList'])->name('tipos.list');
    Route::get('/user',[App\Http\Controllers\Admin\OrdenController::class, 'userList'])->name('user.list');
    Route::get('/edit/{id_ordenes}',[App\Http\Controllers\Admin\OrdenController::class, 'edit'])->name('ordenes.edit');
    Route::put('/{id_ordenes}',[App\Http\Controllers\Admin\OrdenController::class, 'update'])->name('ordenes.update');
    Route::get('/ordenes/exportToPDF/{id_ordenes}', [App\Http\Controllers\Admin\OrdenController::class, 'exportToPDF'])->name('ordenes.export');
    Route::get('/show/{id_ordenes}',[App\Http\Controllers\Admin\OrdenController::class, 'show'])->name('ordenes.show');
    Route::get('/delete/{id}',[App\Http\Controllers\Admin\OrdenController::class, 'delete'])->name('ordenes.delete');
    Route::get('{id}/destroy',[App\Http\Controllers\Admin\OrdenController::class, 'destroy'])->name('ordenes.destroy');
});