<?php

namespace App\Exports\Perdin;

use App\Models\GapPerdin;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class PerdinExport implements WithMultipleSheets
{
    use Exportable;
    public function sheets(): array
    {
        $latestPeriode = GapPerdin::orderBy('periode')->first();
        $data = GapPerdin::where('periode', $latestPeriode->periode)->get();
        $latestPeriode = Carbon::parse($latestPeriode->periode)->format('F Y');
        return [
            new DivisionSummarySheet($data, $latestPeriode),
            new StaffSummarySheet($data, $latestPeriode),
            // new SummarySheet($data, $latestPeriode),
        ];
    }
}
