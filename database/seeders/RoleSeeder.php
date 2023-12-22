<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
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
        ];

        $default_access = ['can view'];
        $superadmin = Role::create(['name' => 'superadmin']);
        $branch_ops = Role::create(['name' => 'branch_ops', 'alt_name' => 'Branch Ops']);
        $procurement = Role::create(['name' => 'procurement', 'alt_name' => 'Procurement']);
        $ga = Role::create(['name' => 'ga', 'alt_name' => 'GA']);
        $cabang = Role::create(['name' => 'cabang', 'alt_name' => 'Cabang']);

        $superadmin->syncPermissions($full_access);
        $branch_ops->syncPermissions($default_access);
        $procurement->syncPermissions($default_access);
        $cabang->syncPermissions($default_access);
        $ga->syncPermissions($default_access);


    }
}
