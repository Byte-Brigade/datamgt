<?php

namespace App\Imports;

use App\Models\Branch;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpsertColumns;
use Maatwebsite\Excel\Concerns\WithUpserts;

class BranchesImport implements ToModel, WithHeadingRow, WithUpserts, WithUpsertColumns
{
    use Importable;
    public function model(array $row)
    {
        return new Branch([
            'branch_code' => $row['kode_cabang'],
            'branch_name' => $row['nama_cabang'],
            'address' => $row['alamat']
        ]);
    }

    public function uniqueBy()
    {
        return 'branch_code';
    }

    public function upsertColumns()
    {
        return ['address'];
    }
}
