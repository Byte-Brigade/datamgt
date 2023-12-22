<?php

namespace App\Http\Controllers;

use App\Imports\TonerImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Throwable;

class GapTonerController extends Controller
{
    public function index()
    {
        return Inertia::render('GA/Procurement/Toner/Page');
    }

    public function import(Request $request)
    {
        try {
            DB::beginTransaction();
            (new TonerImport)->import($request->file('file'));
            DB::commit();
            return redirect(route('gap.toners'))->with(['status' => 'success', 'message' => 'Import Berhasil']);
        } catch (Throwable $e) {
            DB::rollBack();
            dd($e);
            return redirect(route('gap.toners'))->with(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }

    public function detail(Request $request, $branch_code)
    {
        return Inertia::render('GA/Procurement/Toner/Detail', ['branch_code' => $branch_code]);
    }

}
