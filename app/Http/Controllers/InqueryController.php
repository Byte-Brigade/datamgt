<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\EmployeePosition;
use App\Models\OpsApar;
use App\Models\OpsPajakReklame;
use App\Models\OpsSkbirtgs;
use App\Models\OpsSkOperasional;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InqueryController extends Controller
{
    public function branch()
    {
        $branches = Branch::with([
            'branch_types',
            'employees' => function ($query) {
                return $query->whereHas('employee_positions', function ($q) {
                    return $q->where('position_name', 'BM');
                });
            }
        ])->paginate(15);
        return Inertia::render('Inquery/Branch/Page', [
            'branches' => $branches
        ]);
    }

    public function branchDetail($id)
    {
        $branch = Branch::with('employees')->where('branch_code', $id)->firstOrFail();
        $positions = EmployeePosition::get();

        // Lisensi
        $ops_skoperasional = OpsSkOperasional::where('branch_id', $branch->id)->first();
        $ops_skbirtgs = OpsSkbirtgs::where('branch_id', $branch->id)->first();
        $ops_pajak_reklame = OpsPajakReklame::where('branch_id', $branch->id)->first();
        $ops_apar = OpsApar::where('branch_id', $branch->id)->first();
        $lisensi = collect([
            [
                'name' => 'SK Operation',
                'remark' => isset($ops_skoperasional) ? 'Ada' : 'Tidak Ada',
                'jatuh_tempo' => isset($ops_skoperasional->expiry_date) ? $ops_skoperasional->expiry_date : '-'
            ],
            [
                'name' => 'SK BI RTGS',
                'remark' => isset($ops_skbirtgs) ? 'Ada' : 'Tidak Ada',
                'jatuh_tempo' => '-'
            ],
            [
                'name' => 'Reklame',
                'remark' => isset($ops_pajak_reklame) ? 'Ada' : 'Tidak Ada',
                'jatuh_tempo' => '-'
            ],
            [
                'name' => 'APAR',
                'remark' => isset($ops_apar) ? 'Ada' : 'Tidak Ada',
                'jatuh_tempo' => isset($ops_apar->detail) ? $ops_apar->detail()->orderBy('expired_date', 'asc')->first()->expired_date : '-'
            ],
        ]);

        return Inertia::render('Inquery/Branch/Detail', [
            'branch' => $branch,
            'positions' => $positions,
            'licenses' => $lisensi
        ]);
    }
}
