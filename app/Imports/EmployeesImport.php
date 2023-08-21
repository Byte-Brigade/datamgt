<?php

namespace App\Imports;

use App\Models\Branch;
use App\Models\Employee;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class EmployeesImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $branch = Branch::where('branch_code', $row['branch_id'])->first();
        return new Employee([
            'branch_id' => $branch->id,
            'employee_id' => $row['employee_id'],
            'name' => $row['employee_name'],
            'email' => $row['email'],
            'gender' => $row['gender'],
            'birth_date' => $row['tanggal_lahir'],
            'hiring_date' => $row['hiring_date_sfg'],
        ]);
    }
}
