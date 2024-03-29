<?php

namespace App\Http\Controllers\API;

use App\Helpers\PaginationHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\AparDetailResource;
use App\Http\Resources\AparResource;
use App\Http\Resources\Ops\BranchResource;
use App\Http\Resources\Ops\EmployeeResource;
use App\Http\Resources\PajakReklameResource;
use App\Http\Resources\SpecimentResource;
use App\Models\Branch;
use App\Models\Employee;
use App\Models\OpsApar;
use App\Models\OpsAparDetail;
use App\Models\OpsPajakReklame;
use App\Models\OpsSkbirtgs;
use App\Models\OpsSkOperasional;
use App\Models\OpsSpeciment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OpsApiController extends Controller
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
        if (isset($input['type_name'])) {
            $type_name = $input['type_name'];
            $query = $query->whereHas('branch_types', function ($q) use ($type_name) {

                return $q->whereIn('type_name', $type_name);
            });
        }


        if (isset($request->area)) {
            $query = $query->whereIn('area', $request->area);
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


    public function check_db_status()
    {
        $status = "";
        try {
            DB::connection('sqlsrv')->table('dbo.opsstaging')->get();
            $status = "Online";
        } catch (\Throwable $th) {
            $status = "Offline";
        }

        return response()->json([
            'status' => $status,
        ]);
    }

    public function employees(Employee $employees, Request $request)
    {
        $sortFieldInput = $request->input('sort_field', 'employee_id');
        $sortOrder = $request->input('sort_order', 'asc');
        $searchInput = $request->search;
        $query = $employees->select('employees.*')->orderBy($sortFieldInput, $sortOrder)->orderBy('employee_id', 'asc')
            ->join('branches', 'employees.branch_id', 'branches.id')
            ->join('employee_positions', 'employees.position_id', 'employee_positions.id');
        $perpage = $request->perpage ?? 10;

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

        if (!is_null($request->input('branches_branch_name'))) {
            $query = $query->whereHas('branches', function ($q) use ($request) {
                $q->whereIn('branch_name', $request->get('branches_branch_name'));
            });
        }

        if ($perpage == "All") {
            $perpage = $query->count();
        }

        $query = $query->paginate($perpage);

        return EmployeeResource::collection($query);
    }

    public function apars(OpsApar $ops_apar, Request $request)
    {
        $sortFieldInput = $request->input('sort_field') ?? 'branches.branch_code';
        $sortOrder = $request->input('sort_order', 'asc');
        $searchInput = $request->search;
        $query = $ops_apar
            ->join('branches', 'ops_apars.branch_id', 'branches.id')
            ->join('branch_types', 'branches.branch_type_id', 'branch_types.id');

        $perpage = $request->perpage ?? 15;

        if (!is_null($searchInput)) {
            $searchQuery = "%$searchInput%";
            $query = $query->where(function ($q) use ($searchQuery) {
                return $q->where('branch_name', 'like', $searchQuery)
                    ->orWhere('branch_code', 'like', $searchQuery)
                    ->orWhere('type_name', 'like', $searchQuery);
            });
        }
        $query = $query->get();

        $collections = $query->map(function ($apar) {
            $apar->branch_name = $apar->branches->branch_name;
            $apar->branch_code = $apar->branches->branch_code;
            $apar->type_name = $apar->branches->branch_types->type_name;
            return $apar;
        })->groupBy('branch_name')->map(function ($apar, $branch_name) {
            return [
                'branch_name' => $branch_name,
                'branch_code' => $apar->first()->branch_code,
                'type_name' => $apar->first()->type_name,
                'branch_id' => $apar->first()->branch_id,
                'jumlah_tabung' => $apar->count(),
                'slug' => $apar->first()->slug
            ];
        });

        if ($perpage == "All") {
            $perpage = $collections->count();
        }

        if ($sortOrder == "desc") {
            $collections = $collections->sortByDesc($sortFieldInput);
        } else {
            $collections = $collections->sortBy($sortFieldInput);
        }

        return PaginationHelper::paginate($collections, $perpage);
    }

    public function apar_details(OpsApar $ops_apar, Request $request, $id)
    {
        $sortFieldInput = $request->input('sort_field', 'id');
        $sortOrder = $request->input('sort_order', 'asc');
        $searchInput = $request->search;
        $query = $ops_apar->orderBy($sortFieldInput, $sortOrder);

        $query = $query->whereHas('branches', function ($q) use ($id) {
            return $q->where('id', $id);
        });

        $perpage = $request->perpage ?? 10;

        if (!is_null($searchInput)) {
            $searchQuery = "%$searchInput%";
            $query = $query->where('id', 'like', $searchQuery);
        }
        if ($perpage == "All") {
            $perpage = $query->count();
        }

        $query = $query->paginate($perpage);

        return AparDetailResource::collection($query);
    }




    public function pajak_reklames(OpsPajakReklame $ops_pajak_reklame, Request $request)
    {
        $sortFieldInput = $request->input('sort_field') ?? 'branch_code';
        $sortOrder = $request->input('sort_order', 'asc');
        $searchInput = $request->search;
        $query = $ops_pajak_reklame->select('ops_pajak_reklames.*')->orderBy($sortFieldInput, $sortOrder)
            ->join('branches', 'ops_pajak_reklames.branch_id', 'branches.id')
            ->join('branch_types', 'branch_types.id', 'branches.branch_type_id');
        $perpage = $request->perpage ?? 10;

        if (!is_null($searchInput)) {
            $searchQuery = "%$searchInput%";
            $query = $query->where(function ($q) use ($searchQuery) {
                return $q->where('periode_awal', 'like', $searchQuery)
                    ->orWhere('periode_akhir', 'like', $searchQuery)
                    ->orWhere('branch_code', 'like', $searchQuery)
                    ->orWhere('branch_name', 'like', $searchQuery)
                    ->orWhere('type_name', 'like', $searchQuery);
            });
        }

        $input = $request->all();
        if (isset($input['type_name'])) {
            $query = $query->whereIn('type_name', $input['type_name']);
        }
        if ($perpage == "All") {
            $perpage = $query->count();
        }

        $query = $query->paginate($perpage);

        return PajakReklameResource::collection($query);
    }


    public function skbirtgs(OpsSkbirtgs $ops_skbirtgs, Request $request)
    {
        $sortFieldInput = $request->input('sort_field') ?? 'no_surat';
        $sortOrder = $request->input('sort_order', 'asc');
        $searchInput = $request->search;

        $query = $ops_skbirtgs->select('ops_skbirtgs.*')->orderBy($sortFieldInput, $sortOrder)
            ->join('branches', 'ops_skbirtgs.branch_id', 'branches.id');

        $perpage = $request->perpage ?? 15;


        if (!is_null($searchInput)) {
            $searchQuery = "%$searchInput%";
            $query = $query->where(function ($q) use ($searchQuery) {
                return $q->where('no_surat', 'like', $searchQuery)
                    ->orWhere('branch_name', 'like', $searchQuery)
                    ->orWhere('name', 'like', $searchQuery);
            });
        }

        $query = $query->get();
        $collections = collect([]);

        // Nilai default untuk item ketika tidak ada penerima kuasa
        foreach ($query as $item) {

            // Nilai default untuk item ketika tidak ada penerima kuasa
            $defaultValues = [
                'id' => $item->id,
                'no_surat' => str_contains($item->no_surat, 'SK') ? $item->no_surat : '-',
                'branch_id' => $item->branch_id,
                'status' => $item->status,
                'file' => $item->file,
                'penerima_kuasa' => str_contains($item->status, 'Kanwil') ? $item->status : 'Centralized - SKN',
                'branch_name' => $item->branches->branch_name
            ];
            $penerima_kuasa = $item->penerima_kuasa()->get();

            // Jika ada penerima kuasa
            if ($penerima_kuasa->count() > 0) {
                // Buat array sementara untuk menampung item yang telah diubah posisinya
                $tempCollections = [];

                // Jika BM ada, letakkan di posisi pertama
                $bmAdded = false;

                foreach ($penerima_kuasa as $penerima) {
                    $tempItem = array_merge($defaultValues, [
                        'id' => $item->id,
                        'no_surat' => str_contains($item->no_surat, 'SK') ? $item->no_surat : '-',
                        'branch_id' => $item->branch_id,
                        'status' => $item->status,
                        'file' => $item->file,
                        'penerima_kuasa' => '[' . $penerima->getPosition() . ']' . ' ' . $penerima->name,
                        'branch_name' => $item->branches->branch_name
                    ]);

                    // Jika 'BM' belum ditambahkan dan saat ini adalah 'BM',
                    // tambahkan 'BM' ke koleksi di posisi pertama
                    if (!$bmAdded && $penerima->getPosition() === 'BM') {
                        array_unshift($tempCollections, $tempItem);
                        $bmAdded = true;
                    } else {
                        // Tambahkan item ke $tempCollections untuk swap nanti
                        $tempCollections[] = $tempItem;
                    }
                }


                // Menukar posisi item pada $tempCollections (mulai dari item ke-9)
                $count = count($tempCollections);
                for ($i = 8; $i < $count; $i += 2) {
                    if ($i + 1 < $count) {
                        $temp = $tempCollections[$i];
                        $tempCollections[$i] = $tempCollections[$i + 1];
                        $tempCollections[$i + 1] = $temp;
                    }
                }

                // Menambahkan item yang telah ditukar ke $collections
                $collections = $collections->merge(collect($tempCollections));
            } else {
                // Jika tidak ada penerima kuasa, tambahkan item dengan nilai default (null)
                $collections->push($defaultValues);
            }
        }


        if ($perpage == "All") {
            $perpage = $collections->count();
        }


        return response()->json(PaginationHelper::paginate($collections, $perpage));
    }

    public function sk_operasionals(OpsSkOperasional $ops_sk_operasional, Request $request)
    {
        $sortFieldInput = $request->input('sort_field') ?? 'branches.branch_code';
        $sortOrder = $request->input('sort_order', 'asc');
        $searchInput = $request->search;
        $query = $ops_sk_operasional->select('ops_sk_operasionals.*')->orderBy($sortFieldInput, $sortOrder)
            ->join('branches', 'ops_sk_operasionals.branch_id', 'branches.id');
        $perpage = $request->perpage ?? 15;

        if (!is_null($searchInput)) {
            $searchQuery = "%$searchInput%";
            $query = $query->where('no_surat', 'like', $searchQuery)
                ->orWhere('branch_code', 'like', $searchQuery)
                ->orWhere('branch_name', 'like', $searchQuery);
        }
        $query = $query->get();
        $collections = collect([]);

        // Nilai default untuk item ketika tidak ada penerima kuasa


        foreach ($query as $item) {

            // Nilai default untuk item ketika tidak ada penerima kuasa
            $defaultValues = [
                'id' => $item->id,
                'no_surat' => $item->no_surat,
                'branch_id' => $item->branch_id,
                'expiry_date' => $item->expiry_date,
                'note' => $item->note,
                'file' => $item->file,
                'penerima_kuasa' => '-',
                'branch_name' => $item->branches->branch_name,
                'branch_code' => $item->branches->branch_code,
            ];
            $penerima_kuasa = $item->penerima_kuasa()->get();

            // Jika ada penerima kuasa
            if ($penerima_kuasa->count() > 0) {
                // Buat array sementara untuk menampung item yang telah diubah posisinya
                $tempCollections = [];

                // Jika BM ada, letakkan di posisi pertama
                $bmAdded = false;

                foreach ($penerima_kuasa as $penerima) {
                    $tempItem = array_merge($defaultValues, [
                        'id' => $item->id,
                        'no_surat' => str_contains($item->no_surat, 'SK') ? $item->no_surat : '-',
                        'branch_id' => $item->branch_id,
                        'status' => $item->status,
                        'file' => $item->file,
                        'penerima_kuasa' => '[' . $penerima->getPosition() . ']' . ' ' . $penerima->name,
                        'branch_name' => $item->branches->branch_name,
                        'branch_code' => $item->branches->branch_code
                    ]);

                    // Jika 'BM' belum ditambahkan dan saat ini adalah 'BM',
                    // tambahkan 'BM' ke koleksi di posisi pertama
                    if (!$bmAdded && $penerima->getPosition() === 'BM') {
                        array_unshift($tempCollections, $tempItem);
                        $bmAdded = true;
                    } else {
                        // Tambahkan item ke $tempCollections untuk swap nanti
                        $tempCollections[] = $tempItem;
                    }
                }

                // Menukar posisi item pada $tempCollections (mulai dari item ke-9)
                $count = count($tempCollections);
                for ($i = 8; $i < $count; $i += 2) {
                    if ($i + 1 < $count) {
                        $temp = $tempCollections[$i];
                        $tempCollections[$i] = $tempCollections[$i + 1];
                        $tempCollections[$i + 1] = $temp;
                    }
                }

                // Menambahkan item yang telah ditukar ke $collections
                $collections = $collections->merge(collect($tempCollections));
            } else {
                // Jika tidak ada penerima kuasa, tambahkan item dengan nilai default (null)
                $collections->push($defaultValues);
            }
        }

        if ($perpage == "All") {
            $perpage = $collections->count();
        }

        return response()->json(PaginationHelper::paginate($collections, $perpage));
    }

    public function speciments(OpsSpeciment $ops_speciment, Request $request)
    {
        $sortFieldInput = $request->input('sort_field') ?? 'branches.branch_code';
        $sortOrder = $request->input('sort_order', 'asc');
        $searchInput = $request->search;
        $query = $ops_speciment->select('ops_speciments.*')->orderBy($sortFieldInput, $sortOrder)
            ->orderBy('branches.branch_code', 'asc')
            ->join('branches', 'ops_speciments.branch_id', 'branches.id');
        $perpage = $request->perpage ?? 10;

        if (!is_null($searchInput)) {
            $searchQuery = "%$searchInput%";
            $query = $query->where('tgl_speciment', 'like', $searchQuery);
        }
        if ($perpage == "All") {
            $perpage = $query->count();
        }

        $query = $query->paginate($perpage);

        return SpecimentResource::collection($query);
    }
}
