<?php

namespace App\Exports\PKS;


use Carbon\Carbon;
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
                'Vendor' => $item->vendor,
                'Entity' => $item->entity,
                'Type' => $item->type,
                'Description' => $item->description,
                'Contract Date' => Carbon::parse($item->contract_date)->format('d/m/Y'),
                'Contract No.' => $item->contract_no,
                'Durasi Kontrak' => $item->durasi_kontrak,
                'Awal' => Carbon::parse($item->awal)->format('d/m/Y'),
                'Akhir' => Carbon::parse($item->akhir)->format('d/m/Y'),
                'Tahun Akhir' => $item->tahun_akhir,
                'Status' => $item->status,
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
        return 'Data Perjanjian Kerja Sama (PKS) - '.$this->periode;
    }
}
