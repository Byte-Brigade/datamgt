<?php

namespace App\Imports;

use App\Models\Branch;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class BranchesImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Branch([
            'branch_code' => $row['kode_cabang'],
            'branch_name' => $row['nama_cabang'],
            'address' => $row['alamat']
        ]);
    }
}
