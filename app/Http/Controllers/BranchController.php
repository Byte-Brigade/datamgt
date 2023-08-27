<?php

namespace App\Http\Controllers;

use App\Exports\BranchesExport;
use App\Imports\BranchesImport;
use App\Models\Branch;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

class BranchController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Cabang/Page', [
            'branches' => Branch::search(trim($request->search))
                ->orderBy('branch_code', 'asc')
                ->paginate($request->perpage ?? 10)
                ->appends('query', null)
                ->withQueryString()
        ]);
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

    public function api(Request $request)
    {
        $branches = Branch::search(trim($request->search))
            ->orderBy('branch_code', 'asc')
            ->paginate($request->perpage ?? 10)
            ->appends('query', null)
            ->withQueryString();

        return response()->json($branches);
    }

    public function testApi(Request $request)
    {
        return Inertia::render('Cabang/TestApi');
    }
}
