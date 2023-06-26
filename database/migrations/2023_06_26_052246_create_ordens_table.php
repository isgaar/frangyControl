<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orden', function (Blueprint $table) {
            $table->bigIncrements('no_orden');
            $table->bigInteger('id')->unsigned();
            $table->unsignedInteger('cliente_id');
            $table->unsignedInteger('vehiculo_id');
            $table->unsignedInteger('servicio_id');
            $table->unsignedInteger('tvehiculo_id');
            $table->integer('yearVehiculo');
            $table->integer('noSerievehiculo');
            $table->string('modeloVehiculo', 30);
            $table->string('placas', 7);
            $table->string('color');
            $table->float('kilometraje');
            $table->float('motor');
            $table->float('cilindros');
            $table->text('observacionesInt');
            $table->text('recomendacionesCliente');
            $table->text('detallesOrden');
            $table->boolean('retiroRefacciones');
            $table->date('fechaRegistro');
            $table->date('fechaEntrega');
            $table->timestamps();
        
            $table->foreign('id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('cliente_id')->references('id_cliente')->on('clientes')->onDelete('cascade');
            $table->foreign('servicio_id')->references('id_servicio')->on('tipo_servicio')->onDelete('cascade');
            $table->foreign('vehiculo_id')->references('id_vehiculo')->on('datos_vehiculo')->onDelete('cascade');
            $table->foreign('tvehiculo_id')->references('id_tvehiculo')->on('tipo_vehiculo')->onDelete('cascade');
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orden');
    }
}

