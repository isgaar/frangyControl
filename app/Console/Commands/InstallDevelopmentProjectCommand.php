<?php

namespace App\Console\Commands;

use App\Jobs\InstallDefaultAdminUserJob;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;
use Spatie\Permission\PermissionRegistrar;

class InstallDevelopmentProjectCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'project:install-dev
                            {--force : Permite ejecutar la instalacion fuera del entorno local}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Instala el proyecto en desarrollo: clave, migraciones, roles, enlace publico y usuario administrador';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (!file_exists(base_path('.env'))) {
            $this->error('No existe el archivo .env. Crea uno a partir de .env.example antes de continuar.');

            return self::FAILURE;
        }

        if (app()->environment('production') && !$this->option('force')) {
            $this->error('Este comando esta pensado para desarrollo. Usa --force solo si sabes lo que haces.');

            return self::FAILURE;
        }

        $this->components->info('Preparando la instalacion de desarrollo...');

        if (!config('app.key')) {
            $this->components->task('Generando APP_KEY', function () {
                return $this->call('key:generate', ['--force' => true]) === self::SUCCESS;
            });
        } else {
            $this->line('APP_KEY ya configurada, se conserva la actual.');
        }

        $this->components->task('Limpiando caches de arranque', function () {
            return $this->call('optimize:clear') === self::SUCCESS;
        });

        $this->components->task('Ejecutando migraciones', function () {
            return $this->call('migrate', ['--force' => true]) === self::SUCCESS;
        });

        $this->components->task('Creando roles y permisos base', function () {
            app(PermissionRegistrar::class)->forgetCachedPermissions();

            return $this->call('db:seed', [
                '--class' => RoleSeeder::class,
                '--force' => true,
            ]) === self::SUCCESS;
        });

        $this->components->task('Creando el enlace publico de storage', function () {
            if (file_exists(public_path('storage'))) {
                return true;
            }

            return $this->call('storage:link') === self::SUCCESS;
        });

        $this->components->task('Creando o actualizando el administrador inicial', function () {
            Bus::dispatchSync(new InstallDefaultAdminUserJob());

            return true;
        });

        $admin = User::query()
            ->where('email', config('app.dev_admin.email'))
            ->first();

        $this->newLine();
        $this->components->info('Instalacion de desarrollo completada.');
        $this->table(
            ['Dato', 'Valor'],
            [
                ['Aplicacion', config('app.name')],
                ['Version', config('app.version')],
                ['Admin', $admin?->name ?? config('app.dev_admin.name')],
                ['Correo', config('app.dev_admin.email')],
                ['Password', config('app.dev_admin.password')],
            ]
        );

        $this->line('Ya puedes iniciar el entorno con `php artisan serve` o con tus scripts de contenedor.');

        return self::SUCCESS;
    }
}
