<?php

namespace App\Http\Controllers;

use App\Http\Resources\SkbirtgsResource;
use App\Imports\SkBirtgsImport;
use App\Models\ErrorLog;
use App\Models\OpsSkbirtgs;
use Exception;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Maatwebsite\Excel\Validators\ValidationException;

class OpsSkbirtgsController extends Controller
{
    public function __construct(public OpsSkbirtgs $ops_skbirtgs)
    {
    }

    public function api(Request $request)
    {
        $sortField = 'ops_skbirtgs.id';
        $sortOrder = $request->input('sort_order', 'asc');
        $searchInput = $request->search;
        $query = $this->ops_skbirtgs->orderBy($sortField, $sortOrder)
            ->join('branches', 'ops_skbirtgs.branch_id', 'branches.id');
        $perpage = $request->perpage ?? 10;

        if (!is_null($searchInput)) {
            $searchQuery = "%$searchInput%";
            $query = $query->where('no_surat', 'like', $searchQuery)
                ->orWhere('branch_name', 'like', $searchQuery);
        }
        $employees = $query->paginate($perpage);
        return SkbirtgsResource::collection($employees);
    }

    public function index()
    {
        return Inertia::render('Ops/SKBIRTGS/Page');
    }

    public function import(Request $request)
    {
        try {
            (new SkBirtgsImport)->import($request->file('file')->store('temp'));

            return redirect(route('ops.skbirtgs'))->with(['status' => 'success', 'message' => 'Import Success']);
        } catch (ValidationException $e) {
            $failures = $e->failures();
            $list_error = collect([]);
            foreach ($failures as $failure) {
                $failure->row(); // row that went wrong
                $failure->attribute(); // either heading key (if using heading row concern) or column index
                $failure->errors(); // Actual error messages from Laravel validator
                $failure->values(); // The values of the row that has failed.
                $error = ErrorLog::create([
                    'row' => $failure->row(),
                    'attribute' => $failure->row(),
                    'error_message' => $failure->errors(),
                    'value' => $failure->values(),
                ]);

                $list_error->push($error);
            }
            return redirect(route('ops.skbirtgs'))->with(['status' => 'failed', 'message' => 'Import Failed']);
        }
    }

    public function upload(Request $request, $id)
    {
        try {
            $ops_skbirtgs = OpsSkbirtgs::find($id);

            $fileName = $request->file('file')->getClientOriginalName();
            $request->file('file')->storeAs('ops/skbirtgs/', $fileName, ["disk" => 'public']);

            $ops_skbirtgs->file = $fileName;
            $ops_skbirtgs->save();

            return redirect(route('ops.skbirtgs'))->with(['status' => 'success', 'message' => 'File berhasil diupload!']);
        } catch (Exception $e) {
            dd($e);

            return redirect(route('ops.skbirtgs'))->with(['status' => 'failed', 'message' => 'File gagal diupload!']);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $ops_skbirtgs = OpsSkbirtgs::find($id);
            $ops_skbirtgs->update([
                'no_surat' => $request->no_surat,
                'status' => $request->status,
            ]);
            return redirect(route('ops.skbirtgs'))->with(['status' => 'success', 'message' => 'Data berhasil diubah']);
        } catch (Exception $e) {
            return redirect(route('ops.skbirtgs'))->with(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        try {
            $ops_skbirtgs = OpsSkbirtgs::find($id);
            $ops_skbirtgs->delete();
            return redirect(route('ops.skbirtgs'))->with(['status' => 'success', 'message' => 'Data berhasil dihapus']);
        } catch (Exception $e) {
            return redirect(route('ops.skbirtgs'))->with(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }

    public function export(Request $request, $id)
    {
    }
}
