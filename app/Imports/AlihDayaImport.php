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
use Maatwebsite\Excel\Concerns\WithValidation;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class AlihDayaImport implements ToCollection, WithHeadingRow, WithValidation
{
    use Importable;

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $periode = Date::excelToDateTimeObject($row['periode']);
            $branch_name = trim(preg_replace('/\b(KF|KFO|KFNO|KC)\b/', '', $row['lokasi']));
            $branch_name = $branch_name == "KPO" ? "Kantor Pusat" : $branch_name;
            $branch = Branch::where('branch_name', 'like', '%' . $branch_name . '%')->first();
            $exist_periode = GapAlihDaya::where('periode', $periode)->first();
            if ($row['cost'] > 0) {
                if ($exist_periode) {
                    GapAlihDaya::updateOrCreate(
                        [
                            'jenis_pekerjaan' => $row['jenis_pekerjaan'],
                            'nama_pegawai' => $row['nama_pegawai'],
                            'user' => $row['user'],
                            'lokasi' => $row['lokasi'],
                            'vendor' => $row['vendor'],
                            'periode' => $periode
                        ],
                        [
                            'jenis_pekerjaan' => $row['jenis_pekerjaan'],
                            'nama_pegawai' => $row['nama_pegawai'],
                            'user' => $row['user'],
                            'lokasi' => $row['lokasi'],
                            'vendor' => $row['vendor'],
                            'cost' => $row['cost'],
                            'periode' => $periode,
                            'branch_id' => isset($branch) ? $branch->id : null,
                        ],
                    );
                } else {
                    GapAlihDaya::create(
                        [
                            'jenis_pekerjaan' => $row['jenis_pekerjaan'],
                            'nama_pegawai' => $row['nama_pegawai'],
                            'user' => $row['user'],
                            'lokasi' => $row['lokasi'],
                            'vendor' => $row['vendor'],
                            'cost' => $row['cost'],
                            'periode' => $periode,
                            'branch_id' => isset($branch) ? $branch->id : null

                        ]
                    );
                }
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
