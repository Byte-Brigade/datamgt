<?php

namespace App\Http\Controllers;

use App\Helpers\PaginationHelper;
use App\Http\Resources\KdoMobilResource;
use App\Imports\KdoMobilImport;
use App\Models\Branch;
use App\Models\GapKdo;
use App\Models\GapKdoMobil;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Throwable;

class GapKdoController extends Controller
{
    public function index()
    {
        $branchesProps = Branch::get();
        return Inertia::render('GA/KDO/Page', ['branches' => $branchesProps]);
    }

    protected array $sortFields = ['branches.branch_name'];

    public function api(GapKdo $gap_kdo, Request $request)
    {
        $sortField = 'id';
        $sortOrder = $request->input('sort_order', 'asc');
        $searchInput = $request->search;
        $query = $gap_kdo->orderBy($sortField, $sortOrder);

        $perpage = $request->perpage ?? 15;

        if (!is_null($searchInput)) {
            $searchQuery = "%$searchInput%";
            $query = $query->where('id', 'like', $searchQuery);
        }
        $query = $query->get();

        $collections = collect([]);
        $year = date('Y');
        foreach ($query as $item) {
            $item = [
                'id' => $item->id,
                'branches' => $item->branches,
                'jumlah_kendaraan' => $item->gap_kdo_mobil->unique('nopol')->count(),
                'sewa_perbulan' => "Rp " . number_format($item->gap_kdo_mobil->flatMap(function ($mobil) {
                    $mobil->biaya_sewa = collect($mobil->biaya_sewa);
                    return $mobil->biaya_sewa;
                })->groupBy('periode')->sortKeysDesc()->first()->sum('value'), 0, ',', '.'),
                'jatuh_tempo' => $item->gap_kdo_mobil()->orderBy('akhir_sewa', 'asc')->first()->akhir_sewa,
                'sewa_kendaraan' => collect(range(1, 12))->map(function ($num) use ($item, $year) {
                    $value = $item->gap_kdo_mobil->flatMap(function ($mobil) {
                        $mobil->biaya_sewa = collect($mobil->biaya_sewa);
                        return $mobil->biaya_sewa;
                    })->filter(function ($biaya) use ($year, $num) {
                        $biaya = collect($biaya);
                        return Carbon::parse($biaya['periode'])->year == $year && Carbon::parse($biaya['periode'])->month == $num;
                    })->sum('value');
                    if ($value) {

                        return [strtolower(date('F', mktime(0, 0, 0, $num, 1))) => "Rp " . number_format($value, 0, ',', '.')];
                    }
                })->filter(function ($value) {
                return $value != null;
            })->flatMap(function ($data) {
                return $data;
            }),
            ];

            $collections->push($item);
        }

        return response()->json(PaginationHelper::paginate($collections->unique('branches.branch_code'), $perpage));
    }

    public function api_kdo_mobil(GapKdoMobil $gap_kdo_mobil, Request $request, $id)
    {
        $sortFieldInput = $request->input('sort_field', 'id');
        $sortField = in_array($sortFieldInput, $this->sortFields) ? $sortFieldInput : 'id';
        $sortOrder = $request->input('sort_order', 'asc');
        $searchInput = $request->search;
        $query = $gap_kdo_mobil->where('gap_kdo_id', $id)->orderBy($sortField, $sortOrder);

        $perpage = $request->perpage ?? 15;

        if (!is_null($searchInput)) {
            $searchQuery = "%$searchInput%";
            $query = $query->where('id', 'like', $searchQuery);
        }
        $data = $query->paginate($perpage);
        return KdoMobilResource::collection($data);
    }

    public function kdo_mobil($branch_code)
    {
        $kdo_mobil = GapKdo::whereHas('branches', function ($query) use ($branch_code) {
            $query->where('branch_code', $branch_code);
        })->with(['gap_kdo_mobil', 'branches'])->first();

        return Inertia::render('GA/KDO/Detail', [
            'kdo_mobil' => $kdo_mobil
        ]);
    }


    public function import(Request $request)
    {
        try {
            (new KdoMobilImport)->import($request->file('file')->store('temp'));

            return redirect(route('gap.kdo'))->with(['status' => 'success', 'message' => 'Import Berhasil']);
        } catch (Throwable $e) {
            dd($e);
            return redirect(route('gap.kdo'))->with(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }
}
