<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cotizaciones', function (Blueprint $table) {
            $table->bigIncrements('id_cotizacion');
            $table->unsignedInteger('cliente_id');
            $table->unsignedInteger('servicio_id');
            $table->unsignedBigInteger('user_id');
            $table->string('folio', 24)->unique();
            $table->decimal('precio_base', 10, 2);
            $table->decimal('descuento_porcentaje', 5, 2)->default(0);
            $table->decimal('descuento_monto', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->date('vigencia')->nullable();
            $table->string('estado', 20)->default('borrador');
            $table->text('notas')->nullable();
            $table->timestamps();

            $table->foreign('cliente_id')->references('id_cliente')->on('clientes');
            $table->foreign('servicio_id')->references('id_servicio')->on('tipo_servicio');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cotizaciones');
    }
};
