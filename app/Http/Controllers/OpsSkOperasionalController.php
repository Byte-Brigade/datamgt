<?php

namespace App\Http\Controllers;

use Exception;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\OpsSkOperasional;
use App\Imports\SkOperasionalsImport;
use App\Http\Resources\SkOperasionalResource;
use Maatwebsite\Excel\Validators\ValidationException;

class OpsSkOperasionalController extends Controller
{
    protected array $sortFields = ['ops_sk_operasionals.id', 'branches.branch_code', 'branches.branch_name'];

    public function __construct(public OpsSkOperasional $ops_sk_operasional)
    {
    }

    public function api(Request $request)
    {
        $sortFieldInput = $request->input('sort_field', 'ops_sk_operasionals.id');
        $sortField = in_array($sortFieldInput, $this->sortFields) ? $sortFieldInput : 'ops_sk_operasionals.id';
        $sortOrder = $request->input('sort_order', 'asc');
        $searchInput = $request->search;
        $query = $this->ops_sk_operasional->select('ops_sk_operasionals.*')->orderBy($sortField, $sortOrder)
            ->join('branches', 'ops_sk_operasionals.branch_id', 'branches.id');
        $perpage = $request->perpage ?? 10;

        if (!is_null($searchInput)) {
            $searchQuery = "%$searchInput%";
            $query = $query->where('no_surat', 'like', $searchQuery)
                ->orWhere('branch_code', 'like', $searchQuery)
                ->orWhere('branch_name', 'like', $searchQuery);
        }
        $sk_operasional = $query->paginate($perpage);
        return SkOperasionalResource::collection($sk_operasional);
    }

    public function index()
    {
        return Inertia::render('Ops/SkOperasional/Page');
    }

    public function import(Request $request)
    {
        try {
            (new SkOperasionalsImport)->import($request->file('file')->store('temp'));

            return redirect(route('ops.sk-operasional'))->with(['status' => 'success', 'message' => 'Import Success']);
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
                'note' => $request->note,
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
