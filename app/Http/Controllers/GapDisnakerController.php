<?php

namespace App\Http\Controllers;

use App\Exports\DisnakersExport;
use App\Http\Resources\DisnakerResource;
use App\Imports\DisnakerImport;
use App\Models\Branch;
use App\Models\GapDisnaker;
use App\Models\JenisPerizinan;
use Exception;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Throwable;
use Illuminate\Support\Facades\Storage;

class GapDisnakerController extends Controller
{

    public function index()
    {
        $branches = Branch::get();
        $jenis_perizinan = JenisPerizinan::get();
        return Inertia::render('GA/Infra/Disnaker/Page', ['jenis_perizinan' => $jenis_perizinan, 'branches' => $branches]);
    }

    public function import(Request $request)
    {
        try {
            (new DisnakerImport)->import($request->file('file'));

            return redirect(route('infra.disnaker'))->with(['status' => 'success', 'message' => 'Import Berhasil']);
        } catch (Throwable $e) {
            dd($e);
            return redirect(route('infra.disnaker'))->with(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }

    public function template()
    {
        $path = 'app\public\templates\template_disnaker.xlsx';

        return response()->download(storage_path($path));
    }

    public function export()
    {
        $fileName = 'Data_Disnaker_' . date('d-m-y') . '.xlsx';
        return (new DisnakersExport)->download($fileName);
    }

    public function store(Request $request)
    {
        try {

            $branch = Branch::find($request->branch_id);

            $jenis_perizinan = JenisPerizinan::find($request->jenis_perizinan_id);
            $izin = isset($jenis_perizinan) ? $jenis_perizinan : JenisPerizinan::create(['name' => $request->jenis_perizinan]);
            GapDisnaker::create([
                'branch_id' => $branch->id,
                'jenis_perizinan_id' => $izin->id,
                'tgl_pengesahan' => $request->tgl_pengesahan,
                'tgl_masa_berlaku' => $request->tgl_masa_berlaku,
                'progress_resertifikasi' => $request->progress_resertifikasi,
            ]);
            return redirect(route('infra.disnaker'))->with(['status' => 'success', 'message' => 'Data Berhasil disimpan']);
        } catch (Throwable $e) {
            return redirect(route('infra.disnaker'))->with(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }

    public function update(Request $request, $id)
    {

        try {
            $disnaker = GapDisnaker::find($id);
            $branch = Branch::find($request->branch_id);
            $jenis_perizinan = JenisPerizinan::find($request->jenis_perizinan_id);
            $fileName = $request->file('file')->getClientOriginalName();
            $request->file('file')->storeAs('infra/disnaker/'.$disnaker->id.'/', $fileName, ["disk" => 'public']);
            Storage::disk('public')->delete('infra/disnaker/'.$disnaker->id.'/'.$disnaker->file);
            $disnaker->update([
                'branch_id' => $branch->id,
                'jenis_perizinan_id' => $jenis_perizinan->id,
                'tgl_pengesahan' => $request->tgl_pengesahan,
                'tgl_masa_berlaku' => $request->tgl_masa_berlaku,
                'progress_resertifikasi' => $request->progress_resertifikasi,
                'file' => $fileName,
            ]);
            return redirect(route('infra.disnaker'))->with(['status' => 'success', 'message' => 'Data Berhasil diupdate']);
        } catch (Throwable $e) {
            return redirect(route('infra.disnaker'))->with(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }

    public function upload(Request $request, $id)
    {
        try {
            $disnaker = GapDisnaker::with('branches')->find($id);


            $fileName = $request->file('file')->getClientOriginalName();
            $request->file('file')->storeAs('infra/disnaker/'.$disnaker->id.'/', $fileName, ["disk" => 'public']);

            $disnaker->file = $fileName;
            $disnaker->save();

            return redirect(route('infra.disnaker'))->with(['status' => 'success', 'message' => 'File berhasil diupload!']);
        } catch (Exception $e) {
            dd($e);

            return redirect(route('infra.disnaker'))->with(['status' => 'failed', 'message' => 'File gagal diupload!']);
        }
    }



    public function destroy($id)
    {
        $apar_detail = GapDisnaker::find($id);
        $apar_detail->delete();

        return redirect(route('infra.disnaker', $id))->with(['status' => 'success', 'message' => 'Data berhasil dihapus']);
    }


}
