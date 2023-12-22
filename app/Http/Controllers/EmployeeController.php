<?php

namespace App\Http\Controllers;

use App\Exports\EmployeesExport;
use App\Http\Resources\EmployeeResource;
use App\Imports\EmployeesImport;
use App\Models\Branch;
use App\Models\Employee;
use App\Models\EmployeePosition;
use App\Models\ErrorLog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Maatwebsite\Excel\Validators\ValidationException;

class EmployeeController extends Controller
{


    public function index(Request $request)
    {
        $branchesProps = Branch::all();
        $positionsProps = EmployeePosition::all();

        return Inertia::render('Ops/Karyawan/Page', [
            'branches' => $branchesProps,
            'positions' => $positionsProps
        ]);
    }

    public function import(Request $request)
    {
        try {
            (new EmployeesImport)->import($request->file('file'));

            return redirect(route('ops.employees'))->with(['status' => 'success', 'message' => 'Import Berhasil']);
        } catch (ValidationException $e) {
            $failures = $e->failures();
            $list_error = collect([]);
            // dd($failures);
            foreach ($failures as $failure) {
                $failure->row(); // row that went wrong
                $failure->attribute(); // either heading key (if using heading row concern) or column index
                $failure->errors(); // Actual error messages from Laravel validator
                $failure->values(); // The values of the row that has failed
                $error = ErrorLog::create([
                    'row' => $failure->row(),
                    'attribute' => $failure->attribute(),
                    'error_message' => json_encode($failure->errors(), JSON_FORCE_OBJECT),
                    'value' => json_encode($failure->values(), JSON_FORCE_OBJECT),
                ]);

                $list_error->push($error);
            }
            return redirect(route('ops.employees'))->with(['status' => 'failed', 'message' => 'Import Gagal']);
        }
    }

    public function export(Request $request)
    {
        $fileName = 'Data_Karyawan_' . date('d-m-y') . '.xlsx';
        return (new EmployeesExport($request->branch, $request->position))->download($fileName);
    }

    public function store(Request $request)
    {
        try {
            Employee::create([
                'employee_id' => $request->employee_id,
                'branch_id' => $request->branch,
                'position_id' => $request->position,
                'name' => $request->name,
                'email' => $request->email,
                'birth_date' => $request->birth_date,
                'hiring_date' => $request->hiring_date,
            ]);
            return redirect(route('ops.employees'))->with(['status' => 'success', 'message' => 'Data berhasil ditambahkan']);
        } catch (\Exception $e) {
            return redirect(route('ops.employees'))->with(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }
    public function update(Request $request, $id)
    {
        try {
            $employee = Employee::find($id);
            $employee->update([
                'employee_id' => $request->employee_id,
                'branch_id' => $request->branch,
                'position_id' => $request->position,
                'name' => $request->name,
                'email' => $request->email,
                'birth_date' => $request->birth_date,
                'hiring_date' => $request->hiring_date,
            ]);

            return redirect(route('ops.employees'))->with(['status' => 'success', 'message' => 'Data berhasil diubah']);
        } catch (\Exception $e) {
            return redirect(route('ops.employees'))->with(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        $employee = Employee::find($id);
        $employee->delete();

        return redirect(route('ops.employees'))->with(['status' => 'success', 'message' => 'Data berhasil dihapus']);
    }
}
