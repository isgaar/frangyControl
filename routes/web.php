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
        return redirect('/home');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::group(['prefix' => 'users'], function() {
        Route::get('/usuarios', [App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
        Route::get('/nuevo', [App\Http\Controllers\Admin\UserController::class, 'create'])->name('users.create');
        Route::post('/store', [App\Http\Controllers\Admin\UserController::class, 'store'])->name('users.store');
        Route::get('/edit/{id}', [App\Http\Controllers\Admin\UserController::class, 'edit'])->name('users.edit');
        Route::put('/{id}', [App\Http\Controllers\Admin\UserController::class, 'update'])->name('users.update');
        Route::get('/show/{id}', [App\Http\Controllers\Admin\UserController::class, 'show'])->name('users.show');
        Route::get('/delete/{id}', [App\Http\Controllers\Admin\UserController::class, 'delete'])->name('users.delete');
        Route::get('{id}/destroy', [App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('users.destroy');
    });
