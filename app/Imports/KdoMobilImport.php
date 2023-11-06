<?php

namespace App\Imports;

use App\Models\Branch;
use App\Models\GapKdo;
use App\Models\GapKdoMobil;
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
        foreach ($rows as $row) {
            $row = $row->toArray();
            $filteredData = array_intersect_key($row, array_flip(preg_grep('/^(jan|feb|mar|apr|may|june|july|august|sep|oct|nov|dec)$/i', array_keys($row))));

            $currentYear = date('Y');

            $periode = [];
            foreach ($row as $key => $value) {
                if (!is_null($value)) {
                    $value = preg_replace('/[^0-9]/', '', $value);
                    $tanggal_periode = '';

                    if (preg_match('/^(jan|feb|mar|apr|may|jun|jul|aug|sep|oct|nov|dec)_\d{2}$/i', $key)) {
                        $carbonDate = Carbon::createFromFormat('M_y', strtoupper($key));
                        $tanggal_periode = $carbonDate->format('Y-m-d');
                        array_push($periode, ['periode' => $tanggal_periode, 'value' => (int) $value]);
                    } elseif (is_numeric($key)) {
                        $tanggal_periode = Date::excelToDateTimeObject($key)->format('Y-m-d');
                        array_push($periode, ['periode' => $tanggal_periode, 'value' => (int) $value]);
                    }
                }
            }


            GapKdoMobil::updateOrCreate(
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
        }
    }

    public function uniqueBy()
    {
        return 'branch_id';
    }
}
