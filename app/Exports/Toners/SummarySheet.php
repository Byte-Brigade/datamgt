<?php

namespace App\Exports\Toners;

use App\Models\Branch;
use App\Models\GapKdo;
use App\Models\GapToner;
use App\Models\OpsSpeciment;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class SummarySheet implements FromCollection, ShouldAutoSize, WithTitle, WithHeadings
{
    use Exportable;

    private $data;
    private $periode;
    public function __construct($data, $periode)
    {
        $number = 1;
        $this->data = $data->groupBy('branch_id')->map(function ($toners, $id) use (&$number) {
            $branch = Branch::find($id);
            return [
                'No' => $number++,
                'Nama Cabang' => $branch->branch_name,
                'Quantity' => $toners->sum('quantity'),
                'Price' => number_format($toners->sum('price'), 0, '.', ','),
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
        return 'Summary Toner - ' . $this->periode;
    }
}
