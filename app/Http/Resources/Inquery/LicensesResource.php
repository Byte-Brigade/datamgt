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
            'type_name' => $this->branch_types->type_name,
            'izin' => isset($this->izin) ? 'Ada' : 'Tidak Ada',
            'file_ojk' => isset($this->file_ojk) ? $this->file_ojk : '-',
            'disnaker' => isset($this->gap_disnaker) ? 'Ada' : 'Tidak Ada',
            'file_disnaker' => isset($this->gap_disnaker->file) ? $this->gap_disnaker->file : '-',
            'skbirtgs' => isset($this->ops_skbirtgs) ? 'Ada' : 'Tidak Ada',
            'file_skbirtgs' => isset($this->ops_skbirtgs->file) ? $this->ops_skbirtgs->file : '-',
            'apar' => isset($this->ops_apar) ? 'Ada' : 'Tidak Ada',
            'skoperasional' => isset($this->ops_skoperasional) ? 'Ada' : 'Tidak Ada',
            'file_skoperasional' => isset($this->ops_skoperasional->file) ? $this->ops_skoperasional->file : '-',
            'pajak_reklame' => isset($this->ops_pajak_reklames) ? 'Ada' : 'Tidak Ada',
            'file_pajak_reklame' => isset($this->ops_pajak_reklames->file_izin_reklame) ? $this->ops_pajak_reklames->file_izin_reklame : '-',
        ];
    }
}
