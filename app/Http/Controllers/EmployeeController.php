<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Branch;
use App\Models\Employee;
use App\Models\ErrorLog;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Exports\EmployeesExport;
use App\Imports\EmployeesImport;
use App\Models\EmployeePosition;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\EmployeeResource;
use Illuminate\Support\Facades\Redirect;
use Maatwebsite\Excel\Validators\ValidationException;

class EmployeeController extends Controller
{

    public function index(Request $request)
    {
        $employees = DB::connection('sqlsrv')->table('dbo.opsstaging')->get();
        $branches = Branch::all();
        $positionsProps = EmployeePosition::all();

        return Inertia::render('Ops/Karyawan/Page', [
            'branches' => $branches,
            'positions' => $positionsProps,
            'employees' => $employees
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

    public function sync()
    {
        DB::beginTransaction();

        $employee = '';
        try {
            $employees = DB::connection('sqlsrv')->select('select * from dbo.opsstaging');
            $employees = json_decode(json_encode($employees), true);
            $position_name = '';
            foreach ($employees as $employee) {

                $employee = $employee;
                $position_name = str_replace($employee['emp_office_desc'], "", $employee['position_desc']);
                $position_name = trim($position_name);

                $position_name = preg_replace('/\s*\d+/', "", $position_name);

                $position_name = trim($position_name);

                $position = EmployeePosition::where('position_name', $position_name)->first();

                if (!isset($position)) {
                    $position = EmployeePosition::create(['position_name' => $position_name]);
                }
                $branch_type = preg_match('/\b(KF|KFO|KFNO|KC)\b/', $employee['emp_office_desc'], $match) ? $match[0] : null;
                $branch_name = trim(preg_replace('/\b(KF|KFO|KFNO|KC|Sentra Kredit|)\b/', '', $employee['emp_office_desc']));
                $branch = Branch::query()->where('branch_name', 'like', '%' . $branch_name . '%');

                if (!is_null($branch_type)) {

                    $branch = $branch->whereHas('branch_types', function ($q) use ($branch_type) {
                        $q->where('type_name', $branch_type == 'KF' ? 'KFO' : $branch_type);
                    });
                }
                $branch = $branch->get()->first();
                if (isset($branch)) {
                    $exists = Employee::where('employee_id', $employee['emp_id'])->first();
                    if (!$exists) {
                        Employee::updateOrCreate(
                            ['employee_id' => $employee['emp_id']],
                            [
                                'employee_id' => $employee['emp_id'],
                                'position_id' => $position->id,
                                'branch_id' => $branch->id,
                                'name' => $employee['fullname'],
                                'gender' => 'L',
                                'email' => $employee['email_address'] !== '' ? $employee['email_address'] : str_replace(" ", ".", $employee['fullname']) . '@banksampoerna.com',
                                'birth_date' => Carbon::createFromFormat('Ymd', $employee['birth_date'] !== '' ? $employee['birth_date'] : '19700101')->format('Y-m-d'),
                                'hiring_date' => Carbon::createFromFormat('Ymd', $employee['joint_date'])->format('Y-m-d'),
                            ]
                        );
                    }
                }
            }
            DB::commit();
            Redirect::back()->with(['status' => 'success', 'message' => 'Sync Berhasil']);
        } catch (\Throwable $th) {
            dd($employee);
            DB::rollBack();
            Redirect::back()->with(['status' => 'failed', 'message' => 'Sync Gagal: ' . $th->getMessage() . '' . json_encode($employee)]);
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
