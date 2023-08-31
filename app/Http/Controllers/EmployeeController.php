<?php

namespace App\Http\Controllers;

use App\Exports\EmployeesExport;
use App\Http\Resources\EmployeeResource;
use App\Imports\EmployeesImport;
use App\Models\Branch;
use App\Models\Employee;
use App\Models\EmployeePosition;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Maatwebsite\Excel\Validators\ValidationException;

class EmployeeController extends Controller
{
    protected array $sortFields = ['employee_id', 'name', 'email'];

    public function __construct(public Employee $employee)
    {
    }

    public function api(Request $request)
    {
        $sortFieldInput = $request->input('sort_field', 'employee_id');
        $sortField = in_array($sortFieldInput, $this->sortFields) ? $sortFieldInput : 'employee_id';
        $sortOrder = $request->input('sort_order', 'asc');
        $searchInput = $request->search;
        $query = $this->employee->orderBy($sortField, $sortOrder);
        $perpage = $request->perpage ?? 10;

        if (!is_null($searchInput)) {
            $searchQuery = "%$searchInput%";
            $query = $query->where('employee_id', 'like', $searchQuery)->orWhere('name', 'like', $searchQuery)->orWhere(
                'email',
                'like',
                $searchQuery
            );
        }
        $employees = $query->paginate($perpage);
        return EmployeeResource::collection($employees);
    }

    public function index(Request $request)
    {
        $branchesProps = Branch::all();
        $positionsProps = EmployeePosition::all();

        return Inertia::render('Cabang/Karyawan/Page', [
            'branches' => $branchesProps,
            'positions' => $positionsProps
        ]);
    }

    public function import(Request $request)
    {
        try {
            (new EmployeesImport)->import($request->file('file')->store('temp'));

            return redirect(route('employees'))->with(['status' => 'success', 'message' => 'Import Success']);
        } catch (ValidationException $e) {
            $failures = $e->failures();

            foreach ($failures as $failure) {
                $failure->row(); // row that went wrong
                $failure->attribute(); // either heading key (if using heading row concern) or column index
                $failure->errors(); // Actual error messages from Laravel validator
                $failure->values(); // The values of the row that has failed.
            }
            dd($failures);
            return redirect(route('employees'))->with(['status' => 'failed', 'message' => 'Import Failed']);
        }
    }

    public function export(Request $request)
    {
        $fileName = 'Data_Karyawan_' . date('d-m-y') . '.xlsx';
        return (new EmployeesExport($request->branch, $request->position))->download($fileName);
    }
}
