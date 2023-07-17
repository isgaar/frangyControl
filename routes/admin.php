<?php

use App\Http\Controllers\Admin\DatovController;
use App\Http\Controllers\Admin\TipoVehiculoController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Panel\HomeController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\TiposController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\OrdenController;

Route::get('', [HomeController::class, 'index']);

Route::group(['prefix' => 'users'], function () {
    Route::get('/usuarios', [UserController::class, 'index'])
        ->name('users.index')
        ->middleware('can:admin.users.usuarios');
    Route::get('/nuevo', [UserController::class, 'create'])
        ->name('users.create')
        ->middleware('can:admin.users.usuarios');
    Route::post('/store', [UserController::class, 'store'])
        ->name('users.store')
        ->middleware('can:admin.users.usuarios');
    Route::get('/edit/{id}', [UserController::class, 'edit'])
        ->name('users.edit')
        ->middleware('can:admin.users.usuarios');
    Route::put('/{id}', [UserController::class, 'update'])
        ->name('users.update')
        ->middleware('can:admin.users.usuarios');
    Route::get('/show/{id}', [UserController::class, 'show'])
        ->name('users.show')
        ->middleware('can:admin.users.usuarios');
    Route::get('/delete/{id}', [UserController::class, 'delete'])
        ->name('users.delete')
        ->middleware('can:admin.users.usuarios');
    Route::get('{id}/destroy', [UserController::class, 'destroy'])
        ->name('users.destroy')
        ->middleware('can:admin.users.usuarios');
});

Route::group(['prefix' => 'datosv'], function () {
    Route::get('/vehiculosnom', [DatovController::class, 'index'])
        ->name('datosv.index')
        ->middleware('can:admin.datosv.vehiculosnom');
    Route::get('/nuevo', [DatovController::class, 'create'])
        ->name('datosv.create')
        ->middleware('can:admin.datosv.vehiculosnom');
    Route::get('/nuevounique', [DatovController::class, 'createunique'])
        ->name('datosv.createunique')
        ->middleware('can:admin.datosv.vehiculosnom');
    Route::post('/storeunique', [DatovController::class, 'storeunique'])
        ->name('datosv.storeunique')
        ->middleware('can:admin.datosv.vehiculosnom');
    Route::post('/store', [DatovController::class, 'store'])
        ->name('datosv.store')
        ->middleware('can:admin.datosv.vehiculosnom');
    Route::get('/edit/{id}', [DatovController::class, 'edit'])
        ->name('datosv.edit')
        ->middleware('can:admin.datosv.vehiculosnom');
    Route::put('/{id}', [DatovController::class, 'update'])
        ->name('datosv.update')
        ->middleware('can:admin.datosv.vehiculosnom');
    Route::get('/delete/{id}', [DatovController::class, 'delete'])
        ->name('datosv.delete')
        ->middleware('can:admin.datosv.vehiculosnom');
    Route::get('/{id}/destroy', [DatovController::class, 'destroy'])
        ->name('datosv.destroy')
        ->middleware('can:admin.datosv.vehiculosnom');
});

Route::group(['prefix' => 'tiposvehiculos'], function () {
    Route::get('/tipounique', [TipoVehiculoController::class, 'index'])
        ->name('tipo_vehiculo.index')
        ->middleware('can:admin.datosv.vehiculosnom');
    Route::get('/nuevo', [TipoVehiculoController::class, 'create'])
        ->name('tipo_vehiculo.create')
        ->middleware('can:admin.datosv.vehiculosnom');
    Route::post('/store', [TipoVehiculoController::class, 'store'])
        ->name('tipo_vehiculo.store')
        ->middleware('can:admin.datosv.vehiculosnom');
    Route::get('/edit/{id}', [TipoVehiculoController::class, 'edit'])
        ->name('tipo_vehiculo.edit')
        ->middleware('can:admin.datosv.vehiculosnom');
    Route::put('/{id}', [TipoVehiculoController::class, 'update'])
        ->name('tipo_vehiculo.update')
        ->middleware('can:admin.datosv.vehiculosnom');
    Route::get('/delete/{id}', [TipoVehiculoController::class, 'delete'])
        ->name('tipo_vehiculo.delete')
        ->middleware('can:admin.datosv.vehiculosnom');
    Route::get('/{id}/destroy', [TipoVehiculoController::class, 'destroy'])
        ->name('tipo_vehiculo.destroy')
        ->middleware('can:admin.datosv.vehiculosnom');
});

Route::group(['prefix' => 'tipos'], function () {
    Route::get('/servicios', [TiposController::class, 'index'])
        ->name('tipo_servicio.index')
        ->middleware('can:admin.datosv.vehiculosnom');
    Route::get('/nuevounique', [TiposController::class, 'create'])
        ->name('tipo_servicio.create')
        ->middleware('can:admin.datosv.vehiculosnom');
    Route::post('/store', [TiposController::class, 'store'])
        ->name('tipo_servicio.store')
        ->middleware('can:admin.datosv.vehiculosnom');
    Route::get('/edit/{id}', [TiposController::class, 'edit'])
        ->name('tipo_servicio.edit')
        ->middleware('can:admin.datosv.vehiculosnom');
    Route::put('/{id}', [TiposController::class, 'update'])
        ->name('tipo_servicio.update')
        ->middleware('can:admin.datosv.vehiculosnom');
    Route::get('/delete/{id}', [TiposController::class, 'delete'])
        ->name('tipo_servicio.delete')
        ->middleware('can:admin.datosv.vehiculosnom');
    Route::get('/{id}/destroy', [TiposController::class, 'destroy'])
        ->name('tipo_servicio.destroy')
        ->middleware('can:admin.datosv.vehiculosnom');
});
Route::group(['prefix' => 'cliente'], function () {
    Route::get('/nuevos', [App\Http\Controllers\Admin\ClienteController::class, 'index'])->name('clientes.index');
    Route::get('/nuevo', [App\Http\Controllers\Admin\ClienteController::class, 'create'])->name('clientes.create');
    Route::post('/store', [App\Http\Controllers\Admin\ClienteController::class, 'store'])->name('clientes.store');
    Route::get('/edit/{id}', [App\Http\Controllers\Admin\ClienteController::class, 'edit'])->name('clientes.edit');
    Route::put('/{id}', [App\Http\Controllers\Admin\ClienteController::class, 'update'])->name('clientes.update');
    Route::get('/show/{id}', [App\Http\Controllers\Admin\ClienteController::class, 'show'])->name('clientes.show');
    Route::get('/delete/{id}', [App\Http\Controllers\Admin\ClienteController::class, 'delete'])->name('clientes.delete');
    Route::get('{id}/destroy', [App\Http\Controllers\Admin\ClienteController::class, 'destroy'])->name('clientes.destroy');
});

Route::group(['prefix' => 'orden'], function () {
    Route::get('/listado', [App\Http\Controllers\Admin\OrdenController::class, 'index'])->name('ordenes.index');
    Route::get('/nuevo', [App\Http\Controllers\Admin\OrdenController::class, 'create'])->name('ordenes.create');
    Route::get('/registro', [App\Http\Controllers\Admin\OrdenController::class, 'registro'])->name('ordenes.registro');
    Route::get('/asignacion', [App\Http\Controllers\Admin\OrdenController::class, 'asigne'])->name('ordenes.asigne');
    Route::post('/store2', [App\Http\Controllers\Admin\OrdenController::class, 'store2'])->name('ordenes.store2');
    Route::post('/store', [App\Http\Controllers\Admin\OrdenController::class, 'store'])->name('ordenes.store');
    Route::get('/cliente', [App\Http\Controllers\Admin\OrdenController::class, 'clienteList'])->name('cliente.list');
    Route::get('/marca', [App\Http\Controllers\Admin\OrdenController::class, 'marcaList'])->name('marca.list');
    Route::get('/tipov', [App\Http\Controllers\Admin\OrdenController::class, 'tipovList'])->name('tipov.list');
    Route::get('/tipos', [App\Http\Controllers\Admin\OrdenController::class, 'tiposList'])->name('tipos.list');
    Route::get('/user', [App\Http\Controllers\Admin\OrdenController::class, 'userList'])->name('user.list');
    Route::get('/edit/{id_ordenes}', [App\Http\Controllers\Admin\OrdenController::class, 'edit'])->name('ordenes.edit');
    Route::put('/{id_ordenes}', [App\Http\Controllers\Admin\OrdenController::class, 'update'])->name('ordenes.update');
    Route::get('/ordenes/exportToPDF/{id_ordenes}', [App\Http\Controllers\Admin\OrdenController::class, 'exportToPDF'])->name('ordenes.export');
    Route::get('/show/{id_ordenes}', [App\Http\Controllers\Admin\OrdenController::class, 'show'])->name('ordenes.show');
    Route::get('/delete/{id}', [App\Http\Controllers\Admin\OrdenController::class, 'delete'])->name('ordenes.delete');
    Route::delete('orden/{id}/destroy', [App\Http\Controllers\Admin\OrdenController::class, 'destroy'])->name('ordenes.destroy');

});