<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class KdoMobilResource extends JsonResource
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
            'gap_kdo_id' => $this->gap_kdo_id,
            'branch_id' => $this->branch_id,
            'vendor' => $this->vendor,
            'nopol' => $this->nopol,
            'awal_sewa' => $this->awal_sewa,
            'akhir_sewa' => $this->akhir_sewa,
            'biaya_sewa' => collect($this->biaya_sewa)->flatMap(function ($data) {
                return [strtolower(Carbon::parse($data['periode'])->format('F')) => $data['value'] != 0 ? "Rp " . number_format($data['value'], 0, ',', '.') : '-'];
            }),
            'branches' => $this->branches,
        ];
    }
}
