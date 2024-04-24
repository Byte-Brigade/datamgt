<?php

namespace App\Imports;

use App\Helpers\PartitionManager;
use App\Jobs\ProcessPartitioning;
use App\Models\Branch;
use App\Models\GapAsset;
use App\Models\GapAssetDetail;
use App\Models\GapHasilSto;
use App\Models\GapSto;
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
use Maatwebsite\Excel\Concerns\WithProgressBar;
use Maatwebsite\Excel\Concerns\WithUpserts;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Events\BeforeSheet;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class AssetsImport implements ToCollection, WithHeadingRow, WithValidation, WithProgressBar
{

    protected $sheetName;
    use Importable;

    public function collection(Collection $rows)
    {
        try {
            DB::beginTransaction();
            foreach ($rows as $row) {
                $cabang = str_contains($row['cabang'], 'Sampoerna') ? 'Sampoerna' : $row['cabang'];
                $branch = Branch::where('branch_name', 'like', '%' . $cabang . '%')->first();
                if(!in_array($row['semester'], ['Smtr 1', 'Smtr 2'])) {
                    throw new Exception("Gunakan format semester: Smtr 1 atau Smtr 2");
                }

                activity()->disableLogging();
                if ($branch) {
                    $gap_asset = GapAsset::where('asset_number', $row['asset_number'])->first();
                    if (isset($gap_asset)) {
                        $gap_asset->update([
                            'branch_id' => $branch->id,
                            'category' => $row['category'],
                            'asset_number' => $row['asset_number'],
                            'asset_description' => $row['asset_description'],
                            'date_in_place_service' => is_int($row['date_in_place_service']) ? Date::excelToDateTimeObject($row['date_in_place_service']) : null,
                            'tgl_awal_susut' => is_int($row['tgl_awal_susut']) ? Date::excelToDateTimeObject($row['tgl_awal_susut']) : null,
                            'tgl_akhir_susut' => is_int($row['tgl_akhir_susut']) ? Date::excelToDateTimeObject($row['tgl_awal_susut']) : null,
                            'asset_cost' => round($row['asset_cost']),
                            'accum_depre' => round($row['accum_depre']),
                            'asset_location' => $row['asset_location'],
                            'major_category' => $row['major_category'],
                            'minor_category' => $row['minor_category'],
                            'depre_exp' => round($row['depre_exp']),
                            'net_book_value' => abs(round($row['asset_cost']) - round($row['accum_depre'])),
                        ]);
                    } else {
                        $gap_asset = GapAsset::create(
                            [
                                'branch_id' => $branch->id,
                                'category' => $row['category'],
                                'asset_number' => $row['asset_number'],
                                'asset_description' => $row['asset_description'],
                                'date_in_place_service' => is_int($row['date_in_place_service']) ? Date::excelToDateTimeObject($row['date_in_place_service']) : null,
                                'tgl_awal_susut' => is_int($row['tgl_awal_susut']) ? Date::excelToDateTimeObject($row['tgl_awal_susut']) : null,
                                'tgl_akhir_susut' => is_int($row['tgl_akhir_susut']) ? Date::excelToDateTimeObject($row['tgl_awal_susut']) : null,
                                'asset_cost' => round($row['asset_cost']),
                                'accum_depre' => round($row['accum_depre']),
                                'asset_location' => $row['asset_location'],
                                'major_category' => $row['major_category'],
                                'minor_category' => $row['minor_category'],
                                'depre_exp' => round($row['depre_exp']),
                                'net_book_value' => abs(round($row['asset_cost']) - round($row['accum_depre'])),
                            ]
                        );
                    }

                    $year = Carbon::createFromDate($row['tahun'])->startOfYear()->format('Y-m-d');
                    $sto = GapSto::where('periode', $year)->where('semester', $row['semester'])->where('status', 'Done')->first();
                    if (!isset($sto)) {
                        $sto = GapSto::create([
                            'periode' => $year,
                            'semester' => $row['semester'],
                            'status' => 'Done',
                        ]);
                    }

                    $gap_hasil_sto = GapHasilSto::where('gap_sto_id', $sto->id)->where('branch_id', $branch->id)->first();
                    if (!isset($gap_hasil_sto)) {
                        $gap_hasil_sto = GapHasilSto::create(
                            [
                                'branch_id' => $branch->id,
                                'gap_sto_id' => $sto->id,
                                'remarked' => true,
                            ]
                        );
                    }
                    GapAssetDetail::updateOrCreate(
                        [
                            'asset_number' => $gap_asset->asset_number,
                            'gap_hasil_sto_id' => $gap_hasil_sto->id,
                        ],
                        [
                            'gap_hasil_sto_id' => $gap_hasil_sto->id,
                            'asset_number' => $gap_asset->asset_number,
                            'semester' => $sto->semester,
                            'periode' => $sto->periode,
                            'status' => $row['status'],
                        ]
                    );
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
            '*.tahun' => 'required|integer',
            '*.semester' => 'required|string',
            '*.status' => 'required|string',
        ];
    }
}
