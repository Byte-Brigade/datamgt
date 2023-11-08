<?php

namespace App\Http\Controllers;

use App\Helpers\PaginationHelper;
use App\Models\Branch;
use App\Models\BranchType;
use App\Models\Employee;
use App\Models\EmployeePosition;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $branches = Branch::with('branch_types')->get();
        $areas = Branch::distinct()->pluck('area');
        $jumlahATM = Branch::whereNot('layanan_atm', 'Tidak Ada')->get();
        $jumlahATM24Jam = Branch::where('layanan_atm', '24 Jam')->get();
        $jumlahATMJamOperasional = Branch::where('layanan_atm', 'Jam Operasional')->get();
        $jumlahKaryawan = Employee::with('branches')->get();
        $jumlahKaryawanBSO = Employee::where('position_id', 3)->get();
        $employee_positions = EmployeePosition::get();
        $employees = Employee::with(['employee_positions', 'branches'])->get();
        // }

        $dataAsset = [
            'Kantor Pusat`' => [[
                'name' => 'Kategori A (Depresiasi)',
                'colspan' => 5,
                'item' => 'A',
                'nilai_perolehan' => 100,
                'penyusutan' => '20',
                'network_value' => '30',
            ], [
                'name' => 'Kategori B (Non-Depresiasi)',
                'colspan' => 3,
                'item' => 'B',
                'nilai_perolehan' => '300'
            ]],
            'Kantor Cabang' => [[
                'name' => 'Kategori A (Depresiasi)',
                'colspan' => 5,
                'item' => 'A',
                'nilai_perolehan' => 100,
                'penyusutan' => '20',
                'network_value' => '30',
            ], [
                'name' => 'Kategori B (Non-Depresiasi)',
                'colspan' => 3,
                'item' => 'B',
                'nilai_perolehan' => '300',
                'penyusutan' => '',
                'network_value' => '',
            ]],

        ];

        $data = [
            'branches' => $branches,
            'areas' => $areas,
            // 'jumlahATM' => collect(['fulltime' => count($jumlahATM24Jam), 'operational' => count($jumlahATMJamOperasional)])->toArray(),
            'jumlah_atm' => $jumlahATM->groupBy('layanan_atm'),
            'jumlahATM24Jam' => $jumlahATM24Jam,
            'jumlahKaryawan' => $jumlahKaryawan,
            'jumlahKaryawanBSO' => $jumlahKaryawanBSO,
            'employee_positions' => $employee_positions,
            'employees' => $employees,
            'assets' => $dataAsset,
            'jumlah_cabang' => $branches->groupBy('branch_types.alt_name'),
            'jumlah_cabang_alt' => $branches->groupBy('branch_types.type_name'),
        ];



        return Inertia::render('Dashboard', [
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
