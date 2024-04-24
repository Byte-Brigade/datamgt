<?php

namespace App\Http\Controllers;

use App\Exports\SewaGedung\SewaGedungExport;
use App\Http\Resources\SewaGedungResource;
use App\Imports\SewaGedungImport;
use App\Models\Branch;
use App\Models\BranchType;
use App\Models\InfraSewaGedung;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Maatwebsite\Excel\Validators\ValidationException;
use Throwable;

class InfraSewaGedungController extends Controller
{
    public function index()
    {
        $branches = Branch::get();
        return Inertia::render('GA/Infra/SewaGedung/Page', [
            'branches' => $branches,
            'status_gedung' => ["Milik", "Sewa", "Pinjam Pakai"],
            'type_names' => BranchType::whereNotIn('type_name', ['KF', 'SFI','KP'])->pluck('type_name')->toArray()
        ]);
    }

    public function import(Request $request)
    {
        try {
            DB::beginTransaction();
            (new SewaGedungImport)->import($request->file('file'));
            DB::commit();

            activity()->enableLogging();
            activity("InfraSewaGedung")
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

    public function update(Request $request, $id)
    {
        try {
            $sewa_gedungs = InfraSewaGedung::find($id);

            $sewa_gedungs->update([
                'branch_id' => $request->branch_id,
                'status_kepemilikan' => $request->status_kepemilikan,
                'jangka_waktu' => intval($request->jangka_waktu),
                'open_date' => Carbon::parse($request->open_date)->format('Y-m-d'),
                'jatuh_tempo' => Carbon::parse($request->jatuh_tempo)->format('Y-m-d'),
                'owner' => $request->owner,
                'biaya_per_tahun' => $request->biaya_per_tahun,
                'total_biaya' => $request->total_biaya,
            ]);

            return Redirect::back()->with(['status' => 'success', 'message' => "Data berhasil diupdate"]);
        } catch (\Throwable $th) {
            return Redirect::back()->with(['status' => 'failed', 'message' => $th->getMessage()]);
        }
    }

    public function export()
    {
        $fileName = 'Data_Sewa_Gedung_' . date('d-m-y') . '.xlsx';
        return (new SewaGedungExport)->download($fileName);
    }
    public function template()
    {
        $path = 'app\public\templates\template_sewa_gedung.xlsx';

        return response()->download(storage_path($path));
    }
}
