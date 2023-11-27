<?php

namespace App\Http\Controllers;

use App\Exports\PajakReklameExport;
use App\Http\Resources\PajakReklameResource;
use App\Imports\PajakReklameImport;
use App\Models\Branch;
use App\Models\OpsPajakReklame;
use Exception;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Maatwebsite\Excel\Validators\ValidationException;

class OpsPajakReklameController extends Controller
{

    public function index(Request $request)
    {
        $branchesProps = Branch::get();
        return Inertia::render('Ops/PajakReklame/Page', ['branches' => $branchesProps]);
    }

    public function import(Request $request)
    {
        try {
            (new PajakReklameImport)->import($request->file('file'));

            return redirect(route('ops.pajak-reklame'))->with(['status' => 'success', 'message' => 'Import Berhasil']);
        } catch (ValidationException $e) {
            $failures = $e->failures();

            foreach ($failures as $failure) {
                $failure->row(); // row that went wrong
                $failure->attribute(); // either heading key (if using heading row concern) or column index
                $failure->errors(); // Actual error messages from Laravel validator
                $failure->values(); // The values of the row that has failed.
            }
            dd($failures);
            return redirect(route('ops.pajak-reklame'))->with(['status' => 'failed', 'message' => 'Import Gagal']);
        }
    }

    public function export(Request $request)
    {
        $fileName = 'Data_Pajak_Reklame_' . date('d-m-y') . '.xlsx';

        return (new PajakReklameExport($request->branch))->download($fileName);
    }

    public function store(Request $request)
    {
        try {
            $pajak_reklame = OpsPajakReklame::create([
                'branch_id' => $request->branch_id,
                'periode_awal' => $request->periode_awal,
                'periode_akhir' => $request->periode_akhir,
                'note' => $request->note,
            ]);

            return redirect(route('ops.pajak-reklame'))->with(['status' => 'success', 'message' => 'Data berhasil diubah']);
        } catch (\Exception $e) {
            return redirect(route('ops.pajak-reklame'))->with(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }
    public function update(Request $request, $id)
    {
        try {
            $pajak_reklame = OpsPajakReklame::find($id);
            $pajak_reklame->update([
                'periode_awal' => $request->periode_awal,
                'periode_akhir' => $request->periode_akhir,
                'note' => $request->note,
                'additional_info' => $request->additional_info,
            ]);

            return redirect(route('ops.pajak-reklame'))->with(['status' => 'success', 'message' => 'Data berhasil diubah']);
        } catch (\Exception $e) {
            return redirect(route('ops.pajak-reklame'))->with(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }

    public function upload(Request $request, $id)
    {
        try {
            $ops_pajak_reklame = OpsPajakReklame::find($id);
            $file = $request->file('file_izin_reklame');

            if(!is_null($request->file('file_skpd'))) {
                $file = $request->file('file_skpd');
            }

            $fileName = $file->getClientOriginalName();
            $file->storeAs('ops/pajak-reklame/', $fileName, ["disk" => 'public']);

            if(!is_null($request->file('file_skpd'))) {
                $ops_pajak_reklame->file_skpd = $fileName;
            } else {
                $ops_pajak_reklame->file_izin_reklame = $fileName;
            }

            $ops_pajak_reklame->save();

            return redirect(route('ops.pajak-reklame'))->with(['status' => 'success', 'message' => 'File berhasil diupload!']);
        } catch (Exception $e) {
            dd($e);

            return redirect(route('ops.pajak-reklame'))->with(['status' => 'failed', 'message' => 'File gagal diupload!']);
        }
    }


    public function destroy($id)
    {
        $pajak_reklame = OpsPajakReklame::find($id);
        $pajak_reklame->delete();

        return redirect(route('ops.pajak-reklame'))->with(['status' => 'success', 'message' => 'Data berhasil dihapus']);
    }
}
