<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class AlihDayaPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::create(['name' => 'can alih daya']);
        $full_access = [
            'can view',
            'can edit',
            'can delete',
            'can add',
            'can export',
            'can sto',
            'can alih daya'
        ];

        $superadmin = User::where(
            'email', 'admin@email.com')->first();
        $superadmin->syncPermissions($full_access);
    }
}
