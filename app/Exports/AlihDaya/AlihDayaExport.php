<?php

namespace App\Exports\AlihDaya;

use App\Models\GapAlihDaya;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class AlihDayaExport implements WithMultipleSheets
{
    use Exportable;
    public function sheets(): array
    {
        $latestPeriode = GapAlihDaya::orderBy('periode')->first();
        $data = GapAlihDaya::where('periode', $latestPeriode->periode)->get();
        $latestPeriode = Carbon::parse($latestPeriode->periode)->format('F Y');
        return [
            new DataSheet($data, $latestPeriode),
            // new SummarySheet($data, $latestPeriode),
        ];
    }
}
