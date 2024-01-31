<?php

namespace App\Exports\Report\BRO;

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

class SummarySheet implements FromView, ShouldAutoSize, WithTitle
{
    use Exportable;

    private $data;
    private $periode;
    public function __construct($data, $periode)
    {
        $number = 1;
        $this->data = $data->sortBy('category')->groupBy('category')->map(function ($bros, $category) use(&$number) {
            return  [
                'no' => $number++,
                'category' => $category,
                'target' => $bros->count(),
                'done' => $bros->where('status', 'Done')->count(),
                'on_progress' => $bros->where('status', 'On Progress')->count(),
                'not_start' => $bros->where('all_progress', 0)->whereNotIn('status', ['Done', 'On Progress', 'Drop'])->count(),
                'drop' => $bros->where('status', 'Drop')->count(),
            ];
        });
        $this->periode = $periode;
    }

    public function view(): View
    {
        return view('exports.reporting.bro', [
            'data' => $this->data,
        ]);
    }

    public function title(): string
    {
        return 'Summary BRO - ' . $this->periode;
    }
}
