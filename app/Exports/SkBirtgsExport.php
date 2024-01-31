<?php

namespace App\Exports;

use App\Models\OpsSkbirtgs;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;

class SkBirtgsExport implements FromView
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
        $data = OpsSkbirtgs::with(['branches', 'penerima_kuasa'])->newQuery();
        if (isset($branch_id) && $branch_id != '0') {
            $data = $data->whereHas('branches', function ($query) use ($branch_id) {
                $query->where('id', $branch_id);
            });
        }

        $data = $data->get();
        return view('exports.skbirtgs', [
            'ops_skbirtgs' => $data
        ]);
    }
}
