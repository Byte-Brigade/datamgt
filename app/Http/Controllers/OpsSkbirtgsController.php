<?php

namespace App\Http\Controllers;

use App\Http\Resources\SkbirtgsResource;
use App\Imports\SkBirtgsImport;
use App\Models\ErrorLog;
use App\Models\OpsSkbirtgs;
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

        $sortField =  'id';
        $sortOrder = $request->input('sort_order', 'asc');
        $searchInput = $request->search;
        $query = $this->ops_skbirtgs->orderBy($sortField, $sortOrder);
        $perpage = $request->perpage ?? 10;

        if (!is_null($searchInput)) {
            $searchQuery = "%$searchInput%";
            $query = $query->where('no_surat', 'like', $searchQuery);
        }
        $employees = $query->paginate($perpage);
        return SkbirtgsResource::collection($employees);
    }

    public function index()
    {
        $skbirtgsProps = OpsSkbirtgs::with(['branches', 'penerima_kuasa'])->paginate(10);

        return Inertia::render('Ops/SKBIRTGS/Page', [
            'sks' => $skbirtgsProps,
        ]);
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
}
