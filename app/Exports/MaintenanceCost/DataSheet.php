<?php

namespace App\Exports\MaintenanceCost;

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
        $this->data = $data->map(function ($item) use (&$number) {
           return [
                'No' => $number++,
                'Nama Cabang' => $item->branches->branch_name,
                'Nama Project' => $item->nama_project,
                'Entity' => $item->entity,
                'Category' => $item->category,
                'Jenis Pekerjaan' => $item->jenis_pekerjaan,
                'Nilai OE Interior' => number_format($item->nilai_oe_interior, 0, '.', ','),
                'Nilai OE ME' => number_format($item->nilai_oe_me, 0, '.', ','),
                'Total OE' => number_format($item->total_oe, 0, '.', ','),
                'Nama Vendor' => $item->nama_vendor,
                'Nilai Project sesuai Memo/Persetujuan' => number_format($item->nilai_project_memo, 0, '.', ','),
                'Nilai Project sesuai Final Account' => number_format($item->nilai_project_final, 0, '.', ','),
                'Kerja Tambah / Kurang' => number_format($item->kerja_tambah_kurang, 0, '.', ','),
                ''
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
        return 'Periode - ' . $this->periode;
    }
}
