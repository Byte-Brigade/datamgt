<?php

namespace App\Http\Controllers;

use App\Exports\BranchesExport;
use App\Http\Resources\BranchResource;
use App\Imports\BranchesImport;
use App\Models\Branch;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Maatwebsite\Excel\Validators\ValidationException;

class BranchController extends Controller
{
    protected array $sortFields = ['branch_code', 'branch_name', 'address'];

    public function __construct(public Branch $branch)
    {
    }
    public function api(Request $request)
    {
        $sortFieldInput = $request->input('sort_field', 'branch_name');
        $sortField = in_array($sortFieldInput, $this->sortFields) ? $sortFieldInput : 'branch_name';
        $sortOrder = $request->input('sort_order', 'asc');
        $searchInput = $request->search;
        $query = $this->branch->orderBy($sortField, $sortOrder);
        $perpage = $request->perpage ?? 10;

        if (!is_null($searchInput)) {
            $searchQuery = "%$searchInput%";
            $query = $query->where('branch_code', 'like', $searchQuery)->orWhere('branch_name', 'like', $searchQuery)->orWhere(
                'address',
                'like',
                $searchQuery
            );
        }
        $branches = $query->paginate($perpage);
        return BranchResource::collection($branches);
    }

    public function index(Request $request)
    {
        return Inertia::render('Cabang/Page');
    }

    public function import(Request $request)
    {
        try {
            (new BranchesImport)->import($request->file('file')->store('temp'));

            return redirect('branches')->with(['status' => 'success', 'message' => 'Import Success']);
        } catch (ValidationException $e) {
            $failures = $e->failures();

            foreach ($failures as $failure) {
                $failure->row(); // row that went wrong
                $failure->attribute(); // either heading key (if using heading row concern) or column index
                $failure->errors(); // Actual error messages from Laravel validator
                $failure->values(); // The values of the row that has failed.
            }
            dd($failures);
            return redirect('branches')->with(['status' => 'failed', 'message' => 'Import Failed']);
        }

    }

    public function export()
    {
        $fileName = 'Data_Cabang_' . date('d-m-y') . '.xlsx';
        return (new BranchesExport)->download($fileName);
    }

    public function testApi(Request $request)
    {
        return Inertia::render('Cabang/TestApi');
    }
}
