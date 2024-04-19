<?php

namespace App\Imports;

use App\Models\Branch;
use App\Models\Employee;
use App\Models\OpsSkbirtgs;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;
use Maatwebsite\Excel\Concerns\WithValidation;

class SkBirtgsImport implements ToModel, WithHeadingRow, WithValidation
{
    use Importable;

    public function model(array $row)
    {
        $branch = trim($row['kantor_cabang']);
        $branch_id = Branch::where('branch_name', 'like', "%$branch%")->pluck('id')->first();

        $penerima_kuasa = isset($row['penerima_kuasa']) ? explode(' - ', $row['penerima_kuasa']) : [null, null];
        $penerima_kuasa_1 = isset($penerima_kuasa[0]) ? Employee::whereHas('employee_positions', function(Builder $query) {
            $query->where('position_name', 'Branch Manager')->orWhere('position_name', 'Branch Service Manager');
        })->where([['name', 'like', "%$penerima_kuasa[0]%"], ['branch_id', '=', $branch_id]])->pluck('id')->first() : null;
        $penerima_kuasa_2 = isset($penerima_kuasa[1]) ? Employee::whereHas('employee_positions', function(Builder $query) {
            $query->where('position_name', 'Branch Manager')->orWhere('position_name', 'Branch Service Manager');
        })->where([['name', 'like', "%$penerima_kuasa[1]%"], ['branch_id', '=', $branch_id]])->pluck('id')->first() : null;

        activity()->disableLogging();
        $ops_skbirtgs = OpsSkbirtgs::updateOrCreate(
            [
                'branch_id' => $branch_id
            ],
            [
                'no_surat' => $row['nomor_surat'],
                'branch_id' => $branch_id,
            ]
        );

        $penerima_kuasa_ids = array_filter([$penerima_kuasa_1, $penerima_kuasa_2], fn($value) => !is_null($value) && $value !== '');
        if (count($penerima_kuasa_ids) > 0) {
            $ops_skbirtgs->penerima_kuasa()->sync($penerima_kuasa_ids);
        }
        return $ops_skbirtgs;
    }


    public function rules(): array
    {
        return [
            'kantor_cabang' => ['required', 'string'],
        ];
    }
}
