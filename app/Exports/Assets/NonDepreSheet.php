<?php

namespace App\Exports\Assets;

use App\Models\GapAsset;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class NonDepreSheet implements FromView, ShouldAutoSize, WithTitle, WithColumnFormatting
{
    use Exportable;
    public function view(): View
    {
        return view('exports.assets.non_depre', [
            'assets' => GapAsset::with('branches')->where('category','Non-Depre')->get()
        ]);
    }

    public function title(): string
    {
        return 'Non Depre';
    }

    public function columnFormats(): array
    {
        return [
            'E' => NumberFormat::FORMAT_DATE_DDMMYYYY,
        ];
    }
}
