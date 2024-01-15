<?php

namespace App\Imports;

use App\Models\Branch;
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
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Row;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

class DisnakerImport implements ToCollection, WithHeadingRow, WithValidation
{
    use Importable;
    public function collection(Collection $rows)
    {
        try {
            $currentCabang = null;
            foreach ($rows as $index => $row) {
                if (!is_null($row['cabang'])) {
                    $currentCabang = $row['cabang'];
                }


                $branch = Branch::where('branch_name', 'like', '%' . $currentCabang . '%')->first();

                $jenis_perizinan = JenisPerizinan::where('name', 'like', '%' . $row['jenis_perizinan'] . '%')->get()->first();
                $izin = isset($jenis_perizinan) ? $jenis_perizinan : JenisPerizinan::create(['name' => $row['jenis_perizinan']]);
                $periode = Date::excelToDateTimeObject($row['periode']);
                $exist_periode = GapDisnaker::where('periode', $periode)->first();
                if ($branch) {
                    if ($exist_periode) {
                        // Menambahkan jumlah hari dari tanggal Excel
                        GapDisnaker::updateOrCreate(
                            [
                                'branch_id' => $branch->id,
                                'jenis_perizinan_id' => $izin->id,
                                'tgl_pengesahan' => !is_string($row['tgl_pengesahan']) ? Date::excelToDateTimeObject($row['tgl_pengesahan']) : null,
                                'tgl_masa_berlaku' => !is_string($row['tgl_masa_berlaku_sd']) ?  Date::excelToDateTimeObject($row['tgl_masa_berlaku_sd']) : null,
                                'progress_resertifikasi' => $row['progress_resertifikasi'],
                                'periode' => $periode,
                            ],
                            [
                                'branch_id' => $branch->id,
                                'jenis_perizinan_id' => $izin->id,
                                'tgl_pengesahan' => !is_string($row['tgl_pengesahan']) ? Date::excelToDateTimeObject($row['tgl_pengesahan']) : null,
                                'tgl_masa_berlaku' => !is_string($row['tgl_masa_berlaku_sd']) ?  Date::excelToDateTimeObject($row['tgl_masa_berlaku_sd']) : null,
                                'progress_resertifikasi' => $row['progress_resertifikasi'],
                                'periode' => $periode,
                            ]
                        );
                    } else {
                        GapDisnaker::create([
                                'branch_id' => $branch->id,
                                'jenis_perizinan_id' => $izin->id,
                                'tgl_pengesahan' => !is_string($row['tgl_pengesahan']) ? Date::excelToDateTimeObject($row['tgl_pengesahan']) : null,
                                'tgl_masa_berlaku' => !is_string($row['tgl_masa_berlaku_sd']) ?  Date::excelToDateTimeObject($row['tgl_masa_berlaku_sd']) : null,
                                'progress_resertifikasi' => $row['progress_resertifikasi'],
                                'periode' => $periode,
                        ]);
                    }
                } else {
                    throw new Exception("Error : Nama Branch " . $row['nama_cabang'] . " tidak ditemukan.");
                }
            }
        } catch (\Throwable $th) {
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
