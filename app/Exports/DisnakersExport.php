<?php

namespace App\Exports;

use App\Models\GapDisnaker;
use App\Models\OpsSpeciment;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class DisnakersExport implements FromView, ShouldAutoSize
{
    use Exportable;
    public function view(): View
    {
        return view('exports.disnakers', [
            'disnakers' => GapDisnaker::all()
        ]);
    }
}
