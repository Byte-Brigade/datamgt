<?php

namespace App\Http\Controllers;

use App\Http\Resources\ScoringProjectsResource;
use App\Imports\InfraScoringProjectsImport;
use App\Models\Branch;
use App\Models\InfraScoring;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Throwable;

class InfraScoringProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $branchesProps = Branch::get();
        return Inertia::render('GA/Infra/Scoring/Project/Page', ['branches' => $branchesProps]);
    }

    protected array $sortFields = ['branches.branch_code', 'entity'];

    public function api(InfraScoring $infra_scoring_project, Request $request)
    {
        $sortFieldInput = $request->input('sort_field', 'branches.branch_code');
        $sortField = in_array($sortFieldInput, $this->sortFields) ? $sortFieldInput : 'branches.branch_code';
        $sortOrder = $request->input('sort_order', 'asc');
        $searchInput = $request->search;
        $query = $infra_scoring_project->select('infra_scorings.*')->where('type', 'Post Project')->orderBy($sortField, $sortOrder)
            ->join('branches', 'infra_scorings.branch_id', 'branches.id');

        $perpage = $request->perpage ?? 10;

        if (!is_null($request->branch_code)) {
            $query = $query->where('branch_code', $request->branch_code);
        }

        if (!is_null($searchInput)) {
            $searchQuery = "%$searchInput%";
            $query = $query->where(function ($query) use ($searchQuery) {
                $query->where('pic', 'like', $searchQuery)
                    ->orWhere('vendor', 'like', $searchQuery)
                    ->orWhereHas('branches', function ($q) use ($searchQuery) {
                        $q->where('branch_name', 'like', $searchQuery);
                    });
            });
        }
        $data = $query->paginate($perpage);
        return ScoringProjectsResource::collection($data);
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

    public function import(Request $request)
    {
        try {
            (new InfraScoringProjectsImport)->import($request->file('file'));

            return redirect(route('infra.scoring_projects'))->with(['status' => 'success', 'message' => 'Import Berhasil']);
        } catch (Throwable $e) {
            dd($e);
            return redirect(route('infra.scoring_projects'))->with(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }

    public function export()
    {
        $fileName = 'Data_GAP_Scoring_Projects' . date('d-m-y') . '.xlsx';
        return (new ProjectsExport)->download($fileName);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
