<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Branch;
use App\Models\Employee;
use App\Models\GapAsset;
use App\Models\GapScoring;
use App\Models\EmployeePosition;
use App\Helpers\PaginationHelper;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        if (Auth::user()->hasRole('cabang')) {
            return $this->indexCabang();
        }

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
        $months = [
            "January",
            "February",
            "March",
            "April",
            "May",
            "June",
            "July",
            "August",
            "September",
            "October",
            "November",
            "December"
        ];

        $data = [
            'months' => $months,
            'branches' => $branches,
            'list_branches' => Branch::with('branch_types')->get()->prepend(['branch_name' => 'All', 'branch_code' => 'none']),
            'areas' => $areas,
            'jumlah_atm' => $jumlahATM->groupBy('layanan_atm'),
            'jumlahATM24Jam' => $jumlahATM24Jam,
            'jumlahKaryawan' => $jumlahKaryawan,
            'jumlahKaryawanBSO' => $jumlahKaryawanBSO,
            'employee_positions' => $employee_positions,
            'employees' => $employees,
            'summary_assets' => $gap_asset->groupBy('branch_id')->map(function ($assets, $branch_id) {
                $branch = Branch::find($branch_id);
                return [
                    "branch_id" => $branch->id,
                    "branch_code" => $branch->branch_code,
                    "branch_name" => $branch->branch_name,
                    "area" => $branch->area,
                    'depre_jumlah_item' => $assets->where('category', 'Depre')->count(),
                    'non_depre_jumlah_item' => $assets->where('category', 'Non-Depre')->count(),
                    'depre_nilai_perolehan' => $assets->where('category', 'Depre')->sum('asset_cost'),
                    'non_depre_nilai_perolehan' => $assets->where('category', 'Non-Depre')->sum('asset_cost'),
                    'penyusutan' => $assets->sum('accum_depre'),
                    'net_book_value' => $assets->sum('net_book_value'),
                    'type' => $branch->branch_name == "Kantor Pusat" ? $branch->branch_name : "Kantor Cabang",
                ];
            }),
            'gap_scorings' => $gap_scorings,
            'jumlah_cabang' => $branches->sortBy('branch_code')->groupBy('branch_types.alt_name'),
            'jumlah_cabang_alt' => $branches->groupBy('branch_types.type_name'),
        ];

        return Inertia::render('Dashboard/Page', [
            'data' => $data,
        ]);
    }

    public function indexCabang()
    {
        return Inertia::render('Dashboard/Cabang');
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
