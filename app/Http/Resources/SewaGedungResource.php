<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SewaGedungResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'branch_id' => $this->branch_id,
            'status_kepemilikan' => $this->status_kepemilikan,
            'jangka_waktu' => !is_null($this->jangka_waktu) ? $this->jangka_waktu. ' Tahun' : null,
            'open_date' => $this->open_date,
            'jatuh_tempo' => $this->jatuh_tempo,
            'owner' => $this->owner,
            'biaya_per_tahun' => $this->biaya_per_tahun,
            'total_biaya' => $this->total_biaya,
            'branches' => $this->branches,
            'type_name' => $this->branches->branch_types->type_name,

        ];
    }
}
