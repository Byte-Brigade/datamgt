<?php

namespace App\Imports;

use App\Models\Branch;
use App\Models\BranchType;
use App\Models\OpsPajakReklame;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;
use Maatwebsite\Excel\Concerns\WithValidation;
use Throwable;

class PajakReklameImport implements ToCollection, WithHeadingRow, WithValidation
{
    use Importable;
    public function collection(Collection $rows)

    {
        activity()->disableLogging();
        foreach ($rows as $index => $row) {
            $periode = trim($row['periode_pajak_reklame']) != '-' ? explode(' - ', $row['periode_pajak_reklame']) : [null, null];
            $types = BranchType::all()->pluck('type_name')->toArray();
            $regexPattern = implode('|', array_map('preg_quote', $types));

            $type_name = preg_match("/\b({$regexPattern})\b/", $row['cabang'], $match) ? $match[0] : null;

            $branch_type = BranchType::where('type_name', $type_name)->pluck('id')->first();
            $branch_type = isset($branch_type) ? $branch_type : null;
            $branch_name = trim(preg_replace("/\b({$regexPattern})\b/", "", $row['cabang']));

            $branch = $row['kode_cab'] != null
                ? Branch::where('branch_code', $row['kode_cab'])->get()
                : Branch::where('branch_name', 'like', "%{$branch_name}%")->whereHas('branch_types', function ($q) use ($type_name) {
                    $type_name = $type_name == 'KF' ? ['KFO', 'KFNO'] : ($type_name == 'SFI' ? ['KFNO'] : [$type_name]);
                    return $q->whereIn('type_name', $type_name);
                })->get();

            try {
                if (isset($branch)) {

                    if ($branch->count() > 1) {
                        foreach ($branch as $item) {

                            OpsPajakReklame::updateOrCreate(
                                ['branch_id' => $item->id],
                                [
                                    'branch_id' => $item->id,
                                    'periode_awal' => isset($periode[0]) ? Carbon::createFromFormat('d/m/Y', trim($periode[0])) : null,
                                    'periode_akhir' => isset($periode[1]) ? Carbon::createFromFormat('d/m/Y', trim($periode[1])) : null,
                                    'note' => $row['keterangan'],
                                ]
                            );
                        }
                    } else {
                        $branch = $branch->first();
                        OpsPajakReklame::updateOrCreate(
                            ['branch_id' => $branch->id],
                            [
                                'branch_id' => $branch->id,
                                'periode_awal' => isset($periode[0]) ? Carbon::createFromFormat('d/m/Y', trim($periode[0])) : null,
                                'periode_akhir' => isset($periode[1]) ? Carbon::createFromFormat('d/m/Y', trim($periode[1])) : null,
                                'note' => $row['keterangan'],
                            ]
                        );
                    }
                } else {
                    throw new Exception("Branch tidak tidak ditemukan pada baris ke - " . ($index + 1));
                }
            } catch (\Throwable $th) {
                dd($row);
                throw new Exception("Error pada baris ke - " . ($index + 1) . ".  Message: {$th->getMessage()}");
            }
        }
    }


    public function uniqueBy()
    {
        return 'cabang';
    }


    public function rules(): array
    {
        return [
            'cabang' => ['required', 'string'],
        ];
    }
}
