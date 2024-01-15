<?php

namespace App\Exports\AlihDaya;

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
        $this->data = $data->map(function($item) use(&$number) {
            return [
                'No' => $number++,
                'Nama Pegawai' => $item->nama_pegawai,
                'Jenis Pekerjaan' => $item->jenis_pekerjaan,
                'User' => $item->user,
                'Lokasi' => $item->lokasi,
                'Vendor' => $item->vendor,
                'Cost' => number_format($item->cost, 0, '.', ','),
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
        return 'Data Alih Daya - '.$this->periode;
    }
}
