<?php

namespace App\Exports\STO;

use App\Http\Resources\KdoMobilResource;
use App\Models\Branch;
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
        $number = 1;
        $this->data = $data->flatMap(function($item) use(&$number) {
            return $item->gap_assets->map(function($asset) use(&$number) {
                return [
                    'No' => $number++,
                    'Category' => $asset->category,
                    'Asset Number' => $asset->asset_number,
                    'Asset Description' => $asset->asset_description,
                    'Date In Place Service' => isset($asset->date_in_place_service) ? Carbon::parse($asset->date_in_place_service)->format('d/m/Y') : '',
                    'Asset Cost' => number_format($asset->cost, 0, '.', ','),
                    'Accum Depre' => number_format($asset->accum_depre, 0, '.', ','),
                    'Asset Location' => $asset->location,
                    'Major Category' => $asset->major_category,
                    'Minor Category' => $asset->minor_category,
                    'Depre Exp' => number_format($asset->depre_exp, 0, '.', ','),
                    'Net Book Value' => number_format($asset->net_book_value, 0, '.', ','),
                    'Remark' => $asset->remark,
                ];
            });
        });
        $this->periode = $periode;
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
        return 'Asset STO - '.$this->periode;
    }
}
