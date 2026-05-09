<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE cotizaciones DROP FOREIGN KEY cotizaciones_cliente_id_foreign');
        DB::statement('ALTER TABLE cotizaciones MODIFY cliente_id INT UNSIGNED NULL');
        DB::statement('ALTER TABLE cotizaciones ADD CONSTRAINT cotizaciones_cliente_id_foreign FOREIGN KEY (cliente_id) REFERENCES clientes(id_cliente) ON DELETE SET NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE cotizaciones DROP FOREIGN KEY cotizaciones_cliente_id_foreign');
        DB::statement('ALTER TABLE cotizaciones MODIFY cliente_id INT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE cotizaciones ADD CONSTRAINT cotizaciones_cliente_id_foreign FOREIGN KEY (cliente_id) REFERENCES clientes(id_cliente)');
    }
};
