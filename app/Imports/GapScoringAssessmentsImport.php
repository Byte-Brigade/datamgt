<?php

namespace App\Imports;

use App\Models\Branch;
use App\Models\GapAsset;
use App\Models\GapScoring;
use App\Models\GapScoringAssessment;
use App\Models\GapScoringProject;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;
use Maatwebsite\Excel\Concerns\WithValidation;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class GapScoringAssessmentsImport implements ToCollection, WithHeadingRow
{
    use Importable;

    public function collection(Collection $rows)
    {
        DB::beginTransaction();
        try {
            foreach ($rows as $row) {

                $branch = Branch::where('branch_name', 'like', '%' . $row['nama_cabang'] . '%')->first();
                activity()->disableLogging();
                if ($branch && $row['type'] == 'Assessment') {
                    $tgl_scoring = is_int($row['tgl_scoring']) ? Date::excelToDateTimeObject($row['tgl_scoring'])->format('Y-m-d') : null;

                    GapScoring::updateOrCreate(
                        [
                            'branch_id' => $branch->id,
                            'entity' => $row['entity'],
                            'description' => $row['deskripsi'],
                            'vendor' => $row['nama_vendor'],
                            'type' => $row['type'],
                        ],
                        [
                            'branch_id' => $branch->id,
                            'entity' => $row['entity'],
                            'description' => $row['deskripsi'],
                            'pic' => $row['pic'],
                            'status_pekerjaan' => !is_null($row['scoring_vendor']) ? 'Done' : 'On Progress',
                            'schedule_scoring' => $row['schedule_scoring'],
                            'dokumen_perintah_kerja' => $row['dokumen_perintah_kerja'],
                            'vendor' => $row['nama_vendor'],
                            'tgl_scoring' => $tgl_scoring,
                            'scoring_vendor' => $row['scoring_vendor'],
                            'type' => $row['type'],
                            'keterangan' => $row['ket'],
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

}
