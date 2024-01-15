<?php

namespace App\Exports\Toners;

use App\Models\GapToner;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class TonerExport implements WithMultipleSheets
{
    use Exportable;
    public function sheets(): array
    {
        $latestPeriode = GapToner::orderBy('periode')->first();
        $data = GapToner::with('branches')->where('periode', $latestPeriode->periode)->get()->sortBy('branches.branch_code');
        $latestPeriode = Carbon::parse($latestPeriode->periode)->format('F Y');
        return [
            new SummarySheet($data, $latestPeriode),
            new TonerSheet($data, $latestPeriode),
        ];
    }
}
