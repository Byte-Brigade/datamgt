<?php

namespace App\Imports;

use App\Models\Branch;
use App\Models\BranchType;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;
use Maatwebsite\Excel\Concerns\WithValidation;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class BranchesImport implements ToModel, WithHeadingRow, WithUpserts, WithValidation
{
    use Importable;
    public function model(array $row)
    {
        $types = BranchType::all()->pluck('type_name')->toArray();
        $regexPattern =implode('|', array_map('preg_quote', $types));

        $type_name = preg_match("/\b({$regexPattern})\b/", $row['nama_cabang'], $match) ? $match[0] : null;

        $branch_type = BranchType::where('type_name', $type_name)->pluck('id')->first();
        $branch_type = isset($branch_type) ? $branch_type : null;
        $branch_name = trim(preg_replace("/\b({$regexPattern})\b/","",$row['nama_cabang']));

        # Generate kode baru untuk kode_cabang yang kosong
        if (is_null($row['kode_cabang'])) {
            $branch = Branch::where('branch_type_id', $branch_type)->where('branch_name', $branch_name)->get()->first();
            if (isset($branch)) {
                return;
            }

            $branch = Branch::where('branch_name', $branch_name)
                ->get()->first();
            if (isset($branch)) {
                $row['kode_cabang'] = $type_name . '001' . substr($branch->branch_code, -3);
            } else {
                $row['kode_cabang'] = $type_name . '01' . str_pad(mt_rand(10, 99), 3, "0", STR_PAD_LEFT);
            }
        }

        $slug = strtolower($type_name).'-'.strtolower(str_replace(" ", "-",$branch_name));

        return new Branch([
            'branch_type_id' => $branch_type,
            'branch_code' => $row['kode_cabang'],
            'branch_name' => $branch_name,
            'address' => $row['alamat'],
            'telp' => $row['telp'],
            'layanan_atm' => is_null($row['layanan_atm']) ? "Tidak Ada" : $row['layanan_atm'],
            'npwp' => $row['npwp'],
            'nitku' => $row['nitku'],
            'izin' => $row['ijin_biojk'],
            'status' => $row['status'],
            'area' => $row['area'],
            'slug' => $slug,
            'masa_sewa' => isset($row['masa_sewa']) ? preg_replace('/[^0-9]/', '',  $row['masa_sewa']) : null,
            'expired_date' => !is_null($row['jatuh_tempo']) ? Date::excelToDateTimeObject($row['jatuh_tempo']) : null,
            'open_date' => !is_null($row['open_date']) ? Date::excelToDateTimeObject($row['open_date']) : null,
            'owner' => $row['pemilik'],
            'sewa_per_tahun' => is_int($row['sewa_per_tahun'])  ? $row['sewa_per_tahun'] : null,
            'total_biaya_sewa' => is_int($row['total_biaya_sewa']) ? $row['total_biaya_sewa'] : null,

        ]);
    }

    public function uniqueBy()
    {
        return 'branch_code';
    }

    public function rules(): array
    {
        return [
            'nama_cabang' => ['required', 'string'],
            'alamat' => ['required', 'string'],

        ];
    }
}
