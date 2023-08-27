<?php

namespace App\Exports;

use App\Models\Branch;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;

class BranchesExport implements FromView
{
    use Exportable;
    public function view(): View
    {
        return view('exports.branches', [
            'branches' => Branch::all()
        ]);
    }
}
