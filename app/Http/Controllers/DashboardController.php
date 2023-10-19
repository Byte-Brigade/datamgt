<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\BranchType;
use App\Models\Employee;
use App\Models\EmployeePosition;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $jumlahCabang = Branch::count();
        $jumlahATM24Jam = Branch::where('layanan_atm','24 Jam')->count();
        $jumlahKaryawan = Employee::count();
        $jumlahKaryawanBSO = Employee::where('position_id', 3)->get()->count();
        $employee_positions = EmployeePosition::get();
        $employees = Employee::with('employee_positions')->get();

        $data = [
            'jumlahCabang' => $jumlahCabang,
            'jumlahATM24Jam' => $jumlahATM24Jam,
            'jumlahKaryawan' => $jumlahKaryawan,
            'jumlahKaryawanBSO' => $jumlahKaryawanBSO,
            'employee_positions' => $employee_positions,
            'employees' => $employees
        ];

        return Inertia::render('Dashboard', [
            'data' => $data,
        ]);
    }
}
