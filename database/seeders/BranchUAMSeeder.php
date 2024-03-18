<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class BranchUAMSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $branches = Branch::with('branch_types')->get();
        foreach ($branches as $branch) {
            $email = 'test.' . strtolower($branch->branch_types->type_name) . '.' . str_replace(" ", ".", strtolower($branch->branch_name));
            $default_access = ['can view'];
            User::create([
                'name' => 'Test ' . $branch->branch_types->type_name . ' ' . $branch->branch_name,
                'password' => Hash::make('Sahabat1!'),
                'email' => $email . '@banksampoerna.com',
                'nik' => '0001' . rand(1000, 8000),
                'branch_id' => $branch->id,
            ])->assignRole('cabang')->syncPermissions($default_access);
        }
    }
}
