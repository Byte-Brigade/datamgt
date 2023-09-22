<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $superadmin = User::create([
            'name' => 'superadmin',
            'password' => Hash::make('rahasia123'),
            'email' => 'superadmin@email.com',
        ]);

        $superadmin->assignRole('superadmin');

        $admin = User::create([
            'name' => 'admin',
            'password' => Hash::make('rahasia123'),
            'email' => 'adm@email.com',
        ]);

        $admin->assignRole('admin');

        $user = User::create([
            'name' => 'user',
            'password' => Hash::make('rahasia123'),
            'email' => 'user@email.com',
        ]);

        $user->assignRole('user');
    }
}
