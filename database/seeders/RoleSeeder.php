<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

// ...

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $role1 = Role::firstOrCreate(['name' => 'Administrador']);
        $role2 = Role::firstOrCreate(['name' => 'Estándar']);

        Permission::firstOrCreate(['name' => 'admin.datosv.vehiculosnom'])->syncRoles($role1);
        Permission::firstOrCreate(['name' => 'admin.users.usuarios'])->syncRoles($role1);
        Permission::firstOrCreate(['name' => 'admin.orden.destroy'])->syncRoles($role1);
    }
}
