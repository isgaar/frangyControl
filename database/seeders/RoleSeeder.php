<?php

namespace Database\Seeders;

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
        $role1 = Role::create(['name' => 'Administrador']);
        $role2 = Role::create(['name' => 'Estándar']);


        Permission::create(['name' => 'admin.datosv.vehiculosnom'])->syncRoles($role1);

        Permission::create(['name' => 'admin.users.usuarios'])->syncRoles($role1);
    }
}
