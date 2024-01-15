<?php

namespace App\Exports\BRO;

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
                'Nama Cabang' => $item->branch_name,
                'Type' => $item->branch_type,
                'Kategori Realisasi' => $item->category,
                'Status' => $item->status,
                'Target' => $item->target,
                'Jatuh Tempo Sewa' => isset($item->jatuh_tempo_sewa) ? Carbon::parse($item->jatuh_tempo_sewa)->format('d/m/Y') : '',
                'Start Date' => $item->start_date,
                'All Progress' => round($item->all_progress * 100) . '%',
                'Gedung' => round($item->gedung * 100) . '%',
                'Layout' => round($item->layout * 100) . '%',
                'Kontraktor' => round($item->kontraktor * 100) . '%',
                'Line Telp' => round($item->line_telp * 100) . '%',
                'Tambah Daya' => round($item->tambah_daya * 100) . '%',
                'Renovation' => round($item->renovation * 100) . '%',
                'Inventory Non IT' => round($item->inventory_non_it * 100) . '%',
                'Barang IT' => round($item->barang_it * 100) . '%',
                'Asuransi' => round($item->asuransi * 100) . '%',
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
