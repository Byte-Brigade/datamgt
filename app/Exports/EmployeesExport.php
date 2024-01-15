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
        $branch_id = $this->branch_id;
        $position_id = $this->position_id;
        $employees = Employee::with(['branches', 'employee_positions'])->newQuery();
        if (isset($branch_id)) {
            $employees = $employees->whereHas('branches', function ($query) use ($branch_id) {
                $query->where('id', $branch_id);
            });
        }

        if (isset($position_id)) {
            $employees = $employees->whereHas('employee_positions', function ($query) use ($position_id) {
                $query->where('id', $position_id);
            });
        }
        $employees = $employees->get();

        return view('exports.employees', [
            'employees' => $employees
        ]);
    }
}
