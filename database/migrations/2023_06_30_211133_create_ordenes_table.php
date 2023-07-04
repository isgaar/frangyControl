<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('ordenes', function (Blueprint $table) {
            $table->bigIncrements('id_ordenes');
            $table->unsignedInteger('cliente_id');
            $table->unsignedInteger('vehiculo_id');
            $table->unsignedInteger('servicio_id');
            $table->unsignedInteger('tvehiculo_id');
            $table->unsignedBigInteger('id');
            $table->string('nombreCompleto');
            $table->string('telefono');
            $table->string('correo');
            $table->string('marca');
            $table->string('tipo');
            $table->integer('yearVehiculo');
            $table->string('color');
            $table->string('placas', 7);
            $table->double('kilometraje');
            $table->double('motor');
            $table->double('cilindros');
            $table->string('noSerievehiculo', 11);
            $table->date('fechaEntrega');
            $table->text('observacionesInt')->nullable();
            $table->text('recomendacionesCliente')->nullable();
            $table->text('detallesOrden')->nullable();
            $table->boolean('retiroRefacciones')->default(0);
            $table->timestamps();

            $table->foreign('cliente_id')->references('id_cliente')->on('clientes');
            $table->foreign('vehiculo_id')->references('id_vehiculo')->on('datos_vehiculo');
            $table->foreign('servicio_id')->references('id_servicio')->on('tipo_servicio');
            $table->foreign('tvehiculo_id')->references('id_tvehiculo')->on('tipo_vehiculo');
            $table->foreign('id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('ordenes');
    }
};