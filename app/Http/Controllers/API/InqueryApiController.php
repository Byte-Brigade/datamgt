<?php

namespace App\Http\Controllers\API;

use App\Helpers\PaginationHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\AlihDayaResource;
use App\Http\Resources\Inquery\AssetsResource;
use App\Http\Resources\Inquery\AssetSTOResource;
use App\Http\Resources\Inquery\BranchResource;
use App\Http\Resources\Inquery\LicensesResource;
use App\Http\Resources\Inquery\StoResource;
use App\Http\Resources\Ops\EmployeeResource;
use App\Http\Resources\TonerResource;
use App\Models\Branch;
use App\Models\Employee;
use App\Models\GapAlihDaya;
use App\Models\GapAsset;
use App\Models\GapHasilSto;
use App\Models\GapKdo;
use App\Models\GapSto;
use App\Models\GapToner;
use Carbon\Carbon;
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

        if (isset($request->type_name) && !is_null($request->type_name)) {
            $query = $query->whereIn('type_name', $request->type_name);
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
        $query = $branch->select('branches.*')->orderBy($sortFieldInput, $sortOrder)
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

        $query = $query->whereHas('gap_assets');

        if ($perpage == "All") {
            $perpage = $query->count();
        }

        if (isset($request->type_name) && !is_null($request->type_name)) {
            $query = $query->where('type_name', $request->type_name);
        }


        $query = $query->get();
        // $query = $query->paginate($perpage);

        $collections = $query->map(function ($branch) use ($request) {
            $gap_assets = collect([]);
            if (!is_null($request->input('$y'))) {

                $sto = GapSto::where('status','Done')->where('periode', Carbon::createFromDate($request->input('$y'))->startOfYear()
                    ->format('Y-m-d'))->latest()->first();

                if (isset($sto)) {
                    $hasil_sto = GapHasilSto::where('gap_sto_id', $sto->id)->where('branch_id', $branch->id)->first();
                    if (isset($hasil_sto)) {
                        $gap_assets = GapAsset::where('branch_id', $branch->id)->whereHas('gap_asset_details', function ($q) use ($hasil_sto) {
                            return $q->where('gap_hasil_sto_id', $hasil_sto->id)->where('status', 'Ada');
                        })->get();
                    }
                }
            } else {
                $sto = GapSto::where('status', 'Done')->latest()->first();
                if (isset($sto)) {
                    $hasil_sto = GapHasilSto::where('gap_sto_id', $sto->id)->where('branch_id', $branch->id)->first();
                    if (isset($hasil_sto)) {
                        $gap_assets = GapAsset::where('branch_id', $branch->id)->whereHas('gap_asset_details', function ($q) use ($hasil_sto) {
                            return $q->where('gap_hasil_sto_id', $hasil_sto->id)->where('status', 'Ada');
                        })->get();
                    }
                } else {
                    $gap_assets = $branch->gap_assets;
                }

            }
            return [
                'year' => isset($sto),
                'branch_name' => $branch->branch_name,
                'type_name' => $branch->branch_types->type_name,
                'slug' => $branch->slug,
                'item' => [
                    'depre' => $gap_assets->where('category', 'Depre')->count(),
                    'non_depre' => $gap_assets->where('category', 'Non-Depre')->count(),

                ],
                'nilai_perolehan' => [
                    'depre' => $gap_assets->where('category', 'Depre')->sum('asset_cost'),
                    'non_depre' => $gap_assets->where('category', 'Non-Depre')->sum('asset_cost'),

                ],
                'penyusutan' => [
                    'depre' => $gap_assets->where('category', 'Depre')->sum('accum_depre'),
                    'non_depre' => $gap_assets->where('category', 'Non-Depre')->sum('accum_depre'),
                ],
                'net_book_value' => [
                    'depre' => $gap_assets->where('category', 'Depre')->sum('net_book_value'),
                    'non_depre' => $gap_assets->where('category', 'Non-Depre')->sum('net_book_value'),
                ],
            ];
        });


        return PaginationHelper::paginate($collections, $perpage);
    }

    public function stos(GapHasilSto $gap_hasil_sto, Request $request)
    {
        $sortFieldInput = $request->input('sort_field', 'branch_code');
        $sortOrder = $request->input('sort_order', 'asc');
        $searchInput = $request->search;
        $query = $gap_hasil_sto->select('gap_hasil_stos.*')->orderBy($sortFieldInput, $sortOrder)
            ->join('branches', 'branches.id', 'gap_hasil_stos.branch_id')
            ->join('branch_types', 'branches.branch_type_id', 'branch_types.id');
        $perpage = $request->perpage ?? 15;

        $query = $query->whereHas('gap_stos', function ($q) {
            return $q->where('status', 'On Progress');
        });

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

    public function sto_details(GapAsset $gap_asset, Request $request)
    {
        $sortFieldInput = $request->input('sort_field') ?? 'branches.branch_code';
        $sortOrder = $request->input('sort_order', 'asc');
        $searchInput = $request->search;
        $query = $gap_asset->select('gap_assets.*')->orderBy($sortFieldInput, $sortOrder)
            ->join('branches', 'gap_assets.branch_id', 'branches.id');

        $perpage = $request->perpage ?? 10;
        $latestSTO = GapSto::where('status', 'Done')
        ->latest()
        ->first();
        if(isset($latestSTO)) {
            $query = $query->whereHas('gap_asset_details', function ($q) use($latestSTO) {
                return $q->where('status','Ada')->whereHas('gap_hasil_sto', function($q)  use($latestSTO)  {
                    return $q->whereHas('gap_stos',function($q) use($latestSTO)  {


                        return $q->where('id', $latestSTO->id);
                    });
                });
            });
        }


        if (!is_null($request->branch_code)) {
            $query = $query->where('branch_code', $request->branch_code);
        }
        if (!is_null($request->category)) {
            $query = $query->where('category', $request->category);
        }


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
        if ($perpage == "All") {
            $perpage = $query->count();
        }

        $query = $query->paginate($perpage);

        return AssetSTOResource::collection($query);
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

    public function toners(GapToner $gap_toner, Request $request)
    {
        $sortFieldInput = $request->input('sort_field') ?? 'branches.branch_code';
        $sortOrder = $request->input('sort_order', 'asc');
        $searchInput = $request->search;
        $query = $gap_toner->select('gap_toners.*')->orderBy('branches.branch_code', 'asc')
            ->join('branches', 'gap_toners.branch_id', 'branches.id')
            ->join('branch_types', 'branch_types.id', 'branches.branch_type_id');

        $perpage = $request->perpage ?? 15;

        if (!is_null($request->vendor)) {
            $query = $query->where('vendor', $request->vendor);
        }

        if (!is_null($request->branch_id)) {
            $query = $query->where('branches.id', $request->branch_id);
        }

        if (!is_null($searchInput)) {
            $searchQuery = "%$searchInput%";
            $query = $query->where(function ($query) use ($searchQuery) {
                $query->where('type_name', 'like', $searchQuery)
                    ->orWhere('branch_name', 'like', $searchQuery);
            });
        }

        if (isset($request->type_name)) {
            $query = $query->whereIn('type_name', $request->type_name);
        }

        if (!is_null($request->input('$y'))) {
            $startYear = Carbon::createFromDate($request->input(('$y')))->startOfYear()->format('Y-m-d');
            $endYear = Carbon::createFromDate($request->input(('$y')))->endOfYear()->format('Y-m-d');
            $query = $query->whereBetween('idecice_date', [$startYear, $endYear]);
        }
        $collections = $query->get();
        if (!is_null($request->type) && $request->type == 'quantity') {
            $collections = $collections->groupBy('branch_id')->map(function ($toners, $branch_id) {
                $branch = Branch::find($branch_id);
                $minPeriode = Carbon::parse($toners->min('idecice_date'))->year;
                $maxPeriode = Carbon::parse($toners->max('idecice_date'))->year;
                return [
                    'branch_name' => $branch->branch_name,
                    'slug' => $branch->slug,
                    'january' => $toners->whereBetween('idecice_date', [$minPeriode . '-01-01', $maxPeriode . '-01-31'])->sum('quantity'),
                    'february' => $toners->whereBetween('idecice_date', [$minPeriode . '-02-01', $maxPeriode . '-02-31'])->sum('quantity'),
                    'march' => $toners->whereBetween('idecice_date', [$minPeriode . '-03-01', $maxPeriode . '-03-31'])->sum('quantity'),
                    'april' => $toners->whereBetween('idecice_date', [$minPeriode . '-04-01', $maxPeriode . '-04-31'])->sum('quantity'),
                    'may' => $toners->whereBetween('idecice_date', [$minPeriode . '-05-01', $maxPeriode . '-05-31'])->sum('quantity'),
                    'june' => $toners->whereBetween('idecice_date', [$minPeriode . '-06-01', $maxPeriode . '-06-31'])->sum('quantity'),
                    'july' => $toners->whereBetween('idecice_date', [$minPeriode . '-07-01', $maxPeriode . '-07-31'])->sum('quantity'),
                    'august' => $toners->whereBetween('idecice_date', [$minPeriode . '-08-01', $maxPeriode . '-08-31'])->sum('quantity'),
                    'september' => $toners->whereBetween('idecice_date', [$minPeriode . '-09-01', $maxPeriode . '-09-31'])->sum('quantity'),
                    'october' => $toners->whereBetween('idecice_date', [$minPeriode . '-10-01', $maxPeriode . '-10-31'])->sum('quantity'),
                    'november' => $toners->whereBetween('idecice_date', [$minPeriode . '-11-01', $maxPeriode . '-11-31'])->sum('quantity'),
                    'december' => $toners->whereBetween('idecice_date', [$minPeriode . '-12-01', $maxPeriode . '-12-31'])->sum('quantity'),
                ];
            });
        } else if (!is_null($request->type) && $request->type == 'nominal') {
            $collections = $collections->groupBy('branch_id')->map(function ($toners, $branch_id) {
                $branch = Branch::find($branch_id);
                $minPeriode = Carbon::parse($toners->min('idecice_date'))->year;
                $maxPeriode = Carbon::parse($toners->max('idecice_date'))->year;
                return [
                    'branch_name' => $branch->branch_name,
                    'slug' => $branch->slug,
                    'january' => $toners->whereBetween('idecice_date', [$minPeriode . '-01-01', $maxPeriode . '-01-31'])->sum('total'),
                    'february' => $toners->whereBetween('idecice_date', [$minPeriode . '-02-01', $maxPeriode . '-02-31'])->sum('total'),
                    'march' => $toners->whereBetween('idecice_date', [$minPeriode . '-03-01', $maxPeriode . '-03-31'])->sum('total'),
                    'april' => $toners->whereBetween('idecice_date', [$minPeriode . '-04-01', $maxPeriode . '-04-31'])->sum('total'),
                    'may' => $toners->whereBetween('idecice_date', [$minPeriode . '-05-01', $maxPeriode . '-05-31'])->sum('total'),
                    'june' => $toners->whereBetween('idecice_date', [$minPeriode . '-06-01', $maxPeriode . '-06-31'])->sum('total'),
                    'july' => $toners->whereBetween('idecice_date', [$minPeriode . '-07-01', $maxPeriode . '-07-31'])->sum('total'),
                    'august' => $toners->whereBetween('idecice_date', [$minPeriode . '-08-01', $maxPeriode . '-08-31'])->sum('total'),
                    'september' => $toners->whereBetween('idecice_date', [$minPeriode . '-09-01', $maxPeriode . '-09-31'])->sum('total'),
                    'october' => $toners->whereBetween('idecice_date', [$minPeriode . '-10-01', $maxPeriode . '-10-31'])->sum('total'),
                    'november' => $toners->whereBetween('idecice_date', [$minPeriode . '-11-01', $maxPeriode . '-11-31'])->sum('total'),
                    'december' => $toners->whereBetween('idecice_date', [$minPeriode . '-12-01', $maxPeriode . '-12-31'])->sum('total'),
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

    public function alihdaya_summary(Branch $branch, Request $request)
    {
        $sortFieldInput = $request->input('sort_field', 'branch_code');
        $sortOrder = $request->input('sort_order', 'asc');
        $searchInput = $request->search;
        $query = $branch->select('branches.*')->orderBy($sortFieldInput, $sortOrder)
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
            $latestPeriode = $branch->gap_alih_dayas->max('periode');
            return [
                'id' => $id,
                'branch_code' => $branch->branch_code,
                'branch_name' => $branch->branch_name,
                'type_name' => $branch->branch_types->type_name,
                'slug' => $branch->slug,
                'tenaga_kerja' => $branch->gap_alih_dayas->where('periode', $latestPeriode)->count(),
                'biaya' => $branch->gap_alih_dayas->where('periode', $latestPeriode)->sum('cost'),
            ];
        });

        if ($perpage == "All") {
            $perpage = $collections->count();
        }

        return PaginationHelper::paginate($collections, $perpage);
    }

    public function alihdayas_branch(GapAlihDaya $gap_alih_daya, Request $request, $slug)
    {

        $branch = Branch::where('slug', $slug)->first();
        $sortFieldInput = $request->input('sort_field') ?? 'jenis_pekerjaan';
        $sortOrder = $request->input('sort_order', 'asc');
        $searchInput = $request->search;
        $query = $gap_alih_daya->select('gap_alih_dayas.*')->orderBy($sortFieldInput, $sortOrder);

        $perpage = $request->perpage ?? 15;

        if (!is_null($request->branch_code)) {
            $query = $query->where('branch_code', $request->branch_code);
        }

        $query = $query->where('branch_id', $branch->id);

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

                $query->where('periode', $endDate->startOfMonth()->format('Y-m-d'));
            } else {
                $query->whereBetween('periode', [$startDate->startOfMonth()->format('Y-m-d'), $endDate->startOfMonth()->format('Y-m-d')]);
            }
        } else {
            $maxPeriode = $query->max('periode');
            $query->where('periode', $maxPeriode);
        }
        // } else {
        //     $latestPeriode = $query->max('periode');
        //     $query->where('periode', $latestPeriode);
        // }


        // if ($request->type == "tenaga-kerja") {

        //     $query = $query->select([
        //         'jenis_pekerjaan',
        //         'nama_pegawai',
        //         'user',
        //         'lokasi',
        //         'vendor',
        //     ])->distinct();
        // }
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
    public function alihdaya_details_branch(GapAlihDaya $gap_alih_daya, Request $request, $slug)
    {
        $branch = Branch::where('slug', $slug)->first();
        $sortFieldInput = $request->input('sort_field') ?? 'jenis_pekerjaan';
        $sortOrder = $request->input('sort_order', 'asc');
        $searchInput = $request->search;
        $query = $gap_alih_daya->select('gap_alih_dayas.*')->orderBy($sortFieldInput, $sortOrder);
        $perpage = $request->perpage ?? 15;

        if ($request->type == 'jenis_pekerjaan') {
            $query = $query->where('jenis_pekerjaan', $request->type_item);
        } else if ($request->type == 'vendor') {
            $query = $query->where('vendor', $request->type_item);
        }

        $query = $query->where('branch_id', $branch->id);


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

        if (!is_null($request->input('$M')) && !is_null($request->input('$y'))) {
            $year = $request->input('$y');
            $month = ((int) $request->input('$M')) + 1;
            $date = Carbon::createFromFormat('Y-m', $year . '-' . $month);
            $query->where('periode', $date->startOfMonth()->format('Y-m-d'));
        } else if (!is_null($request->input('$y'))) {
            $startDate = Carbon::createFromDate($request->input('$y'))->startOfYear()->format('Y-m-d');
            $endDate = Carbon::createFromDate($request->input('$y'))->endOfYear()->format('Y-m-d');
            $query->whereBetween('periode', [$startDate, $endDate]);
            $yearToDate = true;
        } else {
            $query->where('periode', $query->max('periode'));
        }


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

        if (!is_null($request->input('$M')) && !is_null($request->input('$y'))) {
            $year = $request->input('$y');
            $month = ((int) $request->input('$M')) + 1;
            $date = Carbon::createFromFormat('Y-m', $year . '-' . $month);
            $query->where('periode', $date->startOfMonth()->format('Y-m-d'));
        } else {
            $query->where('periode', $query->max('periode'));
        }



        if ($perpage == "All") {
            $perpage = $query->count();
        }

        $query = $query->paginate($perpage);

        return AlihDayaResource::collection($query);
    }
}
