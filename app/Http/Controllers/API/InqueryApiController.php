<?php

namespace App\Http\Controllers\API;

use App\Helpers\PaginationHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\Inquery\AssetsResource;
use App\Http\Resources\Inquery\BranchResource;
use App\Http\Resources\Inquery\LicensesResource;
use App\Http\Resources\Inquery\StoResource;
use App\Http\Resources\Ops\EmployeeResource;
use App\Models\Branch;
use App\Models\Employee;
use App\Models\GapAsset;
use App\Models\GapKdo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InqueryApiController extends Controller
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

        if ($perpage == "All") {
            $perpage = $query->count();
        }

        $query = $query->paginate($perpage);


        return BranchResource::collection($query);
    }

    public function staff_detail(Employee $employees, Request $request, $slug)
    {
        $sortFieldInput = $request->input('sort_field', 'employee_id');
        $sortOrder = $request->input('sort_order', 'asc');
        $searchInput = $request->search;
        $query = $employees->select('employees.*')->orderBy($sortFieldInput, $sortOrder)->orderBy('employee_id', 'asc')
            ->join('branches', 'employees.branch_id', 'branches.id')
            ->join('employee_positions', 'employees.position_id', 'employee_positions.id');
        $perpage = $request->perpage ?? 10;

        $query->whereHas('branches', function ($q) use ($slug) {
            return $q->where('slug', $slug);
        });

        if (!is_null($searchInput)) {
            $searchQuery = "%$searchInput%";
            $query = $query->where(function ($query) use ($searchQuery) {
                $query->where('employee_id', 'like', $searchQuery)
                    ->orWhere('name', 'like', $searchQuery)
                    ->orWhere('email', 'like', $searchQuery)
                    ->orWhereHas('branches', function ($q) use ($searchQuery) {
                        $q->where('branch_name', 'like', $searchQuery);
                    })
                    ->orWhereHas('employee_positions', function ($q) use ($searchQuery) {
                        $q->where('position_name', 'like', $searchQuery);
                    });
            });
        }

        if (!is_null($request->input('employee_positions_position_name'))) {
            $query = $query->whereHas('employee_positions', function ($q) use ($request) {
                $q->whereIn('position_name', $request->get('employee_positions_position_name'));
            });
        }
        if ($perpage == "All") {
            $perpage = $query->count();
        }

        $query = $query->paginate($perpage);

        return EmployeeResource::collection($query);
    }
    public function staff(Branch $branch, Request $request)
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

        if (!is_null($searchInput)) {
            $searchQuery = "%$searchInput%";
            $query = $query->where(function ($query) use ($searchQuery) {
                $query->where('branch_code', 'like', $searchQuery)
                    ->orWhere('branch_name', 'like', $searchQuery)
                    ->orWhere('address', 'like', $searchQuery);
            });
        }

        $data = $query->get();

        $collections = $data->groupBy('id')->map(function ($branches, $id) {
            $branch = Branch::find($id);
            return [
                'id' => $id,
                'branch_code' => $branch->branch_code,
                'branch_name' => $branch->branch_name,
                'type_name' => $branch->branch_types->type_name,
                'slug' => $branch->slug,
                'jumlah_karyawan' => $branch->employees->count()
            ];
        });

        if ($perpage == "All") {
            $perpage = $collections->count();
        }

        return PaginationHelper::paginate($collections, $perpage);
    }

    public function assets(Branch $branch, Request $request)
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

        if ($perpage == "All") {
            $perpage = $query->count();
        }

        $query = $query->paginate($perpage);


        return AssetsResource::collection($query);
    }
    public function stos(Branch $branch, Request $request)
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

        if (!is_null($request->branch_id)) {
            $query = $query->where('branches.id', $request->branch_id);
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

        return StoResource::collection($query);
    }

    public function licenses(Branch $branch, Request $request)
    {
        $sortFieldInput = $request->input('sort_field', 'branch_code');
        $sortOrder = $request->input('sort_order', 'asc');
        $searchInput = $request->search;
        $query = $branch->with(['branch_types', 'ops_pajak_reklames', 'gap_disnaker', 'ops_apar', 'ops_skoperasional', 'ops_skbirtgs'])->where('branches.branch_name', '!=', 'Kantor Pusat')->orderBy($sortFieldInput, $sortOrder);
        $perpage = $request->perpage ?? 15;

        $input = $request->all();
        if (isset($input['branch_types_type_name'])) {
            $type_name = $input['branch_types_type_name'];
            $query = $query->whereHas('branch_types', function ($q) use ($type_name) {
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

        if ($perpage == "All") {
            $perpage = $query->count();
        }

        $query = $query->paginate($perpage);

        return LicensesResource::collection($query);
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

        if (!is_null($request->branch_id)) {
            $query = $query->where('branches.id', $request->branch_id);
        }

        $collections = $query->get();

        $collections = $collections->groupBy('branches.id')->map(function ($kdos, $branch) {
            $biaya_sewa = $kdos->flatMap(function ($mobil) {
                return $mobil->biaya_sewas;
            })->groupBy('periode')->sortKeysDesc()->first();
            return [
                'branches' => Branch::find($branch),
                'branch_types' => $kdos->first()->branches->branch_types,
                'jumlah_kendaraan' => $biaya_sewa->where('value', '>', 0)->count(),
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

        if ($perpage == "All") {
            $perpage = $collections->count();
        }

        return response()->json(PaginationHelper::paginate($collections, $perpage));
    }
}
