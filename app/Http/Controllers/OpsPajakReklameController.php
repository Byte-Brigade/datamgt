<?php

namespace App\Http\Controllers;

use App\Imports\PajakReklameImport;
use App\Models\OpsPajakReklame;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Maatwebsite\Excel\Validators\ValidationException;

class OpsPajakReklameController extends Controller
{
    public function index(Request $request)
    {
        $reklamesProps = OpsPajakReklame::search(trim($request->search) ?? '')
            ->query(function ($query) {
                $query->select('ops_pajak_reklames.*')
                    ->join('branches', 'ops_pajak_reklames.branch_id', 'branches.id')
                    ->with(['branches'])
                    ->orderBy('ops_pajak_reklames.id');
            })
            ->paginate($request->perpage ?? 10)
            ->appends('query', null)
            ->withQueryString();

        return Inertia::render('Ops/PajakReklame/Page', [
            'reklames' => $reklamesProps
        ]);
    }

    public function import(Request $request)
    {
        try {
            (new PajakReklameImport)->import($request->file('file')->store('temp'));

            return back()->with(['status' => 'success', 'message' => 'Import Success']);
        } catch (ValidationException $e) {
            $failures = $e->failures();

            foreach ($failures as $failure) {
                $failure->row(); // row that went wrong
                $failure->attribute(); // either heading key (if using heading row concern) or column index
                $failure->errors(); // Actual error messages from Laravel validator
                $failure->values(); // The values of the row that has failed.
            }
            dd($failures);
            return back()->with(['status' => 'failed', 'message' => 'Import Failed']);
        }
    }
}
