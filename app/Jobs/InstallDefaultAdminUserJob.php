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

    /**
     * Create or update the default development admin user.
     */
    public function handle(): void
    {
        $role = Role::firstOrCreate(['name' => 'Administrador']);

        $user = User::updateOrCreate(
            ['email' => config('app.dev_admin.email')],
            [
                'name' => config('app.dev_admin.name'),
                'password' => Hash::make(config('app.dev_admin.password')),
            ]
        );

        $user->syncRoles([$role->name]);
    }
}
