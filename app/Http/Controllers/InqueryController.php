<?php

namespace App\Http\Controllers;

use App\Http\Resources\InqueryAssetsResource;
use App\Models\Branch;
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
    public function staff_detail($slug)
    {
        $branch = Branch::with('branch_types')->where('slug', $slug)->firstOrFail();

        $positionsProps = EmployeePosition::all();
        return Inertia::render('Inquery/Staff/Detail', [
            'slug' => $slug,
            'branch' => $branch,
            'positions' => $positionsProps,
        ]);
    }

    public function branchDetail($slug)
    {
        $branch = Branch::with('employees')->where('slug', $slug)->firstOrFail();
        $positions = EmployeePosition::get();

        // Lisensi
        $ops_skoperasional = OpsSkOperasional::where('branch_id', $branch->id)->first();
        $ops_skbirtgs = OpsSkbirtgs::where('branch_id', $branch->id)->first();
        $ops_pajak_reklame = OpsPajakReklame::where('branch_id', $branch->id)->first();
        $ops_apar = OpsApar::where('branch_id', $branch->id)->first();

        $izin_disnaker = GapDisnaker::where('branch_id', $branch->id)->orderBy('tgl_masa_berlaku', 'asc')->get()->map(function ($disnaker) {
            return [
                'name' => $disnaker->jenis_perizinan->name,
                'remark' =>  'Ada',
                'jatuh_tempo' => $disnaker->tgl_masa_berlaku,
                'url' => isset($disnaker->file) ? "infra/disnaker/{$disnaker->id}/{$disnaker->file}" : false,

            ];
        });
        $lisensi = collect([
            [
                'name' => 'Izin OJK',
                'remark' => isset($branch->izin) ? 'Ada' : 'Tidak Ada',
                'jatuh_tempo' => '-',
                'url' => isset($branch->file_ojk) ? "ops/branches/{$branch->id}/{$branch->file_ojk}" : false,

            ],
            [
                'name' => 'SK BI RTGS',
                'remark' => isset($ops_skbirtgs) ? 'Ada' : 'Tidak Ada',
                'jatuh_tempo' => '-',
                'url' => isset($ops_skbirtgs->file) ? "ops/skbirtgs/{$ops_skbirtgs->id}/{$ops_skbirtgs->file}" : false,
            ],
            [
                'name' => 'Reklame',
                'remark' => isset($ops_pajak_reklame) ? 'Ada' : 'Tidak Ada',
                'jatuh_tempo' => isset($ops_pajak_reklame->periode_akhir) ? $ops_pajak_reklame->periode_akhir : '-',
                'url' => isset($ops_pajak_reklame->file_izin_reklame) ? "ops/pajak-reklame/{$ops_pajak_reklame->id}/{$ops_skbirtgs->file_izin_reklame}" : false,

            ],
            [
                'name' => 'APAR',
                'remark' => isset($ops_apar) ? 'Ada' : 'Tidak Ada',
                'jatuh_tempo' => isset($ops_apar->detail) ? $ops_apar->detail()->orderBy('expired_date', 'asc')->first()->expired_date : '-'
            ],

        ]);

        $lisensi = $lisensi->merge($izin_disnaker);



        return Inertia::render('Inquery/Branch/Detail', [
            'branch' => $branch,
            'positions' => $positions,
            'licenses' => $lisensi
        ]);
    }
    public function assets()
    {

        $gap_toners = GapToner::orderBy('idecice_date', 'asc')->with('branches')->get()->map(function ($toner) {
            $type_name = $toner->branches->branch_types->type_name;
            $toner->cabang = $toner->branches->branch_name;
            $toner->kategori = $toner->branches->branch_name == 'Kantor Pusat' ? 'HO' : ($type_name == 'KFO' ? 'KF' : (in_array($type_name, ['KFNO', 'SFI']) ?  $type_name : 'Cabang'));
            return $toner;
        });


        $months = [
            "January", "February", "March", "April", "May", "June", "July",
            "August", "September", "October", "November", "December"
        ];
        return Inertia::render('Inquery/Asset/Page', ['data' => [
            'gap_toners' => $gap_toners,
            'months' => $months
        ]]);
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
