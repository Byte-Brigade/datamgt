<?php

namespace App\Imports;

use App\Models\Branch;
use App\Models\GapAsset;
use App\Models\GapScoring;
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

class GapScoringsImport implements ToCollection, WithHeadingRow, WithValidation
{
    use Importable;

    public function collection(Collection $rows)
    {
        DB::beginTransaction();
        try {

            foreach ($rows as $row) {

                $periode = Date::excelToDateTimeObject($row['periode']);
                $exist_periode = GapScoring::where('periode', $periode)->first();




                $branch = Branch::where('branch_name', 'like', '%' . $row['nama_cabang'] . '%')->first();
                if ($branch) {



                    // Menambahkan jumlah hari dari tanggal Excel
                    $tgl_bast = Date::excelToDateTimeObject($row['tgl_bast'])->format('Y-m-d');
                    $tgl_scoring = Date::excelToDateTimeObject($row['tgl_scoring'])->format('Y-m-d');
                    $actual = Carbon::createFromFormat('Y-m-d', $tgl_bast)->diffInDays($tgl_scoring);
                    if ($exist_periode) {
                        GapScoring::updateOrCreate(
                            [
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
                                'meet_the_sla' => $actual <= 14 ? true : false,
                                'scoring_vendor' => $row['scoring_vendor'],
                                'schedule_scoring' => $row['schedule_scoring'],
                                'type' => $row['type'],
                                'keterangan' => $row['keterangan'],
                                'periode' => $periode,
                            ],
                            [
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
                                'meet_the_sla' => $actual <= 14 ? true : false,
                                'scoring_vendor' => $row['scoring_vendor'],
                                'schedule_scoring' => $row['schedule_scoring'],
                                'type' => $row['type'],
                                'keterangan' => $row['keterangan'],
                                'periode' => $periode,
                            ]
                        );
                    } else {
                        GapScoring::create([
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
                            'meet_the_sla' => $actual <= 14 ? true : false,
                            'scoring_vendor' => $row['scoring_vendor'],
                            'schedule_scoring' => $row['schedule_scoring'],
                            'type' => $row['type'],
                            'keterangan' => $row['keterangan'],
                            'periode' => $periode,
                        ]);
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
