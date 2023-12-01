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

            GapAlihDaya::create(
                [
                    'jenis_pekerjaan' => $row['jenis_pekerjaan'],
                    'nama_pegawai' => $row['nama_pegawai'],
                    'user' => $row['user'],
                    'lokasi' => $row['lokasi'],
                    'vendor' => $row['vendor'],
                    'cost' => $row['cost'],
                ]
            );
        }
    }
}
