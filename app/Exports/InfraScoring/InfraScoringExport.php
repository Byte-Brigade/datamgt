<?php

namespace App\Exports\InfraScoring;


use App\Models\GapScoring;
use App\Models\InfraScoring;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class InfraScoringExport implements WithMultipleSheets
{

    use Exportable;
    protected $type;

    public function __construct($type) {
        $this->type = $type;
    }
    public function sheets(): array
    {
        $latestPeriode = InfraScoring::orderBy('periode')->first();
        $data = InfraScoring::where('periode', $latestPeriode->periode)->where('type',$this->type)->get();
        $latestPeriode = Carbon::parse($latestPeriode->periode)->format('F Y');
        return [
            new DataSheet($data, $latestPeriode, $this->type),
            // new SummarySheet($data, $latestPeriode),
        ];
    }
}
