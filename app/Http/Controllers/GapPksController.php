<?php

namespace App\Http\Controllers;

use App\Exports\PKS\PKSExport;
use App\Imports\PksImport;
use App\Models\GapPks;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Maatwebsite\Excel\Validators\ValidationException;

class GapPksController extends Controller
{
    public function index()
    {
        return Inertia::render('GA/Procurement/PKS/Page');
    }

    public function import(Request $request)
    {
        try {
            DB::beginTransaction();
            (new PksImport)->import($request->file('file'));
            DB::commit();
            return Redirect::back()->with(['status' => 'success', 'message' => 'Import Berhasil']);
        } catch (ValidationException $e) {
            DB::rollBack();
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
        $fileName = 'Data_PKS_' . date('d-m-y') . '.xlsx';
        return (new PKSExport)->download($fileName);
    }


    public function template()
    {
        $path = 'app\public\templates\template_pks.xlsx';

        return response()->download(storage_path($path));
    }

    public function detail(Request $request,$status)
    {
        return Inertia::render('GA/Procurement/PKS/Detail', ['status' => $status, 'action' => $request->action]);
    }

}
