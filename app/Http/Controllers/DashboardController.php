<?php

namespace App\Http\Controllers;

use App\Helpers\PaginationHelper;
use App\Models\Branch;
use App\Models\Employee;
use App\Models\EmployeePosition;
use App\Models\GapAsset;
use App\Models\GapScoring;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $branches = Branch::with('branch_types')->get();
        $areas = Branch::distinct()->whereNotNull('area')->pluck('area')->prepend('All');
        $jumlahATM = Branch::whereNot('layanan_atm', 'Tidak Ada')->get();
        $jumlahATM24Jam = Branch::where('layanan_atm', '24 Jam')->get();
        $jumlahKaryawan = Employee::with('branches')->get();
        $jumlahKaryawanBSO = Employee::where('position_id', 3)->get();
        $employee_positions = EmployeePosition::get();
        $employees = Employee::with(['employee_positions', 'branches'])->get();
        $gap_asset = GapAsset::with('branches')->get();
        $gap_scorings = GapScoring::with('branches')->get();
        // dd($branches->groupBy('branch_types.type_name'));

        $data = [
            'branches' => $branches,
            'list_branches' => Branch::with('branch_types')->get()->prepend(['branch_name' => 'All', 'branch_code' => 'none']),
            'areas' => $areas,
            'jumlah_atm' => $jumlahATM->groupBy('layanan_atm'),
            'jumlahATM24Jam' => $jumlahATM24Jam,
            'jumlahKaryawan' => $jumlahKaryawan,
            'jumlahKaryawanBSO' => $jumlahKaryawanBSO,
            'employee_positions' => $employee_positions,
            'employees' => $employees,
            'summary_assets' => $gap_asset->sortBy('branches.branch_code')->map(function ($asset) {
                $asset->branch_name = str_contains($asset->branches->branch_name, 'Sampoerna') ? 'Sampoerna Strategic' : $asset->branches->branch_name;
                $asset->branch_code = $asset->branches->branch_code;
                return $asset;
            })->groupBy('branch_name')->mapWithKeys(function ($assets, $branch_name) {

            return [
                $branch_name => $assets->groupBy('category')->map(function ($assets, $index) {
                    return [
                        'name' => $index,
                        'jumlah_item' => $assets->count(),
                        'nilai_perolehan' => $assets->sum('asset_cost'),
                        'penyusutan' => $assets->sum('accum_depre'),
                        'net_book_value' => $assets->sum('net_book_value'),
                    ];
                })
            ];
        }),
            'assets' => $gap_asset,
            'gap_scorings' => $gap_scorings,
            'jumlah_cabang' => $branches->sortBy('branch_code')->groupBy('branch_types.alt_name'),
            'jumlah_cabang_alt' => $branches->groupBy('branch_types.type_name'),
        ];

        return Inertia::render('Dashboard/Page', [
            'data' => $data,
        ]);
    }

    public function api()
    {
        $collections = collect(collect([
            'kantor_pusat' => 1,
            'kantor_cabang' => 17,
            'kantor_cabang_pembantu' => 4,
            'kantor_fungsional_operasional' => 5,
            'kantor_fungsional_non_operasional' => 10,
        ]));

        return response()->json(PaginationHelper::paginate($collections, 15));
    }
}
