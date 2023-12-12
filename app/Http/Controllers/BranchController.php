<?php

namespace App\Http\Controllers;

use App\Exports\BranchesExport;
use App\Http\Resources\BranchResource;
use App\Imports\BranchesImport;
use App\Models\Branch;
use App\Models\BranchType;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Maatwebsite\Excel\Validators\ValidationException;

class BranchController extends Controller
{

    private function handleColumn($column)
    {
        if (str_contains($column, '.')) {
            $arr = explode('.', $column);
            $column = array_shift($arr);
            $column = $arr[0];
        }
        return $column;
    }

    public function index(Request $request)
    {
        $areas = Branch::distinct()->whereNotNull('area')->pluck('area');

        return Inertia::render('Ops/Cabang/Page', [
            'branches' => Branch::get(),
            'branch_types' => BranchType::get(),
            'areas' => $areas
        ]);
    }

    public function import(Request $request)
    {
        try {
            (new BranchesImport)->import($request->file('file'));


            return redirect(route('ops.branches'))->with(['status' => 'success', 'message' => 'Import Berhasil']);
        } catch (ValidationException $e) {
            $failures = $e->failures();

            foreach ($failures as $failure) {
                $failure->row(); // row that went wrong
                $failure->attribute(); // either heading key (if using heading row concern) or column index
                $failure->errors(); // Actual error messages from Laravel validator
                $failure->values(); // The values of the row that has failed.
            }
            dd($failures);

            return redirect(route('ops.branches'))->with(['status' => 'failed', 'message' => 'Import Gagal']);
        }
    }

    public function export()
    {
        $fileName = 'Data_Cabang_' . date('d-m-y') . '.xlsx';
        return (new BranchesExport)->download($fileName);
    }

    public function store(Request $request)
    {
        try {

            Branch::create([
                'branch_type_id' => $request->branch_type_id,
                'branch_code' => $request->branch_code,
                'branch_name' => $request->branch_name,
                'address' => $request->address,
                'telp' => $request->telp,
                'layanan_atm' => $request->layanan_atm,
                'npwp' => $request->npwp
            ]);

            return redirect(route('ops.branches'))->with(['status' => 'success', 'message' => 'Data berhasil diubah']);
        } catch (\Exception $e) {
            return redirect(route('ops.branches'))->with(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }
    public function upload(Request $request, $id)
    {
        try {
            $branch = Branch::find($id);
            $file = $request->file('file_ojk');

            $fileName = $file->getClientOriginalName();
            $file->storeAs("ops/branches/{$branch->id}/", $fileName, ["disk" => 'public']);

            $branch->file_ojk = $fileName;
            $branch->save();

            return redirect(route('ops.branches'))->with(['status' => 'success', 'message' => 'File berhasil diupload!']);
        } catch (Exception $e) {
            dd($e);

            return redirect(route('ops.branches'))->with(['status' => 'failed', 'message' => 'File gagal diupload!']);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $layanan_atm = $request->layanan_atm != '' ? $request->layanan_atm : null;
            $branch = Branch::find($id);
            $branch->update([
                'branch_type_id' => $request->branch_type_id,
                'branch_code' => $request->branch_code,
                'branch_name' => $request->branch_name,
                'address' => $request->address,
                'telp' => $request->telp,
                'layanan_atm' => $layanan_atm,
            ]);

            return redirect(route('ops.branches'))->with(['status' => 'success', 'message' => 'Data berhasil diubah']);
        } catch (\Exception $e) {
            return redirect(route('ops.branches'))->with(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        $branch = Branch::find($id);
        $branch->delete();

        return redirect(route('ops.branches'))->with(['status' => 'success', 'message' => 'Data berhasil dihapus']);
    }
}
