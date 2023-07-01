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
        Schema::create('fotografias', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ordenes_id');
            $table->string('ruta');
            // Otros campos que puedas necesitar
            $table->timestamps();

            $table->foreign('ordenes_id')->references('id_ordenes')->on('ordenes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('fotografias');
    }
};
