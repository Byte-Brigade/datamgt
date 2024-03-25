<?php

namespace App\Http\Controllers\API;

use App\Helpers\PaginationHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\AssetsResource;
use App\Http\Resources\DisnakerResource;
use App\Http\Resources\FileResource;
use App\Http\Resources\HistoryResource;
use App\Http\Resources\Report\BranchResource;
use App\Models\Branch;
use App\Models\BranchType;
use App\Models\File;
use App\Models\GapAlihDaya;
use App\Models\GapAsset;
use App\Models\GapDisnaker;
use App\Models\GapKdo;
use App\Models\GapPks;
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
        $perpage = $request->perpage ?? 15;

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
        if ($perpage == "All") {
            $perpage = $query->count();
        }

        $query = $query->paginate($perpage);


        return BranchResource::collection($query);
    }

    public function assets(Branch $branch, Request $request)
    {
        $sortFieldInput = $request->input('sort_field', 'branch_code');
        $sortOrder = $request->input('sort_order', 'asc');
        $searchInput = $request->search;
        $query = $branch->select('branches.*')->where('branches.branch_name', '!=', 'Kantor Pusat')->orderBy($sortFieldInput, $sortOrder)
            ->join('branch_types', 'branches.branch_type_id', 'branch_types.id');
        // ->join('gap_assets', 'gap_assets.branch_id', 'branches.id');
        $perpage = $request->perpage ?? 15;


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

        if (!is_null($request->branch_id)) {
            $query = $query->where('branches.id', $request->branch_id);
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


        $data = $query->get();

        $collections = $data->map(function ($branch) {
            $branch->alt_name = $branch->branch_types->alt_name;
            return $branch;
        })->groupBy('alt_name')->map(function ($branches, $alt_name) {
            return [
                'type_name' => $alt_name,
                'depre'     => $branches->flatMap(function ($branch) {
                    return $branch->gap_assets->where('category', 'Depre');
                })->count(),
                'non_depre'     => $branches->flatMap(function ($branch) {
                    return $branch->gap_assets->where('category', 'Non-Depre');
                })->count(),
            ];
        });


        if ($perpage == "All") {
            $perpage = $collections->count();
        }
        return PaginationHelper::paginate($collections, $perpage);
    }
    public function asset_detail(Branch $branch, Request $request, $type_name)
    {
        $sortFieldInput = $request->input('sort_field', 'branch_code');
        $sortOrder = $request->input('sort_order', 'asc');
        $searchInput = $request->search;
        $query = $branch->select('branches.*')->where('branches.branch_name', '!=', 'Kantor Pusat')->orderBy($sortFieldInput, $sortOrder)
            ->join('branch_types', 'branches.branch_type_id', 'branch_types.id');
        // ->join('gap_assets', 'gap_assets.branch_id', 'branches.id');
        $perpage = $request->perpage ?? 15;
        $query = $query->where('branch_types.alt_name', $type_name);

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

        if (!is_null($request->branch_id)) {
            $query = $query->where('branches.id', $request->branch_id);
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


        $data = $query->get();

        $collections = $data->map(function ($branch) {
            return [
                'branch_name' => $branch->branch_name,
                'slug' => $branch->slug,
                'depre'     => $branch->gap_assets->where('category', 'Depre')->count(),
                'non_depre'     => $branch->gap_assets->where('category', 'Non-Depre')->count(),
            ];
        });


        if ($perpage == "All") {
            $perpage = $collections->count();
        }


        return PaginationHelper::paginate($collections, $perpage);
    }


    public function licenses(Branch $branch, Request $request)
    {
        $sortFieldInput = $request->input('sort_field', 'branch_code');
        $sortOrder = $request->input('sort_order', 'asc');
        $searchInput = $request->search;
        $query = $branch->select('branches.*')->where('branches.branch_name', '!=', 'Kantor Pusat')->orderBy($sortFieldInput, $sortOrder)
            ->join('branch_types', 'branches.branch_type_id', 'branch_types.id');
        // ->join('gap_assets', 'gap_assets.branch_id', 'branches.id');
        $perpage = $request->perpage ?? 15;


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

        if (!is_null($request->branch_id)) {
            $query = $query->where('branches.id', $request->branch_id);
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


        $data = $query->get();

        $collections = $data->map(function ($branch) {
            $branch->alt_name = $branch->branch_types->alt_name;
            return $branch;
        })->groupBy('alt_name')->map(function ($branches, $alt_name) {
            return [
                'type_name' => $alt_name,
                'disnaker'     => $branches->flatMap(function ($branch) {
                    return $branch->gap_disnaker;
                })->count(),
                'pajak_reklame'     => $branches->map(function ($branch) {
                    return $branch->ops_pajak_reklames;
                })->count(),
                'skbirtgs'     => $branches->map(function ($branch) {
                    return $branch->ops_skbirtgs;
                })->count(),
                'skoperational'     => $branches->map(function ($branch) {
                    return $branch->ops_skoperational;
                })->count(),
            ];
        });


        if ($perpage == "All") {
            $perpage = $collections->count();
        }

        return PaginationHelper::paginate($collections, $perpage);
    }
    public function vendor(Request $request)
    {
        $collections = collect([
            [
                'group_name' => 'KDO',
                'jumlah_vendor' => GapKdo::get()->unique('vendor')->count(),
            ],
            [
                'group_name' => 'Alih Daya',
                'jumlah_vendor' => GapAlihDaya::get()->unique('vendor')->count(),
            ],
            [
                'group_name' => 'Perjanjian Kerja Sama (PKS)',
                'jumlah_vendor' => GapPks::whereNot('status', 'TIDAK AKTIF')->get()->unique('vendor')->count(),
            ],
        ]);

        return PaginationHelper::paginate($collections, 15);
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


        // Get the latest BRO
        $bro = InfraBro::orderBy('periode', 'desc')->first();



        $bro = InfraBro::orderBy('periode', 'desc')->first();
        if (!is_null($request->periode) && $request->periode == "previous") {
            $distinctPeriods = InfraBro::where('periode', '!=', $bro->periode)->distinct('periode')->pluck('periode');

            if ($distinctPeriods->count() > 0) {
                $previousPeriode = $distinctPeriods->first();
                $bro = InfraBro::where('periode', $previousPeriode)->orderBy('periode', 'desc')->first();
            }
        }

        $query = $query->where('periode', $bro->periode);




        $collections = $query->sortBy('category')->groupBy('category')->map(function ($bros, $category) {
            return  [
                'category' => $category,
                'target' => $bros->count(),
                'done' => $bros->where('status', 'Done')->count(),
                'on_progress' => $bros->where('status', 'On Progress')->count(),
                'not_start' => $bros->where('all_progress', 0)->whereNotIn('status', ['Done', 'On Progress', 'Drop'])->count(),
                'drop' => $bros->where('status', 'Drop')->count(),
            ];
        });


        if ($perpage == "All") {
            $perpage = $collections->count();
        }


        return PaginationHelper::paginate($collections, $perpage);
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
        if ($perpage == "All") {
            $perpage = $query->count();
        }

        $query = $query->paginate($perpage);

        return DisnakerResource::collection($query);
    }

    public function files(File $file, Request $request)
    {
        $sortField = 'id';
        $sortOrder = $request->input('sort_order', 'asc');
        $searchInput = $request->search;
        $query = $file->orderBy('created_at', 'desc');

        $perpage = $request->perpage ?? 10;


        if ($perpage == "All") {
            $perpage = $query->count();
        }

        $query = $query->paginate($perpage);

        return FileResource::collection($query);
    }
}
