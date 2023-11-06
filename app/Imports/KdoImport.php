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

class KdoImport implements ToCollection, WithHeadingRow, WithUpserts
{
    use Importable;

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {

            $branch = Branch::where('branch_name', 'like', '%' . str_contains($row['unit'], 'KF') ? trim(str_replace('KF','',$row['unit'])) : $row['unit'] . '%')->first();
            $row = $row->toArray();
            $filteredData = array_intersect_key($row, array_flip(preg_rep('/^\d+$/', array_keys($row))));

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

                        $tanggal_periode = Date::excelToDateTimeObject($key)->format('Y-m-d');
                        array_push($periode, ['periode' => $tanggal_periode, 'value' => $value]);
                    }
                }

                GapKdoMobil::updateOrCreate([
                    'gap_kdo_id' => $kdo->id,
                    'nopol' => $row['nopol'],
                ],
                [
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
