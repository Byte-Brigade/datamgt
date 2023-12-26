<?php

namespace App\Imports;

use App\Models\Branch;
use Carbon\Carbon;
use App\Models\GapKdo;
use App\Models\GapKdoMobil;
use App\Models\GapPerdin;
use App\Models\GapPerdinDetail;
use App\Models\KdoMobilBiayaSewa;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class PerdinImport implements ToCollection, WithHeadingRow
{
    use Importable;

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {

            $row = $row->toArray();
            // $filteredData = array_intersect_key($row, array_flip(preg_grep('/^\d+$/', array_keys($row))));
            $filteredData = array_intersect_key($row, array_flip(preg_grep('/^(jan|feb|mar|apr|may|jun|jul|aug|sep|oct|nov|dec)$/i', array_keys($row))));

            $periode = [];
            $gap_perdin = GapPerdin::updateOrCreate(
                [
                    'periode' => Date::excelToDateTimeObject($row['periode']),
                ],
                [
                    'divisi_pembebanan' => $row['divisi_pembebanan'],
                    'category' => $row['kategori'],
                    'tipe' => $row['tipe'],
                    'periode' => Date::excelToDateTimeObject($row['periode']),
                ]
            );
            foreach ($filteredData as $key => $value) {
                if (!is_null($value)) {
                    $tanggal_periode = strtoupper($key) . '_' . $row['tahun'];
                    $carbonDate = Carbon::createFromFormat('M_Y', $tanggal_periode);
                    $tanggal_periode =  $carbonDate->startOfMonth()->format('Y-m-d');
                    // array_push($periode, ['periode' => $tanggal_periode, 'value' => (int) $value]);
                    // dd($value);
                    GapPerdinDetail::updateOrCreate(
                        [
                            'gap_perdin_id' => $gap_perdin->id,
                            'periode' => $tanggal_periode,
                        ],
                        [
                            'periode' => $tanggal_periode,
                            'value' => round($value)
                        ]
                    );
                }
            }
        }
    }
}
