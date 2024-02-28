<?php

namespace App\Http\Controllers\API;

use App\Helpers\PaginationHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\AlihDayaResource;
use App\Http\Resources\AssetsResource;
use App\Http\Resources\KdoMobilResource;
use App\Http\Resources\PerdinResource;
use App\Http\Resources\PksResource;
use App\Http\Resources\ScoringAssessmentsResource;
use App\Http\Resources\ScoringProjectsResource;
use App\Http\Resources\StoResource;
use App\Http\Resources\TonerResource;
use App\Models\Branch;
use App\Models\BranchType;
use App\Models\GapAlihDaya;
use App\Models\GapAsset;
use App\Models\GapKdo;
use App\Models\GapKdoMobil;
use App\Models\GapPerdin;
use App\Models\GapPks;
use App\Models\GapScoring;
use App\Models\GapSto;
use App\Models\GapToner;
use Carbon\Carbon;
use Illuminate\Http\Request;

class GapApiController extends Controller
{

    public function assets(GapAsset $gap_asset, Request $request)
    {
        $sortFieldInput = $request->input('sort_field') ?? 'branches.branch_code';
        $sortOrder = $request->input('sort_order', 'asc');
        $searchInput = $request->search;
        $query = $gap_asset->select('gap_assets.*')->orderBy($sortFieldInput, $sortOrder)
            ->join('branches', 'gap_assets.branch_id', 'branches.id');

        $perpage = $request->perpage ?? 10;

        if (!is_null($request->branch_code)) {
            $query = $query->where('branch_code', $request->branch_code);
        }
        if (!is_null($request->category)) {
            $query = $query->where('category', $request->category);
        }

        // if (isset($request->category)) {
        //     $query = $query->whereIn('category', $request->category);
        // }

        if (isset($request->major_category)) {
            $query = $query->whereIn('major_category', $request->major_category);
        }

        if (!is_null($searchInput)) {
            $searchQuery = "%$searchInput%";
            $query = $query->where(function ($query) use ($searchQuery) {
                $query->where('asset_number', 'like', $searchQuery)
                    ->orWhere('category', 'like', $searchQuery)
                    ->orWhere('asset_description', 'like', $searchQuery)
                    ->orWhere('branch_name', 'like', $searchQuery);
            });
        }

        if (!is_null($request->month) && !is_null($request->year)) {
            $paddedMonth = str_pad($request->month, 2, '0', STR_PAD_LEFT);

            // Create a Carbon instance using the year and month
            $carbonInstance = Carbon::createFromDate($request->year, $paddedMonth, 1)->format('Y-m-d');
            $query->where('periode', $carbonInstance);
        } else {
            $latestPeriode = $query->max('periode');
            $query->where('periode', $latestPeriode);
        }
        if ($perpage == "All") {
            $perpage = $query->count();
        }

        $query = $query->paginate($perpage);

        return AssetsResource::collection($query);
    }

    public function kdos(GapKdo $gap_kdo, Request $request, $type)
    {
        $sortFieldInput = $request->input('sort_field') ?? 'branches.branch_code';
        $sortOrder = $request->input('sort_order', 'asc');
        $searchInput = $request->search;
        $query = $gap_kdo->select('gap_kdos.*')->orderBy('branches.branch_code', 'asc')
            ->join('branches', 'gap_kdos.branch_id', 'branches.id')
            ->join('branch_types', 'branch_types.id', 'branches.branch_type_id');

        $perpage = $request->perpage ?? 15;

        if (!is_null($request->vendor)) {
            $query = $query->where('vendor', $request->vendor);
        }

        if (!is_null($searchInput)) {
            $searchQuery = "%$searchInput%";
            $query = $query->where(function ($query) use ($searchQuery) {
                $query->where('type_name', 'like', $searchQuery)
                    ->orWhere('branch_name', 'like', $searchQuery)
                    ->orWhere('vendor', 'like', $searchQuery);
            });
        }

        if (isset($request->type_name)) {
            $query = $query->whereIn('type_name', $request->type_name);
        }

        if (!is_null($request->month) && !is_null($request->year)) {
            $paddedMonth = str_pad($request->month, 2, '0', STR_PAD_LEFT);

            // Create a Carbon instance using the year and month
            $carbonInstance = Carbon::createFromDate($request->year, $paddedMonth, 1)->format('Y-m-d');
            $query->where('periode', $carbonInstance);
        } else {
            $latestPeriode = $query->max('periode');
            $query->where('periode', $latestPeriode);
        }
        $collections = $query->get();
        if ($type == 'cabang') {
            $collections = $collections->groupBy('branches.id')->map(function ($kdos, $branch) {
                $biaya_sewa = $kdos->flatMap(function ($mobil) {
                    return $mobil->biaya_sewas;
                })->groupBy('periode')->sortKeysDesc()->first();
                return [
                    'branches' => Branch::find($branch),
                    'type_name' => $kdos->first()->branches->branch_types->type_name,
                    'jumlah_kendaraan' => isset($biaya_sewa) ? $biaya_sewa->where('value', '>', 0)->count() : 0,
                    'sewa_perbulan' => isset($biaya_sewa) ? $biaya_sewa->sum('value')
                        : 0,
                    'akhir_sewa' => $kdos->filter(function ($kdo) {
                        $biaya_sewa = $kdo->biaya_sewas()->orderBy('periode', 'desc')->first();
                        return $biaya_sewa->value > 0;
                    })->sortBy('akhir_sewa')->first()->akhir_sewa,
                    'periode' => $kdos->first()->periode,
                    'biaya_sewas' => $biaya_sewa
                ];
            });
        } else if ($type == 'vendor') {
            $collections = $collections->groupBy('vendor')->map(function ($kdos, $vendor) {
                $biaya_sewa = $kdos->flatMap(function ($mobil) {
                    return $mobil->biaya_sewas;
                })->groupBy('periode')->sortKeysDesc()->first();
                return [
                    'vendor' => $vendor,
                    'jumlah_kendaraan' => $biaya_sewa->where('value', '>', 0)->count(),
                    'sewa_perbulan' => isset($biaya_sewa) ? $biaya_sewa->sum('value')
                        : 0,
                    'akhir_sewa' => $kdos->filter(function ($kdo) {
                        $biaya_sewa = $kdo->biaya_sewas()->orderBy('periode', 'desc')->first();
                        return $biaya_sewa->value > 0;
                    })->sortBy('akhir_sewa')->first()->akhir_sewa,
                    'periode' => $kdos->first()->periode,
                ];
            });
        }


        if ($sortOrder == 'desc') {
            $collections = $collections->sortByDesc($sortFieldInput);
        } else {
            $collections = $collections->sortBy($sortFieldInput);
        }

        if ($perpage == "All") {
            $perpage = $collections->count();
        }

        return response()->json(PaginationHelper::paginate($collections, $perpage));
    }

    public function kdo_mobil_details(GapKdo $gap_kdo_mobil, Request $request, $branch_id)
    {

        $sortFieldInput = $request->input('sort_field') ?? 'id';
        $sortOrder = $request->input('sort_order', 'asc');
        $searchInput = $request->search;
        $query = $gap_kdo_mobil->where('branch_id', $branch_id)->orderBy($sortFieldInput, $sortOrder);

        $perpage = $request->perpage ?? 15;

        if (!is_null($request->periode)) {

            $query = $query->where('periode', $request->periode);
        } else {
            $latestPeriode = $query->max('periode');
            $query = $query->where('periode', $latestPeriode);
        }

        if (!is_null($request->vendor)) {
            $query = $query->where('vendor', $request->vendor);
        }

        if (!is_null($searchInput)) {
            $searchQuery = "%$searchInput%";
            $query = $query->where(function ($query) use ($searchQuery) {
                $query->where('vendor', 'like', $searchQuery)
                    ->orWhere('nopol', 'like', $searchQuery);
            });
        }


        if ($perpage == "All") {
            $perpage = $query->count();
        }

        $query = $query->paginate($perpage);

        return KdoMobilResource::collection($query);
    }


    public function scoring_projects(GapScoring $gap_scoring_project, Request $request)
    {
        $sortFieldInput = $request->input('sort_field') ?? 'branches.branch_code';
        $sortOrder = $request->input('sort_order', 'asc');
        $searchInput = $request->search;
        $query = $gap_scoring_project->select('gap_scorings.*')->where('type', 'Project')->orderBy($sortFieldInput, $sortOrder)
            ->join('branches', 'gap_scorings.branch_id', 'branches.id');

        $perpage = $request->perpage ?? 15;

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
        $data = $query->get();

        $collections = $data->groupBy('scoring_vendor')->map(function ($scorings, $grade) {
            return [
                'scoring_vendor' => $grade,
                'jumlah_vendor' => $scorings->count(),
                'q1' => $scorings->where('schedule_scoring', 'Q1')->count(),
                'q2' => $scorings->where('schedule_scoring', 'Q2')->count(),
                'q3' => $scorings->where('schedule_scoring', 'Q3')->count(),
                'q4' => $scorings->where('schedule_scoring', 'Q4')->count(),
                'nilai_project' => $scorings->sum('nilai_project')
            ];
        })->sortBy('scoring_vendor');

        if ($perpage == "All") {
            $perpage = $collections->count();
        }



        return response()->json(PaginationHelper::paginate($collections, $perpage));
    }

    public function scoring_project_details(GapScoring $gap_scoring_project, Request $request, $scoring_vendor)
    {
        $sortFieldInput = $request->input('sort_field') ?? 'branches.branch_code';
        $sortOrder = $request->input('sort_order', 'asc');
        $searchInput = $request->search;
        $query = $gap_scoring_project->select('gap_scorings.*')->where('type', 'Project')->where('scoring_vendor', $scoring_vendor)->orderBy($sortFieldInput, $sortOrder)
            ->join('branches', 'gap_scorings.branch_id', 'branches.id');

        $perpage = $request->perpage ?? 15;

        if (!is_null($request->branch_code)) {
            $query = $query->where('branch_code', $request->branch_code);
        }

        if (!is_null($searchInput)) {
            $searchQuery = "%$searchInput%";
            $query = $query->where(function ($query) use ($searchQuery) {
                $query->where('pic', 'like', $searchQuery)
                    ->orWhere('vendor', 'like', $searchQuery)
                    ->orWhere('branch_name', 'like', $searchQuery)
                    ->orWhere('description', 'like', $searchQuery)
                    ->orWhere('vendor', 'like', $searchQuery);
            });
        }

        if ($perpage == "All") {
            $perpage = $query->count();
        }

        $query = $query->paginate($perpage);

        return ScoringProjectsResource::collection($query);
    }


    public function scoring_assessments(GapScoring $gap_scoring_assessment, Request $request)
    {
        $sortFieldInput = $request->input('sort_field') ?? 'branches.branch_code';
        $sortOrder = $request->input('sort_order', 'asc');
        $searchInput = $request->search;
        $query = $gap_scoring_assessment->select('gap_scorings.*')->where('type', 'Assessment')->orderBy($sortFieldInput, $sortOrder)
            ->join('branches', 'gap_scorings.branch_id', 'branches.id');

        $perpage = $request->perpage ?? 15;

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
        $data = $query->get();

        $collections = $data->groupBy('scoring_vendor')->map(function ($scorings, $grade) {
            return [
                'scoring_vendor' => $grade == "" ? 'Tidak Ada' : $grade,
                'jumlah_vendor' => $scorings->count(),
                'q1' => $scorings->where('schedule_scoring', 'Q1')->count(),
                'q2' => $scorings->where('schedule_scoring', 'Q2')->count(),
                'q3' => $scorings->where('schedule_scoring', 'Q3')->count(),
                'q4' => $scorings->where('schedule_scoring', 'Q4')->count(),
            ];
        })->sortBy('scoring_vendor');


        if ($perpage == "All") {
            $perpage = $collections->count();
        }

        return response()->json(PaginationHelper::paginate($collections, $perpage));
    }

    public function scoring_assessment_details(GapScoring $gap_scoring_assessment, Request $request, $scoring_vendor)
    {
        $sortFieldInput = $request->input('sort_field') ?? 'branches.branch_code';
        $sortOrder = $request->input('sort_order', 'asc');
        $searchInput = $request->search;
        $query = $gap_scoring_assessment->select('gap_scorings.*')->where('type', 'Assessment')->orderBy($sortFieldInput, $sortOrder)
            ->join('branches', 'gap_scorings.branch_id', 'branches.id');

        $perpage = $request->perpage ?? 15;

        if (!is_null($request->branch_code)) {
            $query = $query->where('branch_code', $request->branch_code);
        }

        if (!is_null($searchInput)) {
            $searchQuery = "%$searchInput%";
            $query = $query->where(function ($query) use ($searchQuery) {
                $query->where('pic', 'like', $searchQuery)
                    ->orWhere('vendor', 'like', $searchQuery)
                    ->orWhere('branch_name', 'like', $searchQuery)
                    ->orWhere('description', 'like', $searchQuery)
                    ->orWhere('vendor', 'like', $searchQuery);
            });
        }

        if ($perpage == "All") {
            $perpage = $query->count();
        }

        $query = $query->paginate($perpage);

        return ScoringAssessmentsResource::collection($query);
    }

    public function perdins(GapPerdin $gap_perdin, Request $request)
    {
        $sortFieldInput = $request->input('sort_field') ?? 'divisi_pembebanan';
        $sortOrder = $request->input('sort_order', 'asc');
        $searchInput = $request->search;
        $query = $gap_perdin->select('gap_perdins.*')->orderBy($sortFieldInput, $sortOrder);

        $perpage = $request->perpage ?? 15;

        if (!is_null($request->branch_code)) {
            $query = $query->where('branch_code', $request->branch_code);
        }


        if (!is_null($searchInput)) {
            $searchQuery = "%$searchInput%";
            $query = $query->where(function ($query) use ($searchQuery) {
                $query->where('divisi_pembebanan', 'like', $searchQuery)
                    ->orWhere('user', 'like', $searchQuery);
            });
        }

        if (!is_null($request->month) && !is_null($request->year)) {
            $paddedMonth = str_pad($request->month, 2, '0', STR_PAD_LEFT);

            // Create a Carbon instance using the year and month
            $carbonInstance = Carbon::createFromDate($request->year, $paddedMonth, 1)->format('Y-m-d');
            $query->where('periode', $carbonInstance);
        } else {
            $latestPeriode = $query->max('periode');
            $query->where('periode', $latestPeriode);
        }




        $query = $query->get();
        $collections = collect([]);
        if (!is_null($request->summary) && $request->summary == "divisi") {
            $collections = $query->groupBy('divisi_pembebanan')->map(function ($perdins, $divisi) {
                $spender = $perdins->flatMap(function ($spender) {
                    return $spender->gap_perdin_details;
                });
                return [
                    'divisi_pembebanan' => $divisi,
                    'airline' => $spender->where('category', 'Airline')->sum('value'),
                    'ka' => $spender->where('category', 'KA')->sum('value'),
                    'hotel' => $spender->where('category', 'Hotel')->sum('value'),
                    'total' => $spender->sum('value')
                ];
            })->sortByDesc(function ($item) {
                return $item['total'];
            });
        } else if (!is_null($request->summary) && $request->summary == "spender") {
            $collections = $query->groupBy('user')->map(function ($perdins, $user) {
                $spender = $perdins->flatMap(function ($spender) {
                    return $spender->gap_perdin_details;
                });
                return [
                    'user' => $user,
                    'airline' => $spender->where('category', 'Airline')->sum('value'),
                    'ka' => $spender->where('category', 'KA')->sum('value'),
                    'hotel' => $spender->where('category', 'Hotel')->sum('value'),
                    'total' => $spender->sum('value')
                ];
            })->sortByDesc(function ($item) {
                return $item['total'];
            });
        }

        if ($perpage == "All") {
            $perpage = $collections->count();
        }
        return response()->json(PaginationHelper::paginate($collections, $perpage));
    }

    public function perdin_details(GapPerdin $gap_perdin, Request $request, $divisi_pembebanan)
    {
        $sortFieldInput = $request->input('sort_field') ?? 'periode';
        $sortOrder = $request->input('sort_order', 'asc');
        $searchInput = $request->search;
        $query = $gap_perdin->select('gap_perdins.*')->where('divisi_pembebanan', $divisi_pembebanan)->orderBy($sortFieldInput, $sortOrder);

        $perpage = $request->perpage ?? 15;

        if (!is_null($request->branch_code)) {
            $query = $query->where('branch_code', $request->branch_code);
        }



        if (!is_null($searchInput)) {
            $searchQuery = "%$searchInput%";
            $query = $query->where(function ($query) use ($searchQuery) {
                $query->where('divisi_pembebanan', 'like', $searchQuery)
                    ->orWhere('category', 'like', $searchQuery);
            });
        }

        $data = $query->get();
        $collections = $data->flatMap(function ($perdin) {
            return $perdin->gap_perdin_details;
        })->groupBy('periode')->map(function ($spenders, $periode) {
            return [
                'periode' => Carbon::parse($periode)->format('F Y'),
                'airline' => $spenders->where('category', 'Airline')->sum('value'),
                'ka' => $spenders->where('category', 'KA')->sum('value'),
                'hotel' => $spenders->where('category', 'Hotel')->sum('value'),
                'total' => $spenders->sum('value'),
            ];
        });

        if ($perpage == "All") {
            $perpage = $collections->count();
        }

        return PaginationHelper::paginate($collections, $perpage);
    }

    public function alihdayas(GapAlihDaya $gap_alih_daya, Request $request)
    {
        $sortFieldInput = $request->input('sort_field') ?? 'jenis_pekerjaan';
        $sortOrder = $request->input('sort_order', 'asc');
        $searchInput = $request->search;
        $query = $gap_alih_daya->select('gap_alih_dayas.*')->orderBy($sortFieldInput, $sortOrder);

        $perpage = $request->perpage ?? 15;

        if (!is_null($request->branch_code)) {
            $query = $query->where('branch_code', $request->branch_code);
        }

        if (!is_null($searchInput)) {
            $searchQuery = "%$searchInput%";
            $query = $query->where(function ($query) use ($searchQuery) {
                $query->where('jenis_pekerjaan', 'like', $searchQuery);
            });
        }
        $yearToDate = false;

        if (!is_null($request->startDate) && !is_null($request->endDate)) {
            $startDate = Carbon::parse($request->startDate);
            $endDate = Carbon::parse($request->endDate);
            if ($startDate->isSameMonth($endDate)) {
                $sameMonth = true;
                $query->where('periode', $endDate->startOfMonth()->format('Y-m-d'));
            } else {
                $yearToDate = true;
                $query->whereBetween('periode', [$startDate->startOfMonth()->format('Y-m-d'), $endDate->startOfMonth()->format('Y-m-d')]);
            }
        } else {
            $yearToDate = true;
            $minPeriode = $query->min('periode');
            $maxPeriode = $query->max('periode');
            $query->whereBetween('periode', [$minPeriode, $maxPeriode]);
        }
        // } else {
        //     $latestPeriode = $query->max('periode');
        //     $query->where('periode', $latestPeriode);
        // }


        if ($yearToDate && $request->type == "tenaga-kerja") {

            $query = $query->select([
                'jenis_pekerjaan',
                'nama_pegawai',
                'user',
                'lokasi',
                'vendor',
            ])->distinct();
        }
        $query = $query->get();

        $collections = $query->groupBy('jenis_pekerjaan')->map(function ($alihdayas, $jenis_pekerjaan) {
            return [
                'jenis_pekerjaan' => $jenis_pekerjaan,
                'vendor' => $alihdayas,
                'total_pegawai' => $alihdayas->count(),
                'total_biaya' => $alihdayas->sum('cost'),
                'alihdaya' => $alihdayas,
            ];
        });

        if ($perpage == "All") {
            $perpage = $collections->count();
        }

        return response()->json(PaginationHelper::paginate($collections, $perpage));
    }
    public function alihdaya_details(GapAlihDaya $gap_alih_daya, Request $request, $type)
    {
        $sortFieldInput = $request->input('sort_field') ?? 'jenis_pekerjaan';
        $sortOrder = $request->input('sort_order', 'asc');
        $searchInput = $request->search;
        $query = $gap_alih_daya->select('gap_alih_dayas.*')->orderBy($sortFieldInput, $sortOrder);
        $perpage = $request->perpage ?? 15;

        if ($type == 'jenis_pekerjaan') {
            $query = $query->where('jenis_pekerjaan', $request->type_item);
        } else if ($type == 'vendor') {
            $query = $query->where('vendor', $request->type_item);
        }

        if (!is_null($searchInput)) {
            $searchQuery = "%$searchInput%";
            $query = $query->where(function ($query) use ($searchQuery) {
                return $query->where('nama_pegawai', 'like', $searchQuery)
                    ->orWhere('user', 'like', $searchQuery);
            });
        }

        if (!is_null($request->startDate) && !is_null($request->endDate)) {
            $startDate = Carbon::parse($request->startDate);
            $endDate = Carbon::parse($request->endDate);
            if ($startDate->isSameMonth($endDate)) {
                $query->where('periode', $endDate->startOfMonth()->format('Y-m-d'));
            } else {
                $query->whereBetween('periode', [$startDate->startOfMonth()->format('Y-m-d'), $endDate->startOfMonth()->format('Y-m-d')]);
            }
        } else {
            $latestPeriode = $query->max('periode');
            $query->where('periode', $latestPeriode);
        }



        if ($perpage == "All") {
            $perpage = $query->count();
        }

        $query = $query->paginate($perpage);

        return AlihDayaResource::collection($query);
    }
    public function toners(GapToner $gap_toner, Request $request)
    {
        $sortFieldInput = $request->input('sort_field') ?? 'branch_id';
        $sortOrder = $request->input('sort_order', 'asc');
        $searchInput = $request->search;
        $query = $gap_toner->select('gap_toners.*')->orderBy($sortFieldInput, $sortOrder)
            ->join('branches', 'gap_toners.branch_id', 'branches.id')
            ->join('branch_types', 'branches.branch_type_id', 'branch_types.id');;
        $perpage = $request->perpage ?? 15;

        if (!is_null($searchInput)) {
            $searchQuery = "%$searchInput%";
            $query = $query->where(function ($query) use ($searchQuery) {
                $query->whereHas('branches', function ($q) use ($searchQuery) {
                    $q->where('branch_name', 'like', $searchQuery);
                });
            });
        }
        if (!is_null($request->month) && !is_null($request->year)) {
            $paddedMonth = str_pad($request->month, 2, '0', STR_PAD_LEFT);

            // Create a Carbon instance using the year and month
            $carbonInstance = Carbon::createFromDate($request->year, $paddedMonth, 1)->format('Y-m-d');
            $query->where('periode', $carbonInstance);
        } else {
            $latestPeriode = $query->max('periode');
            $query->where('periode', $latestPeriode);
        }

        if (isset($request->type_name)) {
            $query = $query->whereIn('type_name', $request->type_name);
        }



        $data = $query->get();

        $collections = $data->groupBy('branch_id')->map(function ($toners, $id) {
            $branch = Branch::find($id);
            return [
                'branch_id' => $id,
                'branch_name' => $branch->branch_name,
                'branch_code' => $branch->branch_code,
                'type_name' => $branch->branch_types->type_name,
                'slug' => $branch->slug,
                'quantity' => $toners->sum('quantity'),
                'total' => $toners->sum('total'),
            ];
        });

        if ($perpage == "All") {
            $perpage = $collections->count();
        }

        return PaginationHelper::paginate($collections, $perpage);
    }

    public function toner_details(GapToner $gap_toner, Request $request, $slug)
    {
        $sortFieldInput = $request->input('sort_field') ?? 'branch_id';
        $sortOrder = $request->input('sort_order', 'asc');
        $searchInput = $request->search;
        $branch = Branch::where('slug', $slug)->first();
        $query = $gap_toner->select('gap_toners.*')->where('branch_id', $branch->id)->orderBy($sortFieldInput, $sortOrder);
        $perpage = $request->perpage ?? 15;


        if (!is_null($searchInput)) {
            $searchQuery = "%$searchInput%";
            $query = $query->where(function ($q) use ($searchQuery) {
                return $q->where('cartridge_order', 'like', $searchQuery)
                    ->orWhere('invoice', 'like', $searchQuery);
            });
        }
        if (!is_null($request->startDate)) {
            $query = $query->whereBetween('idecice_date', [Carbon::parse($request->startDate)->startOfMonth(), Carbon::parse($request->endDate)->startOfMonth()]);
        }


        if (!is_null($request->month) && !is_null($request->year)) {
            $paddedMonth = str_pad($request->month, 2, '0', STR_PAD_LEFT);

            // Create a Carbon instance using the year and month
            $carbonInstance = Carbon::createFromDate($request->year, $paddedMonth, 1)->format('Y-m-d');
            $query->where('periode', $carbonInstance);
        } else {
            $latestPeriode = $query->max('periode');
            $query->where('periode', $latestPeriode);
        }


        if ($perpage == "All") {
            $perpage = $query->count();
        }

        $query = $query->paginate($perpage);

        return TonerResource::collection($query);
    }
    public function pks(GapPks $gap_pks, Request $request)
    {
        $sortFieldInput = $request->input('sort_field') ?? 'vendor';
        $sortOrder = $request->input('sort_order', 'asc');
        $searchInput = $request->search;
        $query = $gap_pks->select('gap_pks.*')->orderBy($sortFieldInput, $sortOrder);
        $perpage = $request->perpage ?? 15;
        $query = $query->where('status', '!=', 'TIDAK AKTIF');

        if (!is_null($request->month) && !is_null($request->year)) {
            $paddedMonth = str_pad($request->month, 2, '0', STR_PAD_LEFT);

            // Create a Carbon instance using the year and month
            $carbonInstance = Carbon::createFromDate($request->year, $paddedMonth, 1)->format('Y-m-d');
            $query->where('periode', $carbonInstance);
        } else {
            $latestPeriode = $query->max('periode');
            $query->where('periode', $latestPeriode);
        }


        $data = $query->get();

        $collections = $data->groupBy('status')->map(function ($pks, $status) {
            return [
                'status' => $status,
                'jumlah_pks' => $pks->count(),
                'jumlah_vendor' => $pks->unique('vendor')->count(),
            ];
        });


        if ($perpage == "All") {
            $perpage = $collections->count();
        }

        return PaginationHelper::paginate($collections, $perpage);
    }
    public function pks_details(GapPks $gap_pks, Request $request, $status)
    {
        $sortFieldInput = $request->input('sort_field') ?? 'vendor';
        $sortOrder = $request->input('sort_order', 'asc');
        $searchInput = $request->search;
        $query = $gap_pks->select('gap_pks.*')->orderBy($sortFieldInput, $sortOrder);
        $perpage = $request->perpage ?? 15;

        $query->where('status', $status);

        if (!is_null($searchInput)) {
            $searchQuery = "%$searchInput%";
            $query = $query->where(function ($q) use ($searchQuery) {
                return $q->where('vendor', 'like', $searchQuery)
                    ->orWhere('description', 'like', $searchQuery)
                    ->orWhere('contract_no', 'like', $searchQuery);
            });
        }

        if (!is_null($request->month) && !is_null($request->year)) {
            $paddedMonth = str_pad($request->month, 2, '0', STR_PAD_LEFT);

            // Create a Carbon instance using the year and month
            $carbonInstance = Carbon::createFromDate($request->year, $paddedMonth, 1)->format('Y-m-d');
            $query->where('periode', $carbonInstance);
        } else {
            $latestPeriode = $query->max('periode');
            $query->where('periode', $latestPeriode);
        }

        if ($perpage == "All") {
            $perpage = $query->count();
        }

        $query = $query->paginate($perpage);
        return PksResource::collection($query);
    }

    public function stos(GapSto $gap_stos, Request $request)
    {
        $sortFieldInput = $request->input('sort_field', 'id');
        $sortOrder = $request->input('sort_order', 'asc');
        $searchInput = $request->search;
        $query = $gap_stos->select('gap_stos.*')->orderBy($sortFieldInput, $sortOrder);
        $perpage = $request->perpage ?? 15;


        $input = $request->all();






        if (!is_null($searchInput)) {
            $searchQuery = "%$searchInput%";
            $query = $query->where(function ($query) use ($searchQuery) {
                $query->where('branch_code', 'like', $searchQuery)
                    ->orWhere('branch_name', 'like', $searchQuery)
                    ->orWhere('address', 'like', $searchQuery);
            });
        }


        if ($perpage == "All") {
            $perpage = $query->count();
        }

        $query = $query->paginate($perpage);


        return StoResource::collection($query);
    }
}
