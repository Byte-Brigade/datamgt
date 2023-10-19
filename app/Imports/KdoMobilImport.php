<?php

namespace App\Imports;

use App\Models\Branch;
use App\Models\GapKdo;
use App\Models\GapKdoMobil;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class KdoMobilImport implements ToCollection, WithHeadingRow, WithUpserts
{
    use Importable;

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $branch = Branch::where('branch_name', 'like', '%' . $row['unit'] . '%')->first();
            $row = $row->toArray();
            $filteredData = array_intersect_key($row, array_flip(preg_grep('/^(jan|feb|mar|apr|may|jun|jul|aug|sep|oct|nov|dec)$/i', array_keys($row))));

            $currentYear = date('Y');
            if (isset($branch)) {
                $kdo = GapKdo::where('branch_id', $branch->id)->first();
                if (!isset($kdo)) {
                    $kdo = GapKdo::create([
                        'branch_id' => $branch->id
                    ]);
                }
                $periode = [];
                foreach ($filteredData as $key => $value) {
                    if (!is_null($value)) {

                        $tanggal_periode = date('Y-m-d', strtotime("01-$key-$currentYear"));
                        array_push($periode, ['periode' => $tanggal_periode, 'value' => $value]);
                    }
                }
                GapKdoMobil::create([
                    'branch_id' => $branch->id,
                    'gap_kdo_id' => $kdo->id,
                    'vendor' => $row['vendor'],
                    'nopol' => $row['nopol'],
                    'awal_sewa' => is_int($row['awal_sewa']) ? Date::excelToDateTimeObject($row['awal_sewa']) : null,
                    'akhir_sewa' => is_int($row['akhir_sewa']) ? Date::excelToDateTimeObject($row['akhir_sewa']) : null,
                    'biaya_sewa' => $periode
                ]);
            }
        }
    }

    public function uniqueBy()
    {
        return 'branch_id';
    }
}
