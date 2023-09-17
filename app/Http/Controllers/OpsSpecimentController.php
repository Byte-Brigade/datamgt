<?php

namespace App\Http\Controllers;

use App\Http\Resources\SpecimentResource;
use App\Imports\SpecimentImport;
use App\Models\OpsSpeciment;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Maatwebsite\Excel\Validators\ValidationException;

class OpsSpecimentController extends Controller
{

    public function __construct(public OpsSpeciment $ops_speciment)
    {
    }

    public function api(Request $request)
    {
        $sortField = 'id';
        $sortOrder = $request->input('sort_order', 'asc');
        $searchInput = $request->search;
        $query = $this->ops_speciment->orderBy($sortField, $sortOrder);
        $perpage = $request->perpage ?? 10;

        if (!is_null($searchInput)) {
            $searchQuery = "%$searchInput%";
            $query = $query->where('tgl_speciment', 'like', $searchQuery);
        }
        $employees = $query->paginate($perpage);
        return SpecimentResource::collection($employees);
    }

    public function index(Request $request)
    {
        return Inertia::render('Ops/Speciment/Page');
    }

    public function import(Request $request)
    {
        try {
            (new SpecimentImport)->import($request->file('file')->store('temp'));

            return redirect(route('ops.speciment'))->with(['status' => 'success', 'message' => 'Import Success']);
        } catch (ValidationException $e) {
            $failures = $e->failures();

            foreach ($failures as $failure) {
                $failure->row(); // row that went wrong
                $failure->attribute(); // either heading key (if using heading row concern) or column index
                $failure->errors(); // Actual error messages from Laravel validator
                $failure->values(); // The values of the row that has failed.
            }
            dd($failures);
            return redirect(route('ops.speciment'))->with(['status' => 'failed', 'message' => 'Import Failed']);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $speciment = OpsSpeciment::find($id);
            $speciment->update([
                'tgl_speciment' => $request->tgl_speciment,
                'hasil_konfirmasi_cabang' => $request->hasil_konfirmasi_cabang,
                'keterangan' => $request->keterangan,
            ]);

            return redirect(route('ops.speciment'))->with(['status' => 'success', 'message' => 'Data berhasil diubah']);
        } catch (\Exception $e) {
            return redirect(route('ops.speciment'))->with(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        $speciment = OpsSpeciment::find($id);
        $speciment->delete();

        return redirect(route('ops.speciment'))->with(['status' => 'success', 'message' => 'Data berhasil dihapus']);
    }
}
