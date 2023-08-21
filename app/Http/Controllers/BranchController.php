<?php

namespace App\Http\Controllers;

use App\Exports\BranchesExport;
use Inertia\Inertia;
use App\Models\Branch;
use Illuminate\Http\Request;
use App\Imports\BranchesImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\StoreBranchRequest;
use App\Http\Requests\UpdateBranchRequest;

class BranchController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Cabang/Page', [
            'branches' => Branch::search($request->search)
                ->orderBy('branch_code', 'asc')
                ->paginate($request->perpage ?? 10)
                ->appends('query', null)
                ->withQueryString()
        ]);
    }

    public function importData(Request $request)
    {
        Excel::import(new BranchesImport, $request->file('file')->store('temp'));

        return redirect('branches')->with(['status' => 'success', 'message' => 'Import Success']);
    }

    public function exportData()
    {
        return Excel::download(new BranchesExport, 'data_cabang.xlsx');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreBranchRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreBranchRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Branch  $branch
     * @return \Illuminate\Http\Response
     */
    public function show(Branch $branch)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Branch  $branch
     * @return \Illuminate\Http\Response
     */
    public function edit(Branch $branch)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateBranchRequest  $request
     * @param  \App\Models\Branch  $branch
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateBranchRequest $request, Branch $branch)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Branch  $branch
     * @return \Illuminate\Http\Response
     */
    public function destroy(Branch $branch)
    {
        //
    }
}
