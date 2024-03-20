<?php

namespace App\Exports\HasilSTO;

use App\Http\Resources\KdoMobilResource;
use App\Models\Branch;
use App\Models\GapAsset;
use App\Models\GapKdo;
use App\Models\GapKdoMobil;
use App\Models\KdoMobilBiayaSewa;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class DataSheet implements FromCollection, ShouldAutoSize, WithTitle, WithHeadings
{
    use Exportable;
    private $data;
    private $periode;

    public function __construct($data, $periode)
    {
        $this->periode = $data->periode;
        $number = 1;
        $data = GapAsset::whereHas('gap_asset_details', function ($q) use ($data) {
            return $q->where('periode', $data->periode)->where('semester', $data->semester);
        })->get();
        $this->data = $data->map(function ($asset) use (&$number) {
            $gap_asset_details = $asset->gap_asset_details->first();
            return [
                'No' => $number++,
                'Category' => $asset->category,
                'Asset Number' => $asset->asset_number,
                'Asset Description' => $asset->asset_description,
                'Date In Place Service' => isset($asset->date_in_place_service) ? Carbon::parse($asset->date_in_place_service)->format('d/m/Y') : '',
                'Asset Cost' => number_format($asset->cost, 0, '.', ','),
                'Accum Depre' => number_format($asset->accum_depre, 0, '.', ','),
                'Asset Location' => $asset->branches->branch_name,
                'Major Category' => $asset->major_category,
                'Minor Category' => $asset->minor_category,
                'Depre Exp' => number_format($asset->depre_exp, 0, '.', ','),
                'Net Book Value' => number_format($asset->net_book_value, 0, '.', ','),
                'Remark' => isset($gap_asset_details) ? $gap_asset_details->status : null,
                'Tahun' => isset($gap_asset_details) ? Carbon::parse($gap_asset_details->periode)->year : null,
                'Semester' => isset($gap_asset_details) ? $gap_asset_details->semester : null,

            ];
        });
    }


    public function collection()
    {

        $collections = $this->data;

        return $collections;
    }

    public function headings(): array
    {
        // Assuming the collection is not empty, get the keys of the first item
        if ($this->data->isNotEmpty()) {
            return array_keys($this->data->first());
        }
        return [];
    }

    public function title(): string
    {
        return 'Asset STO - ' . $this->periode;
    }
}
