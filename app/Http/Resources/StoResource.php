<?php

namespace App\Http\Resources;

use App\Models\Branch;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class StoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'periode' => Carbon::parse($this->periode)->year,
            'semester' => $this->semester,
            'status' => $this->status,
            'keterangan' => $this->keterangan,
        ];
    }
}
