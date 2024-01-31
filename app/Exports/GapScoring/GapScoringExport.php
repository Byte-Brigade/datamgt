<?php

namespace App\Exports\GapScoring;


use App\Models\GapScoring;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class GapScoringExport implements WithMultipleSheets
{

    use Exportable;
    protected $type;

    public function __construct($type) {
        $this->type = $type;
    }
    public function sheets(): array
    {
        $latestPeriode = GapScoring::orderBy('periode')->first();
        $data = GapScoring::where('periode', $latestPeriode->periode)->where('type',$this->type)->get();
        $latestPeriode = Carbon::parse($latestPeriode->periode)->format('F Y');
        return [
            new DataSheet($data, $latestPeriode, $this->type),
            // new SummarySheet($data, $latestPeriode),
        ];
    }
}
