<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PerdinResource extends JsonResource
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
            'divisi_pembebanan' => $this->divisi_pembebanan,
            'category' => $this->category,
            'tipe' => $this->tipe,
            'periode' => $this->periode,
            'value' => $this->value,

        ];
    }
}
