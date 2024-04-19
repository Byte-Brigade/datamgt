<?php

namespace App\Http\Controllers;

use App\Exports\AlihDaya\AlihDayaExport;
use App\Imports\AlihDayaImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Maatwebsite\Excel\Validators\ValidationException;
use Throwable;

class GapAlihDayaController extends Controller
{
    public function index()
    {
        return Inertia::render('GA/Procurement/AlihDaya/Page');
    }

    public function import(Request $request)
    {
        try {
            DB::beginTransaction();
            (new AlihDayaImport)->import($request->file('file'));
            DB::commit();

            activity("GapAlihDaya")
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
        $fileName = 'Data_AlihDaya_' . date('d-m-y') . '.xlsx';
        return (new AlihDayaExport)->download($fileName);
    }

    public function template()
    {
        $path = 'app\public\templates\template_alih_daya.xlsx';

        return response()->download(storage_path($path));
    }

    public function detail(Request $request, $type)
    {
        return Inertia::render('GA/Procurement/AlihDaya/Detail', ['type' => $type, 'type_item' => $request->type_item, 'periode' => ['startDate' => $request->startDate, 'endDate' => $request->endDate]]);
    }
}
