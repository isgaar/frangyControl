<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $role1 = Role::firstOrCreate(['name' => 'Administrador']);
        $role2 = Role::firstOrCreate(['name' => 'Estándar']);

        Permission::firstOrCreate(['name' => 'admin.datosv.vehiculosnom'])->syncRoles($role1);
        Permission::firstOrCreate(['name' => 'admin.users.usuarios'])->syncRoles($role1);
        Permission::firstOrCreate(['name' => 'admin.orden.destroy'])->syncRoles($role1);
    }
}
