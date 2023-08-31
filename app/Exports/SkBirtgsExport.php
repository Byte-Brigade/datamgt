<?php

namespace App\Exports;

use App\Models\OpsSkbirtgs;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;

class SkBirtgsExport implements FromView
{
    use Exportable;

    public function view(): View
    {
        return view('exports.skbirtgs', [
            'ops_skbirtgs' => OpsSkbirtgs::with('branches')->get()
        ]);
    }
}
