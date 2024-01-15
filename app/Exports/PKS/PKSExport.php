<?php

namespace App\Exports\PKS;

use App\Models\GapPks;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class PKSExport implements WithMultipleSheets
{
    use Exportable;
    public function sheets(): array
    {
        $latestPeriode = GapPks::orderBy('periode')->first();
        $data = GapPks::where('periode', $latestPeriode->periode)->get();
        $latestPeriode = Carbon::parse($latestPeriode->periode)->format('F Y');
        return [
            new DataSheet($data, $latestPeriode),
            // new SummarySheet($data, $latestPeriode),
        ];
    }
}
