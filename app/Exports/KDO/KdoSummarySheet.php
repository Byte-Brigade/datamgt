<?php

namespace App\Exports\KDO;

use App\Models\Branch;
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

        $latestPeriode = GapKdo::with(['branches','biaya_sewas'])->max('periode');
        return view('exports.kdo.summary', [
            'branches' => Branch::with(['gap_kdo' => function($q) use($latestPeriode) {
                return $q->where('periode', $latestPeriode);
            }])->get(),
        ]);
    }

    public function title(): string
    {
        return 'KDO Summary';
    }
}
