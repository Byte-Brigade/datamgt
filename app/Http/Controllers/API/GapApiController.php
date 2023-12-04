<?php

namespace App\Http\Controllers\API;

use App\Helpers\PaginationHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\AlihDayaResource;
use App\Http\Resources\AssetsResource;
use App\Http\Resources\KdoMobilResource;
use App\Http\Resources\PerdinResource;
use App\Http\Resources\ScoringAssessmentsResource;
use App\Http\Resources\ScoringProjectsResource;
use App\Http\Resources\TonerResource;
use App\Models\Branch;
use App\Models\BranchType;
use App\Models\GapAlihDaya;
use App\Models\GapAsset;
use App\Models\GapKdo;
use App\Models\GapKdoMobil;
use App\Models\GapPerdin;
use App\Models\GapScoring;
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

        if (!is_null($searchInput)) {
            $searchQuery = "%$searchInput%";
            $query = $query->where(function ($query) use ($searchQuery) {
                $query->where('asset_number', 'like', $searchQuery)
                    ->orWhere('category', 'like', $searchQuery)
                    ->orWhereHas('branches', function ($q) use ($searchQuery) {
                        $q->where('branch_name', 'like', $searchQuery);
                    });
            });
        }
        $data = $query->paginate($perpage);
        return AssetsResource::collection($data);
    }

    public function kdos(GapKdo $gap_kdo, Request $request)
    {
        $sortFieldInput = $request->input('sort_field') ?? 'branches.branch_code';
        $sortOrder = $request->input('sort_order', 'asc');
        $searchInput = $request->search;
        $query = $gap_kdo->select('gap_kdos.*')->orderBy('branches.branch_code', 'asc')
            ->join('branches', 'gap_kdos.branch_id', 'branches.id');

        $perpage = $request->perpage ?? 15;

        if (!is_null($searchInput)) {
            $searchQuery = "%$searchInput%";
            $query = $query->where('id', 'like', $searchQuery);
        }
        $query = $query->get();

        $collections = $query->groupBy('branches.id')->map(function ($kdos, $branch) {
            $biaya_sewa = $kdos->flatMap(function ($mobil) {
                return $mobil->biaya_sewas;
            })->groupBy('periode')->sortKeysDesc()->first();
            return [
                'branches' => Branch::find($branch),
                'branch_types' => $kdos->first()->branches->branch_types,
                'jumlah_kendaraan' => $kdos->count(),
                'sewa_perbulan' => isset($biaya_sewa)  ? $biaya_sewa->sum('value')
                    : 0,
                'akhir_sewa' => $kdos->sortBy('akhir_sewa')->first()->akhir_sewa
            ];
        });


        if ($sortOrder == 'desc') {
            $collections = $collections->sortByDesc($sortFieldInput);
        } else {
            $collections = $collections->sortBy($sortFieldInput);
        }

        return response()->json(PaginationHelper::paginate($collections->unique('branches.branch_code'), $perpage));
    }

    public function kdo_mobil_details(GapKdo $gap_kdo_mobil, Request $request, $branch_id)
    {
        $sortFieldInput = $request->input('sort_field') ?? 'id';
        $sortOrder = $request->input('sort_order', 'asc');
        $searchInput = $request->search;
        $query = $gap_kdo_mobil->where('branch_id', $branch_id)->orderBy($sortFieldInput, $sortOrder);

        $perpage = $request->perpage ?? 15;

        if (!is_null($searchInput)) {
            $searchQuery = "%$searchInput%";
            $query = $query->where('id', 'like', $searchQuery);
        }
        $data = $query->paginate($perpage);
        return KdoMobilResource::collection($data);
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
                    ->orWhereHas('branches', function ($q) use ($searchQuery) {
                        $q->where('branch_name', 'like', $searchQuery);
                    });
            });
        }
        $data = $query->paginate($perpage);


        return ScoringProjectsResource::collection($data);
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
                'scoring_vendor' => $grade == "" ?  'Tidak Ada' : $grade,
                'jumlah_vendor' => $scorings->count(),
                'q1' => $scorings->where('schedule_scoring', 'Q1')->count(),
                'q2' => $scorings->where('schedule_scoring', 'Q2')->count(),
                'q3' => $scorings->where('schedule_scoring', 'Q3')->count(),
                'q4' => $scorings->where('schedule_scoring', 'Q4')->count(),
            ];
        })->sortBy('scoring_vendor');


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
                    ->orWhereHas('branches', function ($q) use ($searchQuery) {
                        $q->where('branch_name', 'like', $searchQuery);
                    });
            });
        }
        $data = $query->paginate($perpage);
        return ScoringAssessmentsResource::collection($data);
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

        if(!is_null($request->startDate)) {
            $query = $query->whereBetween('periode',[Carbon::parse($request->startDate)->startOfMonth(), Carbon::parse($request->endDate)->startOfMonth()]);
        }

        if (!is_null($searchInput)) {
            $searchQuery = "%$searchInput%";
            $query = $query->where(function ($query) use ($searchQuery) {
                $query->where('divisi_pembebanan', 'like', $searchQuery)
                    ->orWhere('category', 'like', $searchQuery);
            });
        }

        $query = $query->get();

        $collections = $query->groupBy('divisi_pembebanan')->map(function ($perdins, $divisi) {
            return [
                'divisi_pembebanan' => $divisi,
                'airline' => $perdins->where('category', 'Airline')->sum('value'),
                'ka' => $perdins->where('category', 'KA')->sum('value'),
                'hotel' => $perdins->where('category', 'Hotel')->sum('value'),
                'total' => $perdins->sum('value')
            ];
        })->sortByDesc(function ($item) {
            return $item['total'];
        });
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

        if(!is_null($request->startDate)) {
            $query = $query->whereBetween('periode',[Carbon::parse($request->startDate)->startOfMonth(), Carbon::parse($request->endDate)->startOfMonth()]);
        }

        if (!is_null($searchInput)) {
            $searchQuery = "%$searchInput%";
            $query = $query->where(function ($query) use ($searchQuery) {
                $query->where('divisi_pembebanan', 'like', $searchQuery)
                    ->orWhere('category', 'like', $searchQuery);
            });
        }

        $data = $query->paginate($perpage);
        return PerdinResource::collection($data);
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
                $query->where('divisi_pembebanan', 'like', $searchQuery)
                    ->orWhere('category', 'like', $searchQuery);
            });
        }

        $query = $query->get();
        $collections = $query->groupBy('jenis_pekerjaan')->map(function ($alihdayas, $jenis_pekerjaan) {
            return [
                'jenis_pekerjaan' => $jenis_pekerjaan,
                'vendor' => $alihdayas,
                'total_pegawai' => $alihdayas->count(),
                'total_biaya' => $alihdayas->sum('cost'),
            ];
        });

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


        $data = $query->paginate($perpage);


        return AlihDayaResource::collection($data);
    }
    public function toners(GapToner $gap_toner, Request $request)
    {
        $sortFieldInput = $request->input('sort_field') ?? 'branch_id';
        $sortOrder = $request->input('sort_order', 'asc');
        $searchInput = $request->search;
        $query = $gap_toner->select('gap_toners.*')->orderBy($sortFieldInput, $sortOrder);
        $perpage = $request->perpage ?? 15;

        if (!is_null($searchInput)) {
            $searchQuery = "%$searchInput%";
            $query = $query->where(function ($query) use ($searchQuery) {
                $query->whereHas('branches', function ($q) use ($searchQuery) {
                        $q->where('branch_name', 'like', $searchQuery);
                    });
            });
        }
        if(!is_null($request->startDate)) {
            $query = $query->whereBetween('idecice_date',[Carbon::parse($request->startDate)->startOfMonth(), Carbon::parse($request->endDate)->startOfMonth()]);
        }



        $data = $query->paginate($perpage);


        return TonerResource::collection($data);
    }
}
