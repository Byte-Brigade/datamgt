<?php

namespace App\Imports;

use App\Models\Branch;
use App\Models\GapDisnaker;
use App\Models\InfraBro;
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
use Maatwebsite\Excel\Row;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

class BroImport implements ToModel, WithHeadingRow
{
    use Importable;
    use Importable;

    public function model(array $row)
    {

        $status = preg_match('/drop/i', $row['cabang'], $match) ? ucfirst($match[0]) : $row['status'];
        $branch_name = preg_replace('/bss|cabang|[!@#$%^&*()_+-]|drop/i', '', $row['cabang']);
        $branch_type = preg_match('/\b(KF|KFO|KFNO|SFI|KC|KCP)\b/', $branch_name, $match) ? $match[0] : null;
        $branch_name = trim(preg_replace('/\b(KF|KFO|KFNO|SFI|KC|KCP)\b/','', $branch_name));
        $branch = Branch::where('branch_name', $branch_name)->first();
        $branch_type = !is_null($branch_type) ? $branch_type : (isset($branch) ? $branch->branch_types->type_name : null);
        $target = is_int($row['target']) ? Date::excelToDateTimeObject($row['target'])->format('Y-m-d') : null;
        $jatuh_tempo = is_int($row['jatuh_tempo_sewa'])  ? Date::excelToDateTimeObject($row['jatuh_tempo_sewa']) : null;

        return new InfraBro([
            'branch_name' => $branch_name,
            'branch_type' => $branch_type,
            'activity' => $row['kategori_realisasi'],
            'status' => $status,
            'target' => $target,
            'jatuh_tempo_sewa' => $jatuh_tempo,
            'all_progress' => $row['all_progress'],
            'periode' => Date::excelToDateTimeObject($row['periode'])
        ]);
    }
}
