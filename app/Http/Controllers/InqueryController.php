<?php

namespace App\Http\Controllers;

use App\Http\Resources\InqueryAssetsResource;
use App\Models\Branch;
use App\Models\BranchType;
use App\Models\EmployeePosition;
use App\Models\GapAsset;
use App\Models\GapDisnaker;
use App\Models\GapScoring;
use App\Models\GapToner;
use App\Models\OpsApar;
use App\Models\OpsPajakReklame;
use App\Models\OpsSkbirtgs;
use App\Models\OpsSkOperasional;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class InqueryController extends Controller
{
    protected array $sortFields = ['branch_types.type_name', 'branch_code', 'branch_name', 'address'];

    public function branch()
    {
        return Inertia::render('Inquery/Branch/Page');
    }
    public function staff()
    {
        return Inertia::render('Inquery/Staff/Page');
    }
    public function staff_detail($slug, Request $request)
    {
        $branch = Branch::with('branch_types')->where('slug', $slug)->firstOrFail();

        $positionsProps = EmployeePosition::all();
        return Inertia::render('Inquery/Staff/Detail', [
            'slug' => $slug,
            'branch' => $branch,
            'positions' => $positionsProps,
        ]);
    }
    public function alihdaya_summary()
    {
        return Inertia::render('Inquery/AlihDaya/Summary');
    }
    public function alihdayas($slug, Request $request)
    {
        return Inertia::render('Inquery/AlihDaya/Page', [
            'slug' => $slug,
        ]);
    }
    public function alihdaya_detail($slug, Request $request)
    {
        return Inertia::render('Inquery/AlihDaya/Detail', [
            'slug' => $slug,
        ]);
    }
    public function assets()
    {
        $months = [
            "January",
            "February",
            "March",
            "April",
            "May",
            "June",
            "July",
            "August",
            "September",
            "October",
            "November",
            "December"
        ];
        return Inertia::render('Inquery/Asset/Page', [
            'data' => [
                'months' => $months,
            ],
            'type_names' => BranchType::whereNotIn('type_name', ['KF', 'SFI'])->pluck('type_name')->toArray()
        ]);
    }
    public function scorings()
    {
        $gap_scorings = GapScoring::with('branches')->get();
        return Inertia::render('Inquery/Vendor/Page', ['data' => ['gap_scorings' => $gap_scorings]]);
    }
    public function licenses()
    {
        $gap_scorings = GapScoring::with('branches')->get();
        return Inertia::render('Inquery/Lisensi/Page');
    }

    public function assets_remark(Request $request)
    {
        $remarks = $request->input('remark');
        // Format the data for createMany
        DB::beginTransaction();
        try {
            foreach ($remarks as $id => $value) {
                // Assuming you have a 'gap_assets' table
                $gapAsset = GapAsset::find($id);

                if ($gapAsset) {
                    // Update the 'remark' field based on the condition
                    $gapAsset->remark = $value;
                    $gapAsset->save();
                }
            }
            DB::commit();
            return Redirect::back()->with(['status' => 'success', 'message' => 'Data Berhasil disimpan']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return Redirect::back()->with(['status' => 'success', 'message' => 'Data gagal disimpan. ' . $th->getMessage()]);
        }
    }



    public function asset_detail($slug)
    {
        $branch = Branch::with('branch_types')->where('slug', $slug)->firstOrFail();
        return Inertia::render('Inquery/Asset/Detail', [
            'branch' => $branch,
        ]);
    }
}
