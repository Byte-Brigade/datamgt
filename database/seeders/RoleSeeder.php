<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role_superadmin = Role::create(['name' => 'superadmin']);
        $role_admin = Role::create(['name' => 'admin']);
        $role_user = Role::create(['name' => 'user']);


    }
}
