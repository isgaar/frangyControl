<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('inventarios')) {
            Schema::create('inventarios', function (Blueprint $table) {
                $table->id('id_inventario');
            $table->string('codigo_barras')->unique()->nullable();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->decimal('precio_venta', 10, 2)->default(0);
            $table->decimal('costo', 10, 2)->default(0);
            $table->integer('cantidad_stock')->default(0);
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('inventarios');
    }
};
