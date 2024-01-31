<?php

namespace Database\Seeders;

use App\Models\BranchType;
use Illuminate\Database\Seeder;

class BranchTypeSeeder extends Seeder
{
    public function run()
    {
        $types = [
            ['type_name' => 'KC', 'alt_name' => 'Kantor Cabang'] ,
            ['type_name' => 'KCP' , 'alt_name' => 'Kantor Cabang Pembantu'],
            ['type_name' => 'KF' , 'alt_name' => ''],
            ['type_name' => 'KFO' , 'alt_name' => 'Kantor Fungsional Operasional'],
            ['type_name' => 'KFNO' , 'alt_name' => 'Kantor Fungsional Non Operasional'],
            ['type_name' => 'SFI' , 'alt_name' => ''],
            ['type_name' => 'KP', 'alt_name' => 'Kantor Pusat'],
        ];

        foreach ($types as $type) {
            BranchType::create($type);
        }
    }
}
