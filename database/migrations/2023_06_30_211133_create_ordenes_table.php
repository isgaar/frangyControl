<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
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
            $table->integer('yearVehiculo');
            $table->string('color');
            $table->string('placas', 7);
            $table->double('kilometraje');
            $table->string('motor', 10);
            $table->double('cilindros');
            $table->string('noSerievehiculo', 17);
            $table->string('status', 15);
            $table->string('modelo', 100);
            $table->string('motivo')->nullable();
            $table->date('fechaEntrega');
            $table->text('observacionesInt');
            $table->text('recomendacionesCliente');
            $table->text('detallesOrden');
            $table->boolean('retiroRefacciones');
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