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

class AssessmentsExport implements FromView, ShouldAutoSize, WithTitle, WithColumnFormatting
{
    use Exportable;
    public function view(): View
    {
        return view('exports.scoring.assessments', [
            'scoring-assessments' => GapScoring::all()->where('type','Assessment')->sortBy('branches.branch_code')
        ]);
    }

    public function title(): string
    {
        return 'Scoring Assessment Procurement';
    }

    public function columnFormats(): array
    {
        return [
            'I' => '#,##0',
            'H' => NumberFormat::FORMAT_DATE_YYYYMMDD
        ];
    }
}
