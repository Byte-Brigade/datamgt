<?php

namespace App\Http\Controllers;

use App\Exports\SkBirtgsExport;
use App\Helpers\PaginationHelper;
use App\Http\Resources\SkbirtgsResource;
use App\Imports\SkBirtgsImport;
use App\Models\Branch;
use App\Models\ErrorLog;
use App\Models\OpsSkbirtgs;
use Exception;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Validators\ValidationException;

class OpsSkbirtgsController extends Controller
{

    public function index()
    {
        $branchesProps = Branch::get();
        return Inertia::render('Ops/SKBIRTGS/Page', ['branches' => $branchesProps]);
    }

    public function import(Request $request)
    {
        try {
            (new SkBirtgsImport)->import($request->file('file'));

            return redirect(route('ops.skbirtgs'))->with(['status' => 'success', 'message' => 'Import Berhasil']);
        } catch (ValidationException $e) {
            $failures = $e->failures();
            $list_error = collect([]);
            foreach ($failures as $failure) {
                $failure->row(); // row that went wrong
                $failure->attribute(); // either heading key (if using heading row concern) or column index
                $failure->errors(); // Actual error messages from Laravel validator
                $failure->values(); // The values of the row that has failed.
                $error = ErrorLog::create([
                    'row' => $failure->row(),
                    'attribute' => $failure->row(),
                    'error_message' => $failure->errors(),
                    'value' => $failure->values(),
                ]);

                $list_error->push($error);
            }
            return redirect(route('ops.skbirtgs'))->with(['status' => 'failed', 'message' => 'Import Failed']);
        }
    }

    public function upload(Request $request, $id)
    {
        try {
            $ops_skbirtgs = OpsSkbirtgs::find($id);
            $fileName = $request->file('file')->getClientOriginalName();
            $request->file('file')->storeAs('ops/skbirtgs/', $fileName, ["disk" => 'public']);
            $ops_skbirtgs->file = $fileName;
            $ops_skbirtgs->save();

            return redirect(route('ops.skbirtgs'))->with(['status' => 'success', 'message' => 'File berhasil diupload!']);
        } catch (Exception $e) {
            dd($e);
            return redirect(route('ops.skbirtgs'))->with(['status' => 'failed', 'message' => 'File gagal diupload!']);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $ops_skbirtgs = OpsSkbirtgs::find($id);
            $ops_skbirtgs->update([
                'no_surat' => $request->no_surat,
                'status' => $request->status,
            ]);

            if ($request->file('file')) {
                $fileName = $request->file('file')->getClientOriginalName();
                $request->file('file')->storeAs('ops/skbirtgs/', $fileName, ["disk" => 'public']);
                $ops_skbirtgs->file = $fileName;
                $ops_skbirtgs->save();
            }

            return redirect(route('ops.skbirtgs'))->with(['status' => 'success', 'message' => 'Data berhasil diubah']);
        } catch (Exception $e) {
            return redirect(route('ops.skbirtgs'))->with(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        try {
            $ops_skbirtgs = OpsSkbirtgs::find($id);
            $ops_skbirtgs->delete();
            return redirect(route('ops.skbirtgs'))->with(['status' => 'success', 'message' => 'Data berhasil dihapus']);
        } catch (Exception $e) {
            return redirect(route('ops.skbirtgs'))->with(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }

    public function export(Request $request)
    {
        $fileName = 'Data_SK_BI_RTGS_' . date('d-m-y') . '.xlsx';

        return (new SkBirtgsExport($request->branch))->download($fileName);
    }
}
