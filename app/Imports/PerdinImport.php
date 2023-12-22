<?php

namespace App\Imports;

use App\Models\Branch;
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
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeSheet;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class PerdinImport implements ToCollection, WithHeadingRow, WithEvents
{
    use Importable;

    protected $sheetName;


    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function(BeforeSheet $event) {
                $this->sheetName = $event->getSheet()->getTitle();
            }
        ];
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {

           $row = $row->toArray();
            // $filteredData = array_intersect_key($row, array_flip(preg_grep('/^\d+$/', array_keys($row))));
            $filteredData = array_intersect_key($row, array_flip(preg_grep('/^(jan|feb|mar|apr|may|jun|jul|aug|sep|oct|nov|dec)$/i', array_keys($row))));

            $periode = [];
            foreach ($filteredData as $key => $value) {
                if (!is_null($value)) {
                    $tanggal_periode = strtoupper($key) . '_' . $row['tahun'];
                    $carbonDate = Carbon::createFromFormat('M_Y', $tanggal_periode);
                    $tanggal_periode =  $carbonDate->startOfMonth()->format('Y-m-d');
                    // array_push($periode, ['periode' => $tanggal_periode, 'value' => (int) $value]);
                    // dd($value);
                    GapPerdin::updateOrCreate(
                        [
                            'divisi_pembebanan' => $row['divisi_pembebanan_biaya'],
                            'periode' => $tanggal_periode,
                            'category' => $row['kategori'],
                            'user' => $row['user'],
                            'tipe' => $this->sheetName,
                        ],
                        [
                            'divisi_pembebanan' => $row['divisi_pembebanan_biaya'],
                            'category' => $row['kategori'],
                            'user' => $row['user'],
                            'tipe' => $this->sheetName,
                            'periode' => $tanggal_periode,
                            'value' => round($value)
                        ]
                    );
                }
            }
        }
    }

}
