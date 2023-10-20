<?php

namespace App\Http\Controllers;

use App\Exports\BranchesExport;
use App\Http\Resources\DisnakerResource;
use App\Models\Branch;
use App\Models\BranchType;
use App\Models\GapDisnaker;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReportController extends Controller
{
    public function branches(){
        return Inertia::render('Reporting/Branch/Page', [
            'branches' => Branch::get(),
            'branch_types' => BranchType::get(),
        ]);
    }

    public function export_branches() {
        $fileName = 'Data_Cabang_' . date('d-m-y') . '.xlsx';
        return (new BranchesExport(true))->download($fileName);
    }

    public function api_detail(GapDisnaker $gap_disnaker, Request $request, $id)
    {
        $sortField = 'id';
        $sortOrder = $request->input('sort_order', 'asc');
        $searchInput = $request->search;
        $query = $gap_disnaker->where('branch_id', $id)->orderBy($sortField, $sortOrder);

        $perpage = $request->perpage ?? 10;

        if (!is_null($searchInput)) {
            $searchQuery = "%$searchInput%";
            $query = $query->where('id', 'like', $searchQuery);
        }
        $data = $query->paginate($perpage);
        return DisnakerResource::collection($data);
    }

    public function disnaker($branch_code)
    {
        $disnaker = GapDisnaker::whereHas('branches', function ($query) use ($branch_code) {
            $query->where('branch_code', $branch_code);
        })->with('branches')->first();

        return Inertia::render('GA/Disnaker/Detail', [
            'disnaker' => $disnaker
        ]);
    }

}
