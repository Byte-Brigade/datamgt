<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AparResource extends JsonResource
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
            'details' => $this->detail,
            'keterangan' => isset($this->detail) ? $this->detail->count().' Tabung' : 'Tidak Ada',
            'branches' => $this->branches
        ];
    }
}
