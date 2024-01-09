<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Branch;
use App\Models\Employee;
use App\Models\ErrorLog;
use Illuminate\Http\Request;
use App\Exports\EmployeesExport;
use App\Imports\EmployeesImport;
use App\Models\EmployeePosition;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\EmployeeResource;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Validators\ValidationException;

class EmployeeController extends Controller
{

    public function index(Request $request)
    {
        $branches = Branch::all();
        $positionsProps = EmployeePosition::all();

        return Inertia::render('Ops/Karyawan/Page', [
            'branches' => $branches,
            'positions' => $positionsProps
        ]);
    }

    public function import(Request $request)
    {
        try {
            (new EmployeesImport)->import($request->file('file'));

            Redirect::back()->with(['status' => 'success', 'message' => 'Import Berhasil']);
        } catch (ValidationException $e) {
            $errorString = '';
            /** @var array $messages */
            foreach ($e->errors() as $field => $messages) {
                foreach ($messages as $message) {
                    $errorString .= "Field {$field}: {$message} ";
                }
            }
            $errorString = trim($errorString);
            return Redirect::back()->with(['status' => 'failed', 'message' => $errorString]);
        } catch (\Throwable $th) {
            return Redirect::back()->with(['status' => 'failed', 'message' => $th->getMessage()]);
        }
    }

    public function template()
    {
        $path = 'app\public\templates\template_karyawan.xlsx';

        return response()->download(storage_path($path));
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
