<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ScoringAssessmentsResource extends JsonResource
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
            'pic' => $this->pic,
            'entity' => $this->entity,
            'description' => $this->description,
            'dokumen_perintah_kerja' => $this->dokumen_perintah_kerja,
            'vendor' => $this->vendor,
            'tgl_scoring' => $this->tgl_scoring,
            'schedule_scoring' => $this->schedule_scoring,
            'scoring_vendor' => $this->scoring_vendor,
            'type' => $this->type,
            'keterangan' => $this->keterangan,
            'branches' => isset($this->branches) ? $this->branches : [],
        ];
    }
}
