<?php

namespace App\Imports;

use App\Models\Branch;
use App\Models\InfraBro;
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
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

class BroImport implements ToCollection, WithHeadingRow
{
    use Importable;
    use Importable;

    public function collection(Collection $rows)
    {


        foreach ($rows as $row) {
            try {

                $status = preg_match('/drop/i', $row['cabang'], $match) ? ucfirst($match[0]) : $row['status'];
                $branch_name = preg_replace('/bss|cabang|[!@#$%^&*()_+-]|drop/i', '', $row['cabang']);
                $branch_type = preg_match('/\b(KF|KFO|KFNO|KC)\b/', $branch_name, $match) ? $match[0] : null;
                $branch_name = trim(preg_replace('/\b(KF|KFO|KFNO|KC)\b/','', $branch_name));
                $branch = Branch::where('branch_name', $branch_name)->first();
                $branch_type = !is_null($branch_type) ? $branch_type : (isset($branch) ? $branch->branch_types->type_name : null);
                $target = is_int($row['target']) ? Date::excelToDateTimeObject($row['target'])->format('Y-m-d') : null;
                $jatuh_tempo = is_int($row['jatuh_tempo_sewa'])  ? Date::excelToDateTimeObject($row['jatuh_tempo_sewa']) : null;

                if (isset($row['cabang'])) {

                    InfraBro::create([
                        'branch_name' => $row['cabang'],
                        'branch_type' => $row['type'],
                        'category' => $row['kategori_realisasi'],
                        'status' => $row['status'],
                        'target' => $target,
                        'jatuh_tempo_sewa' => $jatuh_tempo,
                        'start_date' => $row['start_date'],
                        'all_progress' => doubleval(preg_replace('/[^0-9.]/', "", $row['all_progress'])),
                        'gedung' => doubleval(preg_replace('/[^0-9.]/', "", $row['gedung'])),
                        'layout' => doubleval(preg_replace('/[^0-9.]/', "", $row['layout'])),
                        'kontraktor' => doubleval(preg_replace('/[^0-9.]/', "", $row['kontraktor'])),
                        'line_telp' => doubleval(preg_replace('/[^0-9.]/', "", $row['line_telp'])),
                        'tambah_daya' => doubleval(preg_replace('/[^0-9.]/', "", $row['tambah_daya'])),
                        'renovation' => doubleval(preg_replace('/[^0-9.]/', "", $row['renovation'])),
                        'inventory_non_it' => doubleval(preg_replace('/[^0-9.]/', "", $row['inventory_non_it'])),
                        'barang_it' => doubleval(preg_replace('/[^0-9.]/', "", $row['barang_it'])),
                        'asuransi' => doubleval(preg_replace('/[^0-9.]/', "", $row['asuransi'])),
                    ]);
                } else {
                    throw new Exception($row);
                }
            } catch (\Throwable $th) {
                throw new Exception("Error: ".$th->getMessage()." at row: " . $row);
            }
        }
    }
}
