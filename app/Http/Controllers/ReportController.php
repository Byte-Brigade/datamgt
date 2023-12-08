<?php

namespace App\Http\Controllers;

use App\Exports\BranchesExport;
use App\Http\Resources\DisnakerResource;
use App\Models\Branch;
use App\Models\BranchType;
use App\Models\GapDisnaker;
use App\Models\InfraBro;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReportController extends Controller
{
    public function branches()
    {
        return Inertia::render('Reporting/Branch/Page', [
            'branches' => Branch::get(),
            'branch_types' => BranchType::get(),
        ]);
    }

    public function export_branches()
    {
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

    public function bros()
    {
        // $query = InfraBro::get();
        // $collections = $query->groupBy(['category', 'branch_type'])->map(function ($bros, $category) {
        //     return $bros->map(function ($bros, $branch_type) use ($category){
        //             return [
        //                 'category' => $category,
        //                 'branch_type' => $branch_type,
        //                 'target' => $bros->count(),
        //                 'done' => $bros->where('status', 'Done')->count(),
        //                 'on_progress' => $bros->where('status', 'On Progress')->count(),
        //                 'not_start' => $bros->where('all_progress', 0)->count(),
        //                 'drop' => $bros->where('status', 'Drop')->count(),
        //             ];
        //         });

        // })->flatten(1);


        // dd($collections);
        return Inertia::render('Reporting/BRO/Page', [
            'branches' => Branch::get(),
            'branch_types' => BranchType::get(),
        ]);
    }
    public function bro_category($category)
    {
        // $query = InfraBro::get();
        // $collections = $query->groupBy(['category', 'branch_type'])->map(function ($bros, $category) {
        //     return $bros->map(function ($bros, $branch_type) use ($category){
        //             return [
        //                 'category' => $category,
        //                 'branch_type' => $branch_type,
        //                 'target' => $bros->count(),
        //                 'done' => $bros->where('status', 'Done')->count(),
        //                 'on_progress' => $bros->where('status', 'On Progress')->count(),
        //                 'not_start' => $bros->where('all_progress', 0)->count(),
        //                 'drop' => $bros->where('status', 'Drop')->count(),
        //             ];
        //         });

        // })->flatten(1);


        // dd($collections);
        $branchesProps = Branch::get();

        return Inertia::render('Reporting/BRO/Detail', [
            'branches' => $branchesProps,
            'category' => $category,
        ]);
    }
}
