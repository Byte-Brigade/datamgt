<?php

namespace App\Imports;

use App\Models\Branch;
use App\Models\OpsPajakReklame;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;

class PajakReklameImport implements ToModel, WithHeadingRow, WithUpserts
{
    use Importable;
    public function model(array $row)
    {
        // dd($row);
        $periode = $row['periode_pajak_reklame'] != '-' ? explode(' - ', $row['periode_pajak_reklame']) : [null, null];
        $branch = $row['cabang'];
        return new OpsPajakReklame([
            'branch_id' => Branch::where('branch_code', $row['kode_cab'])->orWhere('branch_name', 'like', "%$branch%")->pluck('id')->first(),
            'periode_awal' => $periode[0] != null ? Carbon::createFromFormat('d/m/Y', $periode[0]) : null,
            'periode_akhir' => $periode[1] != null ? Carbon::createFromFormat('d/m/Y', $periode[1]) : null,
            'note' => $row['keterangan'],
            'additional_info' => $row['info_tambahan']
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
