<?php

namespace App\Exports;


use App\Models\OpsSpeciment;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;

class SpecimentsExport implements FromView
{
    use Exportable;
    public function view(): View
    {
        return view('exports.speciments', [
            'speciments' => OpsSpeciment::all()
        ]);
    }
}
