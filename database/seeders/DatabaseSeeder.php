<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Database\Seeders\BranchTypeSeeder;
use Database\Seeders\EmployeePositionSeeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            BranchTypeSeeder::class,
            EmployeePositionSeeder::class
        ]);
    }
}
