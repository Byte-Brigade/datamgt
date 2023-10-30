<?php

namespace App\Exports\KDO;

use App\Http\Resources\KdoMobilResource;
use App\Models\GapKdoMobil;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class KdoMobilSheet implements FromView, ShouldAutoSize, WithTitle
{
    use Exportable;

    private $months = [
        "January", "February", "March", "April", "May", "June", "July",
        "August", "September", "October", "November", "December"
    ];
    public function view(): View
    {
        return view('exports.kdo.mobils', [
            'kdo_mobils' => KdoMobilResource::collection(GapKdoMobil::all()),
            'months' => $this->months,
            'periode' => Carbon::now()->year
        ]);
    }

    public function title(): string
    {
        return 'KDO Mobil';
    }
}
