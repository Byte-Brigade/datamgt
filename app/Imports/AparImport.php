<?php

namespace App\Imports;

use App\Models\Branch;
use App\Models\BranchType;
use App\Models\OpsApar;
use Carbon\Carbon;
use Exception;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;
use Throwable;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithValidation;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class AparImport implements ToCollection, WithHeadingRow, WithUpserts
{
    use Importable;

    public function collection(Collection $rows)
    {

        $rows->shift(1);
        foreach ($rows as $num => $row) {
            // Temukan indeks kunci 'keterangan'
            $types = BranchType::all()->pluck('type_name')->toArray();
            $regexPattern = implode('|', array_map('preg_quote', $types));

            $type_name = preg_match("/\b({$regexPattern}|KPO)\b/", $row['cabang'], $match) ? $match[0] : null;

            $branch_name = trim(preg_replace("/\b({$regexPattern}|KPO)\b/", "", $row['cabang']));

            $branch = Branch::where('branch_name', 'like', '%' . $branch_name . '%')->whereHas('branch_types', function ($q) use ($type_name) {
                return $q->where('type_name', $type_name == 'KPO' ? 'KC' : $type_name);
            })->first();
            $row = $row->toArray();
            $keteranganIndex = array_search('keterangan', array_keys($row));

            // Ambil hanya bagian dari array sebelum 'keterangan'
            $row = array_slice($row, 0, $keteranganIndex + 1, true);

            if ($branch) {


                $aparKeys = preg_grep('/^apar_/', array_keys($row)); // Mendapatkan semua kunci yang dimulai dengan "apar_"
                Carbon::setLocale('id');
                $number = 1;
                foreach ($aparKeys as $index => $aparKey) {
                    if (!is_null($row[$aparKey]) && !is_null($row[$index + 1])) {
                        try {
                            activity()->withoutLogs(function () use ($branch, $row, $aparKey, $index) {
                                OpsApar::updateOrCreate(
                                    [
                                        'branch_id' => $branch->id,
                                        'titik_posisi' => $row[$aparKey]
                                    ],
                                    [
                                        'branch_id' => $branch->id,
                                        'keterangan' => $row['keterangan'],
                                        'titik_posisi' => $row[$aparKey],
                                        'expired_date' => Date::excelToDateTimeObject($row[$index + 1]),
                                    ]
                                );
                            });
                            $number++;
                        } catch (Throwable $th) {
                            throw new Exception("Format expired_date harus berupa date pada baris ke-" . $num + 1 . " dan apar ke-" . $number . " " . $th->getMessage());
                        }
                    }
                }
            } else {
                throw new Exception("Cabang {$branch_name} tidak ditemukan pada baris ke-" . $num + 1);
            }
        }
    }

    public function uniqueBy()
    {
        return 'branch_id';
    }
}
