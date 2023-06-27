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
            'name' => 'Dev Ismael',
            'email' => 'may17jun2002@outlook.com',
            'password' => bcrypt('superuser1706')
        ])->assignRole('Administrador');

        User::factory(13)->create();
    }
}
