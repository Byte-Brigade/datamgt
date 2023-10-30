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
        $branches = Branch::get();
        $jumlahATM24Jam = Branch::where('layanan_atm', '24 Jam')->get();
        $jumlahKaryawan = Employee::get();
        $jumlahKaryawanBSO = Employee::where('position_id', 3)->get();
        $employee_positions = EmployeePosition::get();
        $employees = Employee::with('employee_positions')->get();
        // }


        $data = [
            'branches' => $branches,
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
