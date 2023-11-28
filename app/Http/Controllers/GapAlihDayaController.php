<?php

namespace App\Http\Controllers;

use App\Imports\AlihDayaImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
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
            return redirect(route('gap.alihdayas'))->with(['status' => 'success', 'message' => 'Import Berhasil']);
        } catch (Throwable $e) {
            DB::rollBack();
            dd($e);
            return redirect(route('gap.alihdayas'))->with(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }

    public function detail(Request $request, $type)
    {
        return Inertia::render('GA/Procurement/AlihDaya/Detail', ['type' => $type, 'type_item' => $request->type_item]);
    }

}
