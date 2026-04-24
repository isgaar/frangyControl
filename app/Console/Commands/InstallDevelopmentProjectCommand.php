<?php

namespace App\Console\Commands;

use App\Jobs\InstallDefaultAdminUserJob;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;

class InstallDevelopmentProjectCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'project:install-dev
                            {--force : Permite ejecutar la instalacion fuera del entorno local}
                            {--admin-name= : Nombre del administrador inicial}
                            {--admin-email= : Correo del administrador inicial}
                            {--admin-password= : Password del administrador inicial}
                            {--admin-no-prompt : Usa los valores configurados sin preguntar}';

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
        $admin = $this->resolveAdminUser();

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

        $this->components->task('Ejecutando seeders disponibles', function () {
            return $this->call('db:seed', [
                '--force' => true,
            ]) === self::SUCCESS;
        });

        $this->components->task('Creando el enlace publico de storage', function () {
            if (file_exists(public_path('storage'))) {
                return true;
            }

            return $this->call('storage:link') === self::SUCCESS;
        });

        $this->components->task('Creando o actualizando el administrador inicial', function () use ($admin) {
            Bus::dispatchSync(new InstallDefaultAdminUserJob(
                $admin['name'],
                $admin['email'],
                $admin['password'],
            ));

            return true;
        });

        $adminUser = User::query()
            ->where('email', $admin['email'])
            ->first();

        $this->newLine();
        $this->components->info('Instalacion de desarrollo completada.');
        $this->table(
            ['Dato', 'Valor'],
            [
                ['Aplicacion', config('app.name')],
                ['Version', config('app.version')],
                ['Admin', $adminUser?->name ?? $admin['name']],
                ['Correo', $admin['email']],
                ['Password', $admin['password']],
            ]
        );

        $this->line('Ya puedes iniciar el entorno con `php artisan serve` o con tus scripts de contenedor.');

        return self::SUCCESS;
    }

    protected function resolveAdminUser(): array
    {
        $admin = [
            'name' => (string) ($this->option('admin-name') ?: config('app.dev_admin.name')),
            'email' => (string) ($this->option('admin-email') ?: config('app.dev_admin.email')),
            'password' => (string) ($this->option('admin-password') ?: config('app.dev_admin.password')),
        ];

        if (!$this->input->isInteractive() || $this->option('admin-no-prompt')) {
            return $admin;
        }

        $this->newLine();
        $this->components->info('Configura el administrador inicial.');

        $admin['name'] = $this->askRequiredValue(
            'Nombre del administrador',
            $admin['name']
        );
        $admin['email'] = $this->askRequiredValue(
            'Correo del administrador',
            $admin['email']
        );

        $password = $this->secret('Password del administrador (deja vacio para conservar el configurado)');
        if (is_string($password) && $password !== '') {
            $admin['password'] = $password;
        }

        return $admin;
    }

    protected function askRequiredValue(string $label, string $default): string
    {
        do {
            $value = trim((string) $this->ask($label, $default));
        } while ($value === '');

        return $value;
    }
}
