<?php

namespace App\Http\Controllers;

use App\Exports\SkOperasionalExport;
use App\Helpers\PaginationHelper;
use Exception;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\OpsSkOperasional;
use App\Imports\SkOperasionalsImport;
use App\Http\Resources\SkOperasionalResource;
use App\Models\Branch;
use Maatwebsite\Excel\Validators\ValidationException;

class OpsSkOperasionalController extends Controller
{

    public function index()
    {
        $branchesProps = Branch::get();

        return Inertia::render('Ops/SkOperasional/Page', ['branches' => $branchesProps]);
    }

    public function import(Request $request)
    {
        try {
            (new SkOperasionalsImport)->import($request->file('file'));

            return redirect(route('ops.sk-operasional'))->with(['status' => 'success', 'message' => 'Import Berhasil']);
        } catch (ValidationException $e) {
            $failures = $e->failures();
            dd($failures);
            $list_error = collect([]);
            // foreach ($failures as $failure) {
            //     $failure->row(); // row that went wrong
            //     $failure->attribute(); // either heading key (if using heading row concern) or column index
            //     $failure->errors(); // Actual error messages from Laravel validator
            //     $failure->values(); // The values of the row that has failed.
            //     $error = ErrorLog::create([
            //         'row' => $failure->row(),
            //         'attribute' => $failure->row(),
            //         'error_message' => $failure->errors(),
            //         'value' => $failure->values(),
            //     ]);

            //     $list_error->push($error);
            // }
            return redirect(route('ops.sk-operasional'))->with(['status' => 'failed', 'message' => 'Import Failed']);
        }
    }

    public function export(Request $request)
    {
        $fileName = 'Data_SK_Operasional_' . date('d-m-y') . '.xlsx';
        return (new SkOperasionalExport($request->branch))->download($fileName);
    }

    public function upload(Request $request, $id)
    {
        try {
            $ops_skoperasional = OpsSkOperasional::find($id);

            $fileName = $request->file('file')->getClientOriginalName();
            $request->file('file')->storeAs('ops/skoperasional/', $fileName, ["disk" => 'public']);

            $ops_skoperasional->file = $fileName;
            $ops_skoperasional->save();

            return redirect(route('ops.sk-operasional'))->with(['status' => 'success', 'message' => 'File berhasil diupload!']);
        } catch (Exception $e) {
            dd($e);

            return redirect(route('ops.sk-operasional'))->with(['status' => 'failed', 'message' => 'File gagal diupload!']);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $ops_skoperasional = OpsSkOperasional::find($id);
            $ops_skoperasional->update([
                'no_surat' => $request->no_surat,
                'expiry_date' => $request->expiry_date,

            ]);
            return redirect(route('ops.sk-operasional'))->with(['status' => 'success', 'message' => 'Data berhasil diubah']);
        } catch (Exception $e) {
            return redirect(route('ops.sk-operasional'))->with(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        try {
            $ops_skoperasional = OpsSkOperasional::find($id);
            $ops_skoperasional->delete();
            return redirect(route('ops.sk-operasional'))->with(['status' => 'success', 'message' => 'Data berhasil dihapus']);
        } catch (Exception $e) {
            return redirect(route('ops.sk-operasional'))->with(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }
}
