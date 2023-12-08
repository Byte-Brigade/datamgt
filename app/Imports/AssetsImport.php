<?php

namespace App\Imports;

use App\Helpers\PartitionManager;
use App\Jobs\ProcessPartitioning;
use App\Models\Branch;
use App\Models\GapAsset;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Events\BeforeSheet;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class AssetsImport implements ToCollection, WithHeadingRow, WithEvents
{

    protected $sheetName;
    use Importable;

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                // Retrieve the sheet name from the event
                $sheetName = $event->getSheet()->getTitle();
                $this->sheetName = $sheetName;
                $gap_asset = GapAsset::get();
                $currentMonthDate = Carbon::parse($sheetName)->firstOfMonth()->toDateString();
                $exists = GapAsset::where('periode', '<', $currentMonthDate)->exists();

                if ($exists) {

                    ProcessPartitioning::dispatchSync($sheetName, 'gap_assets');
                }
                // Call the service to handle the partitioning
            },
        ];
    }


    public function collection(Collection $rows)
    {

        try {
            DB::beginTransaction();
            foreach ($rows as $row) {
                $cabang = str_contains($row['cabang'], 'Sampoerna') ? 'Sampoerna' : $row['cabang'];
                $branch = Branch::where('branch_name', 'like', '%' . $cabang . '%')->first();
                if ($branch) {
                    GapAsset::updateOrCreate(
                        ['asset_number' => $row['asset_number'], 'category' => $row['category']],
                        [
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
                            'periode' => Carbon::parse($this->sheetName)->firstOfMonth()->format('Y-m-d'),

                        ]
                    );
                }
            }
            DB::commit();
        } catch (\Throwable $th) {
            throw new Exception($th->getMessage());
        }
    }


    public function uniqueBy()
    {
        return 'branch_id';
    }

}
