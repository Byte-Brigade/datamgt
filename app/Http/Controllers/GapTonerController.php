<?php

namespace App\Http\Controllers;

use App\Exports\Toners\TonerExport;
use App\Imports\TonerImport;
use App\Models\Branch;
use App\Models\BranchType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Maatwebsite\Excel\Validators\ValidationException;
use Throwable;

class GapTonerController extends Controller
{
    public function index()
    {
        return Inertia::render('GA/Procurement/Toner/Page',[
            'type_names' => BranchType::whereNotIn('type_name',['KF','SFI'])->pluck('type_name')->toArray()
        ]);
    }

    public function import(Request $request)
    {
        try {
            DB::beginTransaction();
            (new TonerImport)->import($request->file('file'));
            DB::commit();

            activity()->enableLogging();
            activity("GapToner")
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

    public function export()
    {
        $fileName = 'Data_Toner_' . date('d-m-y') . '.xlsx';
        return (new TonerExport)->download($fileName);
    }


    public function template()
    {
        $path = 'app\public\templates\template_toner.xlsx';

        return response()->download(storage_path($path));
    }


    public function detail(Request $request, $slug)
    {
        $branch = Branch::with('branch_types')->where('slug', $slug)->firstOrFail();
        return Inertia::render('GA/Procurement/Toner/Detail', ['branch' => $branch]);
    }

}
