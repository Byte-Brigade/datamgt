<?php

namespace App\Imports;

use App\Models\Branch;
use Carbon\Carbon;
use App\Models\GapKdo;
use App\Models\GapKdoMobil;
use App\Models\GapPerdin;
use App\Models\GapPerdinDetail;
use App\Models\KdoMobilBiayaSewa;
use Exception;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Events\BeforeSheet;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class PerdinImport implements ToCollection, WithHeadingRow, WithEvents, WithValidation
{
    use Importable;

    protected $sheetName;


    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function (BeforeSheet $event) {
                $this->sheetName = $event->getSheet()->getTitle();
            }
        ];
    }

    public function collection(Collection $rows)
    {
        DB::beginTransaction();
        $row = null;
        try {
            foreach ($rows as $row) {


                $row = $row->toArray();
                $filteredData = array_intersect_key($row, array_flip(preg_grep('/^(jan|feb|mar|apr|may|jun|jul|aug|sep|oct|nov|dec)$/i', array_keys($row))));
                $periode = Carbon::createFromDate($row['tahun'])->startOfYear()->format('Y-m-d');
                $gap_perdin = GapPerdin::updateOrCreate(
                    [
                        'divisi_pembebanan' => $row['divisi_pembebanan'],
                        'user' => $row['user'],
                        'periode' => $periode,
                    ],
                    [
                        'divisi_pembebanan' => $row['divisi_pembebanan'],
                        'periode' => $periode,
                        'user' => $row['user'],
                    ]
                );
                foreach ($filteredData as $key => $value) {
                    if (!is_null($value)) {
                        $tanggal_periode = strtoupper($key) . '_' . Carbon::parse($periode)->year;
                        $carbonDate = Carbon::createFromFormat('M_Y', $tanggal_periode);
                        $tanggal_periode =  $carbonDate->startOfMonth()->format('Y-m-d');
                        GapPerdinDetail::updateOrCreate(
                            [
                                'gap_perdin_id' => $gap_perdin->id,
                                'periode' => $tanggal_periode,
                                'category' => $row['kategori'],
                                'tipe' => $this->sheetName,
                            ],
                            [
                                'gap_perdin_id' => $gap_perdin->id,
                                'periode' => $tanggal_periode,
                                'value' => round($value),
                                'category' => $row['kategori'],
                                'tipe' => $this->sheetName,
                            ]
                        );
                    }
                }
            }
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw new Exception("Error : " . $th . " at row:");
        }
    }

    public function rules(): array
    {
        return [
            '*.tahun' => 'required|integer',
        ];
    }
}
