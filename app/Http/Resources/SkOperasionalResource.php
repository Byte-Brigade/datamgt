<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SkOperasionalResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'no_surat' => $this->no_surat,
            'branch_id' => $this->branch_id,
            'expiry_date' => $this->expiry_date,
            'note' => $this->note,
            'file' => $this->file,
            'penerima_kuasa' => $this->penerima_kuasa,
            'branches' => $this->branches
        ];
    }
}
