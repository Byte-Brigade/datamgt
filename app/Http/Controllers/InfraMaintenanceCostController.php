<?php

namespace App\Http\Controllers;

use App\Exports\MaintenanceCost\MaintenanceCostExport;
use App\Imports\MaintenanceCostImport;
use App\Models\Branch;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Throwable;

class InfraMaintenanceCostController extends Controller
{
    public function index()
    {
        $branches = Branch::get();
        return Inertia::render('GA/Infra/MaintenanceCost/Page', ['branches' => $branches]);
    }

    public function import(Request $request)
    {
        try {
            (new MaintenanceCostImport)->import($request->file('file'));

            return redirect(route('infra.maintenance-costs'))->with(['status' => 'success', 'message' => 'Import Berhasil']);
        } catch (Throwable $e) {
            dd($e);
            return redirect(route('infra.maintenance-costs'))->with(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }

    public function export()
    {
        $fileName = 'Data_Maintenance_Cost_' . date('d-m-y') . '.xlsx';
        return (new MaintenanceCostExport)->download($fileName);
    }

    public function template()
    {
        $path = 'app\public\templates\template_maintenance_cost.xlsx';

        return response()->download(storage_path($path));
    }
}
