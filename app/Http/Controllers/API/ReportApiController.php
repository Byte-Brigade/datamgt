<?php

namespace App\Http\Controllers\API;

use App\Helpers\PaginationHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\DisnakerResource;
use App\Http\Resources\FileResource;
use App\Http\Resources\HistoryResource;
use App\Http\Resources\Report\BranchResource;
use App\Models\Branch;
use App\Models\File;
use App\Models\GapDisnaker;
use App\Models\History;
use App\Models\InfraBro;
use Illuminate\Http\Request;

class ReportApiController extends Controller
{
    public function branches(Branch $branch, Request $request)
    {
        $sortFieldInput = $request->input('sort_field', 'branch_code');
        $sortOrder = $request->input('sort_order', 'asc');
        $searchInput = $request->search;
        $query = $branch->select('branches.*')->where('branches.branch_name', '!=', 'Kantor Pusat')->orderBy($sortFieldInput, $sortOrder)
            ->join('branch_types', 'branches.branch_type_id', 'branch_types.id');
        $perpage = $request->perpage ?? 10;

        $input = $request->all();
        if (isset($input['branch_types_type_name'])) {
            $type_name = $input['branch_types_type_name'];
            $query = $query->whereHas('branch_types', function (Builder $q) use ($type_name) {
                if (in_array('KF', $type_name)) {
                    return $q->whereIn('type_name', ['KF', 'KFNO']);
                }
                return $q->whereIn('type_name', $type_name);
            });
        }
        if (isset($request->layanan_atm)) {
            $query = $query->whereIn('layanan_atm', $request->layanan_atm);
        }
        if (!is_null($searchInput)) {
            $searchQuery = "%$searchInput%";
            $query = $query->where(function ($query) use ($searchQuery) {
                $query->where('branch_code', 'like', $searchQuery)
                    ->orWhere('branch_name', 'like', $searchQuery)
                    ->orWhere('address', 'like', $searchQuery);
            });
        }
        $branches = $query->paginate($perpage);

        return BranchResource::collection($branches);
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

        $query = $query->get();

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

        $collections = $query->sortBy('category')->groupBy('category')->map(function ($bros, $category) {
            return  [
                'category' => $category,
                'target' => $bros->count(),
                'done' => $bros->where('status', 'Done')->count(),
                'on_progress' => $bros->where('status', 'On Progress')->count(),
                'not_start' => $bros->where('all_progress', 0)->whereNotIn('status',['Done','On Progress','Drop'])->count(),
                'drop' => $bros->where('status', 'Drop')->count(),
            ];

        });



        return PaginationHelper::paginate($collections,$perpage);
    }


    public function disnaker_details(GapDisnaker $gap_disnaker, Request $request, $id)
    {
        $sortField = 'id';
        $sortOrder = $request->input('sort_order', 'asc');
        $searchInput = $request->search;
        $query = $gap_disnaker->where('branch_id', $id)->orderBy($sortField, $sortOrder);

        $perpage = $request->perpage ?? 10;

        if (!is_null($searchInput)) {
            $searchQuery = "%$searchInput%";
            $query = $query->where('id', 'like', $searchQuery);
        }
        $data = $query->paginate($perpage);
        return DisnakerResource::collection($data);
    }

    public function files(File $file, Request $request)
    {
        $sortField = 'id';
        $sortOrder = $request->input('sort_order', 'asc');
        $searchInput = $request->search;
        $query = $file->orderBy('created_at', 'desc');

        $perpage = $request->perpage ?? 10;


        $data = $query->paginate($perpage);
        return FileResource::collection($data);
    }


}
