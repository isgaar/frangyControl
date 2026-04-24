<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class InstallDefaultAdminUserJob
{
    use Dispatchable, Queueable, SerializesModels;

    public function __construct(
        protected ?string $name = null,
        protected ?string $email = null,
        protected ?string $password = null,
    ) {
    }

    /**
     * Create or update the default development admin user.
     */
    public function handle(): void
    {
        $name = $this->name ?: config('app.dev_admin.name');
        $email = $this->email ?: config('app.dev_admin.email');
        $password = $this->password ?: config('app.dev_admin.password');

        $role = Role::firstOrCreate(['name' => 'Administrador']);

        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make($password),
            ]
        );

        $user->syncRoles([$role->name]);
    }
}
