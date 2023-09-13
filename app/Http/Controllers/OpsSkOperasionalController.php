<?php

namespace App\Http\Controllers;

use App\Http\Resources\SkOperasionalResource;
use App\Imports\SkOperasionalsImport;
use App\Models\OpsSkOperasional;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Maatwebsite\Excel\Validators\ValidationException;

class OpsSkOperasionalController extends Controller
{
    public function __construct(public OpsSkOperasional $ops_sk_operasional)
    {
    }

    public function api(Request $request)
    {
        $sortField = 'ops_sk_operasionals.id';
        $sortOrder = $request->input('sort_order', 'asc');
        $searchInput = $request->search;
        $query = $this->ops_sk_operasional->orderBy($sortField, $sortOrder)
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
        } catch (\Exception $e) {
            dd($e);

            return redirect(route('ops.sk-operasional'))->with(['status' => 'failed', 'message' => 'File gagal diupload!']);
        }
    }
}
