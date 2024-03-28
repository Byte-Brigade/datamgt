<?php

namespace App\Imports;

use App\Models\Branch;
use App\Models\GapAlihDaya;
use Carbon\Carbon;
use App\Models\GapKdo;
use App\Models\GapKdoMobil;
use App\Models\GapPerdin;
use App\Models\GapToner;
use App\Models\KdoMobilBiayaSewa;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithValidation;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class TonerImport implements ToCollection, WithHeadingRow
{
    use Importable;

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {

            $row['cabang'] = str_replace('BANK SAHABAT SAMPOERNA, PT - ', '', $row['cabang']);
            $branch_type = isset(explode(' ', $row['cabang'])[0]) ? explode(' ', $row['cabang'])[0] : '';
            if (in_array($branch_type, ['KF', 'SFI'])) {

                $branch = Branch::where('branch_name', 'like', '%' . trim(preg_replace("/\b(KF|SFI)\b/i", "", $row['cabang'])) . '%')
                    ->whereHas('branch_types', function ($q) use ($branch_type) {
                        return $q->where('type_name', $branch_type == 'KF' ? 'KFO' : 'KFNO');
                    })->first();
                if (isset($branch)) {
                    GapToner::updateOrCreate(
                        [
                            'branch_id' => $branch->id,
                            'invoice' => $row['invoice'],
                            'idecice_date' => Date::excelToDateTimeObject($row['idecice_date']),
                            'cartridge_order' => $row['cartridge_order'],
                        ],
                        [
                            'quantity' => $row['quantity'],
                            'price' => round($row['price']),
                            'total' => round($row['total']),
                        ]
                    );
                }
            } else {
                $cabang = trim(preg_replace("/\b(KF|SFI)\b/i", "", $row['cabang']));
                $branch = Branch::where('branch_name', 'like', '%' .  ($cabang == 'Head Office' ? 'Kantor Pusat' : $cabang) . '%')->first();

                if (isset($branch)) {

                    GapToner::updateOrCreate(
                        [
                            'branch_id' => $branch->id,
                            'invoice' => $row['invoice'],
                            'idecice_date' => Date::excelToDateTimeObject($row['idecice_date']),
                            'cartridge_order' => $row['cartridge_order'],
                        ],
                        [
                            'quantity' => $row['quantity'],
                            'price' => round($row['price']),
                            'total' => round($row['total']),

                        ]
                    );
                }
            }
        }
    }

}
