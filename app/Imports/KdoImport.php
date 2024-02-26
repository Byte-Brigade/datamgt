<?php

namespace App\Imports;

use App\Models\Branch;
use Carbon\Carbon;
use App\Models\GapKdo;
use App\Models\GapKdoMobil;
use App\Models\KdoMobilBiayaSewa;
use Exception;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithValidation;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class KdoImport implements ToCollection, WithHeadingRow, WithValidation
{
    use Importable;

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $unit = str_contains($row['unit'], 'KF') ? trim(str_replace('KF', '', $row['unit'])) : $row['unit'];
            $branch = Branch::where('branch_name', 'like', '%' . ($unit == 'KPO' ? 'Kantor Pusat' : $unit) . '%')->first();

            $row = $row->toArray();
            // $filteredData = array_intersect_key($row, array_flip(preg_grep('/^\d+$/', array_keys($row))));
            $filteredData = array_intersect_key($row, array_flip(preg_grep('/^(jan|feb|mar|apr|may|june|july|august|sept|oct|nov|dec)$/i', array_keys($row))));
            $periode = Date::excelToDateTimeObject($row['periode']);

            if (!isset($branch)) {
                throw new Exception("Branch " . $row['unit'] . " tidak ditemukan.");
            }
            // dd($rows);
            $gap_kdo_mobil = GapKdo::updateOrCreate(
                [
                    'branch_id' => $branch->id,
                    'vendor' => $row['vendor'],
                    'nopol' => $row['nopol'],
                ],
                [
                    'branch_id' => $branch->id,
                    'vendor' => $row['vendor'],
                    'nopol' => $row['nopol'],
                    'awal_sewa' => is_int($row['awal_sewa']) ? Date::excelToDateTimeObject($row['awal_sewa']) : null,
                    'akhir_sewa' => is_int($row['akhir_sewa']) ? Date::excelToDateTimeObject($row['akhir_sewa']) : null,
                    'periode' => $periode,
                ]
            );
            foreach ($filteredData as $key => $value) {

                $tanggal_periode = strtoupper($key) . '_' . $periode->format('Y');
                $carbonDate = Carbon::createFromFormat('M_Y', $tanggal_periode);
                $tanggal_periode =  $carbonDate->startOfMonth()->format('Y-m-d');

                KdoMobilBiayaSewa::updateOrCreate(
                    [
                        'gap_kdo_id' => $gap_kdo_mobil->id,
                        'periode' => $tanggal_periode,
                    ],

                    [
                        'gap_kdo_id' => $gap_kdo_mobil->id,
                        'periode' => $tanggal_periode,
                        'value' => is_int($value) ? $value : 0
                    ]
                );
            }
        }
    }
    public function rules(): array
    {
        return [
            '*.periode' => 'required|integer',
        ];
    }
}
