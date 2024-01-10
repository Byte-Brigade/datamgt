<?php

namespace App\Exports;

use App\Models\GapSto;
use App\Models\OpsSpeciment;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;

class STOExport implements FromView
{
    use Exportable;
    public function view(): View
    {
        return view('exports.stos', [
            'stos' => GapSto::all()
        ]);
    }
}
