<?php

namespace App\Imports;

use App\Models\Branch;
use App\Models\GapAsset;
use App\Models\GapScoring;
use App\Models\GapScoringAssessment;
use App\Models\GapScoringProject;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class GapScoringAssessmentsImport implements ToModel, WithHeadingRow, WithUpserts, WithBatchInserts, WithChunkReading
{
    use Importable;

    public function model(array $row)
    {
        $branch = Branch::where('branch_name', 'like', '%' . $row['nama_cabang'] . '%')->first();
        if ($branch && $row['type'] == 'Assessment') {


            $tgl_scoring = is_int($row['tgl_scoring']) ? Date::excelToDateTimeObject($row['tgl_scoring'])->format('Y-m-d') : null;
            return new GapScoring([
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
                'schedule_scoring' => $row['schedule_scoring'],
                'type' => $row['type'],
                'keterangan' => $row['ket'],
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
