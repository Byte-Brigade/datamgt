<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\GapAlihDaya;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AlihDayaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $alih_dayas = GapAlihDaya::get();
        foreach ($alih_dayas as $alih_daya) {
            $branch_name = trim(preg_replace('/\b(KF|KFO|KFNO|KC)\b/', '', $alih_daya->lokasi));
            $branch_name = $branch_name == "KPO" ? "Kantor Pusat" : $branch_name;
            $branch = Branch::where('branch_name', 'like', '%' . $branch_name . '%')->first();
            if (isset($branch)) {
                $alih_daya->update([
                    'branch_id' => $branch->id
                ]);
            }
        }
    }
}
