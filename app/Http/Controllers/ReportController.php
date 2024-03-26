<?php

namespace App\Http\Controllers;

use App\Exports\BranchesExport;
use App\Exports\Report\BRO\BROExport;
use App\Http\Resources\DisnakerResource;
use App\Models\Branch;
use App\Models\BranchType;
use App\Models\GapDisnaker;
use App\Models\InfraBro;
use Carbon\Carbon;
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
    public function assets()
    {
        return Inertia::render('Reporting/Asset/Page');
    }
    public function asset_detail($type_name)
    {
        return Inertia::render('Reporting/Asset/Detail', ['type_name' => $type_name]);
    }
    public function asset_detail_branch($slug)
    {
        $branch = Branch::with('branch_types')->where('slug', $slug)->firstOrFail();
        return Inertia::render('Reporting/Asset/Branch', ['branch' => $branch]);
    }


    public function licenses()
    {
        return Inertia::render('Reporting/Lisensi/Page');
    }
    public function vendor()
    {
        return Inertia::render('Reporting/Vendor/Page');
    }

    public function export_branches()
    {
        $fileName = 'Data_Cabang_' . date('d-m-y') . '.xlsx';
        return (new BranchesExport(true))->download($fileName);
    }


    public function disnaker($slug)
    {
        $disnaker = GapDisnaker::whereHas('branches', function ($query) use ($slug) {
            $query->where('slug', $slug);
        })->with('branches')->first();

        if (!$disnaker) {
            return back()->with(['status' => 'failed', 'message' => 'Data belum tersedia!']);
        }

        return Inertia::render('GA/Infra/Disnaker/Detail', [
            'disnaker' => $disnaker
        ]);
    }

    public function bros()
    {
        $latestBRO = InfraBro::orderBy('periode', 'desc')->first();
        if (isset($latestBRO)) {


            $distinctPeriods = InfraBro::where('periode', '!=', $latestBRO->periode)->distinct('periode')->pluck('periode');

            if ($distinctPeriods->count() > 0) {
                $previousPeriode = $distinctPeriods->first();
                $previousBRO = InfraBro::where('periode', $previousPeriode)->orderBy('periode', 'desc')->first();
            } else {
                $previousBRO = null;
            }



            return Inertia::render('Reporting/BRO/Page', [
                'periode' => [
                    "current" => isset($latestBRO) ? Carbon::parse($latestBRO->periode)->format('F Y') : false,
                    "previous" => isset($previousBRO) ? Carbon::parse($previousBRO->periode)->format('F Y') : false,
                ],
                'branches' => Branch::get(),
                'branch_types' => BranchType::get(),
            ]);
        }

        return Inertia::render('Reporting/BRO/Page', [
            'periode' => [
                "current" =>  false,
                "previous" => false,
            ],
            'branches' => Branch::get(),
            'branch_types' => BranchType::get(),
        ]);
    }
    public function bro_category(Request $request, $category)
    {
        $branchesProps = Branch::get();

        return Inertia::render('Reporting/BRO/Detail', [
            'branches' => $branchesProps,
            'category' => $category,
            'periode' => $request->periode,
        ]);
    }

    public function bro_export()
    {
        $fileName = 'Data_BRO_' . date('d-m-y') . '.xlsx';
        return (new BROExport)->download($fileName);
    }
}
