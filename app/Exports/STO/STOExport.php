<?php

namespace App\Exports\STO;

use App\Models\GapPerdin;
use App\Models\GapSto;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class STOExport implements WithMultipleSheets
{
    use Exportable;
    public function sheets(): array
    {
        $latestPeriode = GapSto::with(['branches','gap_assets'])->orderBy('periode')->first();
        $data = GapSto::where('periode', $latestPeriode->periode)->get();
        $latestPeriode = Carbon::parse($latestPeriode->periode)->format('F Y');
        return [
            new DataSheet($data, $latestPeriode),
            // new StaffSummarySheet($data, $latestPeriode),
            // new SummarySheet($data, $latestPeriode),
        ];
    }
}
