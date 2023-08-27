<?php

namespace App\Imports;

use App\Models\Branch;
use App\Models\Employee;
use App\Models\EmployeePosition;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;
use Maatwebsite\Excel\Concerns\WithValidation;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class EmployeesImport implements ToModel, WithHeadingRow, WithUpserts, WithValidation, SkipsEmptyRows
{
    use Importable;

    public function model(array $row)
    {
        return new Employee([
            'branch_id' => Branch::where('branch_code', $row['branch_id'])->pluck('id')->first(),
            'position_id' => random_int(1, 3),
            'employee_id' => $row['employee_id'],
            'name' => $row['employee_name'],
            'email' => $row['email'],
            'gender' => $row['gender'],
            'birth_date' => $row['tanggal_lahir'] ? Date::excelToDateTimeObject($row['tanggal_lahir'])->format('Y-m-d') : null,
            'hiring_date' => $row['hiring_date_sfg'] ? Date::excelToDateTimeObject($row['hiring_date_sfg'])->format('Y-m-d') : null,
        ]);
    }

    public function uniqueBy()
    {
        return 'employee_id';
    }

    public function rules(): array
    {
        return [
            'employee_id' => ['required', 'string'],
            'employee_name' => ['required', 'string'],
            'posisi' => ['required', 'string'],
            'email' => ['required', 'string'],
            'gender' => ['required', 'string'],
        ];
    }
}
