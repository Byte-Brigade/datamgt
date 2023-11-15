<?php

namespace App\Http\Controllers;

use App\Exports\KDO\KdoExport;
use App\Exports\KDO\KdoMobilSheet;
use App\Exports\KDO\KdosExport;
use App\Helpers\PaginationHelper;
use App\Http\Resources\KdoMobilResource;
use App\Imports\KdoImport;
use App\Imports\KdoMobilImport;
use App\Models\Branch;
use App\Models\BranchType;
use App\Models\GapKdo;
use App\Models\GapKdoMobil;
use App\Models\KdoMobilBiayaSewa;
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

    protected array $sortFields = ['branches.branch_code', 'akhir_sewa', 'awal_sewa'];

    public function api(GapKdo $gap_kdo, Request $request)
    {
        $sortFieldInput = $request->input('sort_field', 'branches.branch_code');
        $sortField = in_array($sortFieldInput, $this->sortFields) ? $sortFieldInput : 'branches.branch_code';
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

        $collections = collect([]);
        $year = date('Y');
        foreach ($query as $item) {
            $branch = Branch::find($item->branch_id);

            $biaya_sewa = $item->gap_kdo_mobil->flatMap(function ($mobil) {
                return $mobil->biaya_sewas;
            })->groupBy('periode')->sortKeysDesc()->first();
            $item = [
                'id' => $item->id,
                'branches' => $branch,
                'branch_types' => BranchType::find($branch->branch_type_id),
                'jumlah_kendaraan' => $item->gap_kdo_mobil->unique('nopol')->count(),
                'sewa_perbulan' => isset($biaya_sewa)  ? number_format(
                    $biaya_sewa->sum('value'),
                    0,
                    ',',
                    '.'
                ) : 0,
                'akhir_sewa' => $item->gap_kdo_mobil()->orderBy('akhir_sewa', 'asc')->first()->akhir_sewa,
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


        if ($sortOrder == 'desc') {
            $collections = $collections->sortByDesc($sortField);
        } else {
            $collections = $collections->sortBy($sortField);
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

        $currentYear = date('Y');
        $futureYears = range($currentYear, $currentYear + 10);
        $months = [
            "January", "February", "March", "April", "May", "June", "July",
            "August", "September", "October", "November", "December"
        ];
        return Inertia::render('GA/KDO/Detail', [
            'kdo_mobil' => $kdo_mobil,
            'years' => $futureYears,
            'months' => $months
        ]);
    }

    public function kdo_mobil_store(Request $request, $id)
    {
        $branch = Branch::find($id);
        try {
            GapKdoMobil::create([
                'branch_id' => $request->branch_id,
                'gap_kdo_id' => $request->gap_kdo_id,
                'vendor' => $request->vendor,
                'nopol' => $request->nopol,
                'awal_sewa' => $request->awal_sewa,
                'akhir_sewa' => $request->akhir_sewa,
                'biaya_sewa' => [['periode' => Carbon::create($request->year, $request->month, 1), 'value' => $request->biaya_sewa]],
            ]);

            return redirect(route('gap.kdo.mobil', $branch->branch_code))->with(['status' => 'success', 'message' => 'Data Berhasil disimpan']);
        } catch (Throwable $e) {
            return redirect(route('gap.kdo.mobil', $branch->branch_code))->with(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }
    public function kdo_mobil_update(Request $request, $id)
    {
        $branch = Branch::find($request->branch_id);
        try {
            $gap_kdo_mobil = GapKdoMobil::find($id);
            $gap_kdo_mobil->update([
                'branch_id' => $request->branch_id,
                'gap_kdo_id' => $request->gap_kdo_id,
                'vendor' => $request->vendor,
                'nopol' => $request->nopol,
                'awal_sewa' => $request->awal_sewa,
                'akhir_sewa' => $request->akhir_sewa,
            ]);
            $periode = Carbon::createFromDate($request->year, $request->month, 1)->startOfDay();
            if (is_int($request->biaya_sewa)) {

                KdoMobilBiayaSewa::create([
                    'gap_kdo_mobil_id' => $gap_kdo_mobil->id,
                    'periode' => $periode->format('Y-m-d'),
                    'value' => $request->biaya_sewa
                ]);
            } else {
                $biaya_sewa = KdoMobilBiayaSewa::find($request->biaya_sewa['id']);
                if($request->biaya_sewa['value'] >0) {

                    $biaya_sewa->update(['value' => $request->biaya_sewa['value']]);
                } else {
                    $biaya_sewa->delete();
                }
            }

            return redirect(route('gap.kdo.mobil', $branch->branch_code))->with(['status' => 'success', 'message' => 'Data Berhasil disimpan']);
        } catch (Throwable $e) {
            return redirect(route('gap.kdo.mobil', $branch->branch_code))->with(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }


    public function kdo_mobil_destroy($branch_code, $id)
    {
        try {
            $kdo_mobil = GapKdoMobil::find($id);
            $kdo_mobil->delete();

            return redirect(route('gap.kdo.mobil', $branch_code))->with(['status' => 'success', 'message' => 'Data Berhasil dihapus']);
        } catch (Throwable $e) {
            return redirect(route('gap.kdo.mobil', $branch_code))->with(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }


    public function import(Request $request)
    {
        try {
            (new KdoImport)->import($request->file('file')->store('temp'));

            return redirect(route('gap.kdo'))->with(['status' => 'success', 'message' => 'Import Berhasil']);
        } catch (Throwable $e) {
            dd($e);
            return redirect(route('gap.kdo'))->with(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }
    public function kdo_mobil_import(Request $request)
    {

        $branch = Branch::find($request->branch_id);
        try {
            (new KdoMobilImport($request->branch_id, $request->gap_kdo_id))->import($request->file('file')->store('temp'));

            return redirect(route('gap.kdo.mobil', $branch->branch_code))->with(['status' => 'success', 'message' => 'Import Berhasil']);
        } catch (Throwable $e) {
            dd($e);
            return redirect(route('gap.kdo.mobil', $branch->branch_code))->with(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }

    public function export()
    {
        $fileName = 'Data_KDO_' . date('d-m-y') . '.xlsx';
        return (new KdosExport)->download($fileName);
    }

    public function kdo_mobil_export(Request $request)
    {

        $fileName = 'Template_Import_KDO_Mobil' . date('d-m-y') . '.xlsx';
        return (new KdoMobilSheet($request->gap_kdo_id))->download($fileName);
    }
}
