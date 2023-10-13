<?php

namespace App\Exports;

use App\Models\Branch;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;

class BranchesExport implements FromView {


    use Exportable;
    public function __construct(public $report=false) {
        $this->report = $report;
    }
    public function view(): View
    {
        return view($this->report ? 'exports.reporting.branches' : 'exports.branches', [
            'branches' => Branch::all()
        ]);
    }
}
