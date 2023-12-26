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
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Events\BeforeSheet;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class AssetsImport implements ToCollection, WithHeadingRow, WithEvents, WithValidation
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
                // $gap_asset = GapAsset::get();
                // $currentMonthDate = Carbon::parse($sheetName)->firstOfMonth()->toDateString();
                // $exists = GapAsset::where('periode', '<', $currentMonthDate)->exists();

                // if ($exists) {
                //     // dd(Carbon::parse($sheetName)->firstOfMonth()->format('Ymd'));
                //     // throw new Exception("test");
                //     ProcessPartitioning::dispatch($sheetName, 'gap_assets');
                // }
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
                    $periode = Date::excelToDateTimeObject($row['periode'])->format('Y-m-d');
                    $exist_periode = GapAsset::where('periode', Date::excelToDateTimeObject($row['periode']))->first();
                    if ($exist_periode) {
                        GapAsset::updateOrCreate(
                            ['asset_number' => $row['asset_number'], 'periode' => $periode],
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
                                'periode' => $periode,

                            ]
                        );
                    } else {
                        GapAsset::create(
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
                                'periode' => $periode,

                            ]
                        );
                    }
                }
            }
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw new Exception("Error : " . $th->getMessage());
        }
    }




    public function rules(): array
    {
        return [
            '*.periode' => 'required|integer',
        ];
    }
}
