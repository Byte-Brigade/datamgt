<?php

namespace App\Http\Resources\Inquery;

use App\Models\Branch;
use Illuminate\Http\Resources\Json\JsonResource;

class LicensesResource extends JsonResource
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
            'branch_name' => $this->branch_name,
            'branch_code' => $this->branch_code,
            'branch_types' => $this->branch_types,
            'izin' => isset($this->izin) ? 'Ada' : 'Tidak Ada',
            'disnaker' => isset($this->gap_disnaker) ? 'Ada' : 'Tidak Ada',
            'skbirtgs' => isset($this->ops_skbirtgs) ? 'Ada' : 'Tidak Ada',
            'apar' => isset($this->ops_apar) ? 'Ada' : 'Tidak Ada',
            'skoperasional' => isset($this->ops_skoperasional) ? 'Ada' : 'Tidak Ada',
            'pajak_reklame' => isset($this->ops_pajak_reklames) ? 'Ada' : 'Tidak Ada',
        ];
    }
}
