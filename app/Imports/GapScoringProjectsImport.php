<?php

namespace App\Imports;

use App\Models\Branch;
use App\Models\GapAsset;
use App\Models\GapScoring;
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

class GapScoringProjectsImport implements ToModel, WithHeadingRow, WithUpserts, WithBatchInserts, WithChunkReading
{
    use Importable;

    public function model(array $row)
    {
        $branch = Branch::where('branch_name', 'like', '%' . $row['nama_cabang'] . '%')->first();
        if ($branch && $row['type'] == 'Project') {



            // Menambahkan jumlah hari dari tanggal Excel
            $tgl_bast = Date::excelToDateTimeObject($row['tgl_bast'])->format('Y-m-d');
            $tgl_scoring = Date::excelToDateTimeObject($row['tgl_scoring'])->format('Y-m-d');
            $actual = Carbon::createFromFormat('Y-m-d', $tgl_bast)->diffInDays($tgl_scoring) + 1;
            return new GapScoring([
                'branch_id' => $branch->id,
                'entity' => $row['entity'],
                'description' => $row['description'],
                'pic' => $row['pic'],
                'schedule_scoring' => $row['schedule_scoring'],
                'status_pekerjaan' => $row['status_pekerjaan'],
                'dokumen_perintah_kerja' => $row['dokumen_perintah_kerja'],
                'vendor' => $row['nama_vendor'],
                'nilai_project' => $row['nilai_project'],
                'tgl_selesai_pekerjaan' => Date::excelToDateTimeObject($row['tgl_selesai_pekerjaan'])->format('Y-m-d'),
                'tgl_bast' => $tgl_bast,
                'tgl_request_scoring' => Date::excelToDateTimeObject($row['tgl_request_scoring'])->format('Y-m-d'),
                'tgl_scoring' => $tgl_scoring,
                'sla' => $row['sla'],
                'actual' => $actual,
                'meet_the_sla' => $actual < 15 ? true : ($actual > 14 ? false : true),
                'scoring_vendor' => $row['scoring_vendor'],
                'schedule_scoring' => $row['schedule_scoring'],
                'type' => $row['type'],
                'keterangan' => $row['keterangan'],
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
