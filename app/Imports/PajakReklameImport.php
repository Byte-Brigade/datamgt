<?php

namespace App\Imports;

use App\Models\Branch;
use App\Models\BranchType;
use App\Models\OpsPajakReklame;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;
use Throwable;

class PajakReklameImport implements ToModel, WithHeadingRow, WithUpserts
{
    use Importable;
    public function model(array $row)
    {
        $periode = trim($row['periode_pajak_reklame']) != '-' ? explode(' - ', $row['periode_pajak_reklame']) : [null, null];
        $branch = trim($row['cabang']);
        $branch_arr = explode(' ', $branch);
        $branch_type = array_shift($branch_arr);
        $branch_type_id = BranchType::where('type_name', $branch_type)->pluck('id')->first();
        $branch = join(' ', $branch_arr);
        $branch_id = $row['kode_cab'] != null
            ? Branch::where('branch_code', $row['kode_cab'])->pluck('id')->first()
            : Branch::where(function (Builder $query) use ($branch, $branch_type_id) {
                $query->where('branch_type_id', $branch_type_id)
                    ->where('branch_name', 'like', "%$branch%");
            })->pluck('id')->first();
        try {
            return new OpsPajakReklame([
                'branch_id' => $branch_id,
                'periode_awal' => isset($periode[0]) ? Carbon::createFromFormat('d/m/Y', trim($periode[0])) : null,
                'periode_akhir' => isset($periode[1]) ? Carbon::createFromFormat('d/m/Y', trim($periode[1])) : null,
                'note' => $row['keterangan'],
            ]);
        } catch (Throwable $th) {
            dd($branch);
        }
    }

    public function uniqueBy()
    {
        return 'branch_id';
    }

    public function headingRow()
    {
        return 3;
    }
}
