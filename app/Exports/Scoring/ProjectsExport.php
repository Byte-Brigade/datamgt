<?php

namespace App\Exports\Scoring;

use App\Models\GapKdo;
use App\Models\GapScoringProject;
use App\Models\OpsSpeciment;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class ProjectsExport implements FromView, ShouldAutoSize, WithTitle
{
    use Exportable;
    public function view(): View
    {
        return view('exports.scoring.projects', [
            'scoring_projects' => GapScoringProject::all()->sortBy('branches.branch_code')
        ]);
    }

    public function title(): string
    {
        return 'Scoring Project Procurement';
    }
}
