<?php

namespace App\Exports\HasilSTO;

use App\Models\GapPerdin;
use App\Models\GapSto;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class HasilSTOExport implements WithMultipleSheets

{
    use Exportable;

    protected $gap_sto_id;

    function __construct($gap_sto_id)
    {
        $this->gap_sto_id = $gap_sto_id;
    }
    public function sheets(): array
    {
        $data = GapSto::find($this->gap_sto_id);
        return [
            // new SummarySheet($data, $latestPeriode),
            new DataSheet($data),
        ];
    }
}
