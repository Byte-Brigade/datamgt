<?php

namespace App\Imports;

use App\Models\GapPks;
use Exception;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Maatwebsite\Excel\Concerns\WithValidation;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class PksImport implements ToCollection, WithHeadingRow
{
    use Importable;

    public function collection(Collection $rows)
    {
        try {
            activity()->disableLogging();
            GapPks::query()->delete();
            foreach ($rows as $row) {
                $contract_date = is_int($row['contract_date']) ? Date::excelToDateTimeObject($row['contract_date']) :  null;
                $awal = is_int($row['awal']) ? Date::excelToDateTimeObject($row['awal'])  : null;
                $akhir = is_int($row['akhir']) ? Date::excelToDateTimeObject($row['akhir']) :  null;
                $tahun_akhir = is_int($row['tahun_akhir']) ? $row['tahun_akhir'] : null;
                GapPks::create(
                    [
                        'vendor' => $row['vendor'],
                        'entity' => $row['entity'],
                        'type' => $row['type'],
                        'description' => $row['description'],
                        'contract_date' => $contract_date,
                        'contract_no' => $row['contract_no'],
                        'durasi_kontrak' => $row['durasi_kontrak'],
                        'awal' => $awal,
                        'akhir' => $akhir,
                        'tahun_akhir' => $tahun_akhir,
                        'status' => $row['status'],
                    ]
                );
            }
            Artisan::call("pks:checkexp");
        } catch (\Throwable $th) {
            throw new Exception("Error : " . $th->getMessage());
        }
    }
}
