<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Employee;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $jumlahCabang = Branch::count();
        $jumlahKaryawan = Employee::count();
        $jumlahKaryawanBSO = Employee::where('position_id', 3)->get()->count();

        $data = [
            'jumlahCabang' => $jumlahCabang,
            'jumlahKaryawan' => $jumlahKaryawan,
            'jumlahKaryawanBSO' => $jumlahKaryawanBSO,
        ];

        return Inertia::render('Dashboard', [
            'data' => $data,
        ]);
    }
}
