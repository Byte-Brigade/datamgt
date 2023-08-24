<?php

namespace App\Exports;

use App\Models\Employee;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;

class EmployeesExport implements FromView
{
    use Exportable;

    protected $branch_id;
    protected $position_id;

    public function __construct($branch_id, $position_id)
    {
        $this->branch_id = $branch_id;
        $this->position_id = $position_id;
    }

    public function view(): View
    {
        return view('exports.employees', [
            'employees' => Employee::with(['branches', 'positions'])->get()
        ]);
    }
}
