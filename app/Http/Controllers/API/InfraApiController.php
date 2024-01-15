<?php

namespace App\Http\Controllers\API;

use App\Helpers\PaginationHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\BroResource;
use App\Http\Resources\DisnakerResource;
use App\Http\Resources\MaintenanceCostResource;
use App\Http\Resources\ScoringAssessmentsResource;
use App\Http\Resources\ScoringProjectsResource;
use App\Http\Resources\SewaGedungResource;
use App\Models\GapDisnaker;
use App\Models\InfraBro;
use App\Models\InfraMaintenanceCost;
use App\Models\InfraScoring;
use App\Models\InfraSewaGedung;
use Illuminate\Http\Request;

class InfraApiController extends Controller
{


    public function disnakers(GapDisnaker $gap_disnaker, Request $request)
    {
        $sortFieldInput = $request->input('sort_field') ?? 'branches.branch_code';
        $sortOrder = $request->input('sort_order', 'asc');
        $searchInput = $request->search;
        $query = $gap_disnaker->select('gap_disnakers.*')->orderBy($sortFieldInput, $sortOrder)
            ->join('branches', 'gap_disnakers.branch_id', 'branches.id');

        $perpage = $request->perpage ?? 15;

        if (!is_null($searchInput)) {
            $searchQuery = "%$searchInput%";
            $query = $query->where('id', 'like', $searchQuery);
        }
        $data = $query->paginate($perpage);
        return DisnakerResource::collection($data);
    }

    public function sewa_gedungs(InfraSewaGedung $infra_sewa_gedung, Request $request)
    {
        $sortFieldInput = $request->input('sort_field') ?? 'branches.branch_code';
        $sortOrder = $request->input('sort_order') ?? 'asc';
        $searchInput = $request->search;
        $query = $infra_sewa_gedung
            ->join('branches', 'infra_sewa_gedungs.branch_id', 'branches.id');

        if ($sortFieldInput == 'status_kepemilikan') {
            $query = $query->orderByRaw("SUBSTRING(status, 1, 1) " . $sortOrder);
        } else {
            $query = $query->orderBy($sortFieldInput, $sortOrder);
        }
        $perpage = $request->perpage ?? 15;

        if (!is_null($searchInput)) {
            $searchQuery = "%$searchInput%";
            $query = $query->where('id', 'like', $searchQuery);
        }

        $data = $query->paginate($perpage);

        return SewaGedungResource::collection($data);
    }

    public function bros(InfraBro $infra_bro, Request $request)
    {
        $sortFieldInput = $request->input('sort_field') ?? 'branches.branch_code';
        $sortOrder = $request->input('sort_order') ?? 'asc';
        $searchInput = $request->search;
        $query = $infra_bro;

        $perpage = $request->perpage ?? 15;

        if (!is_null($searchInput)) {
            $searchQuery = "%$searchInput%";
            $query = $query->where('id', 'like', $searchQuery);
        }

        if(!is_null($request->category)) {
            $query = $query->where('category', $request->category);
        }


        $data = $query->paginate($perpage);



        return BroResource::collection($data);
    }
    public function maintenance_costs(InfraMaintenanceCost $infra_maintenance_cost, Request $request)
    {
        $sortFieldInput = $request->input('sort_field') ?? 'branches.branch_code';
        $sortOrder = $request->input('sort_order') ?? 'asc';
        $searchInput = $request->search;
        $query = $infra_maintenance_cost->select('infra_maintenance_costs.*')->orderBy($sortFieldInput, $sortOrder)
            ->join('branches', 'infra_maintenance_costs.branch_id', 'branches.id');

        $perpage = $request->perpage ?? 15;

        if (!is_null($searchInput)) {
            $searchQuery = "%$searchInput%";
            $query = $query->where('id', 'like', $searchQuery);
        }

        $data = $query->paginate($perpage);



        return MaintenanceCostResource::collection($data);
    }


    public function scoring_projects(InfraScoring $infra_scoring_project, Request $request)
    {
        $sortFieldInput = $request->input('sort_field') ?? 'branches.branch_code';
        $sortOrder = $request->input('sort_order', 'asc');
        $searchInput = $request->search;
        $query = $infra_scoring_project->select('infra_scorings.*')->where('type', 'Post Project')->orderBy($sortFieldInput, $sortOrder)
            ->join('branches', 'infra_scorings.branch_id', 'branches.id');

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
                'scoring_vendor' => $grade == '' ? 'Tidak Ada' : $grade,
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

    public function scoring_project_details(InfraScoring $infra_scoring_project, Request $request, $scoring_vendor)
    {
        $sortFieldInput = $request->input('sort_field') ?? 'branches.branch_code';
        $sortOrder = $request->input('sort_order', 'asc');
        $searchInput = $request->search;
        $query = $infra_scoring_project->select('infra_scorings.*')->where('type', 'Post Project')->where('scoring_vendor', $scoring_vendor == 'Tidak Ada' ? null : $scoring_vendor)->orderBy($sortFieldInput, $sortOrder)
            ->join('branches', 'infra_scorings.branch_id', 'branches.id');

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


    public function scoring_assessments(InfraScoring $infra_scoring_assessment, Request $request)
    {
        $sortFieldInput = $request->input('sort_field') ?? 'branches.branch_code';
        $sortOrder = $request->input('sort_order', 'asc');
        $searchInput = $request->search;
        $query = $infra_scoring_assessment->select('infra_scorings.*')->where('type', 'Assessment')->orderBy($sortFieldInput, $sortOrder)
            ->join('branches', 'infra_scorings.branch_id', 'branches.id');

        $perpage = $request->perpage ?? 10;

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
                'scoring_vendor' => $grade == '' ? 'Tidak Ada' : $grade,
                'jumlah_vendor' => $scorings->count(),
                'q1' => $scorings->where('schedule_scoring', 'Q1')->count(),
                'q2' => $scorings->where('schedule_scoring', 'Q2')->count(),
                'q3' => $scorings->where('schedule_scoring', 'Q3')->count(),
                'q4' => $scorings->where('schedule_scoring', 'Q4')->count(),
            ];
        })->sortBy('scoring_vendor');


        return response()->json(PaginationHelper::paginate($collections, $perpage));
    }
    public function scoring_assessment_details(InfraScoring $infra_scoring_assessment, Request $request, $scoring_vendor)
    {
        $sortFieldInput = $request->input('sort_field') ?? 'branches.branch_code';
        $sortOrder = $request->input('sort_order', 'asc');
        $searchInput = $request->search;
        $query = $infra_scoring_assessment->select('infra_scorings.*')->where('type', 'Assessment')->where('scoring_vendor', $scoring_vendor == 'Tidak Ada' ? null : $scoring_vendor)->orderBy($sortFieldInput, $sortOrder)
            ->join('branches', 'infra_scorings.branch_id', 'branches.id');

        $perpage = $request->perpage ?? 10;

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

}
