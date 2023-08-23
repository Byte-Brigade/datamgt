<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EmployeePosition;

class EmployeePositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $positions = [
            ['position_name' => 'BM'],
            ['position_name' => 'BSM'],
            ['position_name' => 'BSO'],
        ];

        foreach ($positions as $position) {
            EmployeePosition::create($position);
        }
    }
}
