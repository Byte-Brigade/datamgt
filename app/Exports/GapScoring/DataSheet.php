<?php

namespace App\Exports\GapScoring;


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
    private $type;


    public function __construct($data, $periode, $type)
    {
        $this->type = $type;
        $number = 1;
        if ($this->type == 'Project') {
            $this->data = $data->map(function ($item) use (&$number) {
                return [
                    'No' => $number++,
                    'Nama Cabang' => $item->branches->branch_name,
                    'Description' => $item->description,
                    'Entity' => $item->entity,
                    'PIC' => $item->pic,
                    'Status Pekerjaan' => $item->status_pekerjaan,
                    'Dokumen Perintah Kerja' => $item->dokumen_perintah_kerja,
                    'Nama Vendor' => $item->vendor,
                    'Nilai Project' => number_format($item->nilai_project, 0, '.', ','),
                    'Tgl Selesai Pekerjaan' => Carbon::parse($item->tgl_selesai_pekerjaan)->format('d/m/Y'),
                    'Tgl BAST' => Carbon::parse($item->tgl_bast)->format('d/m/Y'),
                    'Tgl. Request Scoring' => Carbon::parse($item->tgl_requet_scoring)->format('d/m/Y'),
                    'Tgl Scoring' => Carbon::parse($item->tgl_scoring)->format('d/m/Y'),
                    'SLA' => $item->sla,
                    'Actual' => $item->actual,
                    'Meet the SLA' => $item->meet_the_sla,
                    'Scoring Vendor' => $item->scoring_vendor,
                    'Scoring Schedule' => $item->schedule_schedule,
                    'Type' => $item->type,
                ];
            });
        } else {
            $this->data = $data->map(function ($item) use (&$number) {
                return [
                    'No' => $number++,
                    'Nama Cabang' => $item->branches->branch_name,
                    'Description' => $item->description,
                    'Entity' => $item->entity,
                    'PIC' => $item->pic,
                    'Dokumen Perintah Kerja' => $item->dokumen_perintah_kerja,
                    'Nama Vendor' => $item->vendor,
                    'Tgl Scoring' => Carbon::parse($item->tgl_scoring)->format('d/m/Y'),
                    'Scoring Vendor' => $item->scoring_vendor,
                    'Scoring Schedule' => $item->schedule_schedule,
                    'Type' => $item->type,
                    'Keterangan' => $item->keterangan,
                ];
            });
        }

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
