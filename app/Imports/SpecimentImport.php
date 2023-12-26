<?php

namespace App\Imports;

use App\Models\Branch;
use App\Models\BranchType;
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
use Maatwebsite\Excel\Concerns\WithValidation;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class SpecimentImport implements ToModel, WithHeadingRow, WithUpserts, WithValidation
{
    use Importable;
    public function model(array $row)
    {

        // Temukan indeks kunci 'keterangan'
        $types = BranchType::all()->pluck('type_name')->toArray();
        $regexPattern = implode('|', array_map('preg_quote', $types));


        $branch_name = trim(preg_replace("/\b({$regexPattern}|KPO|CABANG|CAPEM)\b/i", "", $row['cabang']));

        $branch = Branch::where('branch_name', 'like', '%' . $branch_name . '%')->first();

        return new OpsSpeciment([
            'branch_id' => $branch->id,
            'tgl_speciment' =>  Date::excelToDateTimeObject($row['tgl_spesimen']),

        ]);
    }

    public function uniqueBy()
    {
        return 'cabang';
    }

    public function rules(): array
    {
        return [
            'cabang' => ['required','string'],
            'tgl_spesimen' => ['required','integer'],
        ];
    }
}
