<?php

namespace App\Http\Controllers;

use App\Exports\PajakReklameExport;
use App\Http\Resources\PajakReklameResource;
use App\Imports\PajakReklameImport;
use App\Models\Branch;
use App\Models\OpsPajakReklame;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Maatwebsite\Excel\Validators\ValidationException;

class OpsPajakReklameController extends Controller
{

    public function index(Request $request)
    {

        $branchesProps = Branch::with('branch_types')->get();
        return Inertia::render('Ops/PajakReklame/Page', ['branches' => $branchesProps]);
    }

    public function import(Request $request)
    {
        try {
            DB::beginTransaction();
            (new PajakReklameImport)->import($request->file('file'));
            DB::commit();
            return Redirect::back()->with(['status' => 'success', 'message' => 'Import Berhasil']);
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

    public function export(Request $request)
    {
        $fileName = 'Data_Pajak_Reklame_' . date('d-m-y') . '.xlsx';

        return (new PajakReklameExport($request->branch))->download($fileName);
    }

    private function store_file($file)
    {
        $fileName = $file->getClientOriginalName();
        $file->storeAs('ops/pajak-reklame/', $fileName, ["disk", "public"]);

        return $fileName;
    }

    public function store(Request $request)
    {
        try {
            $pajak_reklame = OpsPajakReklame::create([
                'branch_id' => $request->branch_id,
                'periode_awal' => $request->periode_awal,
                'periode_akhir' => $request->periode_akhir,
                'no_izin' => $request->no_izin,
                'note' => $request->note,
            ]);

            if (!is_null($request->file('file_izin_reklame'))) {
                $file_izin_reklame = $request->file('file_izin_reklame');
                $fileNameIzin = $this->store_file($file_izin_reklame);
                $pajak_reklame->file_izin_reklame = $fileNameIzin;
            }

            if (!is_null($request->file('file_skpd'))) {
                $file_skpd = $request->file('file_skpd');
                $fileNameSkpd = $this->store_file($file_skpd);
                $pajak_reklame->file_skpd = $fileNameSkpd;
            }
            $pajak_reklame->save();

            return redirect(route('ops.pajak-reklame'))->with(['status' => 'success', 'message' => 'Data berhasil diubah']);
        } catch (Exception $e) {
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
                'no_izin' => $request->no_izin,
                'note' => $request->note,
            ]);

            if (!is_null($request->file('file_izin_reklame'))) {
                $file_izin_reklame = $request->file('file_izin_reklame');
                $fileNameIzin = $this->store_file($file_izin_reklame);
                $pajak_reklame->file_izin_reklame = $fileNameIzin;
            }

            if (!is_null($request->file('file_skpd'))) {
                $file_skpd = $request->file('file_skpd');
                $fileNameSkpd = $this->store_file($file_skpd);
                $pajak_reklame->file_skpd = $fileNameSkpd;
            }
            $pajak_reklame->save();

            return redirect(route('ops.pajak-reklame'))->with(['status' => 'success', 'message' => 'Data berhasil diubah']);
        } catch (Exception $e) {
            return redirect(route('ops.pajak-reklame'))->with(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }

    public function upload(Request $request, $id)
    {
        try {
            $ops_pajak_reklame = OpsPajakReklame::find($id);
            $file = $request->file('file_izin_reklame');

            if (!is_null($request->file('file_skpd'))) {
                $file = $request->file('file_skpd');
            }

            $fileName = $file->getClientOriginalName();
            $file->storeAs('ops/pajak-reklame/', $fileName, ["disk" => 'public']);

            if (!is_null($request->file('file_skpd'))) {
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

    public function template()
    {
        $path = 'app\public\templates\template_pajak_reklame.xlsx';

        return response()->download(storage_path($path));
    }



    public function destroy($id)
    {
        $pajak_reklame = OpsPajakReklame::find($id);
        $pajak_reklame->delete();

        return redirect(route('ops.pajak-reklame'))->with(['status' => 'success', 'message' => 'Data berhasil dihapus']);
    }
}
