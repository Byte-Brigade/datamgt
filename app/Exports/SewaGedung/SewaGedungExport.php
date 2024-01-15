<?php

namespace App\Exports\SewaGedung;

use App\Models\GapPerdin;
use App\Models\GapSto;
use App\Models\InfraSewaGedung;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class SewaGedungExport implements WithMultipleSheets
{
    use Exportable;
    public function sheets(): array
    {
        $latestPeriode = InfraSewaGedung::orderBy('periode')->first();
        $data = InfraSewaGedung::where('periode', $latestPeriode->periode)->get();
        $latestPeriode = Carbon::parse($latestPeriode->periode)->format('F Y');
        return [
            new DataSheet($data, $latestPeriode),
            // new StaffSummarySheet($data, $latestPeriode),
            // new SummarySheet($data, $latestPeriode),
        ];
    }
}
