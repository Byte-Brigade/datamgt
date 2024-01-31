<?php

namespace App\Exports\KDO;

use App\Http\Resources\KdoMobilResource;
use App\Models\GapKdo;
use App\Models\GapKdoMobil;
use App\Models\KdoMobilBiayaSewa;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class KdoMobilSheet implements FromView, ShouldAutoSize, WithTitle, WithColumnFormatting
{
    private $gap_kdo_id;

    public function __construct($gap_kdo_id = null)
    {
        $this->gap_kdo_id = $gap_kdo_id;
    }
    use Exportable;

    private $months = [
        "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul",
        "Aug", "Sept", "Oct", "Nov", "Dec"
    ];
    public function view(): View
    {
        $kdo_mobil = GapKdo::with(['branches','biaya_sewas'])->get();
        $latestPeriode = $kdo_mobil->max('periode');
        $kdo_mobil = $kdo_mobil->where('periode', $latestPeriode);
        return view('exports.kdo.mobils', [
            'kdo_mobils' => $kdo_mobil->sortBy('branches.branch_code'),
            'months' => $this->months,
            'periode' => Carbon::now()->year
        ]);
    }

    public function title(): string
    {
        return 'KDO Mobil';
    }

    public function columnFormats(): array
    {
        return [
            'E' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'F' => NumberFormat::FORMAT_DATE_DDMMYYYY,
        ];
    }
}
