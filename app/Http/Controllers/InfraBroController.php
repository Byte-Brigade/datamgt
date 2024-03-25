<?php

namespace App\Http\Controllers;

use App\Exports\BRO\BROExport;
use App\Imports\BroImport;
use App\Models\Branch;
use App\Models\BranchType;
use App\Models\InfraBro;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Throwable;

class InfraBroController extends Controller
{
    public function index()
    {
        $branches = Branch::get();


        return Inertia::render('GA/Infra/BRO/Page', [

            'branches' => $branches,
            'type_names' => BranchType::whereNotIn('type_name', ['SFI'])->pluck('type_name')->toArray(),
            'status_bro' => InfraBro::pluck('status')->unique()->toArray(),
        ]);
    }

    public function import(Request $request)
    {
        try {
            (new BroImport)->import($request->file('file'));

            return redirect(route('infra.bros'))->with(['status' => 'success', 'message' => 'Import Berhasil']);
        } catch (Throwable $e) {
            dd($e);
            return redirect(route('infra.bros'))->with(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }

    public function export()
    {
        $fileName = 'Data_BRO_' . date('d-m-y') . '.xlsx';
        return (new BROExport)->download($fileName);
    }

    public function template()
    {
        $path = 'app\public\templates\template_bro.xlsx';

        return response()->download(storage_path($path));
    }
}
