<?php

namespace App\Http\Controllers;

use App\Http\Resources\AparResource;
use App\Imports\AparImport;
use App\Models\OpsApar;
use App\Models\OpsAparDetail;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Maatwebsite\Excel\Validators\ValidationException;

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
            $query = $query->where('expired_date', 'like', $searchQuery);
        }
        $employees = $query->paginate($perpage);
        return AparResource::collection($employees);
    }

    public function index()
    {
        return Inertia::render('Ops/APAR/Page');
    }

    public function detail($id)
    {
        $ops_apar = OpsApar::with(['branches', 'detail'])->find($id);
        return Inertia::render('Ops/APAR/Detail', [
            'ops_apar' => $ops_apar
        ]);
    }

    public function import(Request $request)
    {
        try {
            (new AparImport)->import($request->file('file')->store('temp'));

            return redirect(route('ops.apar'))->with(['status' => 'success', 'message' => 'Import Success']);
        } catch (ValidationException $e) {
            $failures = $e->failures();

            foreach ($failures as $failure) {
                $failure->row(); // row that went wrong
                $failure->attribute(); // either heading key (if using heading row concern) or column index
                $failure->errors(); // Actual error messages from Laravel validator
                $failure->values(); // The values of the row that has failed.
            }
            dd($failures);
            return redirect(route('ops.apar'))->with(['status' => 'failed', 'message' => 'Import Failed']);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $apar = OpsApar::find($id);
            $apar->update([
                'expired_date' => $request->expired_date,
                'keterangan' => $request->keterangan,
            ]);

            return redirect(route('ops.apar'))->with(['status' => 'success', 'message' => 'Data berhasil diubah']);
        } catch (\Exception $e) {
            return redirect(route('ops.apar'))->with(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        $apar = OpsApar::find($id);
        $apar->delete();

        return redirect(route('ops.apar'))->with(['status' => 'success', 'message' => 'Data berhasil dihapus']);
    }
}
