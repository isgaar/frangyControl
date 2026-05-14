<?php

use App\Http\Controllers\Admin\ClienteController;
use App\Http\Controllers\Admin\CotizacionController;
use App\Http\Controllers\Admin\DatovController;
use App\Http\Controllers\Admin\OrdenController;
use App\Http\Controllers\Admin\TiposController;
use App\Http\Controllers\Admin\TipoVehiculoController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CampanaController;
use App\Http\Controllers\Admin\InventarioController;
use App\Http\Controllers\Admin\ChatController;
use Illuminate\Support\Facades\Route;

Route::middleware('can:admin.users.usuarios')
    ->prefix('usuarios')
    ->name('usuarios.')
    ->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/nuevo', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/{id}/editar', [UserController::class, 'edit'])->name('edit');
        Route::put('/{id}', [UserController::class, 'update'])->name('update');
        Route::get('/{id}/eliminar', [UserController::class, 'delete'])->name('delete');
        Route::get('/{id}/destruir', [UserController::class, 'destroy'])->name('destroy');
        Route::get('/{id}', [UserController::class, 'show'])->name('show');
    });

Route::middleware('can:admin.datosv.vehiculosnom')
    ->prefix('catalogos')
    ->name('catalogos.')
    ->group(function () {
        Route::get('/', [DatovController::class, 'index'])->name('index');
        Route::get('/carga-general', [DatovController::class, 'create'])->name('carga_general.create');
        Route::post('/carga-general', [DatovController::class, 'store'])->name('carga_general.store');

        Route::get('/marcas/nueva', [DatovController::class, 'createunique'])->name('marcas.create');
        Route::post('/marcas', [DatovController::class, 'storeunique'])->name('marcas.store');
        Route::get('/marcas/{id}/editar', [DatovController::class, 'edit'])->name('marcas.edit');
        Route::put('/marcas/{id}', [DatovController::class, 'update'])->name('marcas.update');
        Route::get('/marcas/{id}/eliminar', [DatovController::class, 'delete'])->name('marcas.delete');
        Route::get('/marcas/{id}/destruir', [DatovController::class, 'destroy'])->name('marcas.destroy');

        Route::get('/tipos-vehiculo', [TipoVehiculoController::class, 'index'])->name('tipos_vehiculo.index');
        Route::get('/tipos-vehiculo/nuevo', [TipoVehiculoController::class, 'create'])->name('tipos_vehiculo.create');
        Route::post('/tipos-vehiculo', [TipoVehiculoController::class, 'store'])->name('tipos_vehiculo.store');
        Route::get('/tipos-vehiculo/{id}/editar', [TipoVehiculoController::class, 'edit'])->name('tipos_vehiculo.edit');
        Route::put('/tipos-vehiculo/{id}', [TipoVehiculoController::class, 'update'])->name('tipos_vehiculo.update');
        Route::get('/tipos-vehiculo/{id}/eliminar', [TipoVehiculoController::class, 'delete'])->name('tipos_vehiculo.delete');
        Route::get('/tipos-vehiculo/{id}/destruir', [TipoVehiculoController::class, 'destroy'])->name('tipos_vehiculo.destroy');

        Route::get('/servicios', [TiposController::class, 'index'])->name('servicios.index');
        Route::get('/servicios/nuevo', [TiposController::class, 'create'])->name('servicios.create');
        Route::post('/servicios', [TiposController::class, 'store'])->name('servicios.store');
        Route::get('/servicios/{id}/editar', [TiposController::class, 'edit'])->name('servicios.edit');
        Route::put('/servicios/{id}', [TiposController::class, 'update'])->name('servicios.update');
        Route::get('/servicios/{id}/eliminar', [TiposController::class, 'delete'])->name('servicios.delete');
        Route::get('/servicios/{id}/destruir', [TiposController::class, 'destroy'])->name('servicios.destroy');
    });

Route::prefix('clientes')
    ->name('clientes.')
    ->group(function () {
        Route::get('/', [ClienteController::class, 'index'])->name('index');
        Route::get('/nuevo', [ClienteController::class, 'create'])->name('create');
        Route::post('/', [ClienteController::class, 'store'])->name('store');
        Route::get('/{id}/editar', [ClienteController::class, 'edit'])->name('edit');
        Route::put('/{id}', [ClienteController::class, 'update'])->name('update');
        Route::get('/{id}/eliminar', [ClienteController::class, 'delete'])->name('delete');
        Route::get('/{id}/destruir', [ClienteController::class, 'destroy'])->name('destroy');
        Route::get('/{id}', [ClienteController::class, 'show'])->name('show');
    });

Route::prefix('cotizaciones')
    ->name('cotizaciones.')
    ->group(function () {
        Route::get('/', [CotizacionController::class, 'index'])->name('index');
        Route::get('/nueva', [CotizacionController::class, 'create'])->name('create');
        Route::post('/', [CotizacionController::class, 'store'])->name('store');
        Route::get('/{id_cotizacion}/editar', [CotizacionController::class, 'edit'])->name('edit');
        Route::get('/{id_cotizacion}/crear-orden', [CotizacionController::class, 'createOrder'])->name('create_order');
        Route::put('/{id_cotizacion}', [CotizacionController::class, 'update'])->name('update');
        Route::delete('/{id_cotizacion}', [CotizacionController::class, 'destroy'])->name('destroy');
        Route::get('/{id_cotizacion}', [CotizacionController::class, 'show'])->name('show');
    });

Route::prefix('ordenes')
    ->name('ordenes.')
    ->group(function () {
        Route::get('/', [OrdenController::class, 'index'])->name('index');
        Route::get('/nueva', [OrdenController::class, 'create'])->name('create');
        Route::get('/registro', [OrdenController::class, 'registro'])->name('registro');
        Route::post('/registro', [OrdenController::class, 'store'])->name('store');
        Route::post('/registro/fotos-temporales', [OrdenController::class, 'storeTemporaryPhoto'])->name('photos.temporary.store');
        Route::delete('/registro/fotos-temporales', [OrdenController::class, 'destroyTemporaryPhoto'])->name('photos.temporary.destroy');
        Route::get('/cliente-existente', [OrdenController::class, 'asigne'])->name('cliente_existente.create');
        Route::post('/cliente-existente', [OrdenController::class, 'store2'])->name('cliente_existente.store');

        Route::get('/opciones/clientes', [OrdenController::class, 'clienteList'])->name('opciones.clientes');
        Route::get('/opciones/marcas', [OrdenController::class, 'marcaList'])->name('opciones.marcas');
        Route::get('/opciones/tipos-vehiculo', [OrdenController::class, 'tipovList'])->name('opciones.tipos_vehiculo');
        Route::get('/opciones/servicios', [OrdenController::class, 'tiposList'])->name('opciones.servicios');
        Route::get('/opciones/usuarios', [OrdenController::class, 'userList'])->name('opciones.usuarios');

        Route::get('/{id_ordenes}/fotografias/{fotografia}', [OrdenController::class, 'showPhoto'])->name('photos.show');
        Route::get('/{id_ordenes}/editar', [OrdenController::class, 'edit'])->name('edit');
        Route::put('/{id_ordenes}', [OrdenController::class, 'update'])->name('update');
        Route::get('/{id_ordenes}/exportar-pdf', [OrdenController::class, 'exportToPDF'])->name('export');
        Route::get('/{id_ordenes}/ticket', [OrdenController::class, 'imprimirTicket'])->name('ticket');
        Route::post('/{id_ordenes}/inventario', [OrdenController::class, 'addInventario'])->name('add_inventario');
        Route::delete('/{id_ordenes}', [OrdenController::class, 'destroy'])->name('destroy');
        Route::get('/{id_ordenes}', [OrdenController::class, 'show'])->name('show');
    });

Route::prefix('campanas')
    ->name('campanas.')
    ->group(function () {
        Route::get('/', [CampanaController::class, 'index'])->name('index');
        Route::get('/nueva', [CampanaController::class, 'create'])->name('create');
        Route::post('/', [CampanaController::class, 'store'])->name('store');
        Route::get('/{campana}/editar', [CampanaController::class, 'edit'])->name('edit');
        Route::put('/{campana}', [CampanaController::class, 'update'])->name('update');
        Route::delete('/{campana}', [CampanaController::class, 'destroy'])->name('destroy');
    });

Route::prefix('inventario')
    ->name('inventario.')
    ->group(function () {
        Route::get('/', [InventarioController::class, 'index'])->name('index');
        Route::get('/nuevo', [InventarioController::class, 'create'])->name('create');
        Route::post('/', [InventarioController::class, 'store'])->name('store');
        Route::get('/{inventario}/editar', [InventarioController::class, 'edit'])->name('edit');
        Route::put('/{inventario}', [InventarioController::class, 'update'])->name('update');
        Route::delete('/{inventario}', [InventarioController::class, 'destroy'])->name('destroy');
    });

Route::prefix('chat')
    ->name('chat.')
    ->group(function () {
        Route::get('/', [ChatController::class, 'index'])->name('index');
        Route::get('/messages/{userId}', [ChatController::class, 'fetchMessages'])->name('messages');
        Route::post('/send', [ChatController::class, 'sendMessage'])->name('send');
        Route::get('/unread-count', [ChatController::class, 'getUnreadCount'])->name('unread_count');
    });
