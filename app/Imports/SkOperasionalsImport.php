<?php

namespace App\Imports;

use App\Models\Branch;
use App\Models\Employee;
use App\Models\OpsSkOperasional;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class SkOperasionalsImport implements ToCollection, WithHeadingRow, WithUpserts
{
    use Importable;

    public function collection(Collection $rows)
    {
        $mergedCells = [];
        foreach ($rows as $key => $row) {
            if ($key % 2 === 0) {
                $mergedCells[$key] = $row['cabang'];
            } else {
                $row['cabang'] = $mergedCells[$key - 1] ?? null;
            }

            $penerima_kuasa = isset($row['nama_penerima_kuasa']) ? $row['nama_penerima_kuasa'] : null;
            $penerima_kuasa_id = isset($penerima_kuasa) ? Employee::where('name', 'like', "%$penerima_kuasa%")->pluck('id')->first() : null;

            $branch_id = Branch::where('branch_name', $row['cabang'])->pluck('id')->first();
            if (isset($row['no_surat'])) {
                $ops_sk_operasional = OpsSkOperasional::create([
                    'no_surat' => $row['no_surat'],
                    'branch_id' => $branch_id,
                    'note' => $row['keterangan'],
                    'masa_berlaku' => Date::excelToDateTimeObject($row['masa_berlaku'])->format('Y-m-d')
                ]);
                $ops_sk_operasional->penerima_kuasa()->sync($penerima_kuasa_id);
            } else if (isset($penerima_kuasa_id)) {
                $ops_sk_operasional = OpsSkOperasional::where('branch_id', $branch_id)->get()->first();
                $ops_sk_operasional->penerima_kuasa()->attach($penerima_kuasa_id);
            }
        }
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
