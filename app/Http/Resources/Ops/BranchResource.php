<?php

namespace App\Http\Resources\Ops;

use App\Models\Branch;
use Illuminate\Http\Resources\Json\JsonResource;

class BranchResource extends JsonResource
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
            'branch_code' => $this->branch_code,
            'branch_name' => $this->branch_name,
            'type_name' => $this->branch_types->type_name,
            'branch_type_id' => $this->branch_type_id,
            'npwp' => $this->npwp,
            'area' => $this->area,
            'address' => $this->address,
            'telp' => $this->telp,
            'fasilitas_atm' => isset($this->layanan_atm) && $this->layanan_atm != 'Tidak Ada' ? 'Ada' : 'Tidak Ada',
            'layanan_atm' => isset($this->layanan_atm) ? $this->layanan_atm : 'Tidak Ada',
            'file_ojk' => $this->file_ojk,
            'photo' => $this->photo,
            'slug' => $this->slug,
        ];
    }
}
