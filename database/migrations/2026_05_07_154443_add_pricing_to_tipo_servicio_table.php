<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tipo_servicio', function (Blueprint $table) {
            $table->decimal('precio_base', 10, 2)->default(0)->after('nombreServicio');
            $table->decimal('descuento_porcentaje', 5, 2)->default(0)->after('precio_base');
            $table->date('descuento_inicio')->nullable()->after('descuento_porcentaje');
            $table->date('descuento_fin')->nullable()->after('descuento_inicio');
        });
    }

    public function down(): void
    {
        Schema::table('tipo_servicio', function (Blueprint $table) {
            $table->dropColumn([
                'precio_base',
                'descuento_porcentaje',
                'descuento_inicio',
                'descuento_fin',
            ]);
        });
    }
};
