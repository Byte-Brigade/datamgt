<?php

namespace App\Imports;

use App\Models\GapPks;
use Exception;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithValidation;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class PksImport implements ToCollection, WithHeadingRow, WithValidation
{
    use Importable;

    public function collection(Collection $rows)
    {
        try {
            foreach ($rows as $row) {
                $periode = Date::excelToDateTimeObject($row['periode']);
                $contract_date = is_int($row['contract_date']) ? Date::excelToDateTimeObject($row['contract_date']) :  null;
                $awal = is_int($row['awal']) ? Date::excelToDateTimeObject($row['awal'])  : null;
                $akhir = is_int($row['akhir']) ? Date::excelToDateTimeObject($row['akhir']) :  null;
                $tahun_akhir = is_int($row['tahun_akhir']) ? $row['tahun_akhir'] : null;
                $exist_periode = GapPks::where('periode', $periode)->first();
                if ($exist_periode) {
                    GapPks::updateOrCreate(
                        [
                            'vendor' => $row['vendor'],
                            'entity' => $row['entity'],
                            'type' => $row['type'],
                            'description' => $row['description'],
                            'contract_date' => $contract_date,
                            'contract_no' => $row['contract_no'],
                        ],
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
                            'renewal' => $row['renewal'],
                            'end_contract' => $row['end_contract'],
                            'need_update' => $row['need_update'],
                            'periode' => $periode,
                        ]
                    );
                } else {
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
                            'periode' => $periode,
                        ]
                    );
                }
            }
        } catch (\Throwable $th) {
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
