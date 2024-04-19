<?php

namespace App\Imports;

use App\Models\Branch;
use App\Models\GapKdo;
use App\Models\GapKdoMobil;
use App\Models\KdoMobilBiayaSewa;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class KdoMobilImport implements ToCollection, WithHeadingRow, WithUpserts
{
    use Importable;
    private $branch_id;
    private $gap_kdo_id;

    public function __construct($branch_id, $gap_kdo_id)
    {
        $this->branch_id = $branch_id;
        $this->gap_kdo_id = $gap_kdo_id;
    }

    public function collection(Collection $rows)
    {
        activity()->disableLogging();
        foreach ($rows as $row) {
            $row = $row->toArray();
            $filteredData = array_intersect_key($row, array_flip(preg_grep('/^(jan|feb|mar|apr|may|june|jul|aug|sep|oct|nov|dec)$/i', array_keys($row))));
            $currentYear = date('Y');
            $periode = [];

            $gap_kdo_mobil = GapKdoMobil::updateOrCreate(
                [
                    'gap_kdo_id' => $this->gap_kdo_id,
                    'nopol' => $row['nopol'],
                ],
                [
                    'branch_id' => $this->branch_id,
                    'gap_kdo_id' => $this->gap_kdo_id,
                    'vendor' => $row['vendor'],
                    'nopol' => $row['nopol'],
                    'awal_sewa' => is_int($row['awal_sewa']) ? Date::excelToDateTimeObject($row['awal_sewa']) : Carbon::createFromFormat('d/m/Y', $row['awal_sewa']),
                    'akhir_sewa' => is_int($row['akhir_sewa']) ? Date::excelToDateTimeObject($row['akhir_sewa']) : Carbon::createFromFormat('d/m/Y', $row['akhir_sewa']),
                    'biaya_sewa' => $periode
                ]
            );
            foreach ($filteredData as $key => $value) {
                if (!is_null($value)) {
                    $value = preg_replace('/[^0-9]/', '', $value);
                    $tanggal_periode = strtoupper($key) . '_' . Carbon::now()->year;
                    $carbonDate = Carbon::createFromFormat('M_Y', $tanggal_periode);
                    $tanggal_periode =  $carbonDate->startOfMonth()->format('Y-m-d');
                    KdoMobilBiayaSewa::updateOrCreate(
                        [
                            'gap_kdo_mobil_id' => $gap_kdo_mobil->id,
                            'periode' => $tanggal_periode,
                        ],

                        [
                            'gap_kdo_mobil_id' => $gap_kdo_mobil->id,
                            'periode' => $tanggal_periode,
                            'value' => (int) $value
                        ]
                    );
                }
            }
        }
    }

    public function uniqueBy()
    {
        return 'branch_id';
    }
}
