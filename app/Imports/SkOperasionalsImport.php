<?php

namespace App\Imports;

use App\Models\Branch;
use App\Models\Employee;
use App\Models\OpsSkOperasional;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class SkOperasionalsImport implements ToCollection, WithHeadingRow
{
    use Importable;

    public function collection(Collection $rows)
    {
        $mergedCells = [];
        activity()->disableLogging();
        foreach ($rows as $key => $row) {
            if ($key % 2 === 0) {
                $mergedCells[$key] = $row['cabang'];
            } else {
                $row['cabang'] = $mergedCells[$key - 1] ?? null;
            }

            $branch_name = trim($row['cabang']);
            $branch_id = Branch::where('branch_name', 'like', "%$branch_name%")->whereHas('branch_types', function (Builder $q) {
                $q->where('type_name', 'KC')->orWhere('type_name', 'KCP');
            })->pluck('id')->first();

            $penerima_kuasa = isset($row['nama_penerima_kuasa']) ? $row['nama_penerima_kuasa'] : null;
            $penerima_kuasa_id = isset($penerima_kuasa) ? Employee::whereHas('employee_positions', function(Builder $query) {
                $query->where('position_name', 'Branch Manager')->orWhere('position_name', 'Branch Service Manager');
            })->where([['name', 'like', "%$penerima_kuasa%"], ['branch_id', $branch_id]])->pluck('id')->first() : null;

            if (isset($row['no_surat'])) {
                $ops_sk_operasional = OpsSkOperasional::updateOrCreate(
                    [
                        'branch_id' => $branch_id,
                    ],
                    [
                        'no_surat' => $row['no_surat'],
                        'branch_id' => $branch_id,
                        'expiry_date' => Date::excelToDateTimeObject($row['expiry_date'])->format('Y-m-d')
                    ]
                );
                $ops_sk_operasional->penerima_kuasa()->sync($penerima_kuasa_id);
            } else if (isset($penerima_kuasa_id)) {
                $ops_sk_operasional = OpsSkOperasional::where('branch_id', $branch_id)->get()->first();
                $ops_sk_operasional->penerima_kuasa()->attach($penerima_kuasa_id);
            }
        }
    }


}
