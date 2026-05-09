<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cotizacion_conceptos', function (Blueprint $table) {
            $table->bigIncrements('id_concepto');
            $table->unsignedBigInteger('cotizacion_id');
            $table->string('tipo', 30)->default('servicio');
            $table->string('descripcion', 180);
            $table->decimal('cantidad', 10, 2)->default(1);
            $table->decimal('precio_unitario', 10, 2)->default(0);
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->unsignedSmallInteger('orden')->default(0);
            $table->timestamps();

            $table->foreign('cotizacion_id')
                ->references('id_cotizacion')
                ->on('cotizaciones')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cotizacion_conceptos');
    }
};
