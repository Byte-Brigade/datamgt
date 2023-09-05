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

        $penerima_kuasa = isset($row['penerima_kuasa']) ? explode(' - ', $row['penerima_kuasa']) : [null, null];
        $penerima_kuasa_1 = isset($penerima_kuasa[0]) ? Employee::where('name', 'like', "%$penerima_kuasa[0]%")->pluck('id')->first() : null;
        $penerima_kuasa_2 = isset($penerima_kuasa[1]) ? Employee::where('name', 'like', "%$penerima_kuasa[1]%")->pluck('id')->first() : null;

        $branch_id = Branch::where('branch_name', $row['kantor_cabang'])->pluck('id')->first();
        $ops_skbirtgs =  OpsSkbirtgs::create([
            'no_surat' => $row['nomor_surat'],
            'branch_id' => $branch_id,
            'status' => $row['status']
        ]);

        $penerima_kuasa_ids = array_filter([$penerima_kuasa_1, $penerima_kuasa_2], fn ($value) => !is_null($value) && $value !== '');
        if (count($penerima_kuasa_ids) > 0) {
            $ops_skbirtgs->penerima_kuasa()->sync($penerima_kuasa_ids);
        }P
        return $ops_skbirtgs;
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
