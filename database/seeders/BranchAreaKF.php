<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BranchAreaKF extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Branch::whereHas('branch_types', function($q) {
            return $q->whereIn('type_name', ['KFO','KFNO']);
        })->update(['area' => 'KF']);
    }
}
