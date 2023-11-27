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


    public function disnaker($branch_code)
    {
        $disnaker = GapDisnaker::whereHas('branches', function ($query) use ($branch_code) {
            $query->where('branch_code', $branch_code);
        })->with('branches')->first();

        return Inertia::render('GA/Infra/Disnaker/Detail', [
            'disnaker' => $disnaker
        ]);
    }

}
