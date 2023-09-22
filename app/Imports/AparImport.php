<?php

namespace App\Imports;

use App\Models\Branch;
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
use Maatwebsite\Excel\Row;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class AparImport implements ToCollection, WithHeadingRow, WithUpserts, OnEachRow
{
    use Importable;
    // public function model(array $row)
    // {
    //     if (isset($row['tgl_spesimen'])) {


    //         $branch = explode(' ', $row['cabang']);
    //         array_shift($branch);

    //         $branch = implode(' ', $branch);
    //         $id = Branch::where('branch_name', 'like', "%$branch%")->pluck('id')->first();


    //         return new OpsSpeciment([
    //             'branch_id' => Branch::where('branch_name', 'like', "%$branch%")->pluck('id')->first(),
    //             'tgl_speciment' =>  Date::excelToDateTimeObject($row['tgl_spesimen']),
    //             'hasil_konfirmasi_cabang' => isset($row['hasil_konfirmasi_cabang'])  ? $row['hasil_konfirmasi_cabang'] : null,
    //             'keterangan' => isset($row['keterangan']) ? $row['keterangan'] : null,

    //         ]);
    //     }
    // }
    public function collection(Collection $rows)
    {


        $rows->shift(1);
        // dd($rows);
        foreach ($rows as $num => $row) {
            // Temukan indeks kunci 'keterangan'
            $row = $row->toArray();
            $keteranganIndex = array_search('keterangan', array_keys($row));

            // Ambil hanya bagian dari array sebelum 'keterangan'
            $row = array_slice($row, 0, $keteranganIndex + 1, true);

            if (isset($row['cabang'])) {
                $branch = explode(' ', $row['cabang']);
                array_shift($branch);

                $branch = implode(' ', $branch);

                $ops_apar = OpsApar::create([
                    'branch_id' => is_null(Branch::where('branch_name', 'like', "%$branch%")->pluck('id')->first()) ? 1 : Branch::where('branch_name', 'like', "%$branch%")->pluck('id')->first(),
                    'expired_date' => is_string($row['jangka_waktu_expired_date']) ? Carbon::createFromFormat('d M Y', $row['jangka_waktu_expired_date']) : Date::excelToDateTimeObject($row['jangka_waktu_expired_date']),
                    'keterangan' => $row['keterangan']
                ]);



                $apars = [];

                $aparKeys = preg_grep('/^apar_/', array_keys($row)); // Mendapatkan semua kunci yang dimulai dengan "apar_"
                Carbon::setLocale('id');

                $number = 1;
                foreach ($aparKeys as $index => $aparKey) {

                    if(!is_null($row[$aparKey]) && !is_null($row[$index + 1])) {
                        try {
                        array_push($apars, [
                            'ops_apar_id' => $ops_apar->id,
                            'titik_posisi' => $row[$aparKey],
                            'expired_date' =>   Date::excelToDateTimeObject($row[$index + 1]),
                        ]);
                        $number++;

                    }
                        catch(Throwable $th) {
                            throw new Exception("Data must be date format at " .$num + 1 ." row and apar ".$number);
                           }

                        }


                }



                $ops_apar->detail()->createMany($apars);
            }
        }
    }

    public function uniqueBy()
    {
        return 'branch_id';
    }

    public function onRow(Row $row)
    {
        dd($row);
    }

    // public function startRow(): int
    // {
    //     return 6;
    // }



    public function headingRow()
    {
        return 4;
    }
}
