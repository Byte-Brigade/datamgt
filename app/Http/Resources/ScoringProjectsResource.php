<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ScoringProjectsResource extends JsonResource
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
            'status_pekerjaan' => $this->status_pekerjaan,
            'description' => $this->description,
            'dokumen_perintah_kerja' => $this->dokumen_perintah_kerja,
            'vendor' => $this->vendor,
            'nilai_project' => $this->nilai_project,
            'tgl_selesai_pekerjaan' => $this->tgl_selesai_pekerjaan,
            'tgl_bast' => $this->tgl_bast,
            'tgl_request_scoring' => $this->tgl_request_scoring,
            'tgl_scoring' => $this->tgl_scoring,
            'sla' => $this->sla,
            'actual' => $this->actual,
            'meet_the_sla' => $this->meet_the_sla == 1 ? 'YES' : 'NO'   ,
            'schedule_scoring' => $this->schedule_scoring,
            'scoring_vendor' => $this->scoring_vendor,
            'type' => $this->type,
            'keterangan' => $this->keterangan,
            'tgl_selesai' => $this->tgl_selesai,
            'branch_id' => $this->branch_id,
            'branches' => isset($this->branches) ? $this->branches : [],
        ];
    }
}
