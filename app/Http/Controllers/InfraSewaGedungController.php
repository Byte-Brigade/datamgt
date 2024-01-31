<?php

namespace App\Http\Controllers;

use App\Exports\SewaGedung\SewaGedungExport;
use App\Http\Resources\SewaGedungResource;
use App\Imports\SewaGedungImport;
use App\Models\Branch;
use App\Models\BranchType;
use App\Models\InfraSewaGedung;
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
        DB::beginTransaction();
        try {
            (new SewaGedungImport)->import($request->file('file'));
            DB::commit();
            return Redirect::back()->with(['status' => 'success', 'message' => 'Import Berhasil']);
        } catch (ValidationException $e) {
            $errorString = '';
            /** @var array $messages */
            foreach ($e->errors() as $field => $messages) {
                foreach ($messages as $message) {
                    $errorString .= "Field {$field}: {$message} ";
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
        $fileName = 'Data_Sewa_Gedung_' . date('d-m-y') . '.xlsx';
        return (new SewaGedungExport)->download($fileName);
    }
    public function template()
    {
        $path = 'app\public\templates\template_sewa_gedung.xlsx';

        return response()->download(storage_path($path));
    }
}
