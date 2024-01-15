<?php

namespace App\Exports\MaintenanceCost;

use App\Models\GapToner;
use App\Models\InfraBro;
use App\Models\InfraMaintenanceCost;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class MaintenanceCostExport implements WithMultipleSheets
{
    use Exportable;
    public function sheets(): array
    {
        $latestPeriode = InfraMaintenanceCost::orderBy('periode')->first();
        $data = InfraMaintenanceCost::with('branches')->where('periode', $latestPeriode->periode)->get();
        $latestPeriode = Carbon::parse($latestPeriode->periode)->format('F Y');
        return [
            // new SummarySheet($data, $latestPeriode),
            new DataSheet($data, $latestPeriode),
        ];
    }
}
