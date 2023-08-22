<?php

namespace App\Imports;

use App\Models\Branch;
use App\Models\Employee;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class EmployeesImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Employee([
            'branch_id' => Branch::where('branch_code', $row['branch_id'])->pluck('id')->first() ?? 1,
            'employee_id' => $row['employee_id'],
            'name' => $row['employee_name'],
            'email' => $row['email'],
            'gender' => $row['gender'],
            'birth_date' => Date::excelToDateTimeObject($row['tanggal_lahir'])->format('Y-m-d'),
            'hiring_date' => Date::excelToDateTimeObject($row['hiring_date_sfg'])->format('Y-m-d'),
        ]);
    }
}
