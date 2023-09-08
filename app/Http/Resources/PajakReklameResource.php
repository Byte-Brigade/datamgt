<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PajakReklameResource extends JsonResource
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
            'periode_awal' => $this->periode_awal,
            'periode_akhir' => $this->periode_akhir,
            'note' => $this->note,
            'additional_info' => $this->additional_info,
            'branches' => $this->branches
        ];
    }
}
