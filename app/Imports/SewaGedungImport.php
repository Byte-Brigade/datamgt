<?php

namespace App\Imports;

use App\Models\Branch;
use App\Models\GapDisnaker;
use App\Models\InfraSewaGedung;
use App\Models\JenisPerizinan;
use App\Models\OpsApar;
use App\Models\OpsPajakReklame;
use App\Models\OpsSpeciment;
use Carbon\Carbon;
use Exception;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;
use Throwable;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Row;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

class SewaGedungImport implements ToCollection, WithHeadingRow, WithValidation
{
    use Importable;

    public function collection(Collection $rows)
    {
        try {
            foreach ($rows as $row) {
                $periode = Date::excelToDateTimeObject($row['periode']);
                $exist_periode = InfraSewaGedung::where('periode', $periode)->first();
                $branch = Branch::where('branch_name', 'like', '%' . $row['nama_cabang'] . '%')->first();
                if ($branch) {
                    if ($exist_periode) {
                        // Menambahkan jumlah hari dari tanggal Excel
                        InfraSewaGedung::updateOrCreate(
                            [
                                'branch_id' => $branch->id,
                                'status_kepemilikan' => $row['status'],
                                'jangka_waktu' => isset($row['masa_sewa']) ? preg_replace('/[^0-9]/', '',  $row['masa_sewa']) : null,
                                'jatuh_tempo' => !is_null($row['jatuh_tempo']) ? Date::excelToDateTimeObject($row['jatuh_tempo']) : null,
                                'open_date' => !is_null($row['open_date']) ? Date::excelToDateTimeObject($row['open_date']) : null,
                                'owner' => $row['pemilik'],
                                'biaya_per_tahun' => is_int($row['sewa_per_tahun'])  ? $row['sewa_per_tahun'] : null,
                                'total_biaya' => is_int($row['total_biaya_sewa']) ? $row['total_biaya_sewa'] : null,
                                'periode' => $periode,
                            ],
                            [
                                'branch_id' => $branch->id,
                                'status_kepemilikan' => $row['status'],
                                'jangka_waktu' => isset($row['masa_sewa']) ? preg_replace('/[^0-9]/', '',  $row['masa_sewa']) : null,
                                'jatuh_tempo' => !is_null($row['jatuh_tempo']) ? Date::excelToDateTimeObject($row['jatuh_tempo']) : null,
                                'open_date' => !is_null($row['open_date']) ? Date::excelToDateTimeObject($row['open_date']) : null,
                                'owner' => $row['pemilik'],
                                'biaya_per_tahun' => is_int($row['sewa_per_tahun'])  ? $row['sewa_per_tahun'] : null,
                                'total_biaya' => is_int($row['total_biaya_sewa']) ? $row['total_biaya_sewa'] : null,
                                'periode' => $periode,
                            ]
                        );
                    } else {
                        InfraSewaGedung::create(
                            [
                                'branch_id' => $branch->id,
                                'status_kepemilikan' => $row['status'],
                                'jangka_waktu' => isset($row['masa_sewa']) ? preg_replace('/[^0-9]/', '',  $row['masa_sewa']) : null,
                                'jatuh_tempo' => !is_null($row['jatuh_tempo']) ? Date::excelToDateTimeObject($row['jatuh_tempo']) : null,
                                'open_date' => !is_null($row['open_date']) ? Date::excelToDateTimeObject($row['open_date']) : null,
                                'owner' => $row['pemilik'],
                                'biaya_per_tahun' => is_int($row['sewa_per_tahun'])  ? $row['sewa_per_tahun'] : null,
                                'total_biaya' => is_int($row['total_biaya_sewa']) ? $row['total_biaya_sewa'] : null,
                                'periode' => $periode,
                            ]
                        );
                    }
                } else {
                    throw new Exception("Error : Nama Branch ".$row['nama_cabang']." tidak ditemukan.");
                }
            }
        } catch (Throwable $th) {
            throw new Exception("Error : " . $th->getMessage());
        }
    }

    public function rules(): array
    {
        return [
            '*.periode' => 'required|integer',
        ];
    }
}
