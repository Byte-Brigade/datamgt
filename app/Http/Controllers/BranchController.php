<?php

namespace App\Http\Controllers;

use App\Exports\BranchesExport;
use App\Imports\EmployeesImport;
use App\Models\Employee;
use Inertia\Inertia;
use App\Models\Branch;
use Illuminate\Http\Request;
use App\Imports\BranchesImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\StoreBranchRequest;
use App\Http\Requests\UpdateBranchRequest;

class BranchController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Cabang/Page', [
            'branches' => Branch::search($request->search)
                ->orderBy('branch_code', 'asc')
                ->paginate($request->perpage ?? 10)
                ->appends('query', null)
                ->withQueryString()
        ]);
    }

    public function importData(Request $request)
    {
        Excel::import(new BranchesImport, $request->file('file')->store('temp'));

        return redirect('branches')->with(['status' => 'success', 'message' => 'Import Success']);
    }

    public function exportData()
    {
        return Excel::download(new BranchesExport, 'data_cabang.xlsx');
    }

    public function employeeIndex()
    {
        return Inertia::render('Cabang/Karyawan', [
            'employees' => Employee::paginate(10)
        ]);
    }

    public function importEmployee(Request $request)
    {
        Excel::import(new EmployeesImport, $request->file('file')->store('temp'));

        return redirect('employees')->with(['status' => 'success', 'message' => 'Import Success']);
    }
}
