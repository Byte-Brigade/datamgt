<?php

namespace App\Exports\Disnaker;

use App\Models\GapDisnaker;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class DisnakerExport implements WithMultipleSheets
{
    use Exportable;
    public function sheets(): array
    {
        $latestPeriode = GapDisnaker::orderBy('periode')->first();
        $data = GapDisnaker::where('periode', $latestPeriode->periode)->get();
        $latestPeriode = Carbon::parse($latestPeriode->periode)->format('F Y');
        return [
            new DataSheet($latestPeriode),
            // new SummarySheet($data, $latestPeriode),
        ];
    }
}
