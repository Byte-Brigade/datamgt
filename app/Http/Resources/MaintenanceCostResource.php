<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MaintenanceCostResource extends JsonResource
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
            'branch_name' => $this->branches->branch_name,
            'branch_type' => $this->branches->branch_types->type_name,
            'nama_project' => $this->nama_project,
            'category' => $this->category,
            'jenis_pekerjaan' => $this->jenis_pekerjaan,
            'nilai_oe_interior' => $this->nilai_oe_interior,
            'nilai_oe_me' => $this->nilai_oe_me,
            'total_oe' => $this->total_oe,
            'nama_vendor' => $this->nama_vendor,
            'nilai_project_memo' => $this->nilai_project_memo,
            'nilai_project_final' => $this->nilai_project_final,
            'kerja_tambah_kurang' => $this->kerja_tambah_kurang,
            'periode' => $this->periode,
        ];
    }
}
