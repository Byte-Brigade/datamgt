<?php

namespace App\Imports;

use App\Models\Branch;
use App\Models\BranchType;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;
use Maatwebsite\Excel\Concerns\WithValidation;

class BranchesImport implements ToModel, WithHeadingRow, WithUpserts, WithValidation
{
    use Importable;
    public function model(array $row)
    {
        $types = BranchType::all()->pluck('type_name')->toArray();
        $branch_name_arr = explode(' ', $row['nama_cabang']);
        $type_name = array_shift($branch_name_arr);
        $branch_type = null;
        if (in_array($type_name, $types)) {
            $branch_type = BranchType::where('type_name', $type_name)->pluck('id')->first();
        }
        $branch_name = join(' ', $branch_name_arr);
        return new Branch([
            'branch_type_id' => $branch_type,
            'branch_code' => $row['kode_cabang'],
            'branch_name' => $branch_name,
            'address' => $row['alamat'],
            'telp' => $row['telp']
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
