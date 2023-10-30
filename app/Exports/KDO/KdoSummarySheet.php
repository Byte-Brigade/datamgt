<?php

namespace App\Exports\KDO;

use App\Models\GapKdo;
use App\Models\OpsSpeciment;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class KdoSummarySheet implements FromView, ShouldAutoSize, WithTitle
{
    use Exportable;
    public function view(): View
    {
        return view('exports.kdo.summary', [
            'kdos' => GapKdo::all()
        ]);
    }

    public function title(): string
    {
        return 'KDO Summary';
    }
}
