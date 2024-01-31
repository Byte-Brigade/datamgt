<?php

namespace App\Exports\Toners;

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

class TonerSheet implements FromCollection, ShouldAutoSize, WithTitle, WithHeadings
{
    use Exportable;
    private $data;
    private $periode;

    public function __construct($data, $periode)
    {
        $number = 1;
        $this->data = $data->map(function($toner) use(&$number) {
            $toner->idecice_date = Carbon::parse($toner->idecice_date)->format('d/m/Y');
            return [
                'No' => $number++,
                'Nama Cabang' => $toner->branches->branch_name,
                'Invoice' => $toner->invoice,
                'Idecice Date' => $toner->idecice_date,
                'Quantity' => $toner->quantity,
                'Price' => number_format($toner->price, 0, '.', ','),
                'Total' => number_format($toner->total, 0, '.', ','),
            ];
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
        return 'Data Toner - '.$this->periode;
    }
}
