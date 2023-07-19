<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        User::create([
            'name' => 'Super Usuario Dev',
            'email' => 'superusuario@outlook.com',
            'password' => bcrypt('superusuariofrangy')
        ])->assignRole('Administrador');

        User::factory(0)->create();
    }
}
