<?php

namespace App\Exports\Disnaker;

use App\Models\GapDisnaker;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class DataSheet implements FromView, WithTitle, ShouldAutoSize
{


    use Exportable;
    private $periode;

    public function __construct($periode) {
        $this->periode = $periode;
    }
    public function view(): View
    {
        return view('exports.disnakers', [
            'disnakers' => GapDisnaker::all()
        ]);
    }

    public function title(): string
    {
        return 'Data Disnaker - '.$this->periode;
    }
}
