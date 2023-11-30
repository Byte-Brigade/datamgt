<?php

namespace App\Imports;

use App\Models\Branch;
use App\Models\GapAlihDaya;
use Carbon\Carbon;
use App\Models\GapKdo;
use App\Models\GapKdoMobil;
use App\Models\GapPerdin;
use App\Models\KdoMobilBiayaSewa;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class AlihDayaImport implements ToCollection, WithHeadingRow
{
    use Importable;

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $periode = isset($row['periode']) ? Date::excelToDateTimeObject($row['periode']) : Carbon::now()->format('Y-m-y');
            GapAlihDaya::updateOrCreate(
                [
                    'nama_pegawai' => strtoupper($row['nama_pegawai']),
                    'lokasi' => strtoupper($row['lokasi']),
                    'jenis_pekerjaan' => strtoupper($row['jenis_pekerjaan']),
                    'vendor' => $row['vendor'],
                    'user' => strtoupper($row['user']),
                ],
                [
                    'jenis_pekerjaan' => strtoupper($row['jenis_pekerjaan']),
                    'nama_pegawai' => strtoupper($row['nama_pegawai']),
                    'user' => strtoupper($row['user']),
                    'lokasi' => strtoupper($row['lokasi']),
                    'vendor' => $row['vendor'],
                    'cost' => $row['cost'],
                    'periode' => $periode,
                ]
            );
        }
    }
}
