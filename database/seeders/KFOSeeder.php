<?php

namespace Database\Seeders;

use App\Models\BranchType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KFOSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        BranchType::create(['type_name' => 'KFO']);
    }
}
