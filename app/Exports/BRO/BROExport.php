<?php

namespace App\Exports\BRO;

use App\Models\GapToner;
use App\Models\InfraBro;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class BROExport implements WithMultipleSheets
{
    use Exportable;
    public function sheets(): array
    {
        $latestPeriode = InfraBro::orderBy('periode')->first();
        $data = InfraBro::where('periode', $latestPeriode->periode)->get();
        $latestPeriode = Carbon::parse($latestPeriode->periode)->format('F Y');
        return [
            // new SummarySheet($data, $latestPeriode),
            new DataSheet($data, $latestPeriode),
        ];
    }
}
