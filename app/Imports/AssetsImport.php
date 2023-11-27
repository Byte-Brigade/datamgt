<?php

namespace App\Imports;

use App\Models\Branch;
use App\Models\GapAsset;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class AssetsImport implements ToModel, WithHeadingRow, WithUpserts, WithBatchInserts, WithChunkReading
{
    use Importable;

    public function model(array $row)
    {
        $cabang = str_contains($row['cabang'], 'Sampoerna') ? 'Sampoerna' : $row['cabang'];
        $branch = Branch::where('branch_name', 'like', '%' . $cabang . '%')->first();
        if ($branch) {

            return new GapAsset([
                'branch_id' => $branch->id,
                'category' => $row['category'],
                'asset_number' => $row['asset_number'],
                'asset_description' => $row['asset_description'],
                'date_in_place_service' => is_int($row['date_in_place_service']) ? Date::excelToDateTimeObject($row['date_in_place_service']) : null,
                'asset_cost' => round($row['asset_cost']),
                'accum_depre' => round($row['accum_depre']),
                'asset_location' => $row['asset_location'],
                'major_category' => $row['major_category'],
                'minor_category' => $row['minor_category'],
                'depre_exp' => round($row['depre_exp']),
                'net_book_value' => round($row['net_book_value']),
            ]);
        }
    }

    public function uniqueBy()
    {
        return 'branch_id';
    }

    public function batchSize(): int
    {
        return 1024;
    }

    public function chunkSize(): int
    {
        return 1024;
    }
}
