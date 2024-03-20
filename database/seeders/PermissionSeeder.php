<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {


        Permission::create(['name' => 'can view']);
        Permission::create(['name' => 'can edit']);
        Permission::create(['name' => 'can delete']);
        Permission::create(['name' => 'can add']);
        Permission::create(['name' => 'can export']);
        Permission::create(['name' => 'can sto']);
        Permission::create(['name' => 'can alih daya']);
    }
}
