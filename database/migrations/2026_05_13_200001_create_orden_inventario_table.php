<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('orden_inventario')) {
            Schema::create('orden_inventario', function (Blueprint $table) {
                $table->id();
            $table->unsignedBigInteger('orden_id');
            $table->unsignedBigInteger('inventario_id');
            $table->integer('cantidad');
            $table->decimal('precio_unitario', 10, 2);
            $table->timestamps();

            // Ajustando a la FK real, la tabla se llama 'ordenes' y la pk 'id_ordenes' en el modelo
            // pero para no romper convenciones, dejaremos la key libre si no hay constraint
                $table->foreign('orden_id')->references('id_ordenes')->on('ordenes')->onDelete('cascade');
                $table->foreign('inventario_id')->references('id_inventario')->on('inventarios')->onDelete('cascade');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('orden_inventario');
    }
};
