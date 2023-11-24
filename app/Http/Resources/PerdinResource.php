<?php

namespace App\Http\Resources;

use Carbon\Carbon;
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
            'periode' => Carbon::parse($this->periode)->format('M Y'),
            'value' => $this->value,

        ];
    }
}
