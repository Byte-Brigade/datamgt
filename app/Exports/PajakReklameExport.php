<?php

namespace App\Exports;

use App\Models\OpsPajakReklame;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PajakReklameExport implements FromView, ShouldAutoSize
{
    use Exportable;

    protected $branch_id;

    public function __construct($branch_id)
    {
        $this->branch_id = $branch_id;
    }

    public function view(): View
    {
        $branch_id = $this->branch_id;
        $data = OpsPajakReklame::with('branches')->newQuery();
        // if (isset($branch_id) && $branch_id != '0') {
        //     $data = $data->whereHas('branches', function ($query) use ($branch_id) {
        //         $query->where('id', $branch_id);
        //     });
        // }

        $data = $data->get();

        return view('exports.pajakreklames', [
            'pajakreklames' => $data
        ]);
    }
}
