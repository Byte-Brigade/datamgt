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
        $jumlahATM24Jam = Branch::where('layanan_atm','24 Jam')->count();
        $jumlahKaryawan = Employee::count();
        $jumlahKaryawanBSO = Employee::where('position_id', 3)->get()->count();

        $data = [
            'jumlahCabang' => $jumlahCabang,
            'jumlahATM24Jam' => $jumlahATM24Jam,
            'jumlahKaryawan' => $jumlahKaryawan,
            'jumlahKaryawanBSO' => $jumlahKaryawanBSO,
        ];

        return Inertia::render('Dashboard', [
            'data' => $data,
        ]);
    }
}
