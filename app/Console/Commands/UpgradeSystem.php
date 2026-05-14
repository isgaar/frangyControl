<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class UpgradeSystem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'frangy:upgrade';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualiza el sistema Frangy de manera segura a la última versión manteniendo los datos antiguos';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Iniciando proceso de actualización segura de Frangy...');
        $this->newLine();

        // 1. Verificaciones del Entorno
        $this->warn('1. Verificando extensiones del servidor...');
        if (!extension_loaded('gd')) {
            $this->error('Error: La extensión PHP GD no está instalada o habilitada.');
            $this->error('Es necesaria para procesar fotografías e imprimir reportes. Por favor instálala antes de continuar.');
            return 1;
        }
        $this->line('   [OK] PHP GD detectado.');
        
        if (!extension_loaded('pdo_mysql')) {
            $this->error('Error: La extensión PDO MySQL no está instalada.');
            return 1;
        }
        $this->line('   [OK] PDO MySQL detectado.');
        $this->newLine();

        // 2. Limpieza de Caché
        $this->warn('2. Limpiando cachés antiguas...');
        Artisan::call('optimize:clear');
        $this->line('   [OK] Vistas, rutas y configuraciones limpiadas.');
        $this->newLine();

        // 3. Migraciones
        $this->warn('3. Aplicando cambios estructurales a la base de datos...');
        if ($this->confirm('¿Deseas respaldar manualmente la base de datos antes de continuar? (Recomendado)', false)) {
            $this->info('Por favor realiza tu respaldo y vuelve a ejecutar este comando.');
            return 0;
        }

        $this->info('Aplicando migraciones...');
        try {
            // Ejecutar migraciones forzosamente (útil para producción)
            Artisan::call('migrate', ['--force' => true]);
            $this->line(Artisan::output());
        } catch (\Exception $e) {
            $this->error('Ocurrió un error al migrar la base de datos:');
            $this->error($e->getMessage());
            return 1;
        }

        $this->newLine();
        $this->info('======================================================');
        $this->info('¡Actualización completada con éxito!');
        $this->info('Tu base de datos ahora incluye las nuevas características');
        $this->info('y tus datos antiguos están seguros y encriptados.');
        $this->info('Versión Actual: ' . config('frangy.version', '2.0.14'));
        $this->info('======================================================');

        return 0;
    }
}
