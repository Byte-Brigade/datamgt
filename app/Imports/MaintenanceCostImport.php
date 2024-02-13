<?php

namespace App\Imports;

use App\Models\Branch;
use App\Models\InfraBro;
use App\Models\InfraMaintenanceCost;
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

class MaintenanceCostImport implements ToCollection, WithHeadingRow, WithValidation
{
    use Importable;
    use Importable;

    public function collection(Collection $rows)
    {


        foreach ($rows as $row) {
            try {

                $branch_name = preg_replace('/bss|cabang|[!@#$%^&*()_+-]|drop/i', '', $row['nama_cabang']);
                $branch_type = preg_match('/\b(KF|KFO|KFNO|KC)\b/', $branch_name, $match) ? $match[0] : null;
                $branch_name = trim(preg_replace('/\b(KF|KFO|KFNO|KC)\b/', '', $branch_name));
                $branch_name = $branch_name == 'SS' ? 'Sampoerna Strategic' : ucwords($branch_name);
                $branch = Branch::where('branch_name', 'like', '%' . $branch_name . '%')->first();
                $branch_type = !is_null($branch_type) ? $branch_type : (isset($branch) ? $branch->branch_types->type_name : null);

                $periode = Date::excelToDateTimeObject($row['periode']);

                $exist_periode = InfraMaintenanceCost::where('periode', $periode)->first();
                if ($branch) {
                    if ($exist_periode) {
                        InfraMaintenanceCost::updateOrCreate(
                            [
                                'branch_id' => $branch->id,
                                'nama_project' => $row['nama_project'],
                                'entity' => $row['entity'],
                                'category' => $row['category'],
                                'jenis_pekerjaan' => $row['jenis_pekerjaan'],
                                'nilai_oe_interior' => is_int($row['nilai_oe_interior']) ? $row['nilai_oe_interior'] : 0,
                                'nilai_oe_me' => is_int($row['nilai_oe_me']) ? $row['nilai_oe_me'] : 0,
                                'total_oe' => is_int($row['total_oe']) ? $row['total_oe'] : 0,
                                'nama_vendor' => $row['nama_vendor'],
                                'nilai_project_memo' => is_int($row['nilai_project_sesuai_memopersetujuan']) ? $row['nilai_project_sesuai_memopersetujuan'] : 0,
                                'nilai_project_final' => is_int($row['nilai_project_sesuai_final_account']) ? $row['nilai_project_sesuai_final_account'] : 0,
                                'kerja_tambah_kurang' => is_int($row['kerja_tambah_kurang']) ? $row['kerja_tambah_kurang'] : 0,
                                'keterangan' => $row['keterangan'],
                                'periode' => $periode,
                            ],
                            [
                                'branch_id' => $branch->id,
                                'nama_project' => $row['nama_project'],
                                'entity' => $row['entity'],
                                'category' => $row['category'],
                                'jenis_pekerjaan' => $row['jenis_pekerjaan'],
                                'nilai_oe_interior' => is_int($row['nilai_oe_interior']) ? $row['nilai_oe_interior'] : 0,
                                'nilai_oe_me' => is_int($row['nilai_oe_me']) ? $row['nilai_oe_me'] : 0,
                                'total_oe' => is_int($row['total_oe']) ? $row['total_oe'] : 0,
                                'nama_vendor' => $row['nama_vendor'],
                                'nilai_project_memo' => is_int($row['nilai_project_sesuai_memopersetujuan']) ? $row['nilai_project_sesuai_memopersetujuan'] : 0,
                                'nilai_project_final' => is_int($row['nilai_project_sesuai_final_account']) ? $row['nilai_project_sesuai_final_account'] : 0,
                                'kerja_tambah_kurang' => is_int($row['kerja_tambah_kurang']) ? $row['kerja_tambah_kurang'] : 0,
                                'keterangan' => $row['keterangan'],
                                'periode' => $periode,
                            ]
                        );
                    } else {

                        InfraMaintenanceCost::create([
                            'branch_id' => $branch->id,
                            'nama_project' => $row['nama_project'],
                            'entity' => $row['entity'],
                            'category' => $row['category'],
                            'jenis_pekerjaan' => $row['jenis_pekerjaan'],
                            'nilai_oe_interior' => is_int($row['nilai_oe_interior']) ? $row['nilai_oe_interior'] : 0,
                            'nilai_oe_me' => is_int($row['nilai_oe_me']) ? $row['nilai_oe_me'] : 0,
                            'total_oe' => is_int($row['total_oe']) ? $row['total_oe'] : 0,
                            'nama_vendor' => $row['nama_vendor'],
                            'nilai_project_memo' => is_int($row['nilai_project_sesuai_memopersetujuan']) ? $row['nilai_project_sesuai_memopersetujuan'] : 0,
                            'nilai_project_final' => is_int($row['nilai_project_sesuai_final_account']) ? $row['nilai_project_sesuai_final_account'] : 0,
                            'kerja_tambah_kurang' => is_int($row['kerja_tambah_kurang']) ? $row['kerja_tambah_kurang'] : 0,
                            'keterangan' => $row['keterangan'],
                            'periode' => $periode,

                        ]);
                    }
                }
            } catch (\Throwable $th) {
                throw new Exception("Error: " . $th->getMessage() . " at row: " . $row);
            }
        }
    }

    public function rules(): array
    {
        return [
            '*.periode' => 'required|integer',
        ];
    }
}
