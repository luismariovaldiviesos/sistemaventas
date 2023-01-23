<?php

namespace Database\Seeders;


use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       $user = User::create([
            'name' => 'Luis Mario Valdivieso',
            'ci' => '0104649843',
            'phone' => '09873086688',
            'email' => 'admin@mail.com',
            'profile' => 'Admin',
            'status' => 'ACTIVE',
            'password' => bcrypt('administrador')
        ]);

        $user->syncRoles('Admin');


    }
}
