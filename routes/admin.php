<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Panel\HomeController;
use App\Http\Controllers\UserController;

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
    Route::get('/show/{id_vehiculo}',[App\Http\Controllers\Admin\DatovController::class, 'show'])->name('datosv.show');
    Route::get('/delete/{id}',[App\Http\Controllers\Admin\DatovController::class, 'delete'])->name('datosv.delete');
    Route::get('/{id}/destroy',[App\Http\Controllers\Admin\DatovController::class, 'destroy'])->name('datosv.destroy');
});