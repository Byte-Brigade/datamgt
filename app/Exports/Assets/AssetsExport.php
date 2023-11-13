<?php

namespace App\Exports\Assets;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class AssetsExport implements WithMultipleSheets
{
    use Exportable;
    public function sheets(): array
    {
        return [
            new DepreSheet(),
            new NonDepreSheet(),
        ];
    }


}
