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
            ->join('branches', 'gap_disnakers.branch_id', 'branches.id')
            ->join('jenis_perizinans', 'gap_disnakers.jenis_perizinan_id', 'jenis_perizinans.id');

        $perpage = $request->perpage ?? 15;

        if (!is_null($searchInput)) {
            $searchQuery = "%$searchInput%";
            $query = $query->where(function ($q) use ($searchQuery) {
                return $q->where('branch_name', 'like', $searchQuery)
                    ->orWhere('name', 'like', $searchQuery);
            });
        }
        if ($perpage == "All") {
            $perpage = $query->count();
        }

        if (isset($request->jenis_perizinan) && !is_null($request->jenis_perizinan)) {
            $query = $query->whereIn('name', $request->jenis_perizinan);
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
            ->join('branches', 'infra_sewa_gedungs.branch_id', 'branches.id')
            ->join('branch_types', 'branches.branch_type_id', 'branch_types.id');

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

        if (isset($request->type_name) && !is_null($request->type_name)) {
            $query = $query->whereIn('type_name', $request->type_name);
        }

        if (isset($request->status_kepemilikan) && !is_null($request->status_kepemilikan)) {
            $query = $query->whereIn('status_kepemilikan', $request->status_kepemilikan);
        }


        if ($perpage == "All") {
            $perpage = $query->count();
        }

        $data = $query->paginate($perpage);

        return SewaGedungResource::collection($data);
    }

    public function bros(InfraBro $infra_bro, Request $request)
    {
        $sortFieldInput = $request->input('sort_field') ?? 'branch_name';
        $sortOrder = $request->input('sort_order') ?? 'asc';
        $searchInput = $request->search;
        $query = $infra_bro;

        $perpage = $request->perpage ?? 15;

        if (!is_null($searchInput)) {
            $searchQuery = "%$searchInput%";
            $query = $query->where(function ($q) use ($searchQuery) {
                return $q->where('branch_name', 'like', $searchQuery);
            });
        }

        if (!is_null($request->category)) {
            $query = $query->where('category', $request->category);
        }

        if (isset($request->branch_type) && !is_null($request->branch_type)) {
            $query = $query->whereIn('branch_type', $request->branch_type);
        }
        if (isset($request->status) && !is_null($request->status)) {
            $query = $query->whereIn('status', $request->status);
        }

        if (!is_null($request->periode)) {
            $query = $query->where('periode', $request->periode);
        }

        if ($perpage == "All") {
            $perpage = $query->count();
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

        $data = $query->get();

        $collections = $data->groupBy('jenis_pekerjaan')->map(function ($maintenance_costs, $jenis_pekerjaan) {
            return [
                'jenis_pekerjaan' => $jenis_pekerjaan,
                'jumlah_project' => $maintenance_costs->count(),
                'bau' => $maintenance_costs->where('category', 'BAU')->count(),
                'project' => $maintenance_costs->where('category', 'Project')->count(),
                'total_oe' => $maintenance_costs->sum('total_oe'),
                'nilai_project_memo' => $maintenance_costs->sum('nilai_project_memo'),
                'nilai_project_final' => $maintenance_costs->sum('nilai_project_final'),
            ];
        });

        if ($perpage == "All") {
            $perpage = $collections->count();
        }

        return PaginationHelper::paginate($collections, $perpage);
    }

    public function maintenance_cost_details(InfraMaintenanceCost $infra_maintenance_cost, Request $request, $jenis_pekerjaan)
    {
        $sortFieldInput = $request->input('sort_field') ?? 'branches.branch_code';
        $sortOrder = $request->input('sort_order') ?? 'asc';
        $searchInput = $request->search;
        $query = $infra_maintenance_cost->select('infra_maintenance_costs.*')->orderBy($sortFieldInput, $sortOrder)
            ->join('branches', 'infra_maintenance_costs.branch_id', 'branches.id')
            ->join('branch_types', 'branches.branch_type_id', 'branch_types.id');

        $perpage = $request->perpage ?? 15;

        if (!is_null($searchInput)) {
            $searchQuery = "%$searchInput%";
            $query = $query->where(function ($q) use ($searchQuery) {
                return $q->where('branch_name', 'like', $searchQuery)
                    ->orWhere('nama_project', 'like', $searchQuery)
                    ->orWhere('nama_vendor', 'like', $searchQuery);
            });
        }


        if (isset($request->type_name) && !is_null($request->type_name)) {
            $query = $query->whereIn('type_name', $request->type_name);
        }
        if (isset($request->category) && !is_null($request->category)) {
            $query = $query->whereIn('category', $request->category);
        }
        $query->where('jenis_pekerjaan', $jenis_pekerjaan);

        if ($perpage == "All") {
            $perpage = $query->count();
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

        if ($perpage == "All") {
            $perpage = $collections->count();
        }
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
                    ->orWhere('branch_name', 'like', $searchQuery)
                    ->orWhere('description', 'like', $searchQuery);
            });
        }

        if (isset($request->status_pekerjaan) && !is_null($request->status_pekerjaan)) {
            $query = $query->whereIn('status_pekerjaan', $request->status_pekerjaan);
        }

        if ($perpage == "All") {
            $perpage = $query->count();
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

        if ($perpage == "All") {
            $perpage = $collections->count();
        }

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
                    ->orWhere('branch_name', 'like', $searchQuery)
                    ->orWhere('description', 'like', $searchQuery);
            });
        }
        if ($perpage == "All") {
            $perpage = $query->count();
        }

        $data = $query->paginate($perpage);
        return ScoringAssessmentsResource::collection($data);
    }
}
