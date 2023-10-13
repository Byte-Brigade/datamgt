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
            'name' => 'admin',
            'password' => Hash::make('rahasia123'),
            'email' => 'adm@email.com',
            'nik' => '01100100'
        ]);

        $superadmin->assignRole('superadmin');
    }
}
