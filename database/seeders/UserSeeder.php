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

        $full_access = [
            'can view',
            'can edit',
            'can delete',
            'can add',
            'can export',
            'can sto'
        ];

        $superadmin = User::create([
            'name' => 'admin',
            'password' => Hash::make('rahasia123'),
            'email' => 'admin@email.com',
            'nik' => '01100100'
        ]);
        $superadmin->assignRole('superadmin');

        User::create([
            'name' => 'Siswoyo',
            'password' => Hash::make('Sahabat1!'),
            'email' => 'Siswoyo.Siswoyo@banksampoerna.com',
            'nik' => '00005488'
        ])->assignRole('procurement')->syncPermissions($full_access);
        User::create([
            'name' => 'Mahardhika',
            'password' => Hash::make('Sahabat1!'),
            'email' => 'Mahardhika@banksampoerna.com',
            'nik' => '00006083'
        ])->assignRole('procurement')->syncPermissions($full_access);
        User::create([
            'name' => 'Eep Pathurahman',
            'password' => Hash::make('Sahabat1!'),
            'email' => 'Eep.Pathurahman@banksampoerna.com',
            'nik' => '00006583'
        ])->assignRole('procurement');
        User::create([
            'name' => 'Heni Eka',
            'password' => Hash::make('Sahabat1!'),
            'email' => 'Heni.Eka@sahabat-ukm.co.id',
            'nik' => '50010777'
        ])->assignRole('procurement');

        User::create([
            'name' => 'Istava Hartini',
            'password' => Hash::make('Sahabat1!'),
            'email' => 'Istava.Hartini@banksampoerna.com',
            'nik' => '00004313'
        ])->assignRole('ga')->syncPermissions($full_access);
        User::create([
            'name' => 'Mita Lestari',
            'password' => Hash::make('Sahabat1!'),
            'email' => 'Mita.Lestari@banksampoerna.com',
            'nik' => '00005538'
        ])->assignRole('ga')->syncPermissions($full_access);
        User::create([
            'name' => 'Budi Purwanto',
            'password' => Hash::make('Sahabat1!'),
            'email' => 'Budi.Purwanto@banksampoerna.com',
            'nik' => '00002655'
        ])->assignRole('ga');
        User::create([
            'name' => 'Nova Adimurti',
            'password' => Hash::make('Sahabat1!'),
            'email' => 'Nova.Adimurti@banksampoerna.com',
            'nik' => '00005660'
        ])->assignRole('ga');
        User::create([
            'name' => 'Nadim Hasba',
            'password' => Hash::make('Sahabat1!'),
            'email' => 'Nadim.Hasba@banksampoerna.com',
            'nik' => '00006578'
        ])->assignRole('ga');
        User::create([
            'name' => 'Ferdinan Julianto',
            'password' => Hash::make('Sahabat1!'),
            'email' => 'Ferdinan.Julianto@banksampoerna.com',
            'nik' => '00006513'
        ])->assignRole('ga');
        User::create([
            'name' => 'Ahmad Harefli',
            'password' => Hash::make('Sahabat1!'),
            'email' => 'Ahmad.Harefli@sahabat-ukm.co.id',
            'nik' => '50010913'
        ])->assignRole('ga');
    }
}
