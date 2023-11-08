<?php

namespace App\Imports;

use App\Models\Branch;
use App\Models\GapAsset;
use App\Models\GapDisnaker;
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

class AssetsImport implements ToCollection, WithHeadingRow, WithUpserts
{
    use Importable;
    use Importable;

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {

            $branch = Branch::where('branch_name', 'like', '%' . $row['asset_location'] . '%')->first();

            GapAsset::create([
                'branch_id' => isset($branch) ? $branch->id : null,
                'category' => $row['category'],
                'asset_number' => $row['asset_number'],
                'asset_description' => $row['asset_description'],
                'date_in_place_service' => is_int($row['date_in_place_service']) ? Date::excelToDateTimeObject($row['date_in_place_service']) : null,
                'asset_cost' => $row['asset_cost'],
                'asset_location' => $row['asset_location'],
                'major_category' => $row['major_category'],
                'minor_category' => $row['minor_category'],
                'depre_exp' => $row['depre_exp'],
                'net_book_value' => $row['net_book_value'],
            ]);
        }
    }

    public function uniqueBy()
    {
        return 'branch_id';
    }
}
