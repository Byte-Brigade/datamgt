<?php

namespace App\Exports\Scoring;

use App\Models\GapKdo;
use App\Models\GapScoring;
use App\Models\GapScoringProject;
use App\Models\OpsSpeciment;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ProjectsExport implements FromView, ShouldAutoSize, WithTitle, WithColumnFormatting
{
    use Exportable;
    public function view(): View
    {
        return view('exports.scoring.projects', [
            'scoring-projects' => GapScoring::all()->where('type','Project')->sortBy('branches.branch_code')
        ]);
    }

    public function title(): string
    {
        return 'Scoring Project Procurement';
    }

    public function columnFormats(): array
    {
        return [
            'I' => '#,##0', // Format for column A (assuming it's a number)
            'J' => NumberFormat::FORMAT_DATE_YYYYMMDD, // Format for column A (assuming it's a number)
            'K' => NumberFormat::FORMAT_DATE_YYYYMMDD, // Format for column A (assuming it's a number)
            'L' => NumberFormat::FORMAT_DATE_YYYYMMDD, // Format for column A (assuming it's a number)
            'M' => NumberFormat::FORMAT_DATE_YYYYMMDD, // Format for column A (assuming it's a number)

        ];
    }
}
