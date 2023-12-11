<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BranchSlugSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $branch_names = Branch::with('branch_types')->get();

        foreach($branch_names as $branch) {
            $name = $branch->branch_name;
            $type = $branch->branch_types->type_name;
            $type = strtolower($type);
            $branches = Branch::with('branch_types')->where('branch_name', $name)->get();

            if ($branches->count() > 1 && (in_array($type, $branches->pluck('branch_types.type_name')->toArray()))) {
                $type = 'kf';
            }

            $name = strtolower($name);
            $name = explode(' ', $name);
            array_unshift($name, $type);
            $slug = join('-', $name);
            $branch->slug = $slug;
            $branch->save();
        }
    }
}
