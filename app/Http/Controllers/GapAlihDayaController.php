<?php

namespace App\Http\Controllers;

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

    public function detail(Request $request, $type)
    {
        return Inertia::render('GA/Procurement/AlihDaya/Detail', ['type' => $type, 'type_item' => $request->type_item]);
    }
}
