<?php

namespace App\Exports\KDO;

use App\Http\Resources\KdoMobilResource;
use App\Models\GapKdoMobil;
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
        "January", "February", "March", "April", "May", "June", "July",
        "August", "September", "October", "November", "December"
    ];
    public function view(): View
    {
        $kdo_mobil = isset($this->gap_kdo_id) ? GapKdoMobil::where('gap_kdo_id', $this->gap_kdo_id)->get() : GapKdoMobil::all();
        return view('exports.kdo.mobils', [
            'kdo_mobils' => KdoMobilResource::collection($kdo_mobil->sortBy('branches.branch_code')),
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
