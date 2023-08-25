<?php

namespace App\Imports;

use App\Models\Branch;
use App\Models\OpsSkbirtgs;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;

class SkBirtgsImport implements ToModel, WithHeadingRow, WithUpserts
{
    use Importable;

    public function model(array $row)
    {
        $branch_id = Branch::where('branch_name', $row['kantor_cabang'])->pluck('id')->first();
        return new OpsSkbirtgs([
            'no_surat' => $row['nomor_surat'],
            'branch_id' => $branch_id,
            'status' => $row['status']
        ]);
    }

    public function uniqueBy()
    {
        return 'branch_id';
    }

    public function headingRow()
    {
        return 3;
    }
}
