<?php

namespace App\Http\Controllers;

use App\Exports\BranchesExport;
use App\Models\Branch;
use App\Models\BranchType;
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
}
