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
use App\Models\KdoMobilBiayaSewa;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Maatwebsite\Excel\Validators\ValidationException;
use Throwable;

class GapKdoController extends Controller
{
    public function index()
    {

        $branches = Branch::get();
        return Inertia::render('GA/Procurement/KDO/Page', ['branches' => $branches, 'type_names' => BranchType::whereNotIn('type_name',['KF','SFI'])->pluck('type_name')->toArray()]);
    }
    public function vendor($vendor)
    {

        $branches = Branch::get();
        return Inertia::render('GA/Procurement/KDO/Vendor', ['branches' => $branches, 'vendor' => $vendor]);
    }

    public function kdo_mobil(Request $request, $slug)
    {
        $kdo_mobil = GapKdo::whereHas('branches', function ($query) use ($slug) {
            $query->where('slug', $slug);
        })->with(['branches', 'biaya_sewas'])->first();

        $currentYear = date('Y');
        $futureYears = range($currentYear, $currentYear + 10);
        $months = [
            "January", "February", "March", "April", "May", "June", "July",
            "August", "September", "October", "November", "December"
        ];
        return Inertia::render('GA/Procurement/KDO/Detail', [
            'kdo_mobil' => $kdo_mobil,
            'years' => $futureYears,
            'months' => $months,
            'periode' => $request->periode,
            'vendor' => !is_null($request->vendor) ? $request->vendor : null,
        ]);
    }

    public function kdo_mobil_store(Request $request, $id)
    {
        $branch = Branch::find($id);
        try {
            GapKdo::create([
                'branch_id' => $request->branch_id,
                'gap_kdo_id' => $request->gap_kdo_id,
                'vendor' => $request->vendor,
                'nopol' => $request->nopol,
                'awal_sewa' => $request->awal_sewa,
                'akhir_sewa' => $request->akhir_sewa,
                'biaya_sewa' => [['periode' => Carbon::create($request->year, $request->month, 1), 'value' => $request->biaya_sewa]],
            ]);

            return redirect(route('gap.kdos.mobil', $branch->branch_code))->with(['status' => 'success', 'message' => 'Data Berhasil disimpan']);
        } catch (Throwable $e) {
            return redirect(route('gap.kdos.mobil', $branch->branch_code))->with(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }
    public function kdo_mobil_update(Request $request, $id)
    {
        $branch = Branch::find($request->branch_id);
        try {
            // dd($request->all());
            $gap_kdo_mobil = GapKdo::find($id);
            $gap_kdo_mobil->update([
                'branch_id' => $request->branch_id,
                'gap_kdo_id' => $request->gap_kdo_id,
                'vendor' => $request->vendor,
                'nopol' => $request->nopol,
                'awal_sewa' => $request->awal_sewa,
                'akhir_sewa' => $request->akhir_sewa,
            ]);
            $periode = Carbon::createFromFormat('Y-m', $request->periode)->startOfMonth()->format('Y-m-d');

            KdoMobilBiayaSewa::updateOrCreate(
                [
                    'gap_kdo_id' => $gap_kdo_mobil->id,
                    'periode' => $periode
                ],
                [
                    'gap_kdo_id' => $gap_kdo_mobil->id,
                    'periode' => $periode,
                    'value' => (int) $request->biaya_sewa
                ]
            );


            return Redirect::back()->with(['status' => 'success', 'message' => 'Data Berhasil disimpan']);
        } catch (Throwable $e) {
            return Redirect::back()->with(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }


    public function kdo_mobil_destroy(Request $request, $id)
    {
        try {
            $periode = Carbon::createFromFormat('Y-m', $request->periode)->startOfMonth()->format('Y-m-d');
            $biaya_sewa = KdoMobilBiayaSewa::where('gap_kdo_id', $id)->where('periode', $periode)->first();

            if (isset($biaya_sewa)) {
                GapKdo::find($id)->delete();
                $biaya_sewa->delete();
            } else {
                throw new Exception("Biaya sewa pada bulan ".$periode. ' belum ada');
            }
            return Redirect::back()->with(['status' => 'success', 'message' => 'Data Berhasil dihapus']);
        } catch (Throwable $e) {
            return Redirect::back()->with(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }


    public function template()
    {
        $path = 'app\public\templates\template_kdo.xlsx';

        return response()->download(storage_path($path));
    }

    public function import(Request $request)
    {
        try {
            (new KdoImport)->import($request->file('file'));

            activity()->enableLogging();
            activity("GapKdo")
                ->event("imported")
                ->log("This model has been imported");

            return Redirect::back()->with(['status' => 'success', 'message' => 'Import Berhasil']);
        } catch (ValidationException $e) {
            $errorString = '';
            /** @var array $messages */
            foreach ($e->errors() as $field => $messages) {
                foreach ($messages as $message) {
                    $errorString .= "{$field}: {$message},";
                }
            }
            $errorString = trim($errorString);

            return Redirect::back()->with(['status' => 'failed', 'message' => $errorString]);
        } catch (\Throwable $th) {
            return Redirect::back()->with(['status' => 'failed', 'message' => $th->getMessage()]);
        }
    }
    public function kdo_mobil_import(Request $request)
    {

        $branch = Branch::find($request->branch_id);
        try {
            (new KdoMobilImport($request->branch_id, $request->gap_kdo_id))->import($request->file('file'));

            return Redirect::back()->with(['status' => 'success', 'message' => 'Import Berhasil']);
        } catch (ValidationException $e) {
            $errorString = '';
            /** @var array $messages */
            foreach ($e->errors() as $field => $messages) {
                foreach ($messages as $message) {
                    $errorString .= "{$field}: {$message},";
                }
            }
            $errorString = trim($errorString);

            return Redirect::back()->with(['status' => 'failed', 'message' => $errorString]);
        } catch (\Throwable $th) {
            return Redirect::back()->with(['status' => 'failed', 'message' => $th->getMessage()]);
        }
    }

    public function export()
    {
        $fileName = 'Data_KDO_' . date('d-m-y') . '.xlsx';
        return (new KdosExport)->download($fileName);
    }

    public function kdo_mobil_template(Request $request)
    {

        $fileName = 'Template_Import_KDO_Mobil' . date('d-m-y') . '.xlsx';
        return (new KdoMobilSheet($request->gap_kdo_id))->download($fileName);
    }
}
