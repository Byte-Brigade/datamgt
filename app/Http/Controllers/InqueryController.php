<?php

namespace App\Http\Controllers;

use App\Http\Resources\InqueryAssetsResource;
use App\Models\Branch;
use App\Models\BranchType;
use App\Models\EmployeePosition;
use App\Models\GapAsset;
use App\Models\GapAssetDetail;
use App\Models\GapDisnaker;
use App\Models\GapHasilSto;
use App\Models\GapKdo;
use App\Models\GapScoring;
use App\Models\GapSto;
use App\Models\GapToner;
use App\Models\OpsApar;
use App\Models\OpsPajakReklame;
use App\Models\OpsSkbirtgs;
use App\Models\OpsSkOperasional;
use Carbon\Carbon;
use Exception;
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

    public function branchDetail($slug)
    {

        $branch = Branch::with([
            'employees',
            'gap_alih_dayas' => function ($query) {
                $latestPeriode = $query->max('periode');
                return $query->where('periode', $latestPeriode);
            }
        ])->where('slug', $slug)->firstOrFail();
        $positions = EmployeePosition::get();

        // Lisensi
        $ops_skoperasional = OpsSkOperasional::where('branch_id', $branch->id)->first();
        $ops_skbirtgs = OpsSkbirtgs::where('branch_id', $branch->id)->first();
        $ops_pajak_reklame = OpsPajakReklame::where('branch_id', $branch->id)->first();
        $ops_apar = OpsApar::where('branch_id', $branch->id)->first();

        $izin_disnaker = GapDisnaker::where('branch_id', $branch->id)->orderBy('tgl_masa_berlaku', 'asc')->get()->map(function ($disnaker) {
            return [
                'name' => $disnaker->jenis_perizinan->name,
                'remark' => 'Ada',
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
                'url' => isset($ops_skbirtgs->file) ? "ops/skbirtgs/{$ops_skbirtgs->file}" : false,
            ],
            [
                'name' => 'Reklame',
                'remark' => isset($ops_pajak_reklame) ? 'Ada' : 'Tidak Ada',
                'jatuh_tempo' => isset($ops_pajak_reklame->periode_akhir) ? $ops_pajak_reklame->periode_akhir : '-',
                'url' => isset($ops_pajak_reklame->file_izin_reklame) ? "ops/pajak-reklame/{$ops_skbirtgs->file_izin_reklame}" : false,

            ],
            [
                'name' => 'APAR',
                'remark' => isset($ops_apar) ? 'Ada' : 'Tidak Ada',
                'jatuh_tempo' => isset($ops_apar->detail) ? $ops_apar->detail()->orderBy('expired_date', 'asc')->first()->expired_date : '-'
            ],
        ]);

        $lisensi = $lisensi->merge($izin_disnaker);

        $kdos = GapKdo::where('branch_id', $branch->id)->get();
        $kdos = $kdos->map(function ($kdo) {
            $biaya_sewa = $kdo->biaya_sewas()->orderBy('periode', 'desc')->first();
            $kdo->biaya_sewa = isset($biaya_sewa) ? $biaya_sewa->value : 0;
            return $kdo;
        });

        return Inertia::render('Inquery/Branch/Detail', [
            'branch' => $branch,
            'positions' => $positions,
            'licenses' => $lisensi,
            'kdos' => $kdos,
        ]);
    }
    public function staff()
    {
        $positionsProps = EmployeePosition::all();
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
        $branch = Branch::where('slug', $slug)->first();
        return Inertia::render('Inquery/AlihDaya/Page', [
            'slug' => $slug,
            'branch' => $branch,
        ]);
    }
    public function alihdaya_detail($slug, Request $request)
    {
        return Inertia::render('Inquery/AlihDaya/DetailBranch', ['slug' => $slug, 'type' => $request->type, 'type_item' => $request->type_item, 'periode' => ['startDate' => $request->startDate, 'endDate' => $request->endDate]]);
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
        $yearToner = Carbon::parse(GapToner::max('idecice_date'))->year;
        return Inertia::render('Inquery/Asset/Page', [
            'data' => [
                'months' => $months,
            ],
            'yearToner' => $yearToner,
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

    public function assets_remark(Request $request, $slug)
    {
        $remarks = $request->input('remark');
        DB::beginTransaction();
        try {
            $current_sto = GapSto::where('status', 'On Progress')->first();
            if (!is_null($remarks)) {
                foreach ($remarks as $id => $value) {
                    $gapAsset = GapAsset::find($id);
                    if (!isset($current_sto)) {
                        throw new Exception("STO belum dimulai");
                    }
                    if (!isset($gapAsset)) {
                        throw new Exception("Asset tidak ditemukan");
                    }

                    if (!is_null($value)) {
                        $branch = Branch::where('slug', $slug)->first();
                        $gap_hasil_sto = GapHasilSto::where('gap_sto_id', $current_sto->id)->where('branch_id', $branch->id)->first();
                        if (!isset($gap_hasil_sto)) {
                            $gap_hasil_sto = GapHasilSto::create([
                                'branch_id' => $branch->id,
                                'gap_sto_id' => $current_sto->id,
                                'remarked' => false,
                            ]);
                        }
                        if($gapAsset->branch_id == $gap_hasil_sto->branch_id) {
                            GapAssetDetail::updateOrCreate(
                                [
                                    'asset_number' => $gapAsset->asset_number,
                                    'gap_hasil_sto_id' => $gap_hasil_sto->id,
                                ],
                                [
                                    'gap_hasil_sto_id' => $gap_hasil_sto->id,
                                    'asset_number' => $gapAsset->asset_number,
                                    'semester' => $current_sto->semester,
                                    'periode' => $current_sto->periode,
                                    'status' => $value,
                                    'sto' => false,
                                ]
                            );
                        }

                    }
                }
            } else {
                throw new Exception("Data belum diremark");
            }

            DB::commit();
            return Redirect::back()->with(['status' => 'success', 'message' => 'Data Berhasil disimpan']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return Redirect::back()->with(['status' => 'failed', 'message' => 'Data gagal disimpan. ' . $th->getMessage()]);
        }
    }

    public function asset_detail(Request $request, $slug)
    {
        $branch = Branch::with('branch_types')->where('slug', $slug)->firstOrFail();
        return Inertia::render('Inquery/Asset/Detail', [
            'branch' => $branch,
        ]);
    }
    public function toner_detail(Request $request, $slug)
    {
        $branch = Branch::with('branch_types')->where('slug', $slug)->firstOrFail();
        return Inertia::render('Inquery/Toner/Detail', [
            'branch' => $branch,
        ]);
    }

    public function sto_detail(Request $request, $slug)
    {
        $branch = Branch::with('branch_types')->where('slug', $slug)->firstOrFail();
        return Inertia::render('Inquery/Asset/STO', [
            'branch' => $branch,
        ]);
    }
}
