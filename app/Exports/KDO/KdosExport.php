<?php

namespace App\Exports\KDO;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Events\AfterSheet;

class KdosExport implements WithMultipleSheets
{
    use Exportable;
    public function sheets(): array
    {
        return [
            new KdoSummarySheet(),
            new KdoMobilSheet(),
        ];
    }

}
