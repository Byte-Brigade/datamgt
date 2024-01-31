<?php

namespace App\Imports;

use App\Models\Branch;
use App\Models\GapAsset;
use App\Models\GapScoring;
use App\Models\GapScoringProject;
use App\Models\InfraScoring;
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

class InfraScoringAssessmentsImport implements ToModel, WithHeadingRow, WithUpserts, WithBatchInserts, WithChunkReading
{
    use Importable;

    public function model(array $row)
    {
        $branch = Branch::where('branch_name', 'like', '%' . $row['nama_cabang'] . '%')->first();
        if ($branch && $row['type'] == 'Assessment') {



            // Menambahkan jumlah hari dari tanggal Excel
            return new InfraScoring([
                'branch_id' => $branch->id,
                'entity' => $row['entity'],
                'description' => $row['deskripsi'],
                'pic' => $row['pic'],
                'dokumen_perintah_kerja' => $row['dokumen_perintah_kerja'],
                'vendor' => $row['nama_vendor'],
                'tgl_scoring' => is_int($row['tgl_scoring']) ? Date::excelToDateTimeObject($row['tgl_scoring']) : null,
                'scoring_vendor' => $row['scoring_vendor'],
                'schedule_scoring' => $row['schedule_scoring'],
                'type' => $row['type'],
                'keterangan' => $row['ket'],
                'periode' => Date::excelToDateTimeObject($row['periode']),
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
