<?php

namespace App\Imports;

use App\Models\Branch;
use Carbon\Carbon;
use App\Models\GapKdo;
use App\Models\GapKdoMobil;
use App\Models\KdoMobilBiayaSewa;
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
            $unit = str_contains($row['unit'], 'KF') ? trim(str_replace('KF', '', $row['unit'])) : $row['unit'];
            $branches = Branch::where('branch_name', 'like', '%' . ($unit == 'KPO' ? 'Kantor Pusat' : $unit) . '%')->get();

            $row = $row->toArray();
            // $filteredData = array_intersect_key($row, array_flip(preg_grep('/^\d+$/', array_keys($row))));
            $filteredData = array_intersect_key($row, array_flip(preg_grep('/^(jan|feb|mar|apr|may|june|jul|aug|sep|oct|nov|dec)$/i', array_keys($row))));

            if ($branches->count() > 0) {
                if ($branches->count() > 1) {
                    foreach ($branches as $branch) {
                        $gap_kdo_mobil = GapKdo::updateOrCreate(
                            [
                                'nopol' => $row['nopol'],
                                'periode' => Date::excelToDateTimeObject($row['periode']),
                            ],
                            [
                                'branch_id' => $branch->id,
                                'vendor' => $row['vendor'],
                                'nopol' => $row['nopol'],
                                'awal_sewa' => is_int($row['awal_sewa']) ? Date::excelToDateTimeObject($row['awal_sewa']) : null,
                                'akhir_sewa' => is_int($row['akhir_sewa']) ? Date::excelToDateTimeObject($row['akhir_sewa']) : null,
                                'periode' => Date::excelToDateTimeObject($row['periode']),
                            ]
                        );
                        $periode = [];
                        foreach ($filteredData as $key => $value) {
                            if (!is_null($value)) {
                                $value = preg_replace('/[^0-9]/', '', $value);
                                $tanggal_periode = strtoupper($key) . '_' . Carbon::now()->year;
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
                                        'value' => (int) $value
                                    ]
                                );
                            }
                        }
                    }
                }
                $branch = $branches->first();
                $gap_kdo_mobil = GapKdo::updateOrCreate(
                    [
                        'nopol' => $row['nopol'],
                        'periode' => Date::excelToDateTimeObject($row['periode']),
                    ],
                    [
                        'branch_id' => $branch->id,
                        'vendor' => $row['vendor'],
                        'nopol' => $row['nopol'],
                        'awal_sewa' => is_int($row['awal_sewa']) ? Date::excelToDateTimeObject($row['awal_sewa']) : null,
                        'akhir_sewa' => is_int($row['akhir_sewa']) ? Date::excelToDateTimeObject($row['akhir_sewa']) : null,
                        'periode' => Date::excelToDateTimeObject($row['periode']),
                    ]
                );
                $periode = [];
                foreach ($filteredData as $key => $value) {
                    if (!is_null($value)) {
                        $value = preg_replace('/[^0-9]/', '', $value);
                        $tanggal_periode = strtoupper($key) . '_' . Carbon::now()->year;
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
                                'value' => (int) $value
                            ]
                        );
                    }
                }
            }
        }
    }

    public function uniqueBy()
    {
        return 'branch_id';
    }
}
