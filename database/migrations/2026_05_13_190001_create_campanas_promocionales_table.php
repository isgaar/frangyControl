<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('campanas_promocionales')) {
            Schema::create('campanas_promocionales', function (Blueprint $table) {
                $table->id('id_campana');
                $table->string('nombre');
            $table->text('mensaje');
            $table->unsignedInteger('servicio_id')->nullable()->comment('Filtrar por interés (NULL = Todos)');
            $table->dateTime('fecha_programada');
            $table->enum('estado', ['pendiente', 'en_progreso', 'completada'])->default('pendiente');
            $table->timestamps();

            $table->foreign('servicio_id')->references('id_servicio')->on('tipo_servicio')->onDelete('set null');
        });
        }
    }

    public function down()
    {
        Schema::dropIfExists('campanas_promocionales');
    }
};
