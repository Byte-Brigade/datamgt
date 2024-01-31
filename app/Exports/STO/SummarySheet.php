<?php

namespace App\Exports\Perdin;

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

class StaffSummarySheet implements FromCollection, ShouldAutoSize, WithTitle, WithHeadings
{
    use Exportable;

    private $data;
    private $periode;
    public function __construct($data, $periode)
    {
        $number = 1;
        $this->data = $data->groupBy('user')->map(function ($perdins, $user) {
            $spender = $perdins->flatMap(function ($spender) {
                return $spender->gap_perdin_details;
            });
            return [
                'User' => $user,
                'Airline' => number_format($spender->where('category', 'Airline')->sum('value'), 0, '.', ','),
                'KA' => number_format($spender->where('category', 'KA')->sum('value'), 0, '.', ','),
                'Hotel' => number_format($spender->where('category', 'Hotel')->sum('value'), 0, '.', ','),
                'Total' => number_format($spender->sum('value'), 0, '.', ',')

            ];
        })->sortByDesc(function ($item) {
            return (int) str_replace(',','',$item['Total']);
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
        return 'Summary Staff - ' . $this->periode;
    }
}
