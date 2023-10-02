<?php

namespace App\Http\Controllers;

use App\Exports\AparExport;
use App\Http\Resources\AparResource;
use App\Http\Resources\AparDetailResource;
use App\Imports\AparImport;
use App\Models\Branch;
use App\Models\OpsApar;
use App\Models\OpsAparDetail;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Maatwebsite\Excel\Validators\ValidationException;
use Throwable;

class OpsAparController extends Controller
{

    public function __construct(public OpsApar $ops_apar)
    {
    }

    public function api(Request $request)
    {
        $sortField = 'id';
        $sortOrder = $request->input('sort_order', 'asc');
        $searchInput = $request->search;
        $query = $this->ops_apar->orderBy($sortField, $sortOrder);

        $perpage = $request->perpage ?? 10;

        if (!is_null($searchInput)) {
            $searchQuery = "%$searchInput%";
            $query = $query->where('id', 'like', $searchQuery);
        }
        $employees = $query->paginate($perpage);
        return AparResource::collection($employees);
    }

    public function api_detail(OpsAparDetail $ops_apar_detail, Request $request, $id)
    {
        $sortField = 'id';
        $sortOrder = $request->input('sort_order', 'asc');
        $searchInput = $request->search;
        $query = $ops_apar_detail->where('ops_apar_id', $id)->orderBy($sortField, $sortOrder);

        $perpage = $request->perpage ?? 10;

        if (!is_null($searchInput)) {
            $searchQuery = "%$searchInput%";
            $query = $query->where('id', 'like', $searchQuery);
        }
        $employees = $query->paginate($perpage);
        return AparDetailResource::collection($employees);
    }



    public function index()
    {
        $branchesProps = Branch::get();
        return Inertia::render('Ops/APAR/Page', ['branches' => $branchesProps]);
    }

    public function detail($branch_code)
    {
        $ops_apar = OpsApar::whereHas('branches', function($query) use($branch_code) {
            $query->where('branch_code', $branch_code);
        })->get()->first();
        return Inertia::render('Ops/APAR/Detail', [
            'ops_apar_id' => $ops_apar->id
        ]);
    }


    public function import(Request $request)
    {
        try {
            (new AparImport)->import($request->file('file')->store('temp'));

            return redirect(route('ops.apar'))->with(['status' => 'success', 'message' => 'Import Berhasil']);
        } catch (Throwable $e) {


            return redirect(route('ops.apar'))->with(['status' => 'failed', 'message' => $e->getMessage()]);
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
}
