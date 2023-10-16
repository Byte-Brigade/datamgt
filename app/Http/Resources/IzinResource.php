<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class IzinResource extends JsonResource
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
            'jenis_perizinan_id' => $this->jenis_perizinan_id,
            'tgl_pengesahan' => $this->tgl_pengesahan,
            'tgl_masa_berlaku' => $this->tgl_masa_berlaku,
            'progress_resertifikasi' => $this->progress_resertifikasi,
            'branches' => $this->branches,
            'jenis_perizinan' => $this->jenis_perizinan
        ];
    }
}
