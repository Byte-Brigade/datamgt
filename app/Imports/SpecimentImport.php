<?php

namespace App\Imports;

use App\Models\Branch;
use App\Models\OpsPajakReklame;
use App\Models\OpsSpeciment;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;
use Throwable;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class SpecimentImport implements ToModel, WithHeadingRow, WithUpserts
{
    use Importable;
    public function model(array $row)
    {
        if (isset($row['tgl_spesimen'])) {


            $branch = explode(' ', $row['cabang']);
            array_shift($branch);

            $branch = implode(' ', $branch);
            $id = Branch::where('branch_name', 'like', "%$branch%")->pluck('id')->first();


            return new OpsSpeciment([
                'branch_id' => Branch::where('branch_name', 'like', "%$branch%")->pluck('id')->first(),
                'tgl_speciment' =>  Date::excelToDateTimeObject($row['tgl_spesimen']),

            ]);
        }
    }

    public function uniqueBy()
    {
        return 'branch_id';
    }

    public function headingRow()
    {
        return 4;
    }
}
