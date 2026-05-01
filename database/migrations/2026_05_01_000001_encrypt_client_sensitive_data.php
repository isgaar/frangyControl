<?php

use App\Support\ClientDataSecurity;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            foreach (ClientDataSecurity::FIELDS as $field) {
                $hashColumn = ClientDataSecurity::hashColumn($field);

                if (!Schema::hasColumn('clientes', $hashColumn)) {
                    $table->string($hashColumn, 64)->nullable()->after($field)->index('clientes_' . $hashColumn . '_idx');
                }
            }
        });

        $this->expandSensitiveColumns();
        $this->encryptExistingClientData();
    }

    public function down(): void
    {
        $this->decryptExistingClientData();

        Schema::table('clientes', function (Blueprint $table) {
            foreach (ClientDataSecurity::FIELDS as $field) {
                $hashColumn = ClientDataSecurity::hashColumn($field);

                if (Schema::hasColumn('clientes', $hashColumn)) {
                    $table->dropIndex('clientes_' . $hashColumn . '_idx');
                    $table->dropColumn($hashColumn);
                }
            }
        });

        $this->shrinkSensitiveColumns();
    }

    private function expandSensitiveColumns(): void
    {
        $driver = DB::getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            DB::statement('ALTER TABLE clientes MODIFY nombreCompleto TEXT NOT NULL');
            DB::statement('ALTER TABLE clientes MODIFY telefono TEXT NOT NULL');
            DB::statement('ALTER TABLE clientes MODIFY correo TEXT NOT NULL');
            DB::statement('ALTER TABLE clientes MODIFY rfc TEXT NOT NULL');
        }

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE clientes ALTER COLUMN "nombreCompleto" TYPE TEXT');
            DB::statement('ALTER TABLE clientes ALTER COLUMN telefono TYPE TEXT');
            DB::statement('ALTER TABLE clientes ALTER COLUMN correo TYPE TEXT');
            DB::statement('ALTER TABLE clientes ALTER COLUMN rfc TYPE TEXT');
        }
    }

    private function shrinkSensitiveColumns(): void
    {
        $driver = DB::getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            DB::statement('ALTER TABLE clientes MODIFY nombreCompleto VARCHAR(100) NOT NULL');
            DB::statement('ALTER TABLE clientes MODIFY telefono VARCHAR(10) NOT NULL');
            DB::statement('ALTER TABLE clientes MODIFY correo VARCHAR(30) NOT NULL');
            DB::statement('ALTER TABLE clientes MODIFY rfc VARCHAR(13) NOT NULL');
        }

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE clientes ALTER COLUMN "nombreCompleto" TYPE VARCHAR(100)');
            DB::statement('ALTER TABLE clientes ALTER COLUMN telefono TYPE VARCHAR(10)');
            DB::statement('ALTER TABLE clientes ALTER COLUMN correo TYPE VARCHAR(30)');
            DB::statement('ALTER TABLE clientes ALTER COLUMN rfc TYPE VARCHAR(13)');
        }
    }

    private function encryptExistingClientData(): void
    {
        DB::table('clientes')
            ->select(['id_cliente', ...ClientDataSecurity::FIELDS])
            ->orderBy('id_cliente')
            ->chunkById(100, function ($clientes) {
                foreach ($clientes as $cliente) {
                    $updates = [];

                    foreach (ClientDataSecurity::FIELDS as $field) {
                        $prepared = ClientDataSecurity::prepareForStorage(
                            $field,
                            ClientDataSecurity::decrypt($cliente->{$field})
                        );

                        $updates[$field] = ClientDataSecurity::encrypt($prepared);
                        $updates[ClientDataSecurity::hashColumn($field)] = ClientDataSecurity::hash($field, $prepared);
                    }

                    DB::table('clientes')
                        ->where('id_cliente', $cliente->id_cliente)
                        ->update($updates);
                }
            }, 'id_cliente');
    }

    private function decryptExistingClientData(): void
    {
        DB::table('clientes')
            ->select(['id_cliente', ...ClientDataSecurity::FIELDS])
            ->orderBy('id_cliente')
            ->chunkById(100, function ($clientes) {
                foreach ($clientes as $cliente) {
                    $updates = [];

                    foreach (ClientDataSecurity::FIELDS as $field) {
                        $updates[$field] = ClientDataSecurity::prepareForStorage(
                            $field,
                            ClientDataSecurity::decrypt($cliente->{$field})
                        );
                    }

                    DB::table('clientes')
                        ->where('id_cliente', $cliente->id_cliente)
                        ->update($updates);
                }
            }, 'id_cliente');
    }
};
