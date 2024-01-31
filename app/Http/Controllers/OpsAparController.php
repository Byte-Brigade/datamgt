<?php

namespace App\Http\Controllers;

use App\Exports\AparExport;
use App\Http\Resources\AparDetailResource;
use App\Http\Resources\AparResource;
use App\Imports\AparImport;
use App\Models\Branch;
use App\Models\OpsApar;
use App\Models\OpsAparDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Maatwebsite\Excel\Validators\ValidationException;
use Throwable;

class OpsAparController extends Controller
{


    public function index()
    {
        $branches = Branch::get();
        return Inertia::render('Ops/APAR/Page', ['branches' => $branches]);
    }

    public function detail($slug)
    {
        $ops_apar = OpsApar::whereHas('branches', function($query) use($slug) {
            $query->where('slug', $slug);
        })->with('branches')->get()->first();
        return Inertia::render('Ops/APAR/Detail', [
            'ops_apar' => $ops_apar
        ]);
    }

    public function import(Request $request)
    {
        try {
            DB::beginTransaction();
            (new AparImport)->import($request->file('file'));
            DB::commit();
            return Redirect::back()->with(['status' => 'success', 'message' => 'Import Berhasil']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return Redirect::back()->with(['status' => 'failed', 'message' => $th->getMessage()]);
        }
    }

    public function export(Request $request)
    {
        $fileName = 'Data_APAR_' . date('d-m-y') . '.xlsx';

        return (new AparExport($request->branch))->download($fileName);
    }

    public function update(Request $request, $id)
    {
        try {
            $apar = OpsApar::find($id);
            $apar->update([
                'keterangan' => $request->keterangan,
            ]);

            return redirect(route('ops.apar'))->with(['status' => 'success', 'message' => 'Data berhasil diubah']);
        } catch (\Exception $e) {
            return redirect(route('ops.apar'))->with(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }
    public function store(Request $request)
    {
        try {
            $apar = OpsApar::create([
                'branch_id' => $request->branch_id,
                'keterangan' => isset($request->apars) ? count($request->apars). 'Tabung' : 'Tidak Ada',
            ]);

            $apar->detail()->createMany($request->apars);

            return redirect(route('ops.apar'))->with(['status' => 'success', 'message' => 'Data berhasil diubah']);
        } catch (\Exception $e) {
            return redirect(route('ops.apar'))->with(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }


    public function update_detail(Request $request, $id)
    {
        try {
            $apar = OpsAparDetail::find($id);
            $apar->update([
                'titik_posisi' => $request->titik_posisi,
                'expired_date' => $request->expired_date,
            ]);

            return redirect(route('ops.apar.detail', $id))->with(['status' => 'success', 'message' => 'Data berhasil diubah']);
        } catch (\Exception $e) {
            return redirect(route('ops.apar.detail', $id))->with(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        $apar = OpsApar::find($id);
        $apar->delete();

        return redirect(route('ops.apar'))->with(['status' => 'success', 'message' => 'Data berhasil dihapus']);
    }

    public function destroy_detail($id)
    {
        $apar_detail = OpsAparDetail::find($id);
        $apar_detail->delete();

        return redirect(route('ops.apar.detail', $id))->wiith(['status' => 'success', 'message' => 'Data berhasil dihapus']);
    }

    public function template()
    {
        $path = 'app\public\templates\template_apar.xlsx';

        return response()->download(storage_path($path));
    }

}
