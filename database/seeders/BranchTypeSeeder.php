<?php

namespace Database\Seeders;

use App\Models\BranchType;
use Illuminate\Database\Seeder;

class BranchTypeSeeder extends Seeder
{
    public function run()
    {
        $types = [
            ['type_name' => 'KC'],
            ['type_name' => 'KCP'],
            ['type_name' => 'KF'],
            ['type_name' => 'KFNO'],
            ['type_name' => 'SFI'],
        ];

        foreach ($types as $type) {
            BranchType::create($type);
        }
    }
}
