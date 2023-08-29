<?php

namespace App\Imports;

use App\Models\Branch;
use App\Models\Employee;
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
        $penerima_kuasa = $row['penerima_kuasa'] != '' ? explode(' - ', $row['penerima_kuasa']) : [null, null];
        $penerima_kuasa_1 = $penerima_kuasa[0] != null ? Employee::where('name', 'like', "%$penerima_kuasa[0]%")->pluck('id')->first() : null;
        $penerima_kuasa_2 = null;
        if (count($penerima_kuasa) > 1) {
            $penerima_kuasa_2 = Employee::where('name', 'like', "%$penerima_kuasa[1]%")->pluck('id')->first();
        }

        $branch_id = Branch::where('branch_name', $row['kantor_cabang'])->pluck('id')->first();
        return new OpsSkbirtgs([
            'no_surat' => $row['nomor_surat'],
            'branch_id' => $branch_id,
            'penerima_kuasa_1' => $penerima_kuasa_1,
            'penerima_kuasa_2' => $penerima_kuasa_2,
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
